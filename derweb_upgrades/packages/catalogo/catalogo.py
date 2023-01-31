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
        mysql = MySqlManager()
        strUrl = "http://localhost/derweb/app/services/paises/upgrade"
        headers = {
            "Content-Type": "application/json"
        } 

        sap.login() # Me logueo en SAP
        pagina = 0
        procesados = 0
        paises = sap.getData("paises", None, pagina) # Extraigo los datos
        sap.logout() # Me desconecto de SAP
        try:
            # while len(paises["value"]) != 0:
            for pais in paises["value"]:
                #     strParametro = "registro=" + json.dumps(pais)
                #     strParametro = strParametro.replace("&", "y")
                #     repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                # print(repuesta)
                # pagina += 20
                # paises = sap.getData("paises", None, pagina) # Extraigo los datos
                # print("Procesando página: " + str(pagina))
                sql = f"CALL sp_paises_upgrade('{pais['PaisCode']}','{pais['PaisName']}')"
                mysql.execute(sql)
                procesados += 1
                print (f"Pasies Procesados: {procesados}")
            mysql.closeDB()
            print (f"Pasies Finalizado")
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(pais)
            sap.logout()        
            
    def updateProvincias(self):
        """
            Permite actualizar la tabla de provincias
        """
        sap = SAPManager()
        mysql = MySqlManager()
        # strUrl = "http://localhost/derweb/app/services/provincias/upgrade"
        # headers = {
        #     "Content-Type": "application/json"
        # }
        try:
            sap.login()
            pagina = 0
            provincia = 0
            provincias = sap.getData("provincias", None, pagina)
            sap.logout()
            # while len(provincias["value"]) != 0:
            for pcia in provincias["value"]:
                    # strParametro = "registro=" + json.dumps(pcia)
                    # strParametro = strParametro.replace("&", "y")
                    # repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                # pagina += 20
                # provincias = sap.getData("provincias", None, pagina)
                # print("Procesando página: " + str(pagina))
                if (pcia['PaisCode'] == 'AR'):
                    sql = f"CALL sp_provincias_upgrade({pcia['EstadoCode']}, '{pcia['PaisCode']}','{pcia['EstadoName']}')"
                    mysql.execute(sql)
                    provincia += 1
                    # print(f"Provincias procesadas: {provincia}")
            # print(repuesta)
            print (f"Provincias Finalizado")
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(pcia)
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
        print (f"Formas de Envio Finalizado")
    def updateRubros(self):
        """
            Permite actualizar los rubros.
        """
        sap = SAPManager()
        mysql = MySqlManager()
        strUrl = "http://localhost/derweb/app/services/rubros/upgrade"
        headers = {
            "Content-Type": "application/json"
        }         
        try :
            sap.login()
            pagina = 0
            rbr = 0
            rubros = sap.getData("rubros", None, pagina)
            sap.logout()
            # while len(rubros["value"]) != 0 :
            for rubro in rubros["value"]:
                # strParametro = "registro=" + json.dumps(rubro)
                # repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                # print(repuesta)
                # print("Procesando página: " + str(pagina))
                # rubros = sap.getData("rubros", None, pagina)
                # pagina += 20
                sql = f"CALL sp_rubros_upgrade({rubro['RubroCode']},'{rubro['RubroName']}')"
                mysql.execute(sql)
                rbr += 1
                # print(f"Rubros Procesados: {rbr}")
            mysql.closeDB()
            print (f"Rubros Finalizado")
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            sap.logout()
        
    def updateSubrubros(self):
        """
            Permite actualizar los subrubros.
        """
        sap = SAPManager()
        mysql = MySqlManager()
        # strUrl = "http://localhost/derweb/app/services/subrubros/upgrade"
        # headers = {
        #     "Content-Type": "application/json"
        # }        
        try :
            sap.login()
            srb = 0
            subrubros = sap.getData("subrubros")
            sap.logout()
            # while len(subrubros["value"]) != 0 :
            for subrubro in subrubros["value"]:
                subrubro['SubRubroName'] = subrubro['SubRubroName'].replace("Ñ","N")
                subrubro['SubRubroName'] = subrubro['SubRubroName'].replace("´","")
                # strParametro = "registro=" + json.dumps(subrubro)
                # repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                # print(repuesta)
                # subrubros = sap.getData("subrubros", None, pagina)
                # pagina += 20
                # print("Procesando página: " + str(pagina))
                sql = f"CALL sp_subrubros_upgrade({subrubro['SubRubroCode']},'{subrubro['SubRubroName']}')"
                mysql.execute(sql)
                requests.get(f"http://localhost/derweb/app/services/subrubros.php/get?filter=codigo={subrubro['SubRubroCode']}")
                srb += 1
                # print (f"Subrubros Procesados {srb}")     
            mysql.closeDB()
            print (f"Subrubros Finalizado")
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            sap.logout()
        
    def updateMarcas(self):
        """
            Permite actualizar las marcas.
        """
        sap = SAPManager()
        mysql = MySqlManager()
        # strUrl = "http://localhost/derweb/app/services/marcas/upgrade"
        # headers = {
        #     "Content-Type": "application/json"
        # }   
        try :
            sap.login()
            pagina = 0
            marcas = sap.getData("marcas", None, pagina)
            sap.logout()
            # while len(marcas["value"]) != 0 :
            mark = 0
            for marca in marcas["value"] :
                # strParametro = "registro=" + json.dumps(marca)
                # repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                # print(repuesta)
                # marcas = sap.getData("marcas", None, pagina)
                # pagina += 20
                # print("Procesando página: " + str(pagina))
                sql = f"call sp_marcas_upgrade({marca['MarcaCode']}, '{marca['MarcaName']}')"
                mysql.execute(sql)
                mark += 1
                # print(f"Marcas Procesadas: {mark}")          
            mysql.closeDB()
            print (f"Marcas Finalizado")
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            sap.logout()

        
    def updateClientes(self):
        """
            Permite actualizar los clientes en la tabla entidades.
        """
        sap = SAPManager()
        mysql = MySqlManager()
        # strUrl = "http://localhost/derweb/app/services/entidades/upgradeClientes"
        # headers = {
        #     "Content-Type": "application/json"
        # } 
        try :
            sap.login()
            pagina = 0
            clientes = 0;
            start_time = time.perf_counter()
            entidades = sap.getData("clientes", None, pagina)
            sap.logout()
            # while len(entidades["value"]) != 0 :
            for entidad in entidades["value"]:
                if entidad['CardName'] is not None:
                    entidad['CardName'] = entidad['CardName'].replace("'","")
                sql = f"call sp_entidades_upgrade (1, '{entidad['CardCode']}','{entidad['TaxId']}','{entidad['CardName']}','','{entidad['E_Mail']}','{entidad['Phone1']}',{entidad['DescuentoP1']},{entidad['DescuentoP2']},{entidad['SlpCode']})"
                mysql.execute(sql)
                clientes += 1
                # print(f"Clientes Procesados: {clientes}")
                #    strParametro = "registro=" + json.dumps(entidad)
                #    strParametro = strParametro.replace("&", "y")
                #    repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                # print(repuesta)
                # entidades = sap.getData("clientes", None, pagina)
                # pagina += 20
                # print("Procesando página: " + str(pagina))
            mysql.closeDB()
            elapsed_time = time.perf_counter() - start_time
            minute = int(elapsed_time / 60)
            seconds = int(elapsed_time % 60)
            print(f"Tiempo establecido: {minute}.{seconds} ")
            print ("Clientes Finalizado")
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(entidad)
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
            arti = 0
            actualizados = 0
            nuevos = 0
            NoActualizados = 0
            start_time = time.perf_counter()
            articulos = sap.getData("articulos", None, pagina)
            sap.logout()
            #  while len(articulos["value"]) != 0 :
            for articulo in articulos["value"]:
                    Noencontrado = True;
                    # hora = datetime.fromtimestamp(articulo["UpdateTime"]).strftime("%H:%M:%S")
                    for aDER in artDER :
                        if articulo["ItemCode"] == aDER[0]:
                            Noencontrado = False
                            if  '2023-01-20' != aDER[1].strftime("%Y-%m-%d"):   
                                # strParametro = "registro=" + json.dumps(articulo)
                                # strParametro = strParametro.replace("&", "y")
                                # respuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()                        
                                # print(f"Code: {respuesta['result_code']}")
                                # print(f"Mensaje: {respuesta['result_mensaje']}") 
                                if(articulo['RubroCod'] != None and articulo['MarcaCod'] != None and articulo['SubRubroCod'] != None):    
                                    articulo['ItemName'] = articulo["ItemName"].replace("'","")
                                    sql = f"call sp_articulos_upgrade({articulo['RubroCod']},{articulo['SubRubroCod']},{articulo['MarcaCod']},'{articulo['ItemCode']}','','{articulo['ItemName']}',21,0,0,1)"               
                                    mysql.execute(sql)
                                    actualizados+=1
                            else : NoActualizados+=1   
                    if (Noencontrado):
                        # strParametro = "registro=" + json.dumps(articulo)
                        # strParametro = strParametro.replace("&", "y")
                        # respuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
                        if(articulo['RubroCod'] != '' and articulo['MarcaCod'] != '' and articulo['SubRubroCod'] != ''): 
                            articulo['ItemName'] = articulo["ItemName"].replace("'","")
                            sql = f"call sp_articulos_upgrade({articulo['RubroCod']},{articulo['SubRubroCod']},{articulo['MarcaCod']},'{articulo['ItemCode']}','','{articulo['ItemName']}',21,0,0,1)"  
                            if articulo['ItemCode'] == '0415/22-CGRI':
                                print (articulo['RubroCod'])
                                print(sql)
                                time.sleep(20)             
                            mysql.execute(sql)
                            # print(f"Code: {respuesta['result_code']} ")
                            # print(f"Mensaje: {respuesta['result_mensaje']}")
                            nuevos+= 1
                    arti+= 1
                    print (f"Articulos Procesados: {arti}")
            # pagina += 10000
                #print(f"Procesando página: {str(pagina)}")
                #articulos = sap.getData("articulos", None, pagina)
                    
            mysql.closeDB()
            print ("Articulos Finalizado ")
            elapsed_time = time.perf_counter() - start_time
            minute = int(elapsed_time / 60)
            seconds = int(elapsed_time % 60)
            print(f"Tiempo establecido: {minute}.{seconds} ")
            print(f"Actualizados: {str(actualizados)}")
            print(f"NoActualizados: {str(NoActualizados)}")
            print(f"Nuevos: {str(nuevos)}")
            
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(articulo)
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
        mysql = MySqlManager()
        strUrl = "http://localhost/derweb/app/services/sucursales/upgradeSucursales"
        headers = {
            "Content-Type": "application/json"
        } 
        try :
            sap.login()
            sucursales = sap.getData("sucursales")
            sap.logout()
            # while len(sucursales["value"]) != 0 :
            for sucursales in sucursales["value"]:
                if (sucursales['SucursalCode'].startswith('C')):  
                    if(sucursales['SucursalName'] != 'Fiscal'):
            #          strParametro = "registro=" + json.dumps(sucursales)
            #          strParametro = strParametro.replace("&", "y")
            #          repuesta = requests.put(url=strUrl + "?" + strParametro, headers=headers).json()
            #    sucursales = sap.getData("sucursales", None, pagina)
            #    pagina += 20
                        # sucursales['Calle'] = sucursales['Calle'].replace("'","")
                        sql = f"call sp_Sucursales_upgrade('{sucursales['SucursalCode']}','{sucursales['SucursalName']}','{sucursales['CardCode']}','{sucursales['TipoCode']}','{sucursales['Calle']}','{sucursales['Ciudad']}',{sucursales['EstadoCode']},{sucursales['ZipCode']},{sucursales['Gln'] if sucursales['Gln'] != None else 0},{sucursales['CardCodeDER'] if sucursales['CardCodeDER'] != None else 0},'{sucursales['CreateDate']}')"
                        mysql.execute(sql)
                        time.sleep(0.01)
                        print(sql)
                # print("Procesando página: " + str(pagina))

            print("Proceso Finalizado Correctamente")
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(sucursales)
            sap.logout()
            
    def updateTeleVentas(self):
        """
            Este método permite actualizar los televentas del catalogo
        """
        
        sap = SAPManager()
        mysql = MySqlManager()
        
        try:
            procesados = 0
            sap.login()
            teleVentas = sap.getData("televentas")
            sap.logout()
            for tv in teleVentas["value"]:
                if tv['SlpCode'] > 0:
                    sql = f"CALL sp_televentas_upgrade(3,{tv['SlpCode']}, '{tv['SlpName']}','{tv['Telefono']}','{tv['Direccion']}','{tv['Email']}','{tv['FiscalID']}')"
                    mysql.execute(sql)
                    procesados += 1
                    print(f"Televentas Procesados {procesados}")
        except BaseException as err:
            print(f"Unexpected {err=}, {type(err)=}")
            print(tv)
            sap.logout()