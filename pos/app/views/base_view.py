"""
BaseView — shared base class for all page views.
"""
import tkinter as tk
from app.theme.styles import COLORS, FONTS


class BaseView(tk.Frame):
    """Base class for all views. Provides a standard page header."""

    def __init__(self, parent):
        super().__init__(parent, bg=COLORS["bg"])

    # ── Helpers ────────────────────────────────────────────────────────────────

    def build_header(self, parent: tk.Widget, title: str, subtitle: str) -> tk.Frame:
        """Render the standard page title + subtitle block."""
        header = tk.Frame(parent, bg=COLORS["bg"], padx=28, pady=22)
        header.pack(fill=tk.X)

        tk.Label(header, text=title, bg=COLORS["bg"],
                 fg=COLORS["text_primary"], font=FONTS["title"]).pack(anchor="w")
        tk.Label(header, text=subtitle, bg=COLORS["bg"],
                 fg=COLORS["text_secondary"], font=FONTS["subtitle"]).pack(anchor="w")
        return header

    @staticmethod
    def card(parent: tk.Widget, padx=20, pady=20, **kw) -> tk.Frame:
        """Return a white card frame with a subtle border."""
        outer = tk.Frame(parent, bg=COLORS["card_bg"],
                         highlightbackground=COLORS["border"],
                         highlightthickness=1, **kw)
        inner = tk.Frame(outer, bg=COLORS["card_bg"], padx=padx, pady=pady)
        inner.pack(fill=tk.BOTH, expand=True)
        return inner

    @staticmethod
    def primary_button(parent: tk.Widget, text: str, command=None,
                       pady=12, **kw) -> tk.Button:
        """Full-width primary action button."""
        btn = tk.Button(parent, text=text, command=command,
                        bg=COLORS["primary"], fg=COLORS["white"],
                        font=FONTS["button"], relief="flat", bd=0,
                        pady=pady, cursor="hand2", **kw)
        btn.bind("<Enter>", lambda _: btn.configure(bg=COLORS["primary_hover"]))
        btn.bind("<Leave>", lambda _: btn.configure(bg=COLORS["primary"]))
        return btn

    @staticmethod
    def accent_button(parent: tk.Widget, text: str, command=None,
                       pady=12, **kw) -> tk.Button:
        """Lighter blue button."""
        btn = tk.Button(parent, text=text, command=command,
                        bg="#60A5FA", fg=COLORS["white"],
                        font=FONTS["button"], relief="flat", bd=0,
                        pady=pady, cursor="hand2", **kw)
        btn.bind("<Enter>", lambda _: btn.configure(bg="#3B82F6"))
        btn.bind("<Leave>", lambda _: btn.configure(bg="#60A5FA"))
        return btn

    @staticmethod
    def outline_button(parent: tk.Widget, text: str, command=None,
                       padx=15, pady=8, **kw) -> tk.Button:
        """Outlined secondary button."""
        btn = tk.Button(parent, text=text, command=command,
                        bg=COLORS["white"], fg=COLORS["text_primary"],
                        font=FONTS["small_bold"], relief="flat",
                        highlightbackground=COLORS["border"],
                        highlightthickness=1,
                        padx=padx, pady=pady, cursor="hand2", **kw)
        btn.bind("<Enter>", lambda _: btn.configure(bg=COLORS["table_header_bg"]))
        btn.bind("<Leave>", lambda _: btn.configure(bg=COLORS["white"]))
        return btn
