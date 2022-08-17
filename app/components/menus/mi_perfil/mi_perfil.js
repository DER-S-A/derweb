class MiPerfil extends ComponentManager {
    constructor (xidAppContainer) {
        super();
        this.idAppContainer = xidAppContainer;
        this.classMiPerfil = "container-miPerfil"
    }

      /**
     * Permite generar el componente.
     */
     generateComponent() {
        this.clearContainer(this.idAppContainer);

        /**
         *  Genero la logica para traer los datos de la tabla entidades y sucursales
         */
         
        // Aca saco el id desde la sesion storage
        let xIdCliente = JSON.parse(sessionStorage.getItem("derweb_sesion"));
        xIdCliente = xIdCliente.id_cliente;
         
        // Aca uso el fetch para traerme toda la api

        let xurlapi = "http://localhost/derweb/app/services/entidades.php/get";

        fetch(xurlapi).then(response => response.json()).then(data=>{

          // uso este for para poder cortar la interacion ya q con foreach no se puede usar break
        
          for(let i=0;i<data.length;i++) {
            if(xIdCliente == data[i].id) {
              xIdCliente = data[i];
              break;
            }
          }

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
          
          objContenedor.className = this.classMiPerfil;
          objH1.className = "titulo-miPerfil";
          objDiv2.className = "fotoLocal-miPerfil";
          objDiv3.className = "info-miPerfil";
          objButton.name = "reset-pass";
          objButton.type = "button";

          /**
          * Genero el contenido de los nodos.
          */
           objH1.innerHTML = "MI PERFIL";
           objH2.innerHTML = xIdCliente.nombre;
           objButton.innerHTML = "CAMBIAR CONTRASEÑA";
           let valorli = ["NÚMERO DE CLIENTE: ", "DIRECCIÓN: ", "TELÉFONO: ", "MAIL: ", "VENDEDOR: ",
           "BENEFICIO VIGENTE: ", "ENTREGA PREDETERMINADA: "
           ];
           //let valorspan = ["10028","MORENO 1240","11 4764-0463","CASAJORGEREP@GMAIL.COM","AGUSTIN","50%","RETIRA VIAJANTE"];
           let valorspan = [xIdCliente.cliente_cardcode,"MORENO 1240",xIdCliente.telefono,xIdCliente.email,"AGUSTIN",xIdCliente.descuento_1,"RETIRA VIAJANTE"];

          /**
          * Hago los appendChild.
          */
          objContenedor.appendChild(objH1);
          objContenedor.appendChild(objDiv2).appendChild(objImg);
          objDiv3.appendChild(objH2);
          objContenedor.appendChild(objDiv3);
          objDiv3.appendChild(objUl);
          objDiv3.appendChild(objButton);
          for(let i=0;i<7;i++){
            let objLi7 = document.createElement("li");
            let objspan = document.createElement("span");
            objLi7.style.listStyle = "none";
            objUl.appendChild(objLi7).appendChild(objspan);
            objLi7.innerHTML = valorli[i] + "<span>" + valorspan[i] + "</span>";
            objspan.innerHTML = valorspan[i];
          }
          console.log(xIdCliente);
      });
    }

}