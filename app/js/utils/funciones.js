/**
 * Contiene funciones que se van a requerir en todo el proyecto
 */

/**
 * Permite leer un template html.
 * @param {string} xFileName Nombre del archivo html a leer
 * @param {function} xcallback Función callback
 * @param {boolean} xasync True indica asincrónico, false ejecución sincrónica.
 */
function getTemplate(xFileName, xcallback, xasync = false) {
    let xmlRequest = new XMLHttpRequest();
    xmlRequest.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            xcallback(this.responseText);
        }
    };
    xmlRequest.open("GET", xFileName, xasync);
    xmlRequest.send();      
}

/**
 * Permite obtener datos a partir de un endpoint
 * @param {string} xurl 
 * @param {function} xcallback 
 * @param {boolean} xasync 
 */
function getAPI(xurl, xcallback, xasync = false) {
    let xmlRequest = new XMLHttpRequest();
    xmlRequest.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            xcallback(this.responseText);
        }
    };
    xmlRequest.open("GET", xurl, xasync);
    xmlRequest.send();
}

/**
 * Permite agregar una opción a un componente <select></select>
 * @param {DOM} xobjSelectComponent Objeto select que se obtiene mediante getElementById().
 * @param {mixed} xvalue Valor que identifica una opción.
 * @param {string} xtext Texto a mostrar en el select.
 */
function addSelectOption(xobjSelectComponent, xvalue, xtext) {
    let objOpcion = document.createElement("option");
    objOpcion.value = xvalue;
    objOpcion.innerText = xtext;
    xobjSelectComponent.appendChild(objOpcion);
}