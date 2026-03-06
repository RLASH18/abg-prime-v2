"""
NFCReader — threaded serial bridge for the RFID_IR_Monitor Arduino sketch.

Arduino protocol (9600 baud, newline-terminated):
  → PING           ← PONG
  → READ           ← TAG_TYPE:<t>  then  CARD:<code>  |  NOTAG  |  ERROR:<msg>
  → WRITE:<code>   ← TAG_TYPE:<t>  then  WRITE_OK     |  WRITE_FAIL:<msg>  |  ERROR:<msg>

Autonomous output (no command needed):
  ← MOTION         IR obstacle detected (state-change only)
  ← CLEAR          IR path is clear     (state-change only)

All blocking serial calls run in daemon threads so the Tkinter event loop
is never stalled.  Results are delivered via root.after(0, callback, result).
"""

import serial
import serial.tools.list_ports
import threading
import logging

log = logging.getLogger(__name__)

# ── Constants ─────────────────────────────────────────────────────────────────

BAUD_RATE       = 9600
CONNECT_TIMEOUT = 3       # seconds to wait for PONG on connect
# Arduino blocks for up to 7 s waiting for a card; give Python a comfortable margin
READ_TIMEOUT    = 12      # seconds to wait for CARD/NOTAG response
WRITE_TIMEOUT   = 12

DEFAULT_PORT    = "COM3"   # fallback if auto-detect finds nothing

# Result sentinel values consumed by the views
RESULT_OK       = "WRITE_OK"
RESULT_NOTAG    = "NOTAG"


# ── NFCReader ─────────────────────────────────────────────────────────────────

class NFCReader:
    """Serial bridge to the MFRC522_POS_v3 Arduino sketch."""

    def __init__(self):
        self._serial:    serial.Serial | None = None
        self._lock       = threading.Lock()
        self.connected   = False

        # IR listener callbacks (set by start_ir_listener)
        self._ir_on_motion = None
        self._ir_on_clear  = None
        self._ir_tk_root   = None

    # ── Connection ────────────────────────────────────────────────────────────

    def connect(self, port: str | None = None) -> bool:
        """
        Open the serial port and verify the Arduino is alive (PING → PONG).

        Parameters
        ----------
        port : str | None
            Explicit COM port (e.g. "COM3").  When *None* the first port whose
            description contains "Arduino" or "CH340" is used; falls back to
            DEFAULT_PORT.

        Returns
        -------
        bool : True if connected and PONG received.
        """
        target = port or self._auto_detect_port()
        try:
            self._serial = serial.Serial(target, BAUD_RATE, timeout=CONNECT_TIMEOUT)
            # Arduino resets on DTR; give it time to boot
            import time; time.sleep(2)
            self._serial.reset_input_buffer()
            self._serial.write(b"PING\n")
            resp = self._serial.readline().decode(errors="replace").strip()
            if resp == "PONG":
                self.connected = True
                log.info("NFCReader connected on %s", target)
                return True
            log.warning("NFCReader: unexpected PING response: %r", resp)
        except Exception as exc:
            log.warning("NFCReader: could not connect to %s — %s", target, exc)
        self.connected = False
        return False

    def disconnect(self):
        """Close the serial port cleanly."""
        if self._serial and self._serial.is_open:
            try:
                self._serial.close()
            except Exception:
                pass
        self.connected = False
        log.info("NFCReader disconnected")

    @staticmethod
    def _auto_detect_port() -> str:
        keywords = ("arduino", "ch340", "ch341", "ftdi", "usb serial")
        for p in serial.tools.list_ports.comports():
            desc = (p.description or "").lower()
            mfr  = (p.manufacturer or "").lower()
            if any(k in desc or k in mfr for k in keywords):
                log.info("NFCReader auto-detected port: %s (%s)", p.device, p.description)
                return p.device
        log.warning("NFCReader: no Arduino port found, using default %s", DEFAULT_PORT)
        return DEFAULT_PORT

    # ── Public API ────────────────────────────────────────────────────────────

    def read_tag(self, callback, tk_root) -> None:
        """
        Non-blocking NFC read.

        Sends READ to the Arduino in a daemon thread.  When a response arrives
        (or times out) *callback* is scheduled on the Tkinter thread via
        tk_root.after(0, callback, result_str).

        result_str:
          "CARD:<code>"   — tag successfully read
          "NOTAG"         — no tag presented within 7 s
          "ERROR:<msg>"   — reader or communication error
        """
        if not self._check_connected(callback, tk_root):
            return
        t = threading.Thread(
            target=self._run_command,
            args=(b"READ\n", READ_TIMEOUT, callback, tk_root),
            daemon=True,
        )
        t.start()

    def write_tag(self, code: str, callback, tk_root) -> None:
        """
        Non-blocking NFC write.

        Sends WRITE:<code> to the Arduino in a daemon thread.

        result_str:
          "WRITE_OK"      — tag written successfully
          "WRITE_FAIL:<reason>" — write failed (tag error)
          "ERROR:<msg>"   — reader or communication error
          "NOTAG"         — no tag presented within 7 s
        """
        code = code.strip()
        if len(code) == 0:
            tk_root.after(0, callback, "ERROR:Empty item code")
            return
        if len(code) > 16:
            tk_root.after(0, callback, "ERROR:Max 16 characters")
            return
        if not self._check_connected(callback, tk_root):
            return
        cmd = f"WRITE:{code}\n".encode()
        t = threading.Thread(
            target=self._run_command,
            args=(cmd, WRITE_TIMEOUT, callback, tk_root),
            daemon=True,
        )
        t.start()

    # ── IR Listener ───────────────────────────────────────────────────────────

    def start_ir_listener(self, on_motion_callback, on_clear_callback, tk_root) -> None:
        """
        Start a background daemon thread that listens for autonomous
        IR sensor output (MOTION / CLEAR) from the Arduino.

        Parameters
        ----------
        on_motion_callback : callable
            Called (via tk_root.after) when MOTION is received.
        on_clear_callback : callable
            Called (via tk_root.after) when CLEAR is received.
        tk_root : tk.Tk
            The Tkinter root, used to safely schedule callbacks on the UI thread.
        """
        self._ir_on_motion = on_motion_callback
        self._ir_on_clear  = on_clear_callback
        self._ir_tk_root   = tk_root
        t = threading.Thread(target=self._ir_listen_loop, daemon=True)
        t.start()
        log.info("NFCReader: IR listener started")

    def _ir_listen_loop(self) -> None:
        """
        Internal loop — reads serial lines in the background.
        Only reacts to MOTION and CLEAR; all other lines (CARD:, PONG,
        TAG_TYPE:, etc.) are ignored here — they are consumed by
        _run_command via the serial lock.
        """
        import time
        while True:
            if not self.connected or self._serial is None or not self._serial.is_open:
                time.sleep(0.5)
                continue
            try:
                raw = self._serial.readline()
                if not raw:
                    continue
                line = raw.decode(errors="replace").strip()
                if line == "MOTION" and self._ir_on_motion:
                    self._ir_tk_root.after(0, self._ir_on_motion)
                elif line == "CLEAR" and self._ir_on_clear:
                    self._ir_tk_root.after(0, self._ir_on_clear)
                # All other lines (RFID responses) are handled by _run_command
            except Exception as exc:
                log.debug("NFCReader IR loop error: %s", exc)
                time.sleep(0.2)

    # ── Internal helpers ──────────────────────────────────────────────────────

    def _check_connected(self, callback, tk_root) -> bool:
        if not self.connected or self._serial is None or not self._serial.is_open:
            tk_root.after(0, callback, "ERROR:NFC reader not connected")
            return False
        return True

    def _run_command(self, cmd: bytes, timeout: float, callback, tk_root) -> None:
        """
        Acquire the lock, send *cmd*, then read lines until a terminal response
        is found or *timeout* expires.  Fires callback with the result string.
        """
        with self._lock:
            try:
                import time

                # ── Step 1: flush any partial/stale bytes in both directions ─────
                # Sending an empty line makes the Arduino discard whatever is in
                # its inputBuffer (the sketch does `inputBuffer.trim()` then checks
                # `length() > 0`, so an empty line is a safe no-op).
                self._serial.reset_input_buffer()   # drop unsolicited output first
                self._serial.write(b"\n")            # flush Arduino's RX buffer
                self._serial.flush()                 # ensure bytes hit the wire
                time.sleep(0.15)                     # give sketch time to process it
                self._serial.reset_input_buffer()    # drop any echo/READY noise

                # ── Step 2: set timeout BEFORE sending the real command ───────────
                self._serial.timeout = timeout

                # ── Step 3: send the real command ────────────────────────────────
                self._serial.write(cmd)
                self._serial.flush()

                result = self._read_terminal_line()
            except Exception as exc:
                result = f"ERROR:{exc}"

        tk_root.after(0, callback, result)

    def _read_terminal_line(self) -> str:
        """
        Read lines from the Arduino until a terminal line is received.
        TAG_TYPE lines are logged and skipped; everything else is returned.
        """
        while True:
            raw = self._serial.readline()
            if not raw:
                return RESULT_NOTAG          # timeout — readline returned b""
            line = raw.decode(errors="replace").strip()
            if not line:
                continue
            log.debug("NFCReader ← %r", line)
            if line.startswith("TAG_TYPE:"):
                log.info("Tag type: %s", line[9:])
                continue                     # informational, keep reading
            return line                      # CARD:…  NOTAG  WRITE_OK  ERROR:…  etc.
