"""
NFC Tag Writer view.
"""
import json
import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime
from pathlib import Path
from app.theme.styles import COLORS, FONTS
from app.views.base_view import BaseView
from app.core.laravel_api_client import LaravelApiClient

PLACEHOLDER  = "Search or select item code..."
HISTORY_FILE = Path(__file__).resolve().parents[2] / "write_history.json"
MAX_HISTORY  = 100


class NFCTagWriter(BaseView):
    """NFC Tag Writer page: write form + write history table."""

    def __init__(self, parent):
        super().__init__(parent)
        self._item_code_var = tk.StringVar()
        self._nfc    = None
        self._root   = None
        self._writing = False
        self._api = LaravelApiClient()
        self._all_items: list[dict] = []  # Store fetched items
        self._build()

    # ── NFC injection ──────────────────────────────────────────────────────────

    def set_arduino_bridge(self, arduino, root):
        """Called by POSApp after construction to wire in the ArduinoBridge."""
        self._nfc  = arduino
        self._root = root
        # Fetch items from backend after root is available
        self._fetch_items_list()

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
        canvas = tk.Canvas(inner, width=72, height=72,
                           bg=COLORS["card_bg"], highlightthickness=0)
        canvas.pack(pady=(10, 24))
        canvas.create_oval(4, 4, 68, 68,
                           fill=COLORS["primary_light"], outline="")
        canvas.create_text(36, 37, text="((●))",
                           font=("Segoe UI", 22), fill=COLORS["primary"])

        # Centre-column container
        form = tk.Frame(inner, bg=COLORS["card_bg"])
        form.pack()

        tk.Label(form, text="Item Code", bg=COLORS["card_bg"],
                 fg=COLORS["text_primary"], font=FONTS["small_bold"]).pack(anchor="w",
                                                                            pady=(0, 6))

        # Searchable Entry
        self._entry = tk.Entry(form, font=FONTS["label"], relief="flat",
                               textvariable=self._item_code_var,
                               bg=COLORS["white"], fg=COLORS["text_primary"],
                               insertbackground=COLORS["primary"], width=48,
                               justify="center",
                               highlightthickness=1,
                               highlightbackground=COLORS["border"],
                               highlightcolor=COLORS["primary"])
        self._entry.pack(fill=tk.X, ipady=10)
        self._item_code_var.set(PLACEHOLDER)

        self._entry.bind("<FocusIn>",  self._on_focus_in)
        self._entry.bind("<FocusOut>", self._on_focus_out)
        self._entry.bind("<KeyRelease>", self._on_key_release)

        # Suggestions Listbox (Datalist)
        self._list_frame = tk.Frame(form, bg=COLORS["border"], pady=1)
        self._listbox = tk.Listbox(self._list_frame, font=FONTS["small"],
                                   relief="flat", bg=COLORS["white"],
                                   fg=COLORS["text_secondary"],
                                   highlightthickness=0, height=5,
                                   selectbackground=COLORS["primary_light"],
                                   selectforeground=COLORS["primary"])
        self._listbox.pack(fill=tk.X)
        self._listbox.bind("<<ListboxSelect>>", self._on_list_select)
        
        # Initially hide the list
        self._list_frame.pack_forget()

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

        for rec in self._load_history():
            tag = rec.get("tag", "success")
            self._history.insert("", tk.END,
                                 values=(rec["code"], rec["date"], rec["status"]),
                                 tags=(tag,))

    # ── Entry Placeholder ──────────────────────────────────────────────────────

    def _on_focus_in(self, _event=None):
        if self._item_code_var.get() == PLACEHOLDER:
            self._item_code_var.set("")

    def _on_focus_out(self, _event=None):
        if not self._item_code_var.get().strip():
            self._item_code_var.set(PLACEHOLDER)

    # ── Logic ──────────────────────────────────────────────────────────────────

    def _fetch_items_list(self):
        """Fetch all item codes and names from the backend."""
        self._api.fetch_items(self._on_items_fetched, self._root)

    def _on_items_fetched(self, result: dict):
        if result["ok"]:
            self._all_items = result["data"]
            self._update_combo_values()

    def _update_combo_values(self, filter_text: str = ""):
        """Update the listbox values based on the search filter."""
        search = filter_text.lower().strip()
        self._listbox.delete(0, tk.END)
        
        if not search or search == PLACEHOLDER.lower():
            values = [f"{i['item_code']} - {i['item_name']}" for i in self._all_items]
        else:
            values = [
                f"{i['item_code']} - {i['item_name']}"
                for i in self._all_items
                if search in i['item_code'].lower() or search in i['item_name'].lower()
            ]
        
        for val in values:
            self._listbox.insert(tk.END, val)
            
        # Show/Hide list based on results and focus
        if values and search != "":
            self._list_frame.pack(fill=tk.X, pady=(6, 0))
        else:
            self._list_frame.pack_forget()

    def _on_key_release(self, event):
        """Filter the datalist as the user types."""
        if event.keysym in ("Up", "Down", "Return", "Escape", "Tab"):
            # Allow keyboard navigation of the listbox
            if event.keysym == "Down":
                self._listbox.focus_set()
                if self._listbox.size() > 0:
                    self._listbox.selection_set(0)
            return
        
        val = self._item_code_var.get()
        self._update_combo_values(val)

    def _on_list_select(self, event):
        """Handle selection from the suggestion list."""
        selection = self._listbox.curselection()
        if selection:
            val = self._listbox.get(selection[0])
            self._item_code_var.set(val)
            self._list_frame.pack_forget()
            self._entry.focus_set()
            self._entry.icursor(tk.END)

    def _rewrite_tag(self):
        if self._writing:
            return

        full_val = self._item_code_var.get().strip()
        if not full_val or full_val == PLACEHOLDER:
            messagebox.showwarning("Input Required", "Please select an item code.")
            return

        # Extract code if format is "CODE - NAME"
        code = full_val.split(" - ")[0].strip()

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

        full_val = self._item_code_var.get().strip()
        code = full_val.split(" - ")[0].strip()
        if code == PLACEHOLDER:
            code = "?"

        if result == "WRITE_OK":
            self._record_write(code, success=True)
            self._item_code_var.set(PLACEHOLDER)
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

        # Persist so history survives app restarts
        records = self._load_history()
        records.insert(0, {"code": code, "date": now, "status": display, "tag": tag})
        self._save_history(records)

    # ── History persistence ────────────────────────────────────────────────────

    @staticmethod
    def _load_history() -> list:
        """Load write history from JSON file; returns [] on any error."""
        if HISTORY_FILE.exists():
            try:
                with open(HISTORY_FILE, "r", encoding="utf-8") as f:
                    return json.load(f)
            except Exception:
                pass
        return []

    @staticmethod
    def _save_history(records: list) -> None:
        """Persist write history (newest-first, capped at MAX_HISTORY entries)."""
        try:
            with open(HISTORY_FILE, "w", encoding="utf-8") as f:
                json.dump(records[:MAX_HISTORY], f, indent=2, ensure_ascii=False)
        except Exception:
            pass
