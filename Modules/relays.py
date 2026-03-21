from machine import Pin  # type: ignore[import]
from Resource.board_pins import BoardPins


class Relays:
    """Wrapper for all relay pins (outputs)."""

    def __init__(self) -> None:
        self.light = Pin(BoardPins.RELAY_LIGHT, Pin.OUT)
        self.filter = Pin(BoardPins.RELAY_FILTER, Pin.OUT)
        self.heater = Pin(BoardPins.RELAY_HEATER, Pin.OUT)
        self.feeder = Pin(BoardPins.RELAY_FEEDER, Pin.OUT)
