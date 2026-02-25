"""
NFC Tag Writer view.
"""
import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime
from app.theme.styles import COLORS, FONTS
from app.views.base_view import BaseView

PLACEHOLDER = "Enter item code (e.g. 123-ABC)"

SAMPLE_HISTORY = [
    ("8923-AB", "2023-10-24 10:30 AM", "Success"),
    ("1122-CC", "2023-10-24 09:15 AM", "Failed"),
    ("5544-DD", "2023-10-23 04:45 PM", "Success"),
]


class NFCTagWriter(BaseView):
    """NFC Tag Writer page: write form + write history table."""

    def __init__(self, parent):
        super().__init__(parent)
        self._item_code_var = tk.StringVar()
        self._nfc    = None
        self._root   = None
        self._writing = False
        self._build()

    # ── NFC injection ──────────────────────────────────────────────────────────

    def set_nfc_reader(self, nfc, root):
        """Called by POSApp after construction to wire in the NFC service."""
        self._nfc  = nfc
        self._root = root

    # ── Build ──────────────────────────────────────────────────────────────────

    def _build(self):
        self.build_header(self, "NFC Tag Writer",
                          "Program and update NFC tags for inventory items.")
        wrap = tk.Frame(self, bg=COLORS["bg"], padx=24)
        wrap.pack(fill=tk.BOTH, expand=True)

        self._build_write_card(wrap)
        self._build_history(wrap)

    # ── Write Card ─────────────────────────────────────────────────────────────

    def _build_write_card(self, parent):
        inner = self.card(parent, padx=40, pady=28)
        inner.master.pack(fill=tk.X, pady=(0, 24))

        # NFC icon circle (Canvas)
        canvas = tk.Canvas(inner, width=80, height=80,
                           bg=COLORS["card_bg"], highlightthickness=0)
        canvas.pack(pady=(10, 24))
        canvas.create_oval(5, 5, 75, 75,
                           fill=COLORS["primary_light"], outline="")
        canvas.create_text(40, 41, text="(((●)))",
                           font=("Segoe UI", 24), fill=COLORS["primary"])

        # Centre-column container
        form = tk.Frame(inner, bg=COLORS["card_bg"])
        form.pack()

        tk.Label(form, text="Item Code", bg=COLORS["card_bg"],
                 fg=COLORS["text_primary"], font=FONTS["small_bold"]).pack(anchor="w",
                                                                            pady=(0, 6))

        # Entry with focus border
        entry_wrap = tk.Frame(form, bg=COLORS["primary"], padx=1, pady=1)
        entry_wrap.pack(fill=tk.X)

        self._entry = tk.Entry(entry_wrap, font=FONTS["label"], relief="flat",
                               bg=COLORS["white"], fg=COLORS["text_muted"],
                               insertbackground=COLORS["primary"], width=48,
                               justify="center")
        self._entry.pack(fill=tk.X, padx=12, pady=10)
        self._entry.insert(0, PLACEHOLDER)

        self._entry.bind("<FocusIn>",  self._on_focus_in)
        self._entry.bind("<FocusOut>", self._on_focus_out)

        # Write status label
        self._write_status_var = tk.StringVar(value="")
        tk.Label(form, textvariable=self._write_status_var,
                 bg=COLORS["card_bg"], fg=COLORS["text_secondary"],
                 font=FONTS["small_bold"]).pack(pady=(8, 0))

        self._rewrite_btn = self.accent_button(form, "↻  Rewrite Tag",
                                               command=self._rewrite_tag, pady=12)
        self._rewrite_btn.pack(fill=tk.X, pady=(8, 0))

    # ── Write History ──────────────────────────────────────────────────────────

    def _build_history(self, parent):
        tk.Label(parent, text="Write History", bg=COLORS["bg"],
                 fg=COLORS["text_primary"], font=FONTS["label_bold"]).pack(anchor="w",
                                                                            pady=(0, 10))

        card_outer = tk.Frame(parent, bg=COLORS["card_bg"],
                              highlightbackground=COLORS["border"],
                              highlightthickness=1)
        card_outer.pack(fill=tk.BOTH, expand=True, pady=(0, 24))

        columns = ("code", "date", "status")
        self._history = ttk.Treeview(card_outer, columns=columns,
                                     show="headings", style="POS.Treeview",
                                     selectmode="browse")

        col_cfg = {
            "code":   ("Item Code", 200, "w"),
            "date":   ("Date",      0,   "w"),
            "status": ("Status",    120, "center"),
        }
        for col, (heading, width, anchor) in col_cfg.items():
            self._history.heading(col, text=heading, anchor="w")
            self._history.column(col, width=width, anchor=anchor,
                                 stretch=(col == "date"), minwidth=80)

        scrollbar = ttk.Scrollbar(card_outer, orient=tk.VERTICAL,
                                  command=self._history.yview,
                                  style="POS.Vertical.TScrollbar")
        self._history.configure(yscrollcommand=scrollbar.set)
        self._history.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

        # Row tags for status colour
        self._history.tag_configure("success", foreground=COLORS["success_text"])
        self._history.tag_configure("failed",  foreground=COLORS["error_text"])

        for code, date, status in SAMPLE_HISTORY:
            tag     = "success" if status == "Success" else "failed"
            display = f"✅ {status}" if tag == "success" else f"❌ {status}"
            self._history.insert("", tk.END, values=(code, date, display), tags=(tag,))

    # ── Entry Placeholder ──────────────────────────────────────────────────────

    def _on_focus_in(self, _event=None):
        if self._entry.get() == PLACEHOLDER:
            self._entry.delete(0, tk.END)
            self._entry.configure(fg=COLORS["text_primary"])

    def _on_focus_out(self, _event=None):
        if not self._entry.get().strip():
            self._entry.insert(0, PLACEHOLDER)
            self._entry.configure(fg=COLORS["text_muted"])

    # ── Logic ──────────────────────────────────────────────────────────────────

    def _rewrite_tag(self):
        if self._writing:
            return

        code = self._entry.get().strip()
        if not code or code == PLACEHOLDER:
            messagebox.showwarning("Input Required", "Please enter an item code.")
            return

        # No NFC reader — demo mode
        if self._nfc is None or not self._nfc.connected:
            self._record_write(code, success=True)
            messagebox.showinfo("NFC Tag Writer (Demo)",
                                f"Demo mode: tag written for {code}.\n"
                                "Connect the Arduino to write real tags.")
            return

        self._writing = True
        self._rewrite_btn.configure(state="disabled", text="⏳  Writing…")
        self._write_status_var.set("Waiting for tag — hold tag near reader…")
        self._nfc.write_tag(code, self._on_write_result, self._root)

    def _on_write_result(self, result: str):
        """Callback fired on the Tkinter thread when the Arduino responds."""
        self._writing = False
        self._rewrite_btn.configure(state="normal", text="↻  Rewrite Tag")
        self._write_status_var.set("")

        code = self._entry.get().strip()
        if code == PLACEHOLDER:
            code = "?"

        if result == "WRITE_OK":
            self._record_write(code, success=True)
            self._entry.delete(0, tk.END)
            self._entry.configure(fg=COLORS["text_muted"])
            self._entry.insert(0, PLACEHOLDER)
            messagebox.showinfo("NFC Tag Writer", f"Tag successfully written for: {code}")

        elif result == "NOTAG":
            self._record_write(code, success=False, reason="No tag detected")
            messagebox.showwarning("No Tag Detected",
                                   "No NFC tag was presented within the timeout.\n"
                                   "Hold the tag closer and try again.")
        else:
            reason = result[len("WRITE_FAIL:"):] if result.startswith("WRITE_FAIL:") \
                     else result[len("ERROR:"):] if result.startswith("ERROR:") \
                     else result
            self._record_write(code, success=False, reason=reason)
            messagebox.showerror("Write Failed", reason)

    def _record_write(self, code: str, *, success: bool, reason: str = ""):
        now     = datetime.now().strftime("%Y-%m-%d %I:%M %p")
        tag     = "success" if success else "failed"
        display = "✅ Success" if success else f"❌ {reason or 'Failed'}"
        self._history.insert("", 0, values=(code, now, display), tags=(tag,))
