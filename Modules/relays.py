from machine import Pin  # type: ignore[import]
from Resource.board_pins import BoardPins


class Relays:
    """Wrapper for all relay pins (outputs)."""

    def __init__(self) -> None:
        self._light = Pin(BoardPins.RELAY_LIGHT, Pin.OUT)
        self._filter = Pin(BoardPins.RELAY_FILTER, Pin.OUT)
        self._heater = Pin(BoardPins.RELAY_HEATER, Pin.OUT)
        self._feeder = Pin(BoardPins.RELAY_FEEDER, Pin.OUT)

    def init_relays_status(self, config):
        """Initialize relay states based on Config."""
        self._light.value(0 if config.get_on_off_light_auto() else 1)
        self._filter.value(0 if config.get_on_off_filter() else 1)
        self._heater.value(0 if config.get_on_off_heater() else 1)
        self._feeder.value(0 if config.get_on_off_feeder() else 1)

    def get_light_rele(self):
        return self._light

    def get_filter_rele(self):
        return self._filter

    def get_heater_rele(self):
        return self._heater

    def get_feeder_rele(self):
        return self._feeder
