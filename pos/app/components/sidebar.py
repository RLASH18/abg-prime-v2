"""
Sidebar navigation component.
"""
import tkinter as tk
from app.theme.styles import COLORS, FONTS


class _NavItem(tk.Frame):
    """A single sidebar navigation button."""

    ICON_MAP = {
        "cashier": "▦",
        "nfc":     "◎",
    }
    LABEL_MAP = {
        "cashier": "Cashier Dashboard",
        "nfc":     "NFC Tag Writer",
    }

    def __init__(self, parent, view_name: str, on_click):
        super().__init__(parent, bg=COLORS["sidebar_bg"], padx=12, pady=2)
        self.view_name = view_name
        self.on_click = on_click
        self._active = False
        self._build()

    def _build(self):
        self._inner = tk.Frame(self, bg=COLORS["sidebar_bg"], padx=10, pady=9,
                               cursor="hand2")
        self._inner.pack(fill=tk.X)

        icon_text = self.ICON_MAP.get(self.view_name, "•")
        label_text = self.LABEL_MAP.get(self.view_name, self.view_name)

        self._icon = tk.Label(self._inner, text=icon_text,
                              bg=COLORS["sidebar_bg"], fg=COLORS["text_secondary"],
                              font=FONTS["nav"], cursor="hand2")
        self._icon.pack(side=tk.LEFT)

        self._label = tk.Label(self._inner, text=f"  {label_text}",
                               bg=COLORS["sidebar_bg"], fg=COLORS["text_secondary"],
                               font=FONTS["nav"], cursor="hand2")
        self._label.pack(side=tk.LEFT)

        for w in (self, self._inner, self._icon, self._label):
            w.bind("<Button-1>", self._clicked)
            w.bind("<Enter>", self._on_enter)
            w.bind("<Leave>", self._on_leave)

    def _clicked(self, _event=None):
        self.on_click(self.view_name)

    def _on_enter(self, _event=None):
        if not self._active:
            self._set_colors(COLORS["table_header_bg"], COLORS["text_primary"])

    def _on_leave(self, _event=None):
        if not self._active:
            self._set_colors(COLORS["sidebar_bg"], COLORS["text_secondary"])

    def _set_colors(self, bg, fg, font=None):
        font = font or FONTS["nav"]
        for w in (self, self._inner, self._icon, self._label):
            w.configure(bg=bg)
        self._icon.configure(fg=fg, font=font)
        self._label.configure(fg=fg, font=font)

    def set_active(self, active: bool):
        self._active = active
        if active:
            self._set_colors(COLORS["primary_light"], COLORS["primary_text"],
                             FONTS["nav_active"])
        else:
            self._set_colors(COLORS["sidebar_bg"], COLORS["text_secondary"],
                             FONTS["nav"])


class Sidebar(tk.Frame):
    """Left navigation sidebar."""

    def __init__(self, parent, navigate_callback):
        super().__init__(parent, bg=COLORS["sidebar_bg"], width=200)
        self.pack_propagate(False)
        self.navigate_callback = navigate_callback
        self._nav_items: dict[str, _NavItem] = {}
        self._build()

    def _build(self):
        self._build_logo()
        tk.Frame(self, bg=COLORS["border"], height=1).pack(fill=tk.X)
        self._build_nav()

    def _build_logo(self):
        frame = tk.Frame(self, bg=COLORS["sidebar_bg"], padx=20, pady=20)
        frame.pack(fill=tk.X)
        
        tk.Label(frame, text="ABG", bg=COLORS["primary"],
                 fg=COLORS["white"], font=("Segoe UI", 10, "bold"),
                 padx=6, pady=4).pack(side=tk.LEFT, anchor="nw")

        tk.Label(frame, text=" Prime Builders\n Supplies Inc.",
                 bg=COLORS["sidebar_bg"], fg=COLORS["primary"],
                 font=("Segoe UI", 9, "bold"),
                 justify="left").pack(side=tk.LEFT, anchor="w")

    def _build_nav(self):
        nav_frame = tk.Frame(self, bg=COLORS["sidebar_bg"], pady=12)
        nav_frame.pack(fill=tk.X)

        for view_name in ("cashier", "nfc"):
            item = _NavItem(nav_frame, view_name, self.navigate_callback)
            item.pack(fill=tk.X)
            self._nav_items[view_name] = item

    def set_active(self, view_name: str):
        for name, item in self._nav_items.items():
            item.set_active(name == view_name)
