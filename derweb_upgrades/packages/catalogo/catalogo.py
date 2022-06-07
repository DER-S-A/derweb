"""
Clase: Catalogo
Descripción
    Permite extraer los datos desde SAP y actualizar la base
    de datos del catálogo en MySQL.
"""
from dataclasses import replace
from datetime import datetime
from multiprocessing.sharedctypes import Value
from time import strptime
from packages.db.mysql_manager import MySqlManager
from packages.sap.SAPManager import SAPManager
import re
import requests
import json
import sys

class Catalogo:

    def updatePaises(self):
        """
            Actualiza los paises
        """
        sap = SAPManager()
        strUrl = "http://localhost/derweb/app/services/paises/upgrade"
        headers = {
            "Content-Type": "application/json"
        } 

        sap.login() # Me logueo en SAP
        pagina = 0
        paises = sap.getData("paises", None, pagina) # Extraigo los datos
        try:
            while len(paises["value"]) != 0:
                for pais in paises["value"]:
                    strParametro = "registro=" + json.dumps(pais)
                    strParametro = strParametro.replace("&", "y")
                    repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                print(repuesta)
                pagina += 20
                paises = sap.getData("paises", None, pagina) # Extraigo los datos

            sap.logout() # Me desconecto de SAP
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(strParametro)
            sap.logout()        
            
    def updateProvincias(self):
        """
            Permite actualizar la tabla de provincias
        """
        sap = SAPManager()
        strUrl = "http://localhost/derweb/app/services/provincias/upgrade"
        headers = {
            "Content-Type": "application/json"
        }
        try:
            sap.login()
            pagina = 0
            provincias = sap.getData("provincias", None, pagina)
            while len(provincias["value"]) != 0:
                for pcia in provincias["value"]:
                    strParametro = "registro=" + json.dumps(pcia)
                    strParametro = strParametro.replace("&", "y")
                    repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                pagina += 20
                provincias = sap.getData("provincias", None, pagina)

            print(repuesta)
            sap.logout()
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(strParametro)
            sap.logout()

    def updateFormasEnvios(self):
        """
            Permite actualizar las formas de envío.
        """
        db = MySqlManager()
        sap = SAPManager()
        sap.login()
        formas_envios = sap.getData("formas_envios")
        sap.logout()

        for formaEnvio in formas_envios["value"]:
            sql = "CALL sp_formas_envios_upgrade({0}, '{1}')".format(formaEnvio["Code"], formaEnvio["Name"])
            
            db.execute(sql)
        db.closeDB()
        
    def updateRubros(self):
        """
            Permite actualizar los rubros.
        """
        sap = SAPManager()
        strUrl = "http://localhost/derweb/app/services/rubros/upgrade"
        headers = {
            "Content-Type": "application/json"
        }         
        try :
            sap.login()
            pagina = 0
            rubros = sap.getData("rubros", None, pagina)
            while len(rubros["value"]) != 0 :
                for rubro in rubros["value"]:
                    strParametro = "registro=" + json.dumps(rubro)
                    repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()

                print(repuesta)

                rubros = sap.getData("rubros", None, pagina)
                pagina += 20

            sap.logout()
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            sap.logout()
        
    def updateSubrubros(self):
        """
            Permite actualizar los subrubros.
        """
        sap = SAPManager()
        strUrl = "http://localhost/derweb/app/services/subrubros/upgrade"
        headers = {
            "Content-Type": "application/json"
        }        
        try :
            sap.login()
            pagina = 0
            subrubros = sap.getData("subrubros", None, pagina)
            while len(subrubros["value"]) != 0 :
                for subrubro in subrubros["value"]:
                    strParametro = "registro=" + json.dumps(subrubro)
                    repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()

                print(repuesta)

                subrubros = sap.getData("subrubros", None, pagina)
                pagina += 20

            sap.logout()
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            sap.logout()
        
    def updateMarcas(self):
        """
            Permite actualizar las marcas.
        """
        sap = SAPManager()
        strUrl = "http://localhost/derweb/app/services/marcas/upgrade"
        headers = {
            "Content-Type": "application/json"
        }   
        try :
            sap.login()
            pagina = 0
            marcas = sap.getData("marcas", None, pagina)
            while len(marcas["value"]) != 0 :
                for marca in marcas["value"] :
                    strParametro = "registro=" + json.dumps(marca)
                    repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                
                print(repuesta)
                marcas = sap.getData("marcas", None, pagina)
                pagina += 20

            sap.logout()
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            sap.logout()

        
    def updateClientes(self):
        """
            Permite actualizar los clientes en la tabla entidades.
        """
        sap = SAPManager()
        strUrl = "http://localhost/derweb/app/services/entidades/upgradeClientes"
        headers = {
            "Content-Type": "application/json"
        } 
        try :
            sap.login()
            pagina = 0
            entidades = sap.getData("clientes", None, pagina)
            while len(entidades["value"]) != 0 :
                for entidad in entidades["value"]:
                    strParametro = "registro=" + json.dumps(entidad)
                    strParametro = strParametro.replace("&", "y")
                    repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                
                print(repuesta)
                entidades = sap.getData("clientes", None, pagina)
                pagina += 20

            sap.logout()
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(strParametro)
            sap.logout()


    def updateArticulos(self):
        """
            Este método permite actualizar los artículos del catálogo.
        """
        sap = SAPManager()
        strUrl = "http://localhost/derweb/app/services/articulos/upgrade"
        headers = {
            "Content-Type": "application/json"
        } 
        try :
            sap.login()
            pagina = 0
            articulos = sap.getData("articulos", None, pagina)
            while len(articulos["value"]) != 0 :
                for articulo in articulos["value"]:
                    strParametro = "registro=" + json.dumps(articulo)
                    strParametro = strParametro.replace("&", "y")
                    repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()

                print(repuesta)
                pagina += 20
                print("Procesando página: " + str(pagina))
                articulos = sap.getData("articulos", None, pagina)

            sap.logout()
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(strParametro)
            sap.logout()

    def getTasaIVA(self, xcode, xsapObject):
        """
            Obtiene la tasa de IVA a asignar en el artículo
        """
        iva = 0

        # Si xcode viene en NULL entonces pongo como valor predeterminado
        # el 21%.
        if xcode == None:
            xcode = "IVA_21"

        tasas_iva = xsapObject.getData("tasas_IVA", "Code eq '" + xcode + "'")
        for tasa_iva in tasas_iva["value"]:
            iva = tasa_iva["Rate"]
        return iva
