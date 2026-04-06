# boot.py
import gc
import builtins

gc.threshold(gc.mem_free() // 4 + gc.mem_alloc())

try:
    import esp          # type: ignore[import-untyped]
    esp.osdebug(None)   # silenzia i log IDF: risparmia ~8KB di buffer UART
except ImportError:
    pass

gc.collect()
 
# ── STEP 1: WiFi driver – deve girare con heap ancora vergine ──────────
import network      # type: ignore[import-untyped]
import time
  
# Pre-inizializza il driver (alloca i buffer interni del firmware)
# SENZA ancora attivare la radio — questo è il momento più critico.
wlan = network.WLAN(network.STA_IF)
if wlan.active():
    # Spegni un eventuale stato residuo da un crash precedente
    wlan.active(False)
    time.sleep(0.3)
gc.collect()
 
# Esponi l'oggetto globalmente tramite builtins
builtins.WLAN_INSTANCE = wlan

gc.collect()
