# secret.py
# ─────────────────────────────────────────────────────────────────────────────

# ── WiFi ──────────────────────────────────────────────────────────────────────
WIFI_SSID = ""
WIFI_PASSWORD = ""

# ── Web App host ──────────────────────────────────────────────────────────────
# ESP32 reale:      indirizzo IP/hostname del server (es. "192.168.1.100")
# Simulatore Docker: nome del service Docker (es. "web")
WEBAPP_HOST     = "web"             # gateway Docker bridge
WEBAPP_PORT     = 80
WEBAPP_PROTOCOL = "http"          # "http" in rete locale / Docker, "https" in produzione

# ── IoT Auth Token ────────────────────────────────────────────────────────────
IOT_TOKEN = "dry-run-token-placeholder"

# ── Database (usato solo lato server/simulatore, NON sull'ESP32) ──────────────
DB_HOST = "db"
DB_USER = "root"
DB_PASS = ""
DB_NAME = "my_myfishtank"