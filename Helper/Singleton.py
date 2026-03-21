# MicroPython-compatible Singleton mixin with memory management.
#
# Usage:
#   class MyManager(Singleton):
#       def __init__(self):
#           if getattr(self, "_singleton_initialized", True):
#               return
#           # one-time init here
#           self._singleton_initialized = True
#
#   obj = MyManager()  # same instance every time
#   MyManager.reset_instance()  # release instance for GC (e.g. shutdown or tests)

class Singleton:
    __slots__ = ()          # ← blocca __dict__ nella catena ereditaria
    _instances = {}         # class-level, non instance-level, non è toccato da __slots__
    def __new__(cls, *args, **kwargs):
        if cls not in cls._instances:
            inst = super().__new__(cls)
            setattr(inst, "_singleton_initialized", False)
            cls._instances[cls] = inst
        return cls._instances[cls]

    @classmethod
    def reset_instance(cls):
        """Remove the singleton instance so it can be garbage-collected."""
        if cls in cls._instances:
            del cls._instances[cls]

    @classmethod
    def get_instance(cls):
        """Return the current instance or None if reset. Does not create one."""
        return cls._instances.get(cls, None)
