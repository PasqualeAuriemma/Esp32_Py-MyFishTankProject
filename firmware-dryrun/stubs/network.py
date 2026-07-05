"""
Mock eseguibile del modulo `network` per dry-run su Docker/Unix MicroPython.
Firme allineate a network.pyi fornito. Simula una connessione WiFi che
ha sempre successo dopo una breve attesa, per non bloccare il dry-run
su un vero AP che qui non esiste.
"""

import time

STA_IF = 0
AP_IF  = 1

STAT_IDLE            = 0
STAT_CONNECTING      = 1
STAT_WRONG_PASSWORD  = 2
STAT_NO_AP_FOUND     = 3
STAT_GOT_IP          = 4


class WLAN:
    def __init__(self, interface):
        self.interface = interface
        self._active = False
        self._connected = False
        self._status = STAT_IDLE
        print("[network.WLAN] init interface={}".format(interface))

    def active(self, is_active=None):
        if is_active is None:
            return self._active
        self._active = is_active
        print("[network.WLAN] active({})".format(is_active))
        if not is_active:
            self._connected = False
            self._status = STAT_IDLE

    def connect(self, ssid, password, bssid=None):
        print("[network.WLAN] connect(ssid={!r}) — simulo connessione...".format(ssid))
        self._status = STAT_CONNECTING
        # Dry-run: simula una connessione riuscita dopo una breve attesa,
        # così il resto del firmware procede come se il WiFi reale ci fosse.
        time.sleep(0.5)
        self._connected = True
        self._status = STAT_GOT_IP
        print("[network.WLAN] connessione simulata riuscita.")

    def disconnect(self):
        print("[network.WLAN] disconnect()")
        self._connected = False
        self._status = STAT_IDLE

    def isconnected(self):
        return self._connected

    def status(self):
        return self._status

    def ifconfig(self):
        # IP fittizio plausibile per il dry-run.
        return ("192.168.1.50", "255.255.255.0", "192.168.1.1", "8.8.8.8")
