"""
Clase: SAPManager
Descripción:
    Permite manejar las APIs de SAP.
"""
import requests
import json
from packages.lfw_json.json_manager import JSONManager

class SAPManager:
    configuracion = None
    sessionId = ""

    def __init__(self):
        """ Constructor de clase """
        oConfig = JSONManager()
        oConfig.file_name = "config.json"
        oConfig.get_content()
        self.configuracion = oConfig.data

    def login (self):
        """ 
            Este método permite loguearse en SAP.
        """
        url = self.configuracion["urls"]["login"]
        body = self.configuracion["body"]
        headers = {
            "Content-Type": "application/json"
        }
        respuesta = requests.post(url, headers=headers, data=json.dumps(body), verify=False).json()
        self.sessionId = respuesta["SessionId"]

    def getData (self, xurlName, xfilter = None, xskip = 0):
        """ 
            Este método devuelve un dato a partir del nombre de una URL definida en
            el archivo config.json.
            Devuelve un array JSON con los datos de la API
        """
        if xfilter == None:
            url = self.configuracion["urls"][xurlName]
        else:
            url = self.configuracion["urls"][xurlName] + "?$filter=" + xfilter

        if xskip != 0 :
            url = url + "?$skip=" + str(xskip)

        headers = {
            "Content-Type": "application/json",
            "Cookie": 'B1SESSION=' + self.sessionId + "; ROUTER.node1"
        }
        result = requests.get(url, headers=headers, verify=False).json()
        return result

    def logout (self):
        """ Permite desconectarse de SAP """
        url = self.configuracion["urls"]["logout"]
        headers = {
            "Content-Type": "application/json",
            "Cookie": 'B1SESSION=' + self.sessionId + "; ROUTER.node1"
        }
        result = requests.post(url, headers=headers, verify=False)
        self.sessionId = None
