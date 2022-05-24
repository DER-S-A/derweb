"""
Clase: Catalogo
Descripción
    Permite extraer los datos desde SAP y actualizar la base
    de datos del catálogo en MySQL.
"""
from dataclasses import replace
from datetime import datetime
from multiprocessing.sharedctypes import Value
from packages.db.mysql_manager import MySqlManager
from packages.sap.SAPManager import SAPManager
import re

class Catalogo:

    def updatePaises(self):
        """
            Actualiza los paises
        """
        sap = SAPManager()
        db = MySqlManager()

        sap.login() # Me logueo en SAP
        paises = sap.getData("paises") # Extraigo los datos
        sap.logout() # Me desconecto de SAP

        for pais in paises["value"]:
            sql = "CALL sp_paises_upgrade('{0}', '{1}')".format(pais["PaisCode"], pais["PaisName"])
            db.execute(sql)
        db.closeDB()
            
    def updateProvincias(self):
        """
            Permite actualizar la tabla de provincias
        """
        db = MySqlManager()
        sap = SAPManager()
        sap.login()
        provincias = sap.getData("provincias")
        sap.logout()

        for pcia in provincias["value"]:
            sql = "CALL sp_provincias_upgrade ('{0}', '{1}', '{2}')".format(
                                                            pcia["EstadoCode"],
                                                            pcia["PaisCode"],
                                                            pcia["EstadoName"])
            db.execute(sql)
        db.closeDB()

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
        db = MySqlManager()
        sap = SAPManager()
        try :
            sap.login()
            pagina = 0
            rubros = sap.getData("rubros", None, pagina)
            while len(rubros["value"]) != 0 :
                for rubro in rubros["value"]:
                    sql = "CALL sp_rubros_upgrade({0}, '{1}')".format(rubro["RubroCode"], rubro["RubroName"])
                    db.execute(sql)
                rubros = sap.getData("rubros", None, pagina)
                pagina += 20

            sap.logout()
            db.closeDB() 
        except ValueError :
            print(ValueError)
            sap.logout()
            db.closeDB()
        
    def updateSubrubros(self):
        """
            Permite actualizar los subrubros.
        """
        db = MySqlManager()
        sap = SAPManager()
        try :
            sap.login()
            pagina = 0
            subrubros = sap.getData("subrubros", None, pagina)
            while len(subrubros["value"]) != 0 :
                for subrubro in subrubros["value"]:
                    sql = "CALL sp_subrubros_upgrade({0}, '{1}')".format(subrubro["SubRubroCode"], subrubro["SubRubroName"])
                    db.execute(sql)
                subrubros = sap.getData("subrubros", None, pagina)
                pagina += 20

            sap.logout()
            db.closeDB()
        except ValueError:
            print(ValueError)
            sap.logout()
            db.closeDB()
        
    def updateMarcas(self):
        """
            Permite actualizar las marcas.
        """
        db = MySqlManager()
        sap = SAPManager()
        try :
            sap.login()
            pagina = 0
            marcas = sap.getData("marcas", None, pagina)
            while len(marcas["value"]) != 0 :
                for marca in marcas["value"] :
                    sql = "CALL sp_marcas_upgrade({0}, '{1}')".format(marca["MarcaCode"], marca["MarcaName"])
                    db.execute(sql)
                marcas = sap.getData("marcas", None, pagina)
                pagina += 20

            sap.logout()
            db.closeDB()
        except ValueError:
            print(ValueError)
            sap.logout()
            db.closeDB()

        
    def updateClientes(self):
        """
            Permite actualizar los clientes en la tabla entidades.
        """
        db = MySqlManager()
        sap = SAPManager()
        try :
            sap.login()
            pagina = 0
            entidades = sap.getData("clientes", None, pagina)
            while len(entidades["value"]) != 0 :
                for entidad in entidades["value"]:
                    strCardName = str(entidad["CardName"])

                    sql = "CALL sp_entidades_upgrade(1, '{0}', '{1}', '{2}', '{3}' , '{4}' , '{5}', {6}, {7})".format(
                        entidad["CardCode"], 
                        entidad["LicTradNum"], 
                        re.sub("'", "''", strCardName),
                        "", # Dirección 
                        entidad["E_Mail"],
                        entidad["Phone1"],
                        entidad["U_ONESL_DescuentoP1"],
                        entidad["U_ONESL_DescuentoP2"])
                    db.execute(sql)
                entidades = sap.getData("clientes", None, pagina)
                pagina += 20

            sap.logout()
            db.closeDB()
        except ValueError:
            print (ValueError)
            sap.logout()
            db.closeDB()


    def updateArticulos(self):
        """
            Este método permite actualizar los artículos del catálogo.
        """
        rubro_cod = ""
        subrubro_cod = ""
        marca_cod = ""
        codigo = ""
        codigo_original = ""
        descripcion = ""
        alicuota_iva = 0.00
        existencia_stock = 0.00
        stock_minimo = 0.00

        db = MySqlManager()
        sap = SAPManager()
        try :
            sap.login()
            pagina = 0
            articulos = sap.getData("articulos", None, pagina)
            while len(articulos["value"]) != 0 :
                for articulo in articulos["value"]:
                    rubro_cod = articulo["U_ONESL_RubroCod"]
                    subrubro_cod = articulo["U_ONESL_SubRubroCod"]
                    marca_cod = articulo["U_ONESL_MarcaCod"]
                    codigo = articulo["ItemCode"]
                    codigo_original = ""
                    descripcion = re.sub("'", " ", str(articulo["ItemName"]))
                    #alicuota_iva = self.getTasaIVA(articulo["TaxCodeAR"], sap)
                    alicuota_iva = 21
                    existencia_stock = 0.00
                    stock_minimo = 0.00

                    sql = "CALL sp_articulos_upgrade ('{0}', '{1}', '{2}', '{3}', '{4}', '{5}', {6}, {7}, {8})".format(
                        rubro_cod,
                        subrubro_cod,
                        marca_cod,
                        codigo,
                        codigo_original,
                        descripcion,
                        alicuota_iva,
                        existencia_stock,
                        stock_minimo
                    )
                    db.execute(sql)
                pagina += 20
                print("Procesando página: " + str(pagina))
                articulos = sap.getData("articulos", None, pagina)

            sap.logout()
            db.closeDB()
        except ValueError:
            print(ValueError)
            sap.logout()
            db.closeDB()

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
