"""
Mock di `utime` per dry-run.
Su MicroPython, utime è il modulo time "embedded" con funzioni extra
(sleep_ms, sleep_us, ticks_ms). Su CPython usiamo time standard e
aggiungiamo le funzioni mancanti come wrapper.
"""

from time import *  # noqa: F401,F403 — re-esporta sleep, time, localtime, ecc.
import time as _time


def sleep_ms(ms):
    _time.sleep(ms / 1000.0)


def sleep_us(us):
    _time.sleep(us / 1_000_000.0)


def ticks_ms():
    return int(_time.time() * 1000)


def ticks_us():
    return int(_time.time() * 1_000_000)


def ticks_diff(t1, t2):
    return t1 - t2


def ticks_add(t, delta):
    return t + delta
