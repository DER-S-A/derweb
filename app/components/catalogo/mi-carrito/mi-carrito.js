/**
 * Esta clase permite desplegar el modal donde se va a mostrar la
 * grilla con el pedido que se encuentra abierto actualmente.
 */

class MiCarritoModalComponent extends ComponentManager {
    constructor (xidModal) {
        super();
        this.__idModal = xidModal;
        this.__objDivFondo = null;
        this.__objDivModal = null;
        this.__idBotonCerrar = "btn-" + this.__idModal;
        this.__objApp = new App();
        this.__callbackFinalizarPedidoButton = null;
        this.__functionNameVaciarCarrito = null;
    }

    /**
     * Establece la función que se ejecutará al hacer clic en finalizar pedido.
     * @param {function} xcallback 
     */
    setCallbackFinalizarPedidoButton(xcallback) {
        this.__callbackFinalizarPedidoButton = xcallback;
    }

    /**
     * Establece la función que se ejecuta al hacer clic en vaciar carrito.
     * @param {string} xvalue
     */
    setFunctionNameVaciarCarrito(xvalue) {
        this.__functionNameVaciarCarrito = xvalue;
    }

    /**
     * Permite generar el componente.
     */
    generateComponent() {
        var objContainer = document.createElement("div");
        objContainer.id = this.__idModal + "-contenido";

        this.__generarDivFondo();
        this.__generarDivModal();
        this.__generarHeader();
        this.__objDivModal.appendChild(objContainer); 
        this.generarAnchorVaciarCarrito();
        this.generarSubtotal();      
        //this.__generarFooter();  //ACA USAS LA FORMA ESTATICA
        let objConfirmarPedido = new ConfirmacionPedido("app-entidades-getSucursalesByEntidad");
        objConfirmarPedido.setIdModal(this.__idModal);
        objConfirmarPedido.setDivModal(this.__objDivModal);
        objConfirmarPedido.generarFooterPedido();
        this.__objDivFondo.appendChild(this.__objDivModal);
        document.body.appendChild(this.__objDivFondo);

        // Agrego eventos.
        document.getElementById(this.__idBotonCerrar).addEventListener("click", () => {
            document.getElementById(this.__idModal + "_fondo").style.display = "none";
        });
    }

    /**
     * Permite crear el div de fondo para que no se pueda
     * hacer click en otra parte del sistema hasta no cerrar el modal.
     */
    __generarDivFondo() {
        this.__objDivFondo = document.createElement("div");
        this.__objDivFondo.id = this.__idModal + "_fondo";
        this.__objDivFondo.classList.add("modal-div-fondo");
    }

    /**
     * Permite crear el div modal en donde se va a mostrar la grilla
     * de artículos.
     */
    __generarDivModal() {
        this.__objDivModal = document.createElement("div");
        this.__objDivModal.id = this.__idModal;
        this.__objDivModal.classList.add("modal","mc-modal");
    }

    /**
     * Permite generar el header del modal.
     */
    __generarHeader() {
        var objHeaderContainer = document.createElement("div");
        var objTituloContainer = document.createElement("div");
        var objBotonCerrarContainer = document.createElement("div");
        var objTitulo = document.createElement("h5");
        var objImgCarrito = new Image(50,50);
        objImgCarrito.src = "assets/imagenes/icons/carrito.png";
        var objBotonCerrar = document.createElement("button");

        // Contenedor del header
        objHeaderContainer.id = this.__idModal + "-header";
        objHeaderContainer.classList.add("modal-header");

        // Título
        objTituloContainer.id = this.__idModal + "-header-titulo-container"
        objTituloContainer.classList.add("modal-titulo-container")
        objTitulo.innerHTML = "Mi Carrito";
        objTituloContainer.appendChild(objImgCarrito);
        objTituloContainer.appendChild(objTitulo);

        // Contenedor del botón de cerrar.
        objBotonCerrarContainer.id = this.__idModal + "-boton-cerrar";
        objBotonCerrarContainer.classList.add("boton-cerrar-container");
        
        // Boton para cerrar el carrito.
        objBotonCerrar.id = this.__idBotonCerrar;
        objBotonCerrar.classList.add("modal-boton-cerrar");
        objBotonCerrar.innerHTML = "<i class=\"fa-solid fa-xmark\"></i>";
        objBotonCerrar.title = "Cerrar mi carrito";
        objBotonCerrarContainer.appendChild(objBotonCerrar);

        // Vinculación de divs
        objHeaderContainer.appendChild(objTituloContainer);
        objHeaderContainer.appendChild(objBotonCerrarContainer);
        this.__objDivModal.appendChild(objHeaderContainer);
    }

    /**
     * Permite abrir el modal de mi carrito
     */
    open() {
        document.getElementById(this.__idModal + "_fondo").style.display = "block";       
    }

    /**
     * Permite cerrar le modal mi carrito.
     */
    close() {
        document.getElementById(this.__idModal + "_fondo").style.display = "none";
    }

    /**
     * Envio el editar carrito al php y espero la respuesta, fin de la parte del fronted.
     */
    editarCarrito(xcantidad, xid_pedidoItems) {
        // Preparo el json q se va a mandar por parametro por url.
        let datos = JSON.parse(localStorage.getItem("derweb-mi-carrito"));
        let dat = datos["items"].filter(datos => datos.id == xid_pedidoItems);
        let Ojson = dat;
        Ojson[0].id_pedido = datos.id_pedido;
        Ojson[0].cantidad = xcantidad;
        Ojson[0].costo_unitario = dat[0]["costo"];
        Ojson[0].total = dat[0].subtotal_final;
        delete Ojson[0].costo;
        delete Ojson[0].subtotal_final;
        console.log(Ojson);
        // Preparo el api para enviar al php.
        let objApp = new App();
        let urlAPI = objApp.getUrlApi("catalogo-pedidos-modificar-items");
        let objAPI = new APIs();
        objAPI.call(urlAPI, "data=" + JSON.stringify(Ojson[0]), "PUT", (response) => {
            console.log(response);
            sweetalert_editarCarrito_icon(response.mensaje);
            let objMiCarrito = new MiCarritoModalComponent("mi-carrito");
            document.querySelector(".boton_alert_editarCarrito").addEventListener("click", () => {
                setTimeout(funcion_abrir_modalCarrito, 400);
            })
        });
    }

    eliminar_item_carrito(xUrl, xidpedido, xId) {console.log(xId)
        let xparametros = "id_pedido=" + xidpedido + "&id_pedidos_items=" + xId;
        (new APIs()).call(xUrl, xparametros, "PUT", (xdatos) => {
            xdatos = JSON.parse(xdatos);
            xdatos.codigo == 'OK' ? swal(xdatos.codigo, xdatos.mensaje, 'success') : swal(xdatos.codigo, xdatos.mensaje, 'error');
        });
    }

    vaciarMiCarrito(xUrl, xIdPed) {
        let xparametros = "id_pedido=" + xIdPed;
        
        (new APIs()).call(xUrl, xparametros, "PUT", (xdatos) => {
            xdatos = JSON.parse(xdatos);
            if(xdatos.codigo == 'OK') {
                swal(xdatos.codigo, xdatos.mensaje, 'success')
                localStorage.removeItem('derweb-mi-carrito');
                const objSpan = document.querySelector("#mi-carrito #subtotalMicarrito span");
                objSpan.textContent = "Subtotal _ _ _ _ $ 0"
            } else swal(xdatos.codigo, xdatos.mensaje, 'error');
            //xdatos.codigo == 'OK' ? swal(xdatos.codigo, xdatos.mensaje, 'success') : swal(xdatos.codigo, xdatos.mensaje, 'error');
        });
    } 

    /**
     * Muestra o esconde el campo transporte de Mi Carrito.
     * @param {*} opcionElegidaDeEnvio 
     * @param {*} xcodigo 
     * @param {*} objLabelTransporte 
     * @param {*} objSelectTransporte 
     * @param {*} change 
     */

    // displayTransporte(opcionElegidaDeEnvio, xcodigo, objLabelTransporte, objSelectTransporte, change = false) {
    //     console.log(opcionElegidaDeEnvio);
    //     if(change) {
    //         if(opcionElegidaDeEnvio != xcodigo){
    //             objSelectTransporte.style.display = "none";
    //             objLabelTransporte.style.display = "none";
    //         } else {
    //             objSelectTransporte.style.display = "block";
    //             objLabelTransporte.style.display = "inline-block";
    //         } 
    //     } else {
    //         if(opcionElegidaDeEnvio != xcodigo){
    //             objSelectTransporte.style.display = "none";
    //             objLabelTransporte.style.display = "none";
    //         }
    //     }   
    // }
    generarAnchorVaciarCarrito() {
        let objAnchor = document.createElement("a");
        let objDivAnchor = document.createElement("div");
        objDivAnchor.className = "div-anchor-vaciar-carrito";
        objAnchor.classList.add("fa-sharp", "fa-solid", "fa-trash-can");
        objAnchor.id = "vaciarCarrito";
        objAnchor.href = "javascript:" + this.__functionNameVaciarCarrito + "();";
        objAnchor.innerHTML = "   VACIAR CARRITO";
        objDivAnchor.appendChild(objAnchor);
        this.__objDivModal.appendChild(objDivAnchor);
    }
    generarSubtotal() {
        var strSesion = sessionStorage.getItem("derweb_sesion");
        var url = this.__objApp.getUrlApi("catalogo-pedidos-getPedidoActual");

        url = url + "?sesion=" + strSesion;
        /*fetch(url)
        .then(xresponse => xresponse.json())
        .then(xdata => {
            const xitem = xdata["items"];
            if ((xitem !== undefined) && (xitem.length != 0)) {
                this.__crearListaItems(xdata.id_pedido, xdata["items"]);
                this.__total = xdata["total_pedido"];
            }
        });*/
        const objSpan = this.crearElementDom("span", "subtotal-micarrito mb-3");
        const objDivSpan = this.crearElementDom("div", "subtotalMicarrito", "subtotalMicarrito");
        objSpan.innerText = "Subtotal _ _ _ _ $ 00.000";
        this.__objDivModal.appendChild(objDivSpan).appendChild(objSpan);
    }
}

function funcion_abrir_modalCarrito () {
    /*let objMiCarrito = new MiCarritoModalComponent("mi-carrito");
    objMiCarrito.open();*/
    abrir_mi_carrito();
}


