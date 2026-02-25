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
from app.core.nfc_reader import NFCReader

log = logging.getLogger(__name__)


class POSApp:
    """Root application controller."""

    WIDTH    = 1140
    HEIGHT   = 720
    MIN_W    = 960
    MIN_H    = 620
    TITLE    = "POS System"

    def __init__(self):
        self.root = tk.Tk()
        self._configure_root()
        apply_global_styles(self.root)
        self._init_nfc()
        self._build_layout()
        self._register_views()
        self.navigate("cashier")

    # ── Setup ──────────────────────────────────────────────────────────────────

    def _configure_root(self):
        self.root.title(self.TITLE)
        self.root.geometry(f"{self.WIDTH}x{self.HEIGHT}")
        self.root.minsize(self.MIN_W, self.MIN_H)
        self.root.configure(bg=COLORS["bg"])
        self.root.protocol("WM_DELETE_WINDOW", self._on_close)

    def _init_nfc(self):
        self.nfc = NFCReader()
        ok = self.nfc.connect()
        if not ok:
            log.warning("NFC reader not available — running without hardware.")

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
        cashier = CashierDashboard(self.content)
        nfc_writer = NFCTagWriter(self.content)

        # Inject the NFC reader into each view that needs it
        cashier.set_nfc_reader(self.nfc, self.root)
        nfc_writer.set_nfc_reader(self.nfc, self.root)

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
        self._centre_window()
        self.root.mainloop()

    def _centre_window(self):
        self.root.update_idletasks()
        sw = self.root.winfo_screenwidth()
        sh = self.root.winfo_screenheight()
        x  = (sw - self.WIDTH)  // 2
        y  = (sh - self.HEIGHT) // 2
        self.root.geometry(f"{self.WIDTH}x{self.HEIGHT}+{x}+{y}")

    def _on_close(self):
        self.nfc.disconnect()
        self.root.destroy()
