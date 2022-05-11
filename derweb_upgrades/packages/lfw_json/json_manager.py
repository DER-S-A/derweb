import json

class JSONManager :
    """
    Esta clase permite obtener el contenido de un archivo JSON
    """
    file_name = ""
    data = ""

    def get_content (self) :
        """
        Obtiene el contenido de un archivo JSON
        """
        with open(self.file_name) as file:
            self.data = json.load(file)