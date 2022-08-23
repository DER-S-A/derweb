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
   let objButton1 = document.createElement('button');
   let objButton2 = document.createElement('button');

   /**
   * Le asigno clases a los NODOS creados.
   */

   objDivTrans.className = "cambiar-pass-trans";
   objDivConPass.className = "cambiar-pass";
   objInputActual.name = "cmbPs-actual";
   objInputActual.placeholder = "Contraseña actual";
   objInputNueva.name = "cmbPs-nueva";
   objInputNueva.placeholder = "Contraseña nueva"; 
   objInputConf.name = "cmbPs-conf";
   objInputConf.placeholder = "Repita Contraseña";
   objDivButtons.className = "botones";
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

}
