"""
Configuration for the POS application.
Sensitive values are loaded from the .env file in the pos/ root.
"""

import os
from pathlib import Path
from dotenv import load_dotenv

# Load pos/.env
load_dotenv(Path(__file__).resolve().parents[2] / ".env")

API_BASE_URL = os.getenv("API_BASE_URL", "http://localhost")

POS_API_SECRET = os.getenv("POS_API_SECRET", "")