"""
Theme — colors, fonts, and global ttk styles for the POS System.
"""
import tkinter as tk
from tkinter import ttk

# ── Color Palette ──────────────────────────────────────────────────────────────
COLORS = {
    # Backgrounds
    "bg":              "#F8FAFC",
    "sidebar_bg":      "#FFFFFF",
    "card_bg":         "#FFFFFF",
    # Brand
    "primary":         "#2563EB",
    "primary_hover":   "#1D4ED8",
    "primary_light":   "#EFF6FF",
    "primary_text":    "#2563EB",
    "accent":          "#3B82F6",
    # Text
    "text_primary":    "#1E293B",
    "text_secondary":  "#64748B",
    "text_muted":      "#94A3B8",
    # UI
    "border":          "#E2E8F0",
    "white":           "#FFFFFF",
    # Status
    "success_text":    "#16A34A",
    "success_bg":      "#DCFCE7",
    "error_text":      "#DC2626",
    "error_bg":        "#FEE2E2",
    # Table
    "table_header_bg": "#F8FAFC",
}

# ── Font Definitions ───────────────────────────────────────────────────────────
FONTS = {
    "logo":          ("Segoe UI", 14, "bold"),
    "nav":           ("Segoe UI", 10),
    "nav_active":    ("Segoe UI", 10, "bold"),
    "title":         ("Segoe UI", 18, "bold"),
    "subtitle":      ("Segoe UI", 11),
    "label":         ("Segoe UI", 10),
    "label_bold":    ("Segoe UI", 10, "bold"),
    "small":         ("Segoe UI", 9),
    "small_bold":    ("Segoe UI", 9, "bold"),
    "tiny_bold":     ("Segoe UI", 8, "bold"),
    "table_header":  ("Segoe UI", 9,  "bold"),
    "table_body":    ("Segoe UI", 10),
    "amount_large":  ("Segoe UI", 24, "bold"),
    "button":        ("Segoe UI", 10, "bold"),
}


def apply_global_styles(root: tk.Tk) -> None:
    """Configure all global ttk widget styles."""
    style = ttk.Style(root)
    style.theme_use("clam")

    # Treeview body
    style.configure(
        "POS.Treeview",
        background=COLORS["card_bg"],
        foreground=COLORS["text_primary"],
        rowheight=44,
        fieldbackground=COLORS["card_bg"],
        borderwidth=0,
        relief="flat",
        font=FONTS["table_body"],
    )
    # Treeview column headers
    style.configure(
        "POS.Treeview.Heading",
        background=COLORS["table_header_bg"],
        foreground=COLORS["text_secondary"],
        font=FONTS["table_header"],
        relief="flat",
        borderwidth=0,
        padding=(16, 12),
    )
    style.map(
        "POS.Treeview",
        background=[("selected", COLORS["primary_light"])],
        foreground=[("selected", COLORS["text_primary"])],
    )
    style.map(
        "POS.Treeview.Heading",
        background=[("active", COLORS["table_header_bg"])],
    )

    # Thin scrollbar
    style.configure(
        "POS.Vertical.TScrollbar",
        background=COLORS["border"],
        troughcolor=COLORS["bg"],
        borderwidth=0,
        arrowsize=0,
        width=5,
    )
