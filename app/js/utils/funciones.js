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

function validarMail() { 
    let exp = new RegExp('^(.+)@(\\S+)$');
    if (document.formRegister.txtMail.value == "") {
        swal("Oops!", "Campo de email obligatorio", "error");
        document.formRegister.txtMail.focus();
        return false;
    } else if (!exp.test(document.formRegister.txtMail.value)) {
        swal("Oops!", "Email invalido", "error");
        document.formRegister.txtMail.focus();
        return false;
    } else if (document.formRegister.txtMail.value.length > 99) {
        swal("Oops!", "Maximo de caracteres superado en campo Email", "error");
        document.formRegister.txtMail.focus();
        return false;
    }
    return true;
}

function validarRazonSocial() {
    if (document.formRegister.txtRazSoc.value == "" ) {
        swal("Oops!", "Campo Razon Social Obligatorio", "error");
        document.formRegister.txtRazSoc.focus();
        return false;
    } else if (document.formRegister.txtRazSoc.value.length > 99) {
        swal("Oops!", "Maximo de caracteres superado en campo Razon Soc", "error");
        document.formRegister.txtRazSoc.focus();
        return false;
    }
    return true;
}

function validarUbicacion() {
    if (document.formRegister.txtUbicacion.value == "" ) {
        swal("Oops!", "Campo Ubicacion Obligatorio", "error");
        document.formRegister.txtUbicacion.focus();
        return false;
    } else if (document.formRegister.txtUbicacion.value.length > 99) {
        swal("Oops!", "Maximo de caracteres superado en campo Ubicacion", "error");
        document.formRegister.txtUbicacion.focus();
        return false;
    }
    return true;
}

function validarTel() {
    if (document.formRegister.txtTelefono.value == "") {
        swal("Oops!", "Campo Teléfono Obligatorio", "error");
        document.formRegister.txtTelefono.focus();
        return false;
    } else if (document.formRegister.txtTelefono.value.length > 20) {
        swal("Oops!", "Maximo de caracteres superado en campo Teléfono", "error");
        document.formRegister.txtTelefono.focus();
        return false;
    }
    return true;
}

/*
* @param {cerrar menu autogestion cliente}
*/
function cerrarAutogestion() {
    let objmenuPerfil = document.querySelector(".menu-perfil");
    objmenuPerfil.style.display = "none";
}


function getApifetch (xurl, xcallback) {
    return fetch(xurl)
    .then(response => response.json()).then(data => {
        res = data;
        xcallback(res);
    })
}

