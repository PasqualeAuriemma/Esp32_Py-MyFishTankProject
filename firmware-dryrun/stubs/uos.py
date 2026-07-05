"""
Mock di `uos` per dry-run.
Simula un filesystem FAT in memoria con un file SETTINGS.JSON fittizio.
Intercetta sia os.stat che builtins.open per i path /sd/ — necessario
perché sdCardManager usa `import os` per stat e `open()` per leggere,
non le funzioni di uos direttamente.
"""

import os as _os
import json
import builtins as _builtins

MOCK_SD_FILE = True   # False = simula SD assente

_MOUNT_POINT = "/sd"
_CONFIG_FILE = "/SETTINGS.JSON"
_CONFIG_PATH = _MOUNT_POINT + _CONFIG_FILE

_MOCK_CONFIG = {
    "mode": "0",
    "on_off_light_auto": False,
    "on_off_filter": True,
    "on_off_heater": True,
    "on_off_feeder": False,
    "on_off_ec": True,
    "on_off_ph": True,
    "on_off_temperature": True,
    "on_off_ec_sending": True,
    "on_off_ph_sending": True,
    "on_off_temperature_sending": True,
    "freq_update_web_ec": "8",
    "freq_update_web_ph": "8",
    "freq_update_web_temperature": "0.1",
    "ph_offset": 0.0,
    "ph_slope": -0.18,   # NON zero — PHSensor rifiuta slope=0
    "on_off_heater_auto": False,
    "on_off_filter_auto": False,
    "freq_filter": "8",
    "timer_time": "08:00-22:00",
    "connection_action": False,
    "on_off_recovery": False,
}

_mounted = {}


def mount(vfs, mount_point):
    print("[uos MOCK] mount({!r})".format(mount_point))
    _mounted[mount_point] = vfs


def umount(mount_point):
    print("[uos MOCK] umount({!r})".format(mount_point))
    _mounted.pop(mount_point, None)


def listdir(path="."):
    if MOCK_SD_FILE and path == _MOUNT_POINT:
        return [_CONFIG_FILE.lstrip("/")]
    return _os.listdir(path)


class VfsFat:
    def __init__(self, block_device):
        self.block_device = block_device
        print("[uos.VfsFat MOCK] init — filesystem SD simulato")


# ── Patch os.stat per intercettare path /sd/ ─────────────────────────────────
# sdCardManager fa `import os` e usa `os.stat` direttamente — dobbiamo
# patchare il modulo os stesso, non solo uos.stat.
_real_stat = _os.stat


def _patched_stat(path, *args, **kwargs):
    if MOCK_SD_FILE and isinstance(path, str) and path == _CONFIG_PATH:
        print("[uos MOCK] os.stat({!r}) -> file trovato (mock)".format(path))
        return _os.stat_result((0o100644, 0, 0, 1, 0, 0, 256, 0, 0, 0))
    return _real_stat(path, *args, **kwargs)


_os.stat = _patched_stat


# ── Patch builtins.open per intercettare path /sd/ ───────────────────────────
# sdCardManager usa open() nativo per leggere il file JSON.
_real_open = _builtins.open


def _patched_open(path, mode="r", *args, **kwargs):
    if MOCK_SD_FILE and isinstance(path, str) and path == _CONFIG_PATH:
        print("[uos MOCK] open({!r}) -> JSON config mockato".format(path))
        import io
        return io.StringIO(json.dumps(_MOCK_CONFIG))
    return _real_open(path, mode, *args, **kwargs)


_builtins.open = _patched_open
