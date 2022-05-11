"""
Clase: Catalogo
Descripción
    Permite extraer los datos desde SAP y actualizar la base
    de datos del catálogo en MySQL.
"""
from datetime import datetime
from packages.db.mysql_manager import MySqlManager
from packages.sap.SAPManager import SAPManager

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
            sql = "CALL paises_upgrade('{0}', '{1}')".format(pais["Code"], pais["Name"])
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
            sql = "CALL provincias_upgrade ('{0}', '{1}', '{2}')".format(
                                                            pcia["Code"],
                                                            pcia["Country"],
                                                            pcia["Name"])
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
            sql = "CALL formas_envios_upgrade({0}, '{1}')".format(formaEnvio["Code"], formaEnvio["Name"])
            
            db.execute(sql)
        db.closeDB()
        
    def updateRubros(self):
        """
            Permite actualizar los rubros.
        """
        db = MySqlManager()
        sap = SAPManager()
        sap.login()
        rubros = sap.getData("rubros")
        sap.logout()

        for rubro in rubros["value"]:
            sql = "CALL rubros_upgrade({0}, '{1}')".format(rubro["Code"], rubro["Name"])
            db.execute(sql)

        db.closeDB()   
        
    def updateSubrubros(self):
        """
            Permite actualizar los subrubros.
        """
        db = MySqlManager()
        sap = SAPManager()
        sap.login()
        subrubros = sap.getData("subrubros")
        sap.logout()

        for subrubro in subrubros["value"]:
            sql = "CALL subrubros_upgrade({0}, '{1}')".format(subrubro["Code"], subrubro["Name"])
            db.execute(sql)

        db.closeDB()
        
    def updateMarcas(self):
        """
            Permite actualizar las marcas.
        """
        db = MySqlManager()
        sap = SAPManager()
        sap.login()
        marcas = sap.getData("marcas")
        sap.logout()

        for marca in marcas["value"]:
            sql = "CALL marcas_upgrade({0}, '{1}')".format(marca["Code"], marca["Name"])
            db.execute(sql)

        db.closeDB()
        
    def updateEntidades(self):
        """
            Permite actualizar las entidades.
        """
        db = MySqlManager()
        sap = SAPManager()
        sap.login()
        entidades = sap.getData("clientes")
        sap.logout()

        for entidad in entidades["value"]:
            sql = "CALL entidades_upgrade(1, '{0}', '{1}', '{2}', '{3}' , '{4}' , '{5}')".format(
                entidad["CardCode"], 
                entidad["FederalTaxID"], 
                entidad["CardName"], 
                entidad["Address"], 
                entidad["EmailAddress"],
                entidad["Phone1"])
            db.execute(sql)

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
        sap.login()
        articulos = sap.getData("articulos")

        for articulo in articulos["value"]:
            rubro_cod = articulo["U_ONESL_RubroCod"]
            subrubro_cod = articulo["U_ONESL_SubRubroCod"]
            marca_cod = articulo["U_ONESL_MarcaCod"]
            codigo = articulo["ItemCode"]
            codigo_original = ""
            descripcion = articulo["ItemName"]
            alicuota_iva = self.getTasaIVA(articulo["ArTaxCode"], sap)
            existencia_stock = 0.00
            stock_minimo = 0.00

            # Las siguientes líneas de código las pongo provisora porque
            # en la base demo de SAP no tiene asignado rubros, subrubros y marcas.
            if rubro_cod == None:
                rubro_cod = 2

            if subrubro_cod == None:
                subrubro_cod = 1

            if marca_cod == None:
                marca_cod = 1

            sql = "CALL articulos_upgrade ('{0}', '{1}', '{2}', '{3}', '{4}', '{5}', {6}, {7}, {8})".format(
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
