"""
Clase: MySqlManager
Descripción:
    Permite manejar el motor de base de datos de MySQL.
"""
from pickletools import unicodestring1
import traceback
import pymysql
from packages.lfw_json.json_manager import JSONManager
import codecs

class MySqlManager:
    dbConfig = None
    activeConnection = None

    def __init__(self):
        """
        Constructor de clase.
        Al instanaciar la clase establezco la conexión
        """
        oConfig = JSONManager()
        oConfig.file_name = "config.json"
        oConfig.get_content()
        dbConfig = oConfig.data
        self.activeConnection = pymysql.connect(
                                host=dbConfig["mysql"]["host"], 
                                user=dbConfig["mysql"]["username"], 
                                password=dbConfig["mysql"]["password"], 
                                database=dbConfig["mysql"]["database"])

    def closeDB(self):
        """
            Cierro la conexión con la base de datos.
        """
        self.activeConnection.close()

    def execute(self, xsql):
        """
        Permite ejecutar un comando de actualización MySQL
        """
        cursor = self.activeConnection.cursor()
        try:
            cursor.execute(xsql)
            # self.activeConnection.commit()
        except:
            # self.activeConnection.rollback()
            traceback.print_exc()

    def getQuery(self, xsql, xuseFetchOne = False):
        """
            Ejecuta una sentencia de selección y devuelve el conjunto de datos.
        """
        cursor = self.activeConnection.cursor()
        cursor.execute(xsql)
        if xuseFetchOne:
            result = cursor.fetchone()
        else:
            result = cursor.fetchall()
        return result

    def convertirStringAUTF8(self, str) :
        """
        Permite convertir un string a UTF-8 para enviarlo a la base de datos.
        """
        strResult = codecs.decode(str, "UTF-8")
        print(strResult)
        return strResult