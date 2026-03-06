"""
Main application class — window setup, layout, and view routing.
"""
import logging
import tkinter as tk
from tkinter import messagebox

from app.theme.styles import apply_global_styles, COLORS
from app.components.sidebar import Sidebar
from app.views.cashier_dashboard import CashierDashboard
from app.views.nfc_tag_writer import NFCTagWriter
from app.core.arduino_bridge import ArduinoBridge

log = logging.getLogger(__name__)


class POSApp:
    """Root application controller."""

    MIN_W    = 960
    MIN_H    = 620
    TITLE    = "ABG Prime Builders Supplies Inc - POS System"

    def __init__(self):
        self.root = tk.Tk()
        self._configure_root()
        apply_global_styles(self.root)
        self._init_arduino()
        self._build_layout()
        self._register_views()
        self.navigate("cashier")

    # ── Setup ──────────────────────────────────────────────────────────────────

    def _configure_root(self):
        self.root.title(self.TITLE)
        self.root.minsize(self.MIN_W, self.MIN_H)
        self.root.configure(bg=COLORS["bg"])
        self.root.state("zoomed")  # Start maximized (full screen) on Windows
        self.root.protocol("WM_DELETE_WINDOW", self._on_close)

    def _init_arduino(self):
        """Initialise the ArduinoBridge (serial connection to the RFID + IR hardware)."""
        self.arduino = ArduinoBridge()
        ok = self.arduino.connect()
        if not ok:
            log.warning("Arduino not available — running without hardware.")

    def _build_layout(self):
        container = tk.Frame(self.root, bg=COLORS["bg"])
        container.pack(fill=tk.BOTH, expand=True)

        self.sidebar = Sidebar(container, navigate_callback=self.navigate)
        self.sidebar.pack(side=tk.LEFT, fill=tk.Y)

        # 1-px divider
        tk.Frame(container, bg=COLORS["border"], width=1).pack(side=tk.LEFT, fill=tk.Y)

        self.content = tk.Frame(container, bg=COLORS["bg"])
        self.content.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)

    def _register_views(self):
        cashier    = CashierDashboard(self.content)
        nfc_writer = NFCTagWriter(self.content)

        # Inject the Arduino bridge into each view that needs it
        cashier.set_arduino_bridge(self.arduino, self.root)
        nfc_writer.set_arduino_bridge(self.arduino, self.root)

        # Wire IR sensor callbacks to the cashier view
        if self.arduino.connected:
            self.arduino.start_ir_listener(
                on_motion_callback=cashier.on_ir_motion,
                on_clear_callback=cashier.on_ir_clear,
                tk_root=self.root,
            )

        self.views: dict[str, tk.Frame] = {
            "cashier": cashier,
            "nfc":     nfc_writer,
        }
        for view in self.views.values():
            view.place(relx=0, rely=0, relwidth=1, relheight=1)

    # ── Routing ────────────────────────────────────────────────────────────────

    def navigate(self, view_name: str):
        self.sidebar.set_active(view_name)
        view = self.views.get(view_name)
        if view:
            view.tkraise()

    # ── Run ────────────────────────────────────────────────────────────────────

    def run(self):
        self.root.mainloop()

    def _on_close(self):
        self.arduino.disconnect()
        self.root.destroy()
