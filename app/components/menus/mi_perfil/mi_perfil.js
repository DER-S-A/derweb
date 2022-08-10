class MiPerfil extends ComponentManager {
    constructor (xidAppContainer) {
        super();
        this.idAppContainer = xidAppContainer;
    }

    /**
     * Permite generar el componente.
     */
     generateComponent() {
        this.clearContainer(this.idAppContainer);

        /**
         * Creo los nodos.
         */

        let objContenedor = document.getElementById(this.idAppContainer);
        let objH1 = document.createElement("h1");
        let objDiv2 = document.createElement("div");
        let objImg = new Image();
        objImg.src = "assets/imagenes/foto-perfil.jpg";
        let objDiv3 = document.createElement("div");
        let objH2 = document.createElement("h2");
        let objUl = document.createElement("ul");
        ///let objLi7 = document.createElement("li");
        let objButton = document.createElement("button");

        /**
         * Le asigno clases a los NODOS creados.
         */

         objH1.className = "titulo-miPerfil";
         objDiv2.className = "subContenedor-Perfil";
         objButton.name = "reset-pass";
         objButton.type = "button";

         /**
         * Genero el contenido de los nodos.
         */

          objH1.innerHTML = "MI PERFIL";
          objH2.innerHTML = "CASA JORGE SRL";
          objButton.innerHTML = "CAMBIAR CONTRASEÑA";
          let valorli = ["NÚMERO DE CLIENTE:", "DIRECCIÓN:", "TELÉFONO:", "MAIL: ", "VENDEDOR: ",
           "BENEFICIO VIGENTE: ", "ENTREGA PREDETERMINADA: ",
          ];

          console.log(valorli);

         /**
         * Hago los appendChild.
         */

          objContenedor.appendChild(objH1);
          objContenedor.appendChild(objDiv2).appendChild(objImg);
          objDiv2.appendChild(objDiv3).appendChild(objH2);
          objDiv3.appendChild(objUl);
          objDiv3.appendChild(objButton);
          for(let i=0;i<7;i++){
            let objLi7 = document.createElement("li");
            objLi7.style.listStyle = "none";
            objUl.appendChild(objLi7);
            objLi7.innerHTML = valorli[i];
          }

     }

}