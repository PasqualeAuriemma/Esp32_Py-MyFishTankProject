"""
Mock di `micropython` per dry-run.
const() su MicroPython è un'ottimizzazione a compile-time che inlinea
il valore (zero overhead). Su CPython non esiste: la implementiamo come
funzione identità, che ha lo stesso comportamento funzionale (ritorna
il valore invariato) anche se senza l'ottimizzazione del bytecode.
"""


def const(value):
    return value


def mem_info(verbose=False):
    print("[micropython MOCK] mem_info() — non disponibile su CPython dry-run")
