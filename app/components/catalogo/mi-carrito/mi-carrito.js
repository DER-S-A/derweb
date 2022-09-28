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
        this.__generarFooter();
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
     * Genera el footer del modal mi carrito.
     */
    __generarFooter() {
        let objAnchor = document.createElement("a");
        let objDivFooter = document.createElement("div");
        let objBotonFinalizarPedido = document.createElement("button");
        let objSelectSucursal = document.createElement("select");
        let objLabel = document.createElement("label");
        let aSesion = JSON.parse(sessionStorage.getItem("derweb_sesion"));
        
        objAnchor.classList.add("fa-sharp", "fa-solid", "fa-trash-can");
        objAnchor.id = "vaciarCarrito";
        objAnchor.href = "javascript:" + this.__functionNameVaciarCarrito + "();";
        objSelectSucursal.id = "select-sucursales";
        objSelectSucursal.name = "select-sucursales";
        objSelectSucursal.classList.add("form-control");
        objSelectSucursal.classList.add("select-suscursal");
        objLabel.textContent = "Sucursal:";

        // Genero la opcion de forma de envio.

        let objSelectFormaEnvio = document.createElement("select");
        objSelectFormaEnvio.id = "select-formasEnvios";
        objSelectFormaEnvio.name = "select-formasEnvios";
        objSelectFormaEnvio.classList.add("form-control", "select-formasEnvios");
        let obj2Label = document.createElement("label");
        obj2Label.innerHTML = "Forma de envio:";

        // Genero la opcion de transporte.

        let objSelectTransporte = document.createElement("select");
        objSelectTransporte.id = "select-transportes";
        objSelectTransporte.name = "select-transportes";
        objSelectTransporte.classList.add("form-control", "select-transportes");
        let obj3Label = document.createElement("label");
        obj3Label.innerHTML = "Transportes:";

        fetch(this.__objApp.getUrlApi("app-entidades-getSucursalesByEntidad") + "?id_entidad=" + aSesion["id_cliente"])
            .then(xresponse => xresponse.json())
            .then(xsucursales => {
                xsucursales.forEach((xitem) => {
                    let objOption = document.createElement("option");
                    objOption.id = xitem["codigo_sucursal"];
                    objOption.value = xitem["id"];
                    objOption.textContent = xitem["codigo_sucursal"] + " - " + xitem["calle"] + " - " + xitem["ciudad"];
                    objSelectSucursal.appendChild(objOption);
                });

                // Me traigo la id de la sucursal para mandarla por parametro en la url.
                let xselec = document.getElementById("select-sucursales").value;
                let xparametrosxUrl = "id_sucursales=" + xselec; 

                (new APIs()).call(this.__objApp.getUrlApi("app-forma-envio"), xparametrosxUrl, "GET", (xdatos) => {
                    xdatos.forEach((xitem) => {
                        // Completo los option con el resultado de json q traigo con el fetch call.
                        let objOption = document.createElement("option");
                        objOption.innerHTML = xitem.descripcion;
                        objOption.id = "forma-envio_"+xitem.id;
                        objOption.value = xitem.codigo;
                        objSelectFormaEnvio.appendChild(objOption);
                    });

                    // Me traigo la lista de transporte para pegar en el selector de transportes.

                    (new APIs()).call(this.__objApp.getUrlApi("app-transportes"), "", "GET", (xdatos) => {
                        console.log(xdatos);
                        xdatos.forEach((xitem) => {
                            // Completo los option con el resultado de json q traigo con el fetch call.
                            let objOption = document.createElement("option");
                            objOption.innerHTML = xitem.descripcion;
                            objOption.id = "transporteId_"+xitem.id;
                            objOption.value = xitem.codigo;
                            objSelectTransporte.appendChild(objOption);
                        });
                    }); 

                    let opcionElegidaDeEnvio = document.getElementById('select-formasEnvios').value;
                    // Con esta funcion realizo el display-none del selector q muestra todos los transportes.
                    displayTransporte(opcionElegidaDeEnvio, 6, obj3Label, objSelectTransporte); 
                    // 1RA variable es el codigo de la forma de envio q tiene el selector.
                    // El 6  es el codigo de forma de envio transporte.
                    // Y la 3RA variable es el label q dice transportes.
                    // 4TA variable es objeto nodo selector de transporte.
                    addEventListener("change",() => {  // Aca genero evento de cambio de opcion de select
                        let opcionElegidaDeEnvio = document.getElementById('select-formasEnvios').value;
                        displayTransporte(opcionElegidaDeEnvio, 6, obj3Label, objSelectTransporte, true);
                    });
                });                                                                      
            });                                                                       

        objDivFooter.id = this.__idModal + "_footer";
        objDivFooter.classList.add("row");
        objDivFooter.classList.add("modal-div-footer");

        objAnchor.innerHTML = "   VACIAR CARRITO"
        objDivFooter.appendChild(objAnchor);
        objDivFooter.appendChild(objLabel);
        objDivFooter.appendChild(objSelectSucursal);
        objDivFooter.appendChild(obj2Label);
        objDivFooter.appendChild(objSelectFormaEnvio);
        objDivFooter.appendChild(obj3Label);
        objDivFooter.appendChild(objSelectTransporte);
        
        objBotonFinalizarPedido.id = "btn-finalizar-pedido";
        objBotonFinalizarPedido.name = "btn-finalizar-pedido";
        objBotonFinalizarPedido.innerHTML = "<span>Finalizar Pedido</span>";
        objBotonFinalizarPedido.classList.add("btn");
        objBotonFinalizarPedido.classList.add("btn-primary");

        // Agrego la funcionalidad del evento finalizar pedido.
        objBotonFinalizarPedido.addEventListener("click", () => {
            this.__callbackFinalizarPedidoButton();
            this.clearContainer(this.__idModal + "-contenido");
        }, false);

        objDivFooter.appendChild(objBotonFinalizarPedido);
        this.__objDivModal.appendChild(objDivFooter);
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

    eliminar_item_carrito(xUrl, xId) {
        let xparametros = "id_pedidos_items=" + xId;
        console.log(xUrl);
        console.log(xId);
        (new APIs()).call(xUrl, xparametros, "PUT", (xdatos) => {
            xdatos = JSON.parse(xdatos);
            alert(xdatos.mensaje);
        });
    }

    vaciarMiCarrito(xUrl, xIdPed) {
        let xparametros = "id_pedido=" + xIdPed;
        
        (new APIs()).call(xUrl, xparametros, "PUT", (xdatos) => {
            xdatos = JSON.parse(xdatos);
            alert(xdatos.mensaje);
        });
    } 
}


