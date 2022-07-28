/**
 * Este script brinda funcionalidad al control HtmlBuscador
 * 
 * Autor: Leonardo D. Zulli
 * Fecha: 23/07/2021
 */

 var idHtmlBuscador;
 var query_name_htmlBuscado;
 var keyFieldName;               // Campo Id. para retorno
 var descFieldName;              // Campo descrupción para retorno
 
/**
 * Permite realizar la búsqueda al salirse del foco del input Valor Buscado.
 * Esta función se ejecuta cuando se sale del foco de dicho input.
 * @param {string} xid              Id. de control.
 * @param {string} xfn_exactSearch  Nombre de función que contiene la búsqueda exacta.
 * @param {string} xquery           Nombre del Query para dibujar la tabla.
 * @param {string} xKeyFieldName    Nombre del campo id a devolver.
 * @param {string} xDescFieldName   Nombre del campo de descripción a devolver.
 */
function searchData(xid, xfn_exactSearch, xquery, xKeyFieldName, xDescFieldName) {
    idHtmlBuscador = xid;
    query_name_htmlBuscado = xquery;
    keyFieldName = xKeyFieldName;
    descFieldName = xDescFieldName;
    sc_exact_search(xid, xfn_exactSearch);
}

/**
 * 
 * @param {string} xid Id. de control
 * @param {string} xfn Nombre de función a ejecutar para búsqueda exacta
 */
function sc_exact_search(xid, xfn) {
    let valorBuscado = document.getElementById(xid).value;
    let param = [{'xValorBuscado' : valorBuscado}];
    if (valorBuscado !== "")
        sc3InvokeServerFn(xfn, param, get_data);
}

/**
 * Función callback para procesar el array que retorna sc3InvokeServerFn
 * @param {array} aResult 
 */
function get_data(aResult) {
    // Verifico si trajo algún registro ya que si no devuelve nada
    // los campos vienen en vacío.
    // OJO: Hay que revisar el protocolo del array para ver si hay otra forma
    // de verificar esto más segura.
    if (aResult["rs"][keyFieldName] === "") {
        sc_open_modal_buscador(query_name_htmlBuscado);
        document.getElementById(idHtmlBuscador + "desc").focus();
    }
    else {
        document.getElementById(idHtmlBuscador).value = aResult["rs"][keyFieldName];
        document.getElementById(idHtmlBuscador + "desc").value = aResult["rs"][descFieldName];
        document.getElementById(idHtmlBuscador + "desc").focus();
    }
}

/**
 * Abre el modal y dibuja la tabla para buscar el producto
 * @param {string} xquery 
 */
function sc_open_modal_buscador(xquery) {
    // Cargo los metadata para que funcione el openCatalogDiv.
    sc3LoadQueryMetadata(xquery);
    sc3LoadQueryData(xquery);

    // Dibujo la grilla
    openCatalogDiv(idHtmlBuscador, xquery);
}

/**
 * Limpia los controles del buscador
 */
function limpiar_html_buscador() {
    document.getElementById(idHtmlBuscador).value = "";
    document.getElementById(idHtmlBuscador + "desc").value = "";
}