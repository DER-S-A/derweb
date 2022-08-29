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
   objDivButtons.className = "botones";
   //objAncla.href = "./index.php";
   objButton1.id = "confirmar-pass";
   objButton1.type = "button";
   objButton2.id = "cerrar-pass";
   objButton2.type = "button";

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

    let bandera = 0;
    objButton2.addEventListener("click", () => {
      if(bandera) {
        location.href = "./index.php";
      } else objDivTrans.style.display = "none";
      })
    
      objButton1.addEventListener("click", () => {
        objCambiarPass = new Seguridad();
        let xIdCliente = JSON.parse(sessionStorage.getItem("derweb_sesion"));
        if(xIdCliente.clave === objInputActual.value && objInputNueva.value === objInputConf.value) {
          xIdCliente = xIdCliente.id_cliente;
          let respuesta = objCambiarPass.cambiarClave(xIdCliente,objInputConf.value);
          if (respuesta["result_code"] === "OK") {
            swal("WOW!", respuesta["result_message"],"success");
            sessionStorage.removeItem("derweb_sesion");
            bandera = 1;
          }
        } else {
          if(objInputNueva.value === objInputConf.value) {
            alert("CONTRASEÑA ACTUAL INCORRECTA");
          } else alert("CONTRASEÑA NUEVA NO COINCIDE CON EL CAMPO CONFIRMAR CONTRASEÑA");
        }
    });

}
