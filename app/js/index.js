// Inicio la aplicación
var app = new App();
app.init();

var login = new Seguridad();
var expanded = false;

login.generateFormLogin();
login.llenarSelectRubros();

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