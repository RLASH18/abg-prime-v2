"""
Security Logs view — displays historical IR sensor activity.
"""
import tkinter as tk
from tkinter import ttk
from pathlib import Path
from app.theme.styles import COLORS, FONTS
from app.views.base_view import BaseView

LOG_FILE = Path(__file__).resolve().parents[2] / "security_log.txt"

class SecurityLogs(BaseView):
    """View for displaying IR sensor MOTION and CLEAR logs."""

    def __init__(self, parent):
        super().__init__(parent)
        self._build()

    def _build(self):
        self.build_header(self, "Security Logs", 
                          "Review historical IR sensor motion and reset events.")
        
        wrap = tk.Frame(self, bg=COLORS["bg"], padx=32)
        wrap.pack(fill=tk.BOTH, expand=True)

        # ── Toolbar ──────────────────────────────────────────────────────────
        toolbar = tk.Frame(wrap, bg=COLORS["bg"])
        toolbar.pack(fill=tk.X, pady=(0, 15))

        self.outline_button(toolbar, "↻  Refresh Logs", 
                            command=self.refresh_logs).pack(side=tk.LEFT)
        
        # ── Logs Table ───────────────────────────────────────────────────────
        card_outer = tk.Frame(wrap, bg=COLORS["card_bg"],
                               highlightbackground=COLORS["border"],
                               highlightthickness=1)
        card_outer.pack(fill=tk.BOTH, expand=True, pady=(0, 32))

        columns = ("timestamp", "event")
        self._tree = ttk.Treeview(card_outer, columns=columns,
                                   show="headings", style="POS.Treeview",
                                   selectmode="browse")

        self._tree.heading("timestamp", text="TIMESTAMP", anchor="w")
        self._tree.heading("event", text="EVENT DESCRIPTION", anchor="w")

        self._tree.column("timestamp", width=200, anchor="w")
        self._tree.column("event", width=0, anchor="w", stretch=True)

        scrollbar = ttk.Scrollbar(card_outer, orient=tk.VERTICAL,
                                  command=self._tree.yview,
                                  style="POS.Vertical.TScrollbar")
        self._tree.configure(yscrollcommand=scrollbar.set)

        self._tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

        # Row tags for coloring
        self._tree.tag_configure("motion", foreground=COLORS["error_text"])
        self._tree.tag_configure("clear", foreground=COLORS["success_text"])

        self.refresh_logs()

    def refresh_logs(self):
        """Read security_log.txt and update the table."""
        for row in self._tree.get_children():
            self._tree.delete(row)

        if not LOG_FILE.exists():
            return

        try:
            with open(LOG_FILE, "r", encoding="utf-8") as f:
                lines = f.readlines()
            
            # Show newest logs at the top
            for line in reversed(lines):
                line = line.strip()
                if not line or "]" not in line:
                    continue
                
                # Format: [YYYY-MM-DD HH:MM:SS AM/PM] Event
                parts = line.split("] ", 1)
                timestamp = parts[0].replace("[", "")
                event = parts[1]
                
                tag = "motion" if "MOTION" in event else "clear"
                self._tree.insert("", tk.END, values=(timestamp, event), tags=(tag,))
        except Exception:
            pass