function cambiarContraseña() {

  /**
  * Creo los NODOS creados.
  */
  
  let objDivTrans = document.createElement("div")
  let objDivConPass = document.createElement('div');
  let objH2Pass = document.createElement('h2');
  let objhrPass = document.createElement('hr');
  let objFormPass = document.createElement('form');
  let objInputActual = document.createElement('input');
  let objInputNueva = document.createElement('input');
  let objInputConf = document.createElement('input');
  let objDivButtons = document.createElement('div');
  //let objAncla = document.createElement('a');
  let objButton1 = document.createElement('button');
  let objButton2 = document.createElement('button');

  /**
  * Le asigno clases a los NODOS creados.
  */

  objDivTrans.className = "cambiar-pass-trans";
  objDivConPass.className = "cambiar-pass";
  objInputActual.name = "cmbPs-actual";
  objInputActual.placeholder = "Contraseña actual";
  objInputActual.type = "password";
  objInputNueva.name = "cmbPs-nueva";
  objInputNueva.placeholder = "Contraseña nueva";
  objInputNueva.type = "password"; 
  objInputConf.name = "cmbPs-conf";
  objInputConf.placeholder = "Repita Contraseña";
  objInputConf.type = "password"; 
  objInputConf.minLength = "4";
  objDivButtons.className = "botones";
  objButton1.id = "confirmar-pass";
  objButton1.type = "button";
  objButton1.className = "btn";
  objButton2.id = "cerrar-pass";
  objButton2.type = "button";
  objButton2.className = "btn";

  /**
  * Hago los appendChild.
  */
  objFormPass.appendChild(objInputActual);
  objFormPass.appendChild(objInputNueva);
  objFormPass.appendChild(objInputConf);
  objFormPass.appendChild(objDivButtons).appendChild(objButton1);
  objDivButtons.appendChild(objButton2);
  objDivConPass.appendChild(objH2Pass);
  objDivConPass.appendChild(objhrPass);
  objDivConPass.appendChild(objFormPass);
  document.getElementById("app-container").appendChild(objDivTrans).appendChild(objDivConPass);

   
   

  /**
  * Genero el contenido de los nodos.
  */
   
  objH2Pass.innerHTML = "CAMBIAR CONTRASEÑA";
  objButton1.innerHTML = "Confirmar";
  objButton2.innerHTML = "Cerrar";

  /**
  * Evento para cerrar form.
  */

  let bandera = 0;
  objButton2.addEventListener("click", () => {
    if(bandera) {
      location.href = "./index.php";
    } else objDivTrans.style.display = "none";
  });

  /**
  * Evento para confirmar cambio de password.
  */
    
  objButton1.addEventListener("click", () => {
    objCambiarPass = new Seguridad();
    let xIdCliente = JSON.parse(sessionStorage.getItem("derweb_sesion"));
    if(objInputNueva.value === objInputConf.value && objInputConf.value.length > 3) {
      xIdCliente = xIdCliente.id_cliente;
      let respuesta = objCambiarPass.cambiarClave(xIdCliente,objInputConf.value,objInputActual.value);
      if (respuesta["result_code"] === "OK") {
        swal("WOW!", respuesta["result_message"],"success");
        //sessionStorage.removeItem("derweb_sesion");
        setTimeout(cerrarSession, 5000);
        bandera = 1;
      } else {
          swal("Oops", respuesta["result_message"],"error");
      }
    } else if(objInputNueva.value === objInputConf.value) {
        swal("Oops", "Cantidad minima de caracteres 4.","error");
    } else swal("Oops", "Contraseña nueva no coincide con la confirmacion.", "error");
  });
}
