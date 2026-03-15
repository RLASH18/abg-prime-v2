"""
LaravelApiClient — communicates with the Laravel backend.

Endpoints:

  GET  /api/pos/item/{code}     — look up item info (no stock change)
  POST /api/pos/scan            — adjust stock (called on Buy Now)

All network calls run in daemon threads. Results arrive on the Tkinter
thread via root.after(0, callback, result), where result is:
  {"ok": True,  "data": <item dict>}
  {"ok": False, "message": <error string>}
"""

import json
import logging
import threading
import urllib.request
import urllib.error
import urllib.parse

from app.core.config import API_BASE_URL, POS_API_SECRET

log = logging.getLogger(__name__)

REQUEST_TIMEOUT = 10  # seconds

_HEADERS = {
    "Content-Type": "application/json",
    "Accept":       "application/json",
    "X-API-Secret": POS_API_SECRET,
}


class LaravelApiClient:
    """Async wrapper around the Laravel POS endpoints."""

    # ── Item lookup (scan → show in cart) ─────────────────────────────────────

    def fetch_item(self, item_code: str, callback, tk_root) -> None:
        """
        Non-blocking GET /api/pos/item/{code}.
        Returns item name, price etc. without touching stock.
        """
        t = threading.Thread(
            target=self._run_get,
            args=(item_code, callback, tk_root),
            daemon=True,
        )
        t.start()

    def _run_get(self, item_code: str, callback, tk_root) -> None:
        url = f"{API_BASE_URL}/api/pos/item/{urllib.parse.quote(item_code)}"
        req = urllib.request.Request(url, headers=_HEADERS)
        result = self._execute(req, item_code)
        tk_root.after(0, callback, result)

    # ── Stock adjustment (Process Transaction) ──────────────────────────────────

    def send_scan(
        self,
        item_code: str,
        action: str,
        quantity: int,
        callback,
        tk_root,
    ) -> None:
        """
        Non-blocking POST /api/pos/scan.
        Adjusts inventory stock on the server.
        """
        t = threading.Thread(
            target=self._run_post,
            args=(item_code, action, quantity, callback, tk_root),
            daemon=True,
        )
        t.start()

    def _run_post(self, item_code, action, quantity, callback, tk_root) -> None:
        payload = json.dumps(
            {"item_code": item_code, "action": action, "quantity": quantity}
        ).encode("utf-8") 
        url = f"{API_BASE_URL}/api/pos/scan"
        req = urllib.request.Request(url, data=payload, method="POST", headers=_HEADERS)
        result = self._execute(req, item_code)
        tk_root.after(0, callback, result)

    # ── Shared HTTP helper ────────────────────────────────────────────────────

    def _execute(self, req: urllib.request.Request, item_code: str) -> dict:
        try:
            with urllib.request.urlopen(req, timeout=REQUEST_TIMEOUT) as resp:
                body = json.loads(resp.read().decode())
                log.info("API OK [%s]: %s", item_code, body.get("message", ""))
                return {"ok": True, "data": body.get("data", {})}

        except urllib.error.HTTPError as exc:
            try:
                body = json.loads(exc.read().decode())
                msg = body.get("message", f"HTTP {exc.code}")
            except Exception:
                msg = f"HTTP {exc.code}"
            log.warning("API HTTP error [%s]: %s", item_code, msg)
            return {"ok": False, "message": msg}

        except Exception as exc:
            log.warning("API connection error [%s]: %s", item_code, exc)
            return {"ok": False, "message": str(exc)}
