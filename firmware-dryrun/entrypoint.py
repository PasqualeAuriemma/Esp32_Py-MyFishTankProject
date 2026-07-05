"""
entrypoint.py — replica la sequenza di avvio reale dell'ESP32.

Su MicroPython ESP32, boot.py viene eseguito automaticamente PRIMA di
main.py, nello stesso namespace `builtins` (così WLAN_INSTANCE creato
in boot.py è visibile in main.py senza import espliciti).

Sulla Unix port questo non avviene automaticamente: dobbiamo eseguire
noi i due file in sequenza, nello stesso processo, replicando l'ordine
reale. Questo è lo script che il Dockerfile lancia al posto di main.py
direttamente.
"""

import sys

print("=" * 60)
print(" DRY-RUN — esecuzione boot.py + main.py (ordine reale ESP32)")
print("=" * 60)

# ── STEP 1: esegue boot.py ──────────────────────────────────────────────
# Stessa logica del vero ESP32: boot.py popola builtins.WLAN_INSTANCE
# prima che main.py venga importato.
print("\n[entrypoint] Eseguo boot.py...\n")
with open("boot.py") as f:
    boot_code = f.read()
exec(compile(boot_code, "boot.py", "exec"), {"__name__": "__main__"})

# ── STEP 2: esegue main.py ───────────────────────────────────────────────
print("\n[entrypoint] Eseguo main.py...\n")
with open("main.py") as f:
    main_code = f.read()
exec(compile(main_code, "main.py", "exec"), {"__name__": "__main__"})
