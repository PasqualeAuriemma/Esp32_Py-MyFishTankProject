"""
Mock di `ntptime` per dry-run.
Su MicroPython reale, ntptime.settime() fa una query NTP via UDP e
aggiorna machine.RTC() interno. Qui simuliamo successo immediato senza
I/O di rete, dato che l'obiettivo del dry-run è verificare la logica
applicativa (NTP.sync() in ntpManager.py), non la rete reale.
"""

import time

host = "pool.ntp.org"


def settime():
    print("[ntptime MOCK] settime() — simulo sync NTP riuscita (no I/O reale)")
    # Non modifichiamo davvero l'orologio di sistema: il dry-run continua
    # a usare l'ora del container, che va benissimo per verificare la logica.
    return None
