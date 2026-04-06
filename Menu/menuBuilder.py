# Manager/menuBuilder.py
#
# Costruisce il menu in modo lazy: solo il primo livello viene creato
# subito. Ogni sottomenu viene costruito SOLO quando l'utente ci naviga
# dentro, tramite il parametro on_enter di MenuList.
#
# Flusso:
#   1. build_menu()      -> crea solo la radice + 3 voci di primo livello
#   2. click su "RELAYS" -> MenuList.click() -> _enter() -> _build_relays()
#   3. click su "EC"     -> _enter() -> _build_ec()
#   ...e cosi via
#
# Vantaggi:
#   - RAM al boot: ~5 oggetti invece di ~60 (+40 KB heap liberi)
#   - GC meno frammentato
#   - Primo click OK piu veloce

import gc
from Menu.pymenu import (
    MenuList,
    MenuEnum,
    MenuConfirm,
    MenuMonitoringSensor,
    ToggleItem,
    BackItem,
    MenuWifiInfo,
    MenuSetDateTime,
    MenuSetTimer,
    MenuHeaterManage,
)


def build_menu(viewer, cfg):
    """Installa il menu radice. Chiamato quando viewer.menu.main_screen is None."""
    _build_root(viewer, cfg)
    gc.collect()


def _build_root(viewer, cfg):
    d = viewer.display
    relays_list = MenuList(
        d, "RELAYS", on_enter=lambda: _build_relays(relays_list, viewer, cfg)
    )
    sensors_list = MenuList(
        d, "SENSORS", on_enter=lambda: _build_sensors(sensors_list, viewer, cfg)
    )
    settings_list = MenuList(
        d, "SETTINGS", on_enter=lambda: _build_settings(settings_list, viewer, cfg)
    )
    viewer.menu.set_main_screen(
        MenuList(d, "MENU")
        .add(MenuEnum(d, "MODE", cfg.mode_list, cfg.set_mode))
        .add(relays_list)
        .add(sensors_list)
        .add(settings_list)
        .add(BackItem())
    )


def _build_relays(ml, viewer, cfg):
    (
        ml.add(
            ToggleItem(
                "LIGHTS",
                cfg.get_on_off_light_auto,
                viewer.toggle_on_off_light_auto,
                ("ON", "OFF"),
            )
        )
        .add(
            ToggleItem(
                "FILTER",
                cfg.get_on_off_filter,
                viewer.toggle_on_off_filter,
                ("ON", "OFF"),
            )
        )
        .add(
            ToggleItem(
                "HEATER",
                cfg.get_on_off_heater,
                viewer.toggle_on_off_heater,
                ("ON", "OFF"),
            )
        )
        .add(
            ToggleItem(
                "FEEDER",
                cfg.get_on_off_feeder,
                viewer.toggle_on_off_feeder,
                ("ON", "OFF"),
            )
        )
        .add(BackItem())
    )
    gc.collect()


def _build_sensors(ml, viewer, cfg):
    d = viewer.display
    ec_list = MenuList(d, "EC", on_enter=lambda: _build_ec(ec_list, viewer, cfg))
    ph_list = MenuList(d, "PH", on_enter=lambda: _build_ph(ph_list, viewer, cfg))
    temp_list = MenuList(
        d, "THERMOMETER", on_enter=lambda: _build_temperature(temp_list, viewer, cfg)
    )
    (ml.add(ec_list).add(ph_list).add(temp_list).add(BackItem()))
    gc.collect()


def _build_ec(ml, viewer, cfg):
    d = viewer.display
    (
        ml.add(ToggleItem("ACTIVATION", cfg.get_on_off_ec, viewer.toggle_on_off_ec))
        .add(MenuMonitoringSensor(d, "MONITORING", visible=cfg.get_on_off_ec))
        .add(
            ToggleItem(
                "WEB SERVER",
                cfg.get_on_off_ec_sending,
                viewer.toggle_on_off_ec_sending,
                visible=cfg.get_on_off_ec,
            )
        )
        .add(
            MenuEnum(
                d,
                "WEB RATE",
                cfg.freq,
                cfg.set_freq_update_web_ec,
                visible=cfg.get_on_off_ec_sending,
            )
        )
        .add(
            MenuConfirm(
                d,
                "SEND TO WEB",
                ("-> SEND", "<- BACK"),
                viewer._send_ec,
                visible=cfg.get_on_off_ec_sending,
            )
        )
        .add(BackItem())
    )
    gc.collect()


def _build_ph(ml, viewer, cfg):
    d = viewer.display
    (
        ml.add(ToggleItem("ACTIVATION", cfg.get_on_off_ph, viewer.toggle_on_off_ph))
        .add(MenuMonitoringSensor(d, "MONITORING", visible=cfg.get_on_off_ph))
        .add(
            ToggleItem(
                "WEB SERVER",
                cfg.get_on_off_ph_sending,
                viewer.toggle_on_off_ph_sending,
                visible=cfg.get_on_off_ph,
            )
        )
        .add(
            MenuEnum(
                d,
                "WEB RATE",
                cfg.freq,
                cfg.set_freq_update_web_ph,
                visible=cfg.get_on_off_ph_sending,
            )
        )
        .add(
            MenuConfirm(
                d,
                "SEND",
                ("-> SEND", "<- BACK"),
                viewer._send_ph,
                visible=cfg.get_on_off_ph_sending,
            )
        )
        .add(BackItem())
    )
    gc.collect()


def _build_temperature(ml, viewer, cfg):
    d = viewer.display
    (
        ml.add(
            ToggleItem(
                "ACTIVATION",
                cfg.get_on_off_temperature,
                viewer.toggle_on_off_temperature,
            )
        )
        .add(
            ToggleItem(
                "WEB SERVER",
                cfg.get_on_off_temperature_sending,
                viewer.toggle_on_off_temperature_sending,
                visible=cfg.get_on_off_temperature,
            )
        )
        .add(
            MenuEnum(
                d,
                "WEB RATE",
                cfg.freq,
                cfg.set_freq_update_web_temperature,
                visible=cfg.get_on_off_temperature_sending,
            )
        )
        .add(
            MenuConfirm(
                d,
                "SEND",
                ("-> SEND", "<- BACK"),
                viewer.send_temperature,
                visible=cfg.get_on_off_temperature_sending,
            )
        )
        .add(BackItem())
    )
    gc.collect()


def _build_settings(ml, viewer, cfg):
    d = viewer.display
    wifi_list = MenuList(
        d, "WIFI", on_enter=lambda: _build_wifi(wifi_list, viewer, cfg)
    )
    heater_list = MenuList(
        d, "HEATER AUTO", on_enter=lambda: _build_heater_auto(heater_list, viewer, cfg)
    )
    filter_list = MenuList(
        d, "FILTER AUTO", on_enter=lambda: _build_filter_auto(filter_list, viewer, cfg)
    )
    (
        ml.add(wifi_list)
        .add(MenuSetDateTime(d, "DATE/TIME", print))
        .add(MenuSetTimer(d, "LIGHT TIMER", cfg.get_timer_time(), cfg.set_timer_time))
        .add(heater_list)
        .add(filter_list)
        .add(MenuConfirm(d, "RECOVERY", ["-> YES", "<- NO"], cfg.set_on_off_recovery))
        .add(BackItem())
    )
    gc.collect()


def _build_wifi(ml, viewer, cfg):
    d = viewer.display
    (
        ml.add(MenuWifiInfo(d, "INFO"))
        .add(
            MenuConfirm(d, "CONNECTING", ("-> YES", "<- NO"), cfg.set_connection_action)
        )
        .add(BackItem())
    )
    gc.collect()


def _build_heater_auto(ml, viewer, cfg):
    d = viewer.display
    (
        ml.add(
            ToggleItem(
                "ACTIVATION",
                cfg.get_on_off_heater_auto,
                viewer.toggle_on_off_heater_auto,
            )
        )
        .add(
            MenuHeaterManage(
                d,
                "SETTING",
                cfg.get_timer_time(),
                cfg.set_auto_heater,
                visible=cfg.get_on_off_heater_auto,
            )
        )
        .add(BackItem())
    )
    gc.collect()


def _build_filter_auto(ml, viewer, cfg):
    d = viewer.display
    (
        ml.add(
            ToggleItem(
                "ACTIVATION",
                cfg.get_on_off_filter_auto,
                viewer.toggle_on_off_filter_auto,
            )
        )
        .add(
            MenuEnum(
                d,
                "RATE",
                cfg.freq,
                cfg.set_freq_filter,
                visible=cfg.get_on_off_filter_auto,
            )
        )
        .add(BackItem())
    )
    gc.collect()
