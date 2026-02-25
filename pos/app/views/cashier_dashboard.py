"""
Cashier Dashboard view.
"""
import tkinter as tk
from tkinter import ttk, messagebox
from app.theme.styles import COLORS, FONTS
from app.views.base_view import BaseView
from app.core.api_client import LaravelApiClient

TAX_RATE = 0.08


class CashierDashboard(BaseView):
    """Main cashier screen: action bar, item table, and order summary."""

    def __init__(self, parent):
        super().__init__(parent)
        self._qty_var = tk.StringVar(value="1")
        self._scan_mode = "add"  # 'add' or 'deduct'
        self._total_items_var = tk.StringVar(value="0")
        self._subtotal_var   = tk.StringVar(value="$0.00")
        self._tax_var        = tk.StringVar(value="$0.00")
        self._total_var      = tk.StringVar(value="$0.00")
        self._cart: list[dict] = []
        self._nfc  = None
        self._root = None
        self._scanning = False
        self._api = LaravelApiClient()
        self._build()

    # ── NFC injection ──────────────────────────────────────────────────────────

    def set_nfc_reader(self, nfc, root):
        """Called by POSApp after construction to wire in the NFC service."""
        self._nfc  = nfc
        self._root = root

    # ── Build ──────────────────────────────────────────────────────────────────

    def _build(self):
        self.build_header(self, "Cashier Dashboard",
                          "Manage transactions and scan items.")
        wrap = tk.Frame(self, bg=COLORS["bg"], padx=24)
        wrap.pack(fill=tk.BOTH, expand=True)

        self._build_action_bar(wrap)

        body = tk.Frame(wrap, bg=COLORS["bg"])
        body.pack(fill=tk.BOTH, expand=True, pady=(12, 24))

        self._build_table(body)
        self._build_order_summary(body)

    # ── Action Bar ─────────────────────────────────────────────────────────────

    def _build_action_bar(self, parent):
        inner = self.card(parent, padx=16, pady=12)
        inner.master.pack(fill=tk.X, pady=(0, 0))

        left = tk.Frame(inner, bg=COLORS["card_bg"])
        left.pack(side=tk.LEFT)

        # Add / Deduct mode buttons
        self._add_btn = tk.Button(
            left, text="+ Add", command=self._set_mode_add,
            bg=COLORS["primary"], fg=COLORS["white"],
            font=FONTS["button"], relief="flat",
            highlightbackground=COLORS["border"],
            highlightthickness=1, padx=16, pady=8, cursor="hand2")
        self._add_btn.pack(side=tk.LEFT, padx=(0, 6))

        self._deduct_btn = tk.Button(
            left, text="− Deduct", command=self._set_mode_deduct,
            bg=COLORS["white"], fg=COLORS["primary"],
            font=FONTS["button"], relief="flat",
            highlightbackground=COLORS["border"],
            highlightthickness=1, padx=16, pady=8, cursor="hand2")
        self._deduct_btn.pack(side=tk.LEFT, padx=(0, 16))

        self._add_btn.bind("<Enter>",    lambda e: self._on_mode_btn_enter(self._add_btn, "add"))
        self._add_btn.bind("<Leave>",    lambda e: self._on_mode_btn_leave(self._add_btn, "add"))
        self._deduct_btn.bind("<Enter>", lambda e: self._on_mode_btn_enter(self._deduct_btn, "deduct"))
        self._deduct_btn.bind("<Leave>", lambda e: self._on_mode_btn_leave(self._deduct_btn, "deduct"))

        # Separator
        tk.Frame(left, bg=COLORS["border"], width=1, height=30).pack(side=tk.LEFT, padx=(0, 16))

        # Qty
        tk.Label(left, text="Qty:", bg=COLORS["card_bg"],
                 fg=COLORS["text_secondary"], font=FONTS["label"],
                 padx=4).pack(side=tk.LEFT)

        qty_entry = tk.Entry(left, textvariable=self._qty_var, width=5,
                             font=FONTS["label"], relief="flat", justify="center",
                             bg=COLORS["white"], fg=COLORS["text_primary"],
                             highlightbackground=COLORS["border"],
                             highlightthickness=1)
        qty_entry.pack(side=tk.LEFT, ipady=4, padx=(4, 0))

        # ── Right side ──────────────────────────────────────────────────────

        # Scan status label
        self._scan_status_var = tk.StringVar(value="")
        tk.Label(inner, textvariable=self._scan_status_var,
                 bg=COLORS["card_bg"], fg=COLORS["text_secondary"],
                 font=FONTS["small_bold"]).pack(side=tk.RIGHT, padx=(0, 12))

        # Scan NFC button
        self._scan_btn = self.primary_button(
            inner, "⛶  Scan NFC", command=self._scan_item, padx=24, pady=10)
        self._scan_btn.pack(side=tk.RIGHT, padx=(6, 0))

        # Manual item code entry
        self._manual_code_var = tk.StringVar()
        self._manual_entry = tk.Entry(
            inner, textvariable=self._manual_code_var, width=12,
            font=FONTS["label"], relief="flat", justify="center",
            bg=COLORS["white"], fg=COLORS["text_primary"],
            highlightbackground=COLORS["border"], highlightthickness=1)
        self._manual_entry.pack(side=tk.RIGHT, ipady=4, padx=(0, 6))
        self._manual_entry.bind("<Return>", lambda _: self._submit_manual_code())

        tk.Label(inner, text="Item Code:", bg=COLORS["card_bg"],
                 fg=COLORS["text_secondary"], font=FONTS["label"],
                 padx=6).pack(side=tk.RIGHT)

        self._submit_btn = self.outline_button(
            inner, "Submit", command=self._submit_manual_code, padx=14, pady=8)
        self._submit_btn.pack(side=tk.RIGHT, padx=(0, 10))

    # ── Item Table ─────────────────────────────────────────────────────────────

    def _build_table(self, parent):
        card_outer = tk.Frame(parent, bg=COLORS["card_bg"],
                              highlightbackground=COLORS["border"],
                              highlightthickness=1)
        card_outer.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=(0, 12))

        card_outer.grid_rowconfigure(0, weight=1)
        card_outer.grid_columnconfigure(0, weight=1)

        columns = ("code", "action", "name", "qty", "price", "total")
        self._tree = ttk.Treeview(card_outer, columns=columns,
                                  show="headings", style="POS.Treeview",
                                  selectmode="browse")

        col_cfg = {
            "code":   ("ITEM CODE",  120, "w"),
            "action": ("ACTION",      80, "center"),
            "name":   ("ITEM NAME",    0, "w"),
            "qty":    ("QTY",         60, "center"),
            "price":  ("PRICE",       90, "e"),
            "total":  ("TOTAL",       90, "e"),
        }
        for col, (heading, width, anchor) in col_cfg.items():
            self._tree.heading(col, text=heading, anchor="w")
            self._tree.column(col, width=width, anchor=anchor,
                              stretch=(col == "name"), minwidth=50)

        scrollbar = ttk.Scrollbar(card_outer, orient=tk.VERTICAL,
                                  command=self._tree.yview,
                                  style="POS.Vertical.TScrollbar")
        self._tree.configure(yscrollcommand=scrollbar.set)

        self._tree.grid(row=0, column=0, sticky="nsew")
        scrollbar.grid(row=0, column=1, sticky="ns")

        # Empty-state overlay
        self._empty_frame = tk.Frame(card_outer, bg=COLORS["card_bg"])
        self._empty_frame.grid(row=0, column=0, sticky="nsew")

        tk.Label(self._empty_frame, text="🛒", bg=COLORS["card_bg"],
                 fg="#E2E8F0", font=("Segoe UI", 48)).pack(expand=True, pady=(80, 4))
        tk.Label(self._empty_frame, text="No items scanned yet",
                 bg=COLORS["card_bg"], fg=COLORS["text_secondary"],
                 font=FONTS["title"]).pack()
        tk.Label(self._empty_frame, text="Items will appear here after scanning",
                 bg=COLORS["card_bg"], fg=COLORS["text_muted"],
                 font=FONTS["subtitle"]).pack(pady=(2, 80))

        self._empty_frame.tkraise()

    # ── Order Summary ──────────────────────────────────────────────────────────

    def _build_order_summary(self, parent):
        right = tk.Frame(parent, bg=COLORS["bg"], width=268)
        right.pack(side=tk.RIGHT, fill=tk.Y)
        right.pack_propagate(False)

        summary_inner = self.card(right, padx=20, pady=20)
        summary_inner.master.pack(fill=tk.X, pady=(0, 12))

        tk.Label(summary_inner, text="Order Summary", bg=COLORS["card_bg"],
                 fg=COLORS["text_primary"], font=FONTS["label_bold"]).pack(anchor="w")
        tk.Frame(summary_inner, bg=COLORS["border"], height=1).pack(fill=tk.X, pady=14)

        rows = [
            ("Total Items", self._total_items_var),
            ("Subtotal",    self._subtotal_var),
            (f"Tax ({int(TAX_RATE*100)}%)", self._tax_var),
        ]
        for label, var in rows:
            row = tk.Frame(summary_inner, bg=COLORS["card_bg"])
            row.pack(fill=tk.X, pady=3)
            tk.Label(row, text=label, bg=COLORS["card_bg"],
                     fg=COLORS["text_secondary"], font=FONTS["label"]).pack(side=tk.LEFT)
            tk.Label(row, textvariable=var, bg=COLORS["card_bg"],
                     fg=COLORS["text_secondary"], font=FONTS["label"]).pack(side=tk.RIGHT)

        tk.Frame(summary_inner, bg=COLORS["border"], height=1).pack(fill=tk.X, pady=14)

        total_row = tk.Frame(summary_inner, bg=COLORS["card_bg"])
        total_row.pack(fill=tk.X)
        tk.Label(total_row, text="Total Amount", bg=COLORS["card_bg"],
                 fg=COLORS["text_primary"], font=FONTS["label_bold"]).pack(side=tk.LEFT, pady=4)
        tk.Label(total_row, textvariable=self._total_var, bg=COLORS["card_bg"],
                 fg=COLORS["primary"], font=FONTS["amount_large"]).pack(side=tk.RIGHT)

        self._buy_btn = self.accent_button(
            summary_inner, "Buy Now", command=self._buy_now, pady=14)
        self._buy_btn.pack(fill=tk.X, pady=(20, 0))

        qa_inner = self.card(right, padx=16, pady=14)
        qa_inner.master.pack(fill=tk.X)

        tk.Label(qa_inner, text="QUICK ACTIONS", bg=COLORS["card_bg"],
                 fg=COLORS["text_muted"], font=FONTS["tiny_bold"]).pack(anchor="w",
                                                                         pady=(0, 10))
        btn_row = tk.Frame(qa_inner, bg=COLORS["card_bg"])
        btn_row.pack(fill=tk.X)

        clear_btn = self.outline_button(btn_row, "🗑  Clear Cart",
                                        command=self._clear_cart)
        clear_btn.pack(side=tk.LEFT, expand=True, fill=tk.X, padx=(0, 6))

        print_btn = self.outline_button(btn_row, "Print Receipt",
                                        command=self._print_receipt)
        print_btn.pack(side=tk.LEFT, expand=True, fill=tk.X)

    # ── Mode buttons ───────────────────────────────────────────────────────────

    def _set_mode_add(self):
        self._scan_mode = "add"
        self._add_btn.configure(bg=COLORS["primary"], fg=COLORS["white"])
        self._deduct_btn.configure(bg=COLORS["white"], fg=COLORS["primary"])

    def _set_mode_deduct(self):
        self._scan_mode = "deduct"
        self._deduct_btn.configure(bg=COLORS["primary"], fg=COLORS["white"])
        self._add_btn.configure(bg=COLORS["white"], fg=COLORS["primary"])

    def _on_mode_btn_enter(self, btn, mode):
        if self._scan_mode != mode:
            btn.configure(bg=COLORS["table_header_bg"])

    def _on_mode_btn_leave(self, btn, mode):
        if self._scan_mode != mode:
            btn.configure(bg=COLORS["white"])

    # ── Scan / Manual entry ────────────────────────────────────────────────────

    def _set_input_state(self, state: str):
        self._scan_btn.configure(state=state)
        self._submit_btn.configure(state=state)
        self._manual_entry.configure(state=state)

    def _get_qty(self) -> int:
        try:
            return max(1, int(self._qty_var.get()))
        except ValueError:
            self._qty_var.set("1")
            return 1

    def _submit_manual_code(self):
        """Submit the manually typed item code."""
        code = self._manual_code_var.get().strip().upper()
        if not code:
            return
        self._manual_code_var.set("")
        self._lookup_and_add(code, self._get_qty(), self._scan_mode)

    def _scan_item(self):
        """Trigger a real NFC read."""
        if self._scanning:
            return
        if self._nfc is None or not self._nfc.connected:
            messagebox.showwarning(
                "NFC Not Connected",
                "No NFC reader detected.\n"
                "Use the Item Code field to add items manually.")
            return

        self._scanning = True
        self._set_input_state("disabled")
        self._scan_btn.configure(text="⏳  Scanning…")
        self._scan_status_var.set("Tap your NFC tag…")
        self._nfc.read_tag(self._on_scan_result, self._root)

    def _on_scan_result(self, result: str):
        """Callback from the Arduino."""
        self._scanning = False
        self._scan_btn.configure(text="⛶  Scan NFC")
        self._set_input_state("normal")
        self._scan_status_var.set("")

        if result.startswith("CARD:"):
            code = result[5:].strip()
            self._lookup_and_add(code, self._get_qty(), self._scan_mode)
        elif result == "NOTAG":
            messagebox.showwarning("No Tag Detected",
                                   "No NFC tag was presented within the timeout.\n"
                                   "Hold the tag closer and try again.")
        else:
            msg = result[6:] if result.startswith("ERROR:") else result
            messagebox.showerror("Scan Error", msg)

    # ── Lookup → add to cart (no stock change yet) ─────────────────────────────

    def _lookup_and_add(self, code: str, qty: int, action: str):
        """Fetch item info from Laravel, then add to the local cart display."""
        self._set_input_state("disabled")
        self._scan_status_var.set("Looking up item…")
        self._api.fetch_item(
            item_code=code,
            callback=lambda result: self._on_lookup_result(result, code, qty, action),
            tk_root=self._root,
        )

    def _on_lookup_result(self, result: dict, code: str, qty: int, action: str):
        """Add item to local cart from API lookup response (no stock change)."""
        self._set_input_state("normal")

        if not result["ok"]:
            self._scan_status_var.set(f"⚠ {result['message']}")
            if self._root:
                self._root.after(3000, lambda: self._scan_status_var.set(""))
            return

        data  = result.get("data") or {}
        name  = data.get("item_name", code)
        price = float(data.get("unit_price", 0))

        existing = next((i for i in self._cart if i["code"] == code and i["action"] == action), None)

        if action == "add":
            if existing:
                existing["qty"]   += qty
                existing["total"]  = existing["qty"] * existing["price"]
            else:
                item = {"code": code, "action": action, "name": name,
                        "qty": qty, "price": price, "total": qty * price}
                self._cart.append(item)
        else:  # deduct
            if existing:
                existing["qty"]   += qty
                existing["total"]  = existing["qty"] * existing["price"]
            else:
                item = {"code": code, "action": action, "name": name,
                        "qty": qty, "price": price, "total": qty * price}
                self._cart.append(item)

        self._refresh_tree()
        self._update_summary()
        self._scan_status_var.set("✓ Item added")
        if self._root:
            self._root.after(2000, lambda: self._scan_status_var.set(""))

    # ── Buy Now → send all cart items to Laravel ───────────────────────────────

    def _buy_now(self):
        if not self._cart:
            messagebox.showinfo("Cart Empty", "No items in cart.")
            return

        self._buy_btn.configure(state="disabled", text="⏳  Processing…")
        self._set_input_state("disabled")
        self._scan_status_var.set("Sending to server…")

        # Send all cart items sequentially via a queue
        self._pending = list(self._cart)
        self._failed: list[str] = []
        self._process_next_item()

    def _process_next_item(self):
        if not self._pending:
            self._on_all_done()
            return

        item = self._pending.pop(0)
        self._api.send_scan(
            item_code=item["code"],
            action=item["action"],
            quantity=item["qty"],
            callback=lambda result, i=item: self._on_item_sent(result, i),
            tk_root=self._root,
        )

    def _on_item_sent(self, result: dict, item: dict):
        if not result["ok"]:
            self._failed.append(f"{item['code']} — {result['message']}")
        self._process_next_item()

    def _on_all_done(self):
        self._buy_btn.configure(state="normal", text="Buy Now")
        self._set_input_state("normal")
        self._scan_status_var.set("")

        if self._failed:
            errors = "\n".join(self._failed)
            messagebox.showerror(
                "Some Items Failed",
                f"The following items could not be synced:\n\n{errors}")
        else:
            messagebox.showinfo("Done", "Transaction completed!\nStock updated successfully.")
            self._clear_cart()

    # ── Tree / Summary helpers ─────────────────────────────────────────────────

    def _refresh_tree(self):
        for row in self._tree.get_children():
            self._tree.delete(row)
        for item in self._cart:
            action_label = "➕ Add" if item["action"] == "add" else "➖ Deduct"
            self._tree.insert("", tk.END, values=(
                item["code"], action_label, item["name"], item["qty"],
                f"${item['price']:.2f}", f"${item['total']:.2f}",
            ))

    def _buy_now_confirm(self):
        if not self._cart:
            messagebox.showinfo("Cart Empty", "No items in cart.")
            return
        messagebox.showinfo("Purchase", "Transaction completed!")
        self._clear_cart()

    def _clear_cart(self):
        for row in self._tree.get_children():
            self._tree.delete(row)
        self._cart.clear()
        self._update_summary()

    def _print_receipt(self):
        messagebox.showinfo("Receipt", "Printing receipt…")

    def _update_summary(self):
        # For summary, only "add" items contribute to totals (deducts reduce stock, not revenue)
        add_items = [i for i in self._cart if i["action"] == "add"]
        subtotal = sum(i["total"] for i in add_items)
        tax      = subtotal * TAX_RATE
        total    = subtotal + tax

        self._total_items_var.set(str(len(self._cart)))
        self._subtotal_var.set(f"${subtotal:.2f}")
        self._tax_var.set(f"${tax:.2f}")
        self._total_var.set(f"${total:.2f}")

        if self._cart:
            self._tree.tkraise()
        else:
            self._empty_frame.tkraise()
