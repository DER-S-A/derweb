/**
 * Clase: PedidosVendedoresGUI
 * Descripción:
 *  Permite gestionar los pedidos de los clientes tanto para comerciales como
 *  para televentas.
 */

class PedidosVendedoresGUI {
    /**
     * Obtiene los pedidos pendientes del vendedor actuamente logueado.
     */
    getPedidosPendientes() {
        let objApp = new App();
        let url = objApp.getUrlApi("catalogo-pedidos-getPendientesByVendedor");
        let parametros = "sesion=" + sessionStorage.getItem("derweb_sesion");
    
        (new APIs()).call(url, parametros, "GET", xresponse => {
            /*localStorage.setItem("derweb-pedpend-vendedores", JSON.stringify(xresponse));*/
            document.getElementById("app_grid_container").innerHTML = "";
            let objGrid = new LFWDataGrid("app_grid_container", "id");
    
            objGrid.setAsociatedFormId("formulario");
            objGrid.setPermitirOrden(true);
            objGrid.setPermitirFiltros(true);
            objGrid.setPermitirEditarRegistro(true);
            objGrid.setEditJavascriptFunctionName("entrar_al_pedido");
            objGrid.setIconEditButton("fa-arrow-right-to-bracket");
            objGrid.setEditButtonTitle("Entrar al pedido");
        
            objGrid.agregarColumna("Pedido N°", "id", "numeric");
            objGrid.agregarColumna("Fecha", "fecha_alta", "string");
            objGrid.agregarColumna("Cliente", "cliente_cardcode", "numeric");
            objGrid.agregarColumna("Razón Social", "nombre");
            objGrid.agregarColumna("Total", "total", "numeric");
            
            xresponse.forEach(xelement => {
                objGrid.agregarFila(xelement);
            });
    
            objGrid.refresh();
        });        
    }

    /**
     * Permite entrar al pedido para editarlo y/o confirmarlo.
     * @param {int} xidpedido 
     */
    entrarAlPedido(xidpedido) {
        let pedidos = JSON.parse(sessionStorage.getItem("app_grid_container_rows"));
        let pedidoSeleccionado = pedidos.rows.filter(xelement => {
            if (parseInt(xelement["id"]) === parseInt(xidpedido))
                return true;
            else
                return false;
        });
        let items = pedidoSeleccionado[0]["items"];
        document.getElementById("app_grid_container").innerHTML = "";
        document.getElementById("app_grid_container").appendChild(this.__mostrarCabeceraPedido(pedidoSeleccionado));
    
        let objGrid = new LFWDataGrid("app_grid_container", "id");
        objGrid.setAsociatedFormId("formulario");
        objGrid.setPermitirOrden(true);
        objGrid.setPermitirFiltros(true);
        objGrid.setPermitirEditarRegistro(true);
        objGrid.setPermitirEliminarRegistro(true);
        objGrid.setEditJavascriptFunctionName("editar_pedido");
    
        objGrid.agregarColumna("Cantidad", "cantidad", "numeric");
        objGrid.agregarColumna("Código", "codigo", "string");
        objGrid.agregarColumna("Descripción", "descripcion", "string");
        objGrid.agregarColumna("Pr. Unit.", "costo_unitario", "numeric");
        objGrid.agregarColumna("Subtotal", "subtotal", "numeric");
        objGrid.agregarColumna("Total", "total", "numeric");
    
        items.forEach(xelement => {
            objGrid.agregarFila(xelement);
        });
        
        objGrid.refresh();
    }

    /**
     * Muestra los datos de cabecera del pedido seleccionado.
     * @param {array} xaPedidoSeleccionado 
     * @returns 
     */
    __mostrarCabeceraPedido(xaPedidoSeleccionado) {
        let objDivCabecera = document.createElement("div");
        let objLayoutRow = document.createElement("div");
        let objLayoutCol = [];
        let objCampoNroPedido = document.createElement("div");
        let objCampoClienteNro = document.createElement("div");
        let objCampoClienteNombre = document.createElement("div");

        objDivCabecera.classList.add("contenedor-cabecera");

        objLayoutRow.classList.add("row");
        objLayoutCol[0] = document.createElement("div");
        objLayoutCol[1] = document.createElement("div");
        objLayoutCol[2] = document.createElement("div");
        objLayoutCol[0].classList.add("col-4");
        objLayoutCol[1].classList.add("col-4");
        objLayoutCol[2].classList.add("col-4");

        objCampoNroPedido.innerHTML = "<label>Pedido N°:</label> " + xaPedidoSeleccionado[0].id;
        objLayoutCol[0].appendChild(objCampoNroPedido);
        objLayoutRow.appendChild(objLayoutCol[0]);

        objCampoClienteNro.innerHTML = "<label>Código de Cliente: </label> " + xaPedidoSeleccionado[0].cliente_cardcode;
        objLayoutCol[1].appendChild(objCampoClienteNro);
        objLayoutRow.appendChild(objLayoutCol[1]);
        
        objCampoClienteNombre.innerHTML = "<label>Razón Social:</label> " + xaPedidoSeleccionado[0].nombre;
        objLayoutCol[2].appendChild(objCampoClienteNombre);
        objLayoutRow.appendChild(objLayoutCol[2]);

        console.log(objLayoutRow);

        objDivCabecera.appendChild(objLayoutRow);

        return objDivCabecera;
    }
}