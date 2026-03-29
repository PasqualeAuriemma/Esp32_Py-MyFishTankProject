"""
Menu framework per display OLED SSD1306 su ESP32 / MicroPython (progetto PyTank).

Gerarchia delle classi
----------------------
MenuItem  (base astratta)
├── MenuView  — viste full-screen navigabili (sovrascrivono draw/up/down/select)
│   ├── MenuList            lista scorrevole di voci figlie
│   │   ├── MenuEnum        selettore pick-one (contiene EnumItem)
│   │   └── MenuConfirm     dialogo sì/no (contiene ConfirmItem)
│   ├── MenuMonitoringSensor  lettura live del sensore
│   ├── MenuSetDateTime     editor interattivo data e ora
│   ├── MenuSetTimer        impostazione orario ON/OFF per le luci
│   ├── MenuWifiInfo        schermata stato connessione WiFi
│   ├── MenuHeaterManage    editor soglie min/max temperatura
│   └── MenuError           schermata di errore con testo word-wrapped
└── MenuCallback  — voci con callback azione
    ├── MenuRow             riga singola disegnabile in una lista
    │   ├── ListItem        proxy che avvolge qualsiasi MenuItem
    │   ├── EnumItem        opzione dentro MenuEnum
    │   ├── ConfirmItem     scelta dentro MenuConfirm
    │   └── ButtonItem      riga che esegue callback al click
    └── ToggleItem          interruttore on/off booleano
        └── BackItem        navigazione: torna al menu padre
"""

from micropython import const  # type: ignore[import]

# Costanti di layout condivise da tutte le viste del menu.
# Con mpy-cross vengono inlineate nel bytecode: zero lookup a runtime.
_PER_PAGE    = const(4)   # voci visibili per pagina
_LINE_HEIGHT = const(10)  # altezza header in pixel
_FONT_WIDTH  = const(8)   # larghezza carattere in pixel
_FONT_HEIGHT = const(8)   # altezza carattere in pixel


class MenuItem:


    __slots__ = ("name", "_parent", "_display", "_visible")

    def __init__(self, name: str, parent=None, display=None, visible=None):
        self._parent = parent
        self.name = name
        self._visible = True if visible is None else visible
        self._display = display

    @property
    def visible(self):
        return self._visible

    @property
    def display(self):
        return self._display

    @display.setter
    def display(self, value):
        self._display = value

    @property
    def parent(self):
        return self._parent

    @parent.setter
    def parent(self, value):
        self._parent = value

    def draw(self):
        # called when someone click on menu item
        raise NotImplementedError()

    def click(self):
        raise NotImplementedError()


class MenuView(MenuItem):

    __slots__ = ("per_page", "line_height", "font_width", "font_height")

    def __init__(
        self,
        display,
        name: str,
        parent=None,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
        visible=None,
    ):
        super().__init__(name, parent, display, visible)
        self.per_page = per_page
        self.line_height = line_height
        self.font_width = font_width
        self.font_height = font_height

    def _menu_header(self, text):
        pass

    def up(self):
        # called when menu.move(-1) is called
        pass

    def down(self):
        # called when menu.move(1) is called
        pass

    def right(self):
        # called when menu.move(1) is called
        pass

    def left(self):
        # called when menu.move(1) is called
        pass

    def select(self):
        # print("Sono qua select")
        # called when menu.click() is called (on button press)
        # should return Instance of MenuItem (usually self.parent - if want to go level up or self to stay in current context)
        raise NotImplementedError()

    def reset(self):
        raise NotImplementedError()


class MenuCallback(MenuItem):
    __slots__ = ("_callback", "_decorator", "_is_active")

    def __init__(
        self,
        name: str,
        callback=None,
        decorator=None,
        visible=None,
        parent=None,
        display=None,
    ):
        super().__init__(name, parent, display, visible)
        self._callback = callback
        self._decorator = "" if decorator is None else decorator
        self._is_active = False

    @property
    def is_active(self):
        return self._is_active

    @is_active.setter
    def is_active(self, value):
        self._is_active = value

    @property
    def decorator(self):
        return self._decorator if not callable(self._decorator) else self.decorator()

    @decorator.setter
    def decorator(self, value):
        self._decorator = value

    @property
    def visible(self):
        return (
            self._visible
            if not self._check_callable(self._visible, False)
            else self._call_callable(self._visible)
        )

    @property
    def callback(self):
        return self._callback

    @callback.setter
    def callback(self, callback):
        self._check_callable(callback)
        self._callback = callback

    @staticmethod
    def _check_callable(param, raise_error=True):
        """Valida che *param* sia callable o una tupla ``(callable, arg)``.

        Args:
            param:       Valore da verificare.
            raise_error: Se True solleva ValueError in caso di errore; altrimenti restituisce False.

        Returns:
            True se valido, False se non valido e raise_error è False.
        """
        if not (callable(param) or (type(param) is tuple and callable(param[0]))):
            if raise_error:
                raise ValueError(
                    "callable param should be callable or tuple with callable on first place!"
                )
            else:
                return False
        return True

    @staticmethod
    def _call_callable(func, *args):
        """Invoca *func* con eventuali argomenti legati più eventuali *args* extra.

        Supporta due convenzioni di chiamata:
        - ``func``           — callable semplice, chiamato come ``func(*args)``.
        - ``(func, bound)``  — applicazione parziale: ``func(bound, *args)``.
          Se ``bound`` è a sua volta una tupla viene spacchettato.
        """
        if callable(func):
            return func(*args)
        elif type(func) is tuple and callable(func[0]):
            in_args = func[1] if type(func[1]) is tuple else (func[1],)
            return func[0](*tuple(list(in_args) + list(args)))


class MenuRow(MenuCallback):

    __slots__ = ()  # nessun attributo aggiuntivo rispetto a MenuCallback

    def __init__(
        self,
        name,
        callback=None,
        decorator=None,
        visible=None,
        parent=None,
        display=None,
    ):
        super().__init__(name, callback, decorator, visible, parent, display)

    def upd_decorator(self):
        """Aggiorna il label decorator prima del disegno. Sovrascrivere nelle sottoclassi."""
        pass

    def draw(
        self,
        pos,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
    ):
        """Renderizza questa riga nello slot verticale *pos* della lista.

        Il display è diviso verticalmente: i primi ``line_height`` pixel sono riservati
        all'intestazione; il resto è diviso equamente tra ``per_page`` righe.
        La riga attiva (selezionata) viene renderizzata invertita.

        Args:
            pos:         Indice di slot a base zero nella pagina visibile.
            per_page:    Numero di righe per pagina (usato per il calcolo dell'altezza).
            line_height: Altezza in pixel dell'area header.
            font_width:  Larghezza carattere in pixel.
            font_height: Altezza carattere in pixel.
        """
        self.upd_decorator()
        menu_y_end = int((self.display.height - line_height) / per_page)
        y = menu_y_end + (pos * menu_y_end)
        v_padding = int((menu_y_end - font_height) / 2)
        background = int(self.is_active)
        self.display.fill_rect(0, y, self.display.width, menu_y_end, background)
        self.display.text(self.name, 0, y + v_padding, int(not background))
        x_pos = self.display.width - (len(self.decorator) * font_width) - 1
        self.display.text(self.decorator, x_pos, y + v_padding, int(not background))


class MenuList(MenuView):

    __slots__ = ("_items", "_visible_items", "selected", "_visible_cache_valid",
                 "_on_enter", "_built")

    def __init__(
        self,
        display,
        name: str,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
        parent=None,
        visible=None,
        on_enter=None,
    ):
        super().__init__(
            display,
            name,
            parent,
            per_page,
            line_height,
            font_width,
            font_height,
            visible,
        )
        self._items = []
        self._visible_items = []
        self.selected = 0
        self._visible_cache_valid = False
        self._on_enter = on_enter
        self._built    = False

    @property
    def items(self):
        return self._items

    @items.setter
    def items(self, values):
        self._items = values

    def add(self, item, parent=None):
        """Avvolge *item* in un proxy ``ListItem`` e lo appende a questa lista.

        Supporta il method chaining (restituisce ``self``).
        La cache di visibilità viene invalidata affinché il prossimo render
        includa la nuova voce.

        Args:
            item:   Qualsiasi sottoclasse di ``MenuItem`` da aggiungere.
            parent: Override esplicito del padre; di default è questa lista.
        """
        item.parent = self if parent is None else parent
        item.display = self.display
        row = ListItem(item, self.visible)
        self._items.append(row)
        self._visible_cache_valid = False
        return self

    def reset(self):
        """Azzera l'indice di selezione alla prima voce."""
        self.selected = 0

    def __get_visible_item(self):
        """Restituisce la lista memorizzata nella cache delle voci attualmente visibili.

        Ricostruisce la cache da ``_items`` quando ``_visible_cache_valid`` è False
        (es. dopo ``add()`` o un cambio di visibilità dinamico).
        """
        if not self._visible_cache_valid:
            self._visible_items.clear()          # svuota senza deallocare
            for i in self._items:
                if i.visible:
                    self._visible_items.append(i)
            self._visible_cache_valid = True
        return self._visible_items

    def count(self) -> int:
        """Restituisce il numero di voci attualmente visibili."""
        elements = len(self.__get_visible_item())
        return elements

    def up(self) -> None:
        """Sposta la selezione di un passo su, con wrap-around all'ultima voce."""
        self.selected = self.selected - 1
        if self.selected < 0:
            self.selected = self.count() - 1

    def down(self) -> None:
        """Sposta la selezione di un passo giù, con wrap-around alla prima voce."""
        self.selected = self.selected + 1
        if self.selected > self.count() - 1:
            self.selected = 0

    def get(self, position):
        """Restituisce la voce visibile a *position* aggiornando il suo stato attivo.

        Aggiorna anche il decorator ``<<`` usato da ``MenuEnum`` per segnare
        il valore enum correntemente selezionato.
        Restituisce None se *position* è fuori range.
        """
        if position < 0 or position > self.count():
            return None
        else:
            item = self._visible_items[position]
            if hasattr(self, "selected_item"):
                item.decorator = "<<" if position == self.selected_item else ""
            item.is_active = position == self.selected
            return item

    def _enter(self):
        """Chiama _on_enter la prima volta che la lista viene mostrata (lazy build)."""
        if not self._built and self._on_enter is not None:
            self._on_enter()
            self._built = True

    def click(self):
        """Entra nella lista: esegue lazy build se necessario, poi disegna."""
        self._enter()
        self.draw()
        return self

    def select(self) -> None:
        """Restituisce la voce evidenziata affinché il Menu possa chiamarne ``click()``."""
        self.__get_visible_item()
        item = self.get(self.selected)
        return item

    def draw(self):
        """Renderizza la lista: header + finestra scorrevole di ``per_page`` righe.

        La finestra scorre in modo che la voce selezionata sia sempre visibile.
        Dopo aver disegnato tutte le righe, il buffer del display viene inviato con ``show()``.
        """
        self.display.fill(0)
        self._menu_header(self.name)
        elements = self.count()
        # Scorri la finestra: mantieni la riga selezionata nella pagina visibile.
        start = (
            self.selected - self.per_page + 1
            if self.selected + 1 > self.per_page
            else 0
        )
        end = start + self.per_page
        menu_pos = 0
        for i in range(start, end if end < elements else elements):
            self.get(i).draw(
                menu_pos, per_page=self.per_page, line_height=self.line_height
            )
            menu_pos += 1

        self.display.show()

    def _menu_header(self, text):
        """Disegna il titolo centrato e una linea separatrice orizzontale."""
        x = int((self.display.width / 2) - (len(text) * self.font_width / 2))
        self.display.text(str.upper(self.name), x, 0, 1)
        self.display.hline(0, self.line_height, self.display.width, 1)


class ToggleItem(MenuCallback):

    __slots__ = ("state_callback", "toggleValue")

    def __init__(
        self,
        name,
        state_callback,
        change_callback,
        toggleValue=("[x]", "[ ]"),
        parent=None,
        visible=None,
        display=None,
    ):
        super().__init__(name, change_callback, "", visible, parent, display)
        self._check_callable(state_callback)
        self.state_callback = state_callback
        self.toggleValue = toggleValue
        # print("ToggleItem init name " + self.name + " and it is visible " + str(self.visible) + " and it is visible 2 " + str(visible))
        self.upd_decorator()

    def check_status(self):
        """Chiama ``state_callback`` e restituisce lo stato booleano corrente."""
        return self._call_callable(self.state_callback)

    def upd_decorator(self):
        """Aggiorna il label a destra per riflettere lo stato corrente del toggle."""
        self.decorator = (
            self.toggleValue[0] if self.check_status() else self.toggleValue[1]
        )
        # print("ToggleItem decorator setting " + self.decorator)

    def click(self):
        """Esegue ``change_callback``, aggiorna il decorator e ridisegna la lista padre."""
        # print("Toggle item click " + self.name + " parent " + str(self.parent) +  " visible " + str(self.visible))
        self._call_callable(self.callback)
        self.upd_decorator()
        # print("Toggle item click parent name " + self.parent.name)
        self.parent.draw()
        return self.parent


class BackItem(MenuCallback):

    __slots__ = ()  # nessun attributo aggiuntivo rispetto a MenuCallback

    def __init__(self, name="<<< BACK", parent=None, exit=False):
        super().__init__(name)

    def click(self):
        """Azzera la lista corrente e risale di un livello."""
        self.parent.reset()
        self.parent.parent.draw()
        return self.parent.parent


class ListItem(MenuRow):

    __slots__ = ("obj", "obj_decorator", "visible_value")

    def __init__(self, obj, visible=None):
        self.obj = obj
        self.obj_decorator = self.get_after_check_decorator()
        self.visible_value = self.obj.visible if visible is None else visible
        super().__init__(
            obj.name,
            decorator=self.obj_decorator,
            visible=visible,
            parent=obj.parent,
            display=obj.display,
        )

    @property
    def visible(self):
        if hasattr(self.obj, "visible"):
            if not isinstance(self.obj.visible, bool):
                return (
                    True
                    if not self._check_callable(self.obj.visible, False)
                    else self._call_callable(self.obj.visible)
                )
            else:
                return self.obj.visible
        else:
            return True

    def get_after_check_decorator(self):
        """Restituisce il decorator dell'oggetto avvolto, oppure '>' come freccia di default."""
        return self.obj.decorator if hasattr(self.obj, "decorator") else ">"

    def upd_decorator(self):
        """Sincronizza il decorator di questo proxy dall'oggetto avvolto prima del disegno."""
        self.decorator = self.obj.decorator if hasattr(self.obj, "decorator") else ">"

    def click(self):
        """Delega l'evento click all'oggetto avvolto."""
        return self.obj.click()


class EnumItem(MenuRow):
    __slots__ = ()  # nessun attributo aggiuntivo rispetto a MenuRow

    def __init__(
        self, name, callback=None, parent=None, decorator="", visible=None, display=None
    ):
        super().__init__(name, callback, decorator, visible, parent, display)

    def click(self):
        """Applica questa opzione enum, aggiorna il marcatore di selezione del padre e torna indietro."""
        self._call_callable(self.callback)
        self.parent.selected_item = self.callback[1]
        self.parent.decorator = self.parent.get(self.callback[1]).name
        self.parent.reset()
        self.parent.parent.draw()
        return self.parent.parent


class ConfirmItem(MenuRow):

    __slots__ = ()  # nessun attributo aggiuntivo rispetto a MenuRow

    def __init__(
        self, name, callback=None, parent=None, decorator="", visible=None, display=None
    ):
        super().__init__(name, callback, decorator, visible, parent, display)

    def click(self):
        """Esegue l'azione con True (conferma) o False (annulla) in base alla posizione."""
        if self.callback[-1] == 0:
            self._call_callable((self.callback[0], True))
        else:
            self._call_callable((self.callback[0], False))
        self.parent.reset()
        self.parent.parent.draw()
        return self.parent.parent


class ButtonItem(MenuRow):

    __slots__ = ()  # nessun attributo aggiuntivo rispetto a MenuRow

    def __init__(
        self, name, callback=None, parent=None, decorator="", visible=None, display=None
    ):
        super().__init__(name, callback, decorator, visible, parent, display)
        # print(name)

    def click(self):
        """Esegue il callback e ridisegna la lista padre."""
        self._call_callable(self.callback)
        self.parent.draw()
        return self.parent


class MenuEnum(MenuList):

    __slots__ = ("selected_item", "callback")

    def __init__(
        self,
        display,
        name: str,
        items,
        callback,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
        parent=None,
        visible=None,
    ):
        super().__init__(
            display,
            name,
            per_page,
            line_height,
            font_width,
            font_height,
            parent,
            visible,
        )
        self.selected_item = 0
        if not isinstance(items, (list, tuple)):
            raise ValueError("items should be a list or tuple!")
        self.callback = callback
        self.add_items(items, self)
        self.decorator = self.get(self.selected_item).name

    @property
    def decorator(self):
        return self._decorator if not callable(self._decorator) else self.decorator()

    @decorator.setter
    def decorator(self, value):
        self._decorator = value

    def add(self, item, parent=None):
        self._items.append(item)

    def add_items(self, items: tuple, parent=None):
        if not isinstance(items, (list, tuple)):
            raise ValueError("items should be a list or tuple!")
        for pos, item in enumerate(items):
            row = EnumItem(
                str(item), (self.callback, pos), parent, display=self.display
            )
            self.add(row)


class MenuConfirm(MenuList):

    __slots__ = ("callback",)

    def __init__(
        self,
        display,
        name: str,
        items,
        callback,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
        parent=None,
        visible=None,
    ):
        super().__init__(
            display,
            name,
            per_page,
            line_height,
            font_width,
            font_height,
            parent,
            visible,
        )
        if not isinstance(items, tuple):
            raise ValueError("items should be a tuple!")
        self.callback = callback
        self.add_items(items, self)

    def add(self, item, parent=None):
        self._items.append(item)

    def add_items(self, items: list, parent=None):
        for pos, item in enumerate(items):
            row = ConfirmItem(
                str(item),
                (self.callback, pos),
                parent,
                display=self.display,
            )
            self.add(row)


class MenuMonitoringSensor(MenuView):

    __slots__ = ("status", "measure", "temperature", "_switch")

    def __init__(
        self,
        display,
        name,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
        parent=None,
        visible=None,
    ):
        super().__init__(
            display,
            name,
            parent,
            per_page,
            line_height,
            font_width,
            font_height,
            visible,
        )
        self.status = False
        self.measure = 0
        self.temperature = 0
        self._switch = False

    @property
    def switch(self):
        return self._switch

    @switch.setter
    def switch(self, value):
        self._switch = value

    def updatingValues(self, value, temp):
        """Aggiorna le letture del sensore e ridisegna se il monitoraggio live è attivo."""
        self.measure = value
        self.temperature = temp
        if self.switch:
            self.draw()

    def select(self):
        """Disattiva il monitoraggio live e naviga al menu padre."""
        self.switch = not self.switch
        return self.parent

    def draw(self):
        """Renderizza la schermata di monitoraggio con i valori correnti di misura e temperatura."""
        self.display.fill(0)
        self.display.rect(0, 0, self.display.width, self.display.height, 1)
        self._centered_text("WIFI: " + str(self.measure), 20, 1)
        self._centered_text("TEMPERATURE: " + str(self.temperature), 34, 1)
        self._centered_text("225.10.110.30", 44, 1)  # TODO cambiare con Config IP
        self.display.show()

    def click(self):
        """Attiva/disattiva il monitoraggio live e ridisegna; rimane su questa schermata."""
        self.switch = not self.switch
        self.draw()
        return self

    def _centered_text(self, text, y, c):
        """Disegna *text* centrato orizzontalmente alla posizione verticale *y*."""
        x = int(self.display.width / 2 - len(text) * 8 / 2)
        self.display.text(text, x, y, c)


class MenuSetDateTime(MenuView):
    """Editor interattivo di data e ora su display OLED completo.

    L'utente naviga tra cinque campi (giorno, mese, anno, ora, minuto)
    usando ``right``/``left`` (cambia campo) e ``up``/``down`` (cambia valore).
    ``select()`` scrive il valore nell'RTC hardware e crea un ``ButtonItem``
    per persistere la modifica tramite ``callback``.

    Ordine dei campi (indice ``amount``):
        0 → giorno (gg), 1 → mese (mm), 2 → anno (yy),
        3 → ora (hh), 4 → minuto (m)
    """

    __slots__ = ("_gg", "_mm", "_mm_max", "_yy", "_hh", "_m", "amount", "callback")

    def __init__(
        self,
        display,
        name,
        callback,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
        parent=None,
        visible=None,
    ):
        super().__init__(
            display,
            name,
            parent,
            per_page,
            line_height,
            font_width,
            font_height,
            visible,
        )
        self._gg = 1
        self._mm = 1
        self._mm_max = 12
        self._yy = 2025
        self._hh = 0
        self._m = 0
        self.amount = 0
        self.callback = callback

    @property
    def gg(self):
        return self._gg

    @gg.setter
    def gg(self, _value):
        max_value = self.max_day_month()
        if _value > max_value:
            self._gg = 1
        elif _value < 1:
            self._gg = max_value
        else:
            self._gg = _value

    @property
    def mm(self):
        return self._mm

    @mm.setter
    def mm(self, value_mm):
        if value_mm > self._mm_max:
            self._mm = 1
        elif value_mm < 1:
            self._mm = self._mm_max
        else:
            self._mm = value_mm
            if value_mm == 2 and self._gg > self.max_day_month():
                self._gg = self.max_day_month()

    @property
    def m(self):
        return self._m

    @m.setter
    def m(self, value):
        m_max = 60
        if value >= m_max:
            self._m = 0
        elif value < 0:
            self._m = m_max - 1
        else:
            self._m = value

    @property
    def hh(self):
        return self._hh

    @hh.setter
    def hh(self, value):
        hh_max = 24
        if value > hh_max - 1:
            self._hh = 0
        elif value < 0:
            self._hh = hh_max - 1
        else:
            self._hh = value

    @property
    def yy(self):
        return self._yy

    @yy.setter
    def yy(self, value):
        self._yy = value

    def max_day_month(self):
        """Restituisce il numero di giorni del mese/anno correntemente selezionato.

        Gestisce la regola del calendario gregoriano per gli anni bisestili:
        - Divisibile per 4 → bisestile, eccetto i secoli (÷100) che non sono ÷400.
        """
        if self.mm == 2:
            if self.yy % 100 == 0:
                # Anno secolare: bisestile solo se anche divisibile per 400
                if self.yy % 400 == 0:
                    return 29
                else:
                    return 28
            else:
                # Controllo anno bisestile ordinario
                if self.yy % 4 == 0:
                    return 29
                else:
                    return 28
        elif self.mm in (4, 6, 9, 11):
            return 30
        else:
            return 31

    def draw(self):
        self.display.fill(0)
        # background = self.amount == 1
        x_pos1 = int((self.display.width - (10 * 8)) / 2)
        x_pos2 = x_pos1 + 24
        x_pos3 = x_pos2 + 24
        if self.amount == 0:
            self.display.rect(x_pos1 - 2, 17, (2 * 10), 14, 1)
        elif self.amount == 1:
            self.display.rect(x_pos2 - 2, 17, (2 * 10), 14, 1)
        elif self.amount == 2:
            self.display.rect(x_pos3 - 2, 17, (4 * 9), 14, 1)
        elif self.amount == 3:
            self.display.rect(x_pos1 - 2, 45, (2 * 10), 14, 1)
        else:
            self.display.rect(x_pos2 - 2, 45, (2 * 10), 14, 1)
        self.display.text("DATA:", 0, 8, 1)
        self.display.text("{:02d}".format(self.gg), x_pos1, 20, 1)
        self.display.text("{:02d}".format(self.mm), x_pos2, 20, 1)
        self.display.text("{:04d}".format(self.yy), x_pos3, 20, 1)
        self.display.text("ORARIO:", 0, 35, 1)
        self.display.text("{:02d}".format(self.hh), x_pos1, 48, 1)
        self.display.text("{:02d}".format(self.m), x_pos2, 48, 1)
        self.display.hline(0, 3, self.display.width, 1)
        self.display.show()

    def click(self):
        self.draw()
        return self

    def select(self):
        """Scrive la data/ora modificata nell'RTC hardware e conferma tramite ButtonItem."""
        from machine import RTC

        # Scrivi nell'RTC interno dell'ESP32: (anno, mese, giorno, weekday, ore, minuti, secondi, subseconds)
        rtc = RTC()
        rtc.datetime((self._yy, self._mm, self._gg, self._hh, self._m, 0, 0, 0))
        return ButtonItem(
            "OK DATATIME",
            (self.callback, [self._gg, self._mm, self._yy, self._hh, self._m]),
            parent=self.parent,
        )

    def up(self):
        if self.amount == 0:
            self.gg = self.gg + 1
        elif self.amount == 1:
            self.mm = self.mm + 1
        elif self.amount == 2:
            self.yy = self.yy + 1
        elif self.amount == 3:
            self.hh = self.hh + 1
        else:
            self.m = self.m + 1

    def down(self):
        if self.amount == 0:
            self.gg = self.gg - 1
        elif self.amount == 1:
            self.mm = self.mm - 1
        elif self.amount == 2:
            self.yy = self.yy - 1
        elif self.amount == 3:
            self.hh = self.hh - 1
        else:
            self.m = self.m - 1

    def right(self):
        self.amount = self.amount + 1
        if self.amount > 4:
            self.amount = 0

    def left(self):
        self.amount = self.amount - 1
        if self.amount < 0:
            self.amount = 4


class MenuSetTimer(MenuView):
    """Impostazione dell'orario ON/OFF per lo schedule dell'illuminazione.

    Si modificano quattro campi: ora inizio, minuto inizio, ora fine, minuto fine.
    Navigazione: ``right``/``left`` cambiano il campo attivo;
    ``up``/``down`` incrementano/decrementano il valore con wrap-around.
    ``select()`` restituisce un ``ButtonItem`` che chiama
    ``callback([hh_start, min_start, hh_end, min_end])``.

    Ordine dei campi (indice ``amount``):
        0 → ora inizio (hh_start), 1 → minuto inizio (min_start),
        2 → ora fine (hh_end),     3 → minuto fine (min_end)
    """

    __slots__ = (
        "_hh_start",
        "_min_start",
        "_hh_end",
        "_min_end",
        "_m_max",
        "_hh_max",
        "amount",
        "callback",
    )

    def __init__(
        self,
        display,
        name,
        values,
        callback,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
        parent=None,
        visible=None,
    ):
        super().__init__(
            display,
            name,
            parent,
            per_page,
            line_height,
            font_width,
            font_height,
            visible,
        )
        self._hh_start = values[0]
        self._min_start = values[1]
        self._hh_end = values[2]
        self._min_end = values[3]
        self._m_max = 60
        self._hh_max = 24
        self.amount = 0
        self.callback = callback

    def _get_value(self, value, max_value):
        """Limita *value* all'intervallo [0, max_value) con wrap-around a entrambi i lati."""
        if value >= max_value:
            return 0
        elif value < 0:
            return max_value - 1
        else:
            return value

    @property
    def min_start(self):
        return self._min_start

    @min_start.setter
    def min_start(self, value):
        self._min_start = self._get_value(value, self._m_max)

    @property
    def hh_start(self):
        return self._hh_start

    @hh_start.setter
    def hh_start(self, value):
        self._hh_start = self._get_value(value, self._hh_max)

    @property
    def min_end(self):
        return self._min_end

    @min_end.setter
    def min_end(self, value):
        self._min_end = self._get_value(value, self._m_max)

    @property
    def hh_end(self):
        return self._hh_end

    @hh_end.setter
    def hh_end(self, value):
        self._hh_end = self._get_value(value, self._hh_max)

    def draw(self):
        self.display.fill(0)
        # background = self.amount == 1
        x_pos1 = int((self.display.width - (7 * 8)) / 2)
        x_pos2 = x_pos1 + 24
        if self.amount == 0:
            self.display.rect(x_pos1 - 2, 17, (2 * 10), 14, 1)
        elif self.amount == 1:
            self.display.rect(x_pos2 - 2, 17, (2 * 10), 14, 1)
        elif self.amount == 2:
            self.display.rect(x_pos1 - 2, 45, (2 * 10), 14, 1)
        else:
            self.display.rect(x_pos2 - 2, 45, (2 * 10), 14, 1)

        self.display.text("ORARIO START:", 0, 8, 1)
        self.display.text("{:02d}".format(self.hh_start), x_pos1, 20, 1)
        self.display.text("{:02d}".format(self.min_start), x_pos2, 20, 1)

        self.display.text("ORARIO END:", 0, 35, 1)
        self.display.text("{:02d}".format(self.hh_end), x_pos1, 48, 1)
        self.display.text("{:02d}".format(self.min_end), x_pos2, 48, 1)
        self.display.hline(0, 3, self.display.width, 1)
        self.display.show()

    def click(self):
        self.draw()
        return self

    def select(self):
        """Conferma le impostazioni del timer e restituisce un ButtonItem che le salva."""
        return ButtonItem(
            "OK TIMER",
            (
                self.callback,
                [self._hh_start, self._min_start, self._hh_end, self._min_end],
            ),
            parent=self.parent,
        )

    def up(self):
        if self.amount == 0:
            self.hh_start = self.hh_start + 1
        elif self.amount == 1:
            self.min_start = self.min_start + 1
        elif self.amount == 2:
            self.hh_end = self.hh_end + 1
        else:
            self.min_end = self.min_end + 1

    def down(self):
        if self.amount == 0:
            self.hh_start = self.hh_start - 1
        elif self.amount == 1:
            self.min_start = self.min_start - 1
        elif self.amount == 2:
            self.hh_end = self.hh_end - 1
        else:
            self.min_end = self.min_end - 1

    def right(self):
        self.amount = self.amount + 1
        if self.amount > 3:
            self.amount = 0

    def left(self):
        self.amount = self.amount - 1
        if self.amount < 0:
            self.amount = 3


class MenuWifiInfo(MenuView):
    """Schermata informativa in sola lettura sullo stato della connessione WiFi.

    Mostra lo stato della connessione (True/False) e l'indirizzo IP configurato.
    Premere SELECT o CLICK torna al menu padre senza alcuna azione.

    Nota: l'indirizzo IP mostrato è attualmente hard-coded; dovrebbe essere
    sostituito con il valore live dall'istanza ``WifiConnection`` (vedi TODO in draw()).
    """

    __slots__ = ("status",)

    def __init__(
        self,
        display,
        name,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
        parent=None,
        visible=None,
    ):
        super().__init__(
            display,
            name,
            parent,
            per_page,
            line_height,
            font_width,
            font_height,
            visible,
        )
        self.status = False

    def select(self):
        """Torna al menu padre senza eseguire alcuna azione."""
        return self.parent

    def click(self):
        """Disegna questa schermata info e rimane su di essa."""
        self.draw()
        return self

    def draw(self):
        """Renderizza lo stato WiFi all'interno di un rettangolo bordo."""
        self.display.fill(0)
        self.display.rect(0, 0, self.display.width, self.display.height, 1)
        self._centered_text("WIFI: " + str(self.get_status()), 20, 1)
        self._centered_text(
            "225.10.110.30", 34, 1
        )  # TODO: sostituire con IP live da WifiConnection
        self.display.show()

    def get_status(self):
        """Restituisce il booleano corrente dello stato di connessione."""
        return self.status

    def activate(self):
        """Inverte il flag status (chiamato esternamente per riflettere i cambiamenti di connessione)."""
        self.status = not self.status
        self.get_status()

    def _centered_text(self, text, y, c):
        """Disegna *text* centrato orizzontalmente alla posizione verticale *y*."""
        x = int(self.display.width / 2 - len(text) * 8 / 2)
        self.display.text(text, x, y, c)


class MenuHeaterManage(MenuView):
    """Editor soglie min/max temperatura per il controllo automatico del riscaldatore.

    Navigazione:
        up/down   - cambia campo attivo (0=min, 1=max)
        right/left - incrementa/decrementa il valore selezionato
        select()  - valida min < max e chiama callback

    Ordine campi (amount): 0 = min_temperature, 1 = max_temperature
    """

    # FIX: rimosso callback da __slots__ — le funzioni non beneficiano
    # di __slots__ e su MicroPython possono causare problemi con i metodi bound
    __slots__ = ("_temps", "amount", "callback")

    # Costanti di layout — evitano ricalcoli ad ogni draw()
    _FIELD_COUNT = const(2)
    _Y_MIN       = const(17)
    _Y_MAX       = const(45)
    _Y_TEXT_MIN  = const(8)
    _Y_TEXT_MAX  = const(35)
    _RECT_W      = const(20)   # 2 * 10
    _RECT_H      = const(14)

    def __init__(
        self,
        display,
        name,
        values,
        callback,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
        parent=None,
        visible=None,
    ):
        super().__init__(
            display, name, parent,
            per_page, line_height, font_width, font_height, visible,
        )
        # FIX: usa una lista invece di due attributi separati —
        # right/left/draw accedono per indice, zero branch inutili
        self._temps   = [values[0], values[1]]
        self.amount   = 0
        self.callback = callback

    # ── Proprietà ──────────────────────────────────────────────────────
    # FIX: property semplificate — il setter non fa nulla di speciale,
    # ma le teniamo per compatibilità con il resto del menu

    @property
    def min_temperature(self):
        return self._temps[0]

    @min_temperature.setter
    def min_temperature(self, value):
        self._temps[0] = value

    @property
    def max_temperature(self):
        return self._temps[1]

    @max_temperature.setter
    def max_temperature(self, value):
        self._temps[1] = value

    def up(self):
        self.amount = (self.amount + 1) % self._FIELD_COUNT

    def down(self):
        self.amount = (self.amount - 1) % self._FIELD_COUNT

    def right(self):
        self._temps[self.amount] += 1

    def left(self):
        self._temps[self.amount] -= 1

    def draw(self):
        d      = self.display
        x_pos  = (d.width - 8) // 2   # FIX: // invece di int() + divisione float

        d.fill(0)
        d.hline(0, 3, d.width, 1)

        # FIX: seleziona la y del rettangolo per indice invece di if/else
        rect_y = self._Y_MIN if self.amount == 0 else self._Y_MAX
        d.rect(x_pos - 2, rect_y, self._RECT_W, self._RECT_H, 1)

        d.text("MIN TEMPERATURE:", 0, self._Y_TEXT_MIN, 1)
        d.text("{:02d}".format(self._temps[0]), x_pos, self._Y_MIN + 3, 1)

        d.text("MAX TEMPERATURE:", 0, self._Y_TEXT_MAX, 1)
        d.text("{:02d}".format(self._temps[1]), x_pos, self._Y_MAX + 3, 1)

        d.show()

    def click(self):
        self.draw()
        return self

    # ── Validazione ────────────────────────────────────────────────────

    def select(self):
        """Valida min < max; restituisce ButtonItem o MenuError."""
        if self._temps[0] >= self._temps[1]:
            return MenuError(
                self.display,
                self.name,
                "Err: max temp <= min temp",  # FIX: stringa corta — meno RAM heap
                parent=self,
            )
        return ButtonItem(
            "OK HEATER",
            (self.callback, [self._temps[0], self._temps[1]]),
            parent=self.parent,
        )

class MenuError(MenuView):
    """Schermata di errore full-screen con testo word-wrapped.

    La stringa ``message`` viene suddivisa in parole per adattarsi al display
    OLED largo 128 pixel (max 16 caratteri per riga con font 8px) e renderizzata
    centrata.  Premere SELECT torna alla vista padre.

    Args:
        message: Testo di errore in chiaro da mostrare.
    """

    __slots__ = ("message",)

    def __init__(
        self,
        display,
        name,
        message,
        per_page: int = _PER_PAGE,
        line_height: int = _LINE_HEIGHT,
        font_width: int = _FONT_WIDTH,
        font_height: int = _FONT_HEIGHT,
        parent=None,
        visible=None,
    ):
        super().__init__(
            display,
            name,
            parent,
            per_page,
            line_height,
            font_width,
            font_height,
            visible,
        )
        self.message = message

    def select(self):
        """Torna alla vista padre (es. dopo aver letto il messaggio di errore)."""
        return self.parent

    def click(self):
        """Disegna la schermata di errore e rimane su di essa."""
        self.draw()
        return self

    def _count_error_row(self, message):
        """Suddivide *message* in righe di al massimo 16 caratteri.

        Restituisce una lista di stringhe, ognuna contenibile in una riga
        del display OLED con font 8px.
        """
        result = []
        num_char_tot = 0
        temp_row = []
        for word in message.split(" "):
            num_char_word = len(word)
            num_char_word = num_char_word if not temp_row else num_char_word + 1
            num_char_tot = num_char_tot + num_char_word
            if num_char_tot < 16:
                temp_row.append(word)
            else:
                mex = " ".join(temp_row)
                result.append(mex)
                num_char_tot = num_char_word - 1
                temp_row = []
                temp_row.append(word)
        if temp_row:
            result.append(" ".join(temp_row))
        return result

    def draw(self):
        message_rows = self._count_error_row(self.message)
        num_mex = len(message_rows)
        self.display.fill(0)
        self.display.rect(0, 0, self.display.width, self.display.height, 1)
        _center = int(self.display.height / 2 - 4)
        _y = _center - (int(num_mex / 2) * 12)
        for pos, mex in enumerate(message_rows):
            position = _y + (12 * pos)
            self._centered_text(mex, position, 1)

        self.display.show()

    def _centered_text(self, text, y, c):
        """Disegna *text* centrato orizzontalmente alla posizione verticale *y*."""
        x = int(self.display.width / 2 - len(text) * 8 / 2)
        self.display.text(text, x, y, c)



    
class Menu:
    """Controller di alto livello che gestisce quale schermata è attiva.

    Funge da facciata sulla gerarchia MenuView: mantiene current_screen
    e delega tutti gli eventi di navigazione (move, shift, click) ad essa.

    set_main_screen() deve essere chiamato una volta per installare la
    MenuList radice prima che qualsiasi navigazione possa avvenire.
    """

    __slots__ = ("parent", "main_screen", "current_screen")

    def __init__(self, parent):
        self.parent         = parent
        self.main_screen    = None
        self.current_screen = None

    def set_main_screen(self, screen):
        """Installa screen come radice e la imposta come schermata corrente."""
        self.current_screen = screen
        if self.main_screen is None:
            screen.parent    = self.parent
            self.main_screen = screen

    def move(self, direction: int = 1):
        """Sposta selezione su (direction < 0) o giù (direction > 0) e ridisegna."""
        if direction < 0:
            self.current_screen.up()
        else:
            self.current_screen.down()
        self.current_screen.draw()

    def shift(self, direction: int = 1):
        """Cambia campo attivo: right (direction < 0) o left, poi ridisegna."""
        if direction < 0:
            self.current_screen.right()
        else:
            self.current_screen.left()
        self.current_screen.draw()

    def click(self):
        """Conferma la selezione: esegue select() poi click() sulla nuova schermata."""
        screen = self.current_screen.select()
        if screen is not None:
            self.current_screen = screen.click()

    def reset(self):
        """Torna alla schermata radice e azzera l'indice di selezione."""
        main = self.main_screen
        if main is not None:
            main.selected       = 0
            self.current_screen = main

    def draw(self):
        self.current_screen.draw()
