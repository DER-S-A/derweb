// Inicio la aplicación
var app = new App();
app.init();

var login = new Seguridad();
var objRegistro = new RegistrarClientePotencial()
var expanded = false;

login.generateFormLogin();
objRegistro.llenarSelectRubros();


/**
 * La siguiente función se ejecuta al hacer click sobre
 * el select de Rubros de Ventas y permite mostrar las opciones.
 * con los checkbos.
 */
function showOptions() {
  var checkboxes = document.getElementById("checkboxes");
  if (!expanded) {
    checkboxes.style.display = "block";
    expanded = true;
  } else {
    checkboxes.style.display = "none";
    expanded = false;
  }
}

// Agrego el evento para loguearse
document.getElementById("btnIniciarSesion").addEventListener("click", () => {
    login.loginCliente();
});

document.getElementById("txtPassword").addEventListener("keypress", (e) => {
    if(e.keyCode === 13) {
        login.loginCliente();
    }
    
});

// Agrego el evento para registrar un cliente potencial.
document.getElementById("btnRegistrarse").addEventListener("click", () => {
    //console.log(validarForm());
    if(validarForm()){
        var objRegistro = new RegistrarClientePotencial()
        objRegistro.eMail = document.getElementById("txtMail").value;
        objRegistro.razonSocial = document.getElementById("txtRazSoc").value;
        objRegistro.telefono = document.getElementById("txtTelefono").value;
        objRegistro.ubicacion = document.getElementById("txtUbicacion").value;
        var rubrosSeleccionados = new Array();
        
        objCheckBoxes = getCheckBoxes();
        // Uso el for normal porque no toma el método forEach
        for (let i = 0; i < objCheckBoxes.length; i++)
            if (objCheckBoxes[i].checked)
                rubrosSeleccionados.push(objCheckBoxes[i].id);

        objRegistro.aIdsRubrosSeleccioandos = rubrosSeleccionados;
        var repuesta = objRegistro.registrarClientePotencial();
        if (repuesta["result_code"] === "OK") {
            swal("WOW!", repuesta["result_message"],"success")
            blanquearFormRegistro();
        }
    }
});

/**
 * Obtiene un array con los inputs checkboxes del combo.
 * @returns Array
 */
function getCheckBoxes() {
    var objCheckBoxesDiv = document.getElementById("checkboxes");
    var objCheckBoxes = objCheckBoxesDiv.getElementsByTagName("input");
    return objCheckBoxes;
}

/**
 * Permite blanquear el formulario de registro.
 */
function blanquearFormRegistro() {
    document.getElementById("txtMail").value = "";
    document.getElementById("txtRazSoc").value = "";
    document.getElementById("txtTelefono").value = "";
    document.getElementById("txtUbicacion").value = "";

    // Desmarco los checkboxes.
    aCheckBoxes = getCheckBoxes();
    for (let i = 0; i < aCheckBoxes.length; i++)
        if (aCheckBoxes[i].checked)
            aCheckBoxes[i].checked = false;
}

// Validar formulario register
function validarForm() { 
    
    /**  Las funciones q estan a continuacion se encuentran en UTILS/FUNCIONES.JS */
    if(validarMail() && validarRazonSocial() && validarUbicacion() && validarTel()) {
        return true;
    } else return false;
}