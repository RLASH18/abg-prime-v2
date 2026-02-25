"""ABG Prime Cashier — POS System entry point."""
import sys
import os

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from app.core.app import POSApp


def main():
    app = POSApp()
    app.run()


if __name__ == "__main__":
    main()
