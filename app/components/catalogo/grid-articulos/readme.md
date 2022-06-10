# Control Grid para catálogo.
Este control sirve para mostrar los artículos tras realizar una búsqueda en el
catálogo digital.

## Problemas que se resolvieron.
Debido a que DER maneja un volumen grande de artículos, se decidió paginar el conjunto de resultados
en lotes de 40 registros y hacer que las páginas se levanten asincrónicamente de tal forma que la grilla
se vaya cargando página por página devolviendo el control del navegador al usuario generando un muestreo
de información más rápido.

El problema que hubo que resolver es controlar la asoncronicidad de la función que permite realizar la
recuperación de los datos mediante la instrucción fetch ya que al ser asincrónico provocaba que se genere
una sola página en sessionStorage y el resto no se cargara.

Para solucionar este problema se aplicó la recursividad, un llamado recursivo al método que trae la información
y que el llamado se corte cuando encuentre la primera página que no devuelva ningún dato en el array values.
El código que resuelve este problema es el siguiente:

    /**
     * Permite recupera los datos a mostrar en la grilla.
     */
    _getData() {
        var pagina = 0;
        this._getArticulosByRubroAndSubrubro(pagina, "derweb_articulos")
    }

    /**
     * Permite recuperar página por página el resultado de artículos en forma
     * asincrónica.
     * @param {int} xpagina Número de página a recuperar
     * @param {string} xclaveSessionStorage Clave de almacenamiento para sessionStorage.
     */
    _getArticulosByRubroAndSubrubro(xpagina, xclaveSessionStorage) {
        var url = this.aParametros["api_url"];
        var url_con_parametros = url + "?sesion=" + sessionStorage.getItem("derweb_sesion")
            + "&parametros=" + JSON.stringify(this.aParametros) + "&pagina=" + xpagina;

        fetch (url_con_parametros)
            .then(xresponse => xresponse.json())
            .then(xdata  => {
                if (xdata["values"].length !== 0) {
                    sessionStorage.setItem(xclaveSessionStorage + "_" + xpagina, JSON.stringify(xdata));
                    xpagina += 40;
                    this._getArticulosByRubroAndSubrubro(xpagina, xclaveSessionStorage);         
                }
            });
    }

Teniendo en cuenta esto, la ejecución se incicia en el método _getData desde la página 0 y luego _getArticulosByRubroAndSubrubro
se va invocando recursivamente hasta llegar al final del conjunto de resultados a mostrar en pantalla.