class ConfirmacionPedido {
    /**
     * Clase de confirmación de pedidos.
     * @param {string} xurlApi Establece la URL de la api que se utilizará para confirmar un pedido.
     * @param {boolean} xconfirmaVenedor Establece true si se confirma desde el sistema de vendedores.
     */
    constructor(xurlApi, xconfirmaVenedor = false) {
        this.urlApi = xurlApi;
        this.__objApp = new App();        
        this.__idModal = null;
        this.__functionNameVaciarCarrito = null;
        this.__objDivModal = null;
        this.__confirmaVendedor = xconfirmaVenedor;
        this.__cb_finalizar_pedido = null;
    }

    /**
     * Permite setear el Id. de modal asociado
     * @param {string} xvalue 
     */
    setIdModal(xvalue) {
        this.__idModal = xvalue;
    }

    /**
     * Setea el Div del modal asociado.
     * @param {DOM Element} xvalue 
     */
    setDivModal(xvalue) {
        this.__objDivModal = xvalue;
    }

    /**
     * Establece el callback para anexar funcionalidad extra al finalizar el pedido.
     * @param {function} xfunction 
     */
    setCallbackFinalizarPedido(xfunction) {
        this.__cb_finalizar_pedido = xfunction;
    }

    /**
     * Permite devolver el DOM con el footer de pedidos.
     * @returns {DOM Element}
     */
    getFooter() {
        return this.__objDivModal;
    }

    /**
     * Genera el footer del modal mi pedido.
     */

    generarFooterPedido() {
        
        let objDivFooter = document.createElement("div");
        let objDivSuc = document.createElement("div");
        objDivSuc.id = "suc-deselecylabel";
        objDivSuc.style.padding = "0";
        let objBotonFinalizarPedido = document.createElement("button");
        let objSelectSucursal = document.createElement("select");
        let objLabel = document.createElement("label");
        let aSesion = JSON.parse(sessionStorage.getItem("derweb_sesion"));
        
        objSelectSucursal.id = "select-sucursales";
        objSelectSucursal.name = "select-sucursales";
        objSelectSucursal.classList.add("form-control", "select-suscursal");
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

        // this.__confirmaVendedor identifica si es vendedor o no. si es vendedor necesitamos seleccionar la sucursal.
        this.llenarBoxes(aSesion, objSelectSucursal, objSelectFormaEnvio, objSelectTransporte, obj3Label,this.__confirmaVendedor);
        
        objDivFooter.id = this.__idModal + "_footer";
        objDivFooter.classList.add("row");
        objDivFooter.classList.add("modal-div-footer");

        objDivSuc.appendChild(objLabel);
        objDivSuc.appendChild(objSelectSucursal);
        objDivFooter.appendChild(objDivSuc);
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
            if (!this.__confirmaVendedor)
                this.__confirmarPedidoCliente();
            else
                this.__confirmarPedidoVendedor();
                
            // Si viene una función ejecuta callback con funcionalidad
            // extra
            if (this.__cb_finalizar_pedido !== null)
                this.__cb_finalizar_pedido();

        }, false);

        objDivFooter.appendChild(objBotonFinalizarPedido);
        this.__objDivModal.appendChild(objDivFooter);
    }

    /**
     * Confirma el pedido desde el sistema de clientes.
     */
    __confirmarPedidoCliente() {
        let objMiCarrito = new MiCarritoModalComponent();
        this.__confirmarPedido(true);
        objMiCarrito.clearContainer(this.__idModal + "-contenido");
    }

    /**
     * Confirma el pedido desde el sistema de vendedores.
     */
    __confirmarPedidoVendedor() {
        this.__confirmarPedido();
    }

    /**
     * Permite confirmar un pedido.
     */
    __confirmarPedido(EsCarritoCliente = false) {
        // Recupero los parámetros de envío
        let aPedidoActual = [];
        let idsucursal = document.getElementById("select-sucursales").value;
        let idformaenvio = document.getElementById("select-formasEnvios").value;
        let idtransporte = document.getElementById("select-transportes").value;
        let url = "";
        let parametros = "";

        // Verifico desde qué sistema se invoca la confirmación de pedidos.
        if (!this.__confirmaVendedor)
            aPedidoActual = JSON.parse(localStorage.getItem("derweb-mi-carrito"));
        else
            aPedidoActual["id_pedido"] = parseInt(sessionStorage.getItem("derven_id_pedido_sel"));
        
        // Mando a confirmar el pedido.
        url =  app.getUrlApi("catalogo-pedidos-confirmarPedido");
        
        // Armo un JSON con los parámetros a invocar.
        let aParametrosConfirmacion = {
            "id_pedido": parseInt(aPedidoActual["id_pedido"]),
            "id_sucursal": parseInt(idsucursal),
            "id_formaenvio": parseInt(idformaenvio),
            "id_transporte": parseInt(idtransporte)
        };

        // Armo los parámetros para pasarle al API.
        parametros = "?sesion=" + sessionStorage.getItem("derweb_sesion") 
            + "&pedido=" + JSON.stringify(aParametrosConfirmacion);

        url = url + parametros;
        console.log(url);
        
        // Llamo a la API que permite confirmar el pedido.
        fetch(url, {
            method: "PUT",
            //method:"POST",
            headers: {
                'content-type': 'applitacion/json'
            }
        }).then(xresponse => xresponse.json())
            .then(xdata => {
                if (xdata["codigo"] !== "OK")
                    alert(xdata["mensaje"]);
                else
                    alert(xdata["mensaje"]);
            });

            // Si viene del lado del cliente se cierra el modal de mi carrito.
            if(EsCarritoCliente)
                objMiCarrito.close();
    }

    llenarBoxes(xaSesion, xobjSelectSucursal, xobjSelectFormaEnvio, xobjSelectTransporte, obj3Label, mostrarSuc) {

        let id_entidad = xaSesion["id_cliente"];
        if(id_entidad == null) {
            let Sesionpedidoactual = JSON.parse(sessionStorage.getItem("derweb_pedido-actual"));
            id_entidad = Sesionpedidoactual[0]["id_entidad"];
        }

        fetch(this.__objApp.getUrlApi(this.urlApi) + "?id_entidad=" + id_entidad)
            .then(xresponse => xresponse.json())
            .then(xsucursales => {
                xsucursales.forEach((xitem) => {
                    let objOption = document.createElement("option");
                    objOption.id = xitem["codigo_sucursal"];
                    objOption.value = xitem["id"];
                    objOption.textContent = xitem["codigo_sucursal"] + " - " + xitem["calle"] + " - " + xitem["ciudad"];
                    xobjSelectSucursal.appendChild(objOption);
                });

                // Me traigo la id de la sucursal para mandarla por parametro en la url.
                if(mostrarSuc) {
                    var xselec = document.getElementById("select-sucursales").value;
                    console.log("vendedor");
                } else {
                    var xselec = JSON.parse(sessionStorage.getItem("derweb_sesion"));
                    xselec = xselec.id_sucursal;
                    let objOption = document.getElementById("suc-deselecylabel");
                    objOption.style.display = "none";
                } 
                let xparametrosxUrl = "id_sucursales=" + xselec;

                (new APIs()).call(this.__objApp.getUrlApi("app-forma-envio"), xparametrosxUrl, "GET", (xdatos) => {
                    xdatos.forEach((xitem) => {
                        // Completo los option con el resultado de json q traigo con el fetch call.
                        let objOption = document.createElement("option");
                        objOption.innerHTML = xitem.descripcion;
                        objOption.id = "forma-envio_"+xitem.id;
                        objOption.value = xitem.codigo;
                        xobjSelectFormaEnvio.appendChild(objOption);
                    });

                    // Me traigo la lista de transporte para pegar en el selector de transportes.

                    (new APIs()).call(this.__objApp.getUrlApi("app-transportes"), "", "GET", (xdatos) => {
                        xdatos.forEach((xitem) => {
                            // Completo los option con el resultado de json q traigo con el fetch call.
                            let objOption = document.createElement("option");
                            objOption.innerHTML = xitem.descripcion;
                            objOption.id = "transporteId_" + xitem.id;
                            objOption.value = xitem.id;
                            xobjSelectTransporte.appendChild(objOption);
                        });
                    }); 

                    let opcionElegidaDeEnvio = document.getElementById('select-formasEnvios').value;
                    // Con esta funcion realizo el display-none del selector q muestra todos los transportes.
                    
                    //console.log(objMiCarrito);
                    //if (objMiCarrito !== null)
                    this.displayTransporte(opcionElegidaDeEnvio, 6, obj3Label/*this.obj3Label*/, xobjSelectTransporte/*this.objSelectTransporte*/); 
                        //objMiCarrito.displayTransporte(opcionElegidaDeEnvio, 6, obj3Label, xobjSelectTransporte);
                    // 1RA variable es el codigo de la forma de envio q tiene el selector.
                    // El 6  es el codigo de forma de envio transporte.
                    // Y la 3RA variable es el label q dice transportes.
                    // 4TA variable es objeto nodo selector de transporte.
                    addEventListener("change",() => {  // Aca genero evento de cambio de opcion de select
                        let opcionElegidaDeEnvio = document.getElementById('select-formasEnvios').value;
                        //(new MiCarritoModalComponent()).displayTransporte(opcionElegidaDeEnvio, 6, obj3Label, xobjSelectTransporte, true);
                        this.displayTransporte(opcionElegidaDeEnvio, 6, obj3Label, xobjSelectTransporte, true);
                    });
                });                                                                      
            });
    }

    llenarBoxes2() {

    fetch(this.__objApp.getUrlApi(this.urlApi) + "?id_entidad=" + this.aSesion["id_cliente"])
        .then(xresponse => xresponse.json())
        .then(xsucursales => {
            xsucursales.forEach((xitem) => {
                let objOption = document.createElement("option");
                objOption.id = xitem["codigo_sucursal"];
                objOption.value = xitem["id"];
                objOption.textContent = xitem["codigo_sucursal"] + " - " + xitem["calle"] + " - " + xitem["ciudad"];
                this.objSelectSucursal.appendChild(objOption);
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
                    this.objSelectFormaEnvio.appendChild(objOption);
                });

                // Me traigo la lista de transporte para pegar en el selector de transportes.

                (new APIs()).call(this.__objApp.getUrlApi("app-transportes"), "", "GET", (xdatos) => {
                    console.log(xdatos);
                    xdatos.forEach((xitem) => {
                        // Completo los option con el resultado de json q traigo con el fetch call.
                        let objOption = document.createElement("option");
                        objOption.innerHTML = xitem.descripcion;
                        objOption.id = "transporteId_" + xitem.id;
                        objOption.value = xitem.id;
                        this.objSelectTransporte.appendChild(objOption);
                    });
                }); 

                let opcionElegidaDeEnvio = document.getElementById('select-formasEnvios').value;
                // Con esta funcion realizo el display-none del selector q muestra todos los transportes.

                //(new MiCarritoModalComponent()).displayTransporte(opcionElegidaDeEnvio, 6, this.obj3Label, this.objSelectTransporte);

                this.displayTransporte(opcionElegidaDeEnvio, 6, this.obj3Label, this.objSelectTransporte); 
                // 1RA variable es el codigo de la forma de envio q tiene el selector.
                // El 6  es el codigo de forma de envio transporte.
                // Y la 3RA variable es el label q dice transportes.
                // 4TA variable es objeto nodo selector de transporte.
                addEventListener("change",() => {  // Aca genero evento de cambio de opcion de select
                    let opcionElegidaDeEnvio = document.getElementById('select-formasEnvios').value;
                    //(new MiCarritoModalComponent()).displayTransporte(opcionElegidaDeEnvio, 6, this.obj3Label, this.objSelectTransporte, true);
                    this.displayTransporte(opcionElegidaDeEnvio, 6, this.obj3Label, this.objSelectTransporte, true);
                });
            });                                                                      
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

     displayTransporte(opcionElegidaDeEnvio, xcodigo, objLabelTransporte, objSelectTransporte, change = false) {
        
        if(change) {
            if(opcionElegidaDeEnvio != xcodigo){
                objSelectTransporte.style.display = "none";
                objLabelTransporte.style.display = "none";
            } else {
                objSelectTransporte.style.display = "block";
                objLabelTransporte.style.display = "inline-block";
            } 
        } else {
            if(opcionElegidaDeEnvio != xcodigo){
                objSelectTransporte.style.display = "none";
                objLabelTransporte.style.display = "none";
            }
        }   
    }

}