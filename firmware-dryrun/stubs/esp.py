"""
Mock di `esp` per dry-run.
Modulo ESP32-specifico per funzioni IDF di basso livello. boot.py lo usa
solo per silenziare i log di debug — qui basta un no-op. boot.py ha già
un try/except ImportError attorno a questo import, quindi questo stub
è opzionale: senza, il path "except ImportError: pass" verrebbe testato
invece; con lo stub, testiamo il path "import riuscito" — entrambi
plausibili sul vero hardware a seconda del build MicroPython.
"""


def osdebug(level):
    print("[esp MOCK] osdebug({}) — log IDF silenziati (simulato)".format(level))
