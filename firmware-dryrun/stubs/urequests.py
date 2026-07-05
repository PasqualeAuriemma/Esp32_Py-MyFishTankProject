"""
Mock eseguibile di urequests per dry-run.
Logga le richieste invece di farle realmente: l'obiettivo del dry-run è
verificare che il loop MicroPython giri senza eccezioni logiche, non
testare la connettività di rete reale (per quello c'è già il simulatore
Docker separato che parla http reale con la webapp).
"""


class Response:
    def __init__(self, status_code=200, text='{"status":"ok (mock)"}', reason="OK (mock)"):
        self.status_code = status_code
        self.text = text
        self.reason = reason

    def close(self):
        pass


def get(url, **kwargs):
    print("[urequests MOCK] GET {} kwargs={}".format(url, kwargs))
    return Response()


def post(url, data=None, json=None, headers=None, **kwargs):
    print("[urequests MOCK] POST {}".format(url))
    print("[urequests MOCK]   headers={}".format(headers))
    print("[urequests MOCK]   data={}".format(data))
    return Response()
