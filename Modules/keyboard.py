from machine import Pin, ADC  # type: ignore[import]
from micropython import const  # type: ignore[import]

from Helper.Singleton import Singleton


class Keyboard(Singleton):
    """
    Singleton wrapper for the analog "keyboard" connected on a single ADC pin.

    The external hardware is a resistor ladder: each key produces a different
    analog voltage on the same ADC input. This class reads the ADC value and
    returns a symbolic code representing which key is currently pressed.
    """

    __slots__ = (
        "board_pin",
        "_singleton_initialized",
    )  # limit instance attributes to save memory (MicroPython-specific optimization)

    # Raw threshold used to decide when the analog line is considered "active".
    _PRESS_THRESHOLD = const(2000)
    # code_keys = [1, 2, 3, 4, 5, 6, 0]
    # {right, left, up, down, ok, 'null at the moment', resetKey} 
    # Key codes as class-level const — zero dict lookup, inlineabili da mpy-cross.
    # Eliminano il dict _KEY_TO_CODE e le property che lo interrogavano.
    right_keypad_value = const(1)
    left_keypad_value  = const(2)
    up_keypad_value    = const(3)
    down_keypad_value  = const(4)
    ok_keypad_value    = const(5)
    reset_keypad_value = const(0)
    null_keypad_value  = const(6)  # nessun tasto premuto

    def __init__(self, pin: int) -> None:
        """
        Initialize the ADC channel used by the keyboard.

        Args:
            pin (int): GPIO number where the resistor-ladder keyboard is connected.
        """
        # Ensure one-time initialization when used as a Singleton.
        if getattr(self, "_singleton_initialized", True):
            return

        # Configure the ADC on the shared keyboard pin.
        self.board_pin = ADC(Pin(pin))
        # 11dB attenuation extends the measurable voltage range (board specific).
        self.board_pin.atten(ADC.ATTN_11DB)

        self._singleton_initialized = True

    def get_digit_keyboard(self):
        """
        Read the analog keyboard and translate it into a numeric key code.

        Returns:
            int: A code that identifies which "key" is pressed:
                 0 = none, 1 = up, 2 = down, 3 = shift -1, 4 = shift +1, 5 = click.
        """
        # Take a single ADC reading as the current "snapshot" of the keyboard.
        value = self.board_pin.read()
        
        # Convert the raw ADC reading to an approximate voltage (×100, integer arithmetic).
        # equivalente: value * 330 // 4095  (risultato = tensione * 100, int)
        analog_voltage = value * 330 // 4095
        
        # NOTE: at the moment all branches use the same threshold. In a typical
        # resistor-ladder keyboard each key will occupy a different voltage
        # range, so in the future you may want to add per-key ranges here.

        if 0 <= analog_voltage < 280:
            if 0 <= analog_voltage < 21:
                # LEFT
                return self.left_keypad_value
            elif 21 <= analog_voltage < 61:
                # DOWN
                return self.down_keypad_value
            elif 61 <= analog_voltage < 121:
                # UP
                return self.up_keypad_value
            elif 121 <= analog_voltage < 191:
                # RIGHT
                return self.right_keypad_value
            else:
                # 191 <= analog_voltage < 280  -> OK
                return self.ok_keypad_value
        else:
            # No press detected
            return self.null_keypad_value
