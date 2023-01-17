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
from requests.packages import urllib3
import time 

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
                print("Procesando página: " + str(pagina))
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
                    if (pcia['PaisCode'] == 'AR'):
                        strParametro = strParametro.replace("&", "y")
                        repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                pagina += 20
                provincias = sap.getData("provincias", None, pagina)
                print("Procesando página: " + str(pagina))
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
                print("Procesando página: " + str(pagina))
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
                print("Procesando página: " + str(pagina))
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
                print("Procesando página: " + str(pagina))
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
               print("Procesando página: " + str(pagina))
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
        mysql = MySqlManager()
        # strUrl = "http://localhost/derweb/app/services/articulos/upgrade"
        # headers = {
        #     "Content-Type": "application/json"
        # } 
        try :
            artDER = mysql.getQuery("SELECT CODIGO, FECHA_MODIFICADO FROM ARTICULOS;")
            sap.login()
            pagina = 0
            queondaperro = 0
            actualizados = 0
            nuevos = 0
            NoActualizados = 0
            start_time = time.perf_counter()
            articulos = sap.getData("articulos", None, pagina)
          #  while len(articulos["value"]) != 0 :
            for articulo in articulos["value"]:
                    Noencontrado = True;
                    Error = False;
                    # hora = datetime.fromtimestamp(articulo["UpdateTime"]).strftime("%H:%M:%S")
                    for aDER in artDER :
                        if articulo["ItemCode"] == aDER[0]:
                            Noencontrado = False
                            if  '2023-01-16' != aDER[1].strftime("%Y-%m-%d"):   
                                # strParametro = "registro=" + json.dumps(articulo)
                                # strParametro = strParametro.replace("&", "y")
                                # respuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()                        
                                # print(f"Code: {respuesta['result_code']}")
                                # print(f"Mensaje: {respuesta['result_mensaje']}")    
                                if type(articulo['RubroCod']) != str:
                                    Error = True    
                                if type(articulo['SubRubroCod']) != str:
                                    Error = True
                                if type(articulo['MarcaCod']) != str :
                                    Error = True
                                if type(articulo['ItemCode']) != str: 
                                    Error = True  
                                if type(articulo["ItemName"]) != str:
                                    Error = True  
                                if (Error != True):    
                                    articulo['ItemName'] = articulo["ItemName"].replace("'","")
                                    sql = f"call sp_articulos_upgrade({articulo['RubroCod']},{articulo['SubRubroCod']},{articulo['MarcaCod']},'{articulo['ItemCode']}','','{articulo['ItemName']}',21,0,0,1)"               
                                    mysql.execute(sql)
                                    actualizados+=1
                                else : print('Error')
                            else : NoActualizados+=1   
                    if (Noencontrado):
                        # strParametro = "registro=" + json.dumps(articulo)
                        # strParametro = strParametro.replace("&", "y")
                        # respuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json() 
                        if type(articulo['RubroCod']) != str:
                            Error = True 
                        if type(articulo['SubRubroCod']) != str:
                            Error = True
                        if type(articulo['MarcaCod']) != str :
                            Error = True
                        if type(articulo['ItemCode']) != str: 
                            Error = True  
                        if type(articulo["ItemName"]) != str:
                            Error = True  
                        if (Error != True):    
                            articulo['ItemName'] = articulo["ItemName"].replace("'","")
                            sql = f"call sp_articulos_upgrade({articulo['RubroCod']},{articulo['SubRubroCod']},{articulo['MarcaCod']},'{articulo['ItemCode']}','','{articulo['ItemName']}',21,0,0,1)"               
                            mysql.execute(sql)
                        # print(f"Code: {respuesta['result_code']} ")
                        # print(f"Mensaje: {respuesta['result_mensaje']}")
                        nuevos+= 1
                    queondaperro+= 1
                    print (f"QUE ONDA PERRRRRRRRRRO: {queondaperro}")
            pagina += 10000
                #print(f"Procesando página: {str(pagina)}")
                #articulos = sap.getData("articulos", None, pagina)
            print ("Proceso Finalizado ")
            elapsed_time = time.perf_counter() - start_time
            minute = int(elapsed_time / 60)
            seconds = int(elapsed_time % 60)
            print(f"Tiempo establecido: {minute}.{seconds} ")
            print(f"Actualizados: {str(actualizados)}")
            print(f"NoActualizados: {str(NoActualizados)}")
            print(f"Nuevos: {str(nuevos)}")
            sap.logout()
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            # print(strParametro)
            sap.logout()
            sys.exit(1);

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

    def updateSucursales(self):
        """
            Este método permite actualizar las Sucursales del catalogo
        """
        sap = SAPManager()
        strUrl = "http://localhost/derweb/app/services/sucursales/upgradeSucursales"
        headers = {
            "Content-Type": "application/json"
        } 
        try :
            sap.login()
            pagina = 0
            sucursales = sap.getData("sucursales", None, pagina)
            while len(sucursales["value"]) != 0 :
               for sucursales in sucursales["value"]:
                if (sucursales['SucursalCode'].startswith('C')):  
                    if(sucursales['SucursalName'] != 'Fiscal'):
                        strParametro = "registro=" + json.dumps(sucursales)
                        strParametro = strParametro.replace("&", "y")
                        repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
               sucursales = sap.getData("sucursales", None, pagina)
               pagina += 20
               print("Procesando página: " + str(pagina))
            sap.logout()
            print("Proceso Finalizado Correctamente")
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(strParametro)
            sap.logout()
            