/**
 * Contiene la clase que arma la grilla para mostrar en mi carrito.
 */

class CarritoGridComponent extends ComponentManager {
    /**
     * Constructor de clase.
     * @param {string} xidGrilla Id. de grilla.
     * @param {string} xidContainer Id. del contenedor en donde se generará la grilla.
     */
    constructor(xidGrilla, xidContainer) {
        super();
        this.__objContenedorGrilla = document.createElement("div");
        this.__objContenedorGrilla.classList.add("grid-mi-carrito-container");
        this.__idContenedor = xidContainer;
        this.__idGrilla = xidGrilla;
        this.__total = "";
        this.__editarFunc = "editar_carrito";
        this.__eliminarFunc = "";
        this.__objApp = new App();
    }

    /**
     * Establce el nombre de la función que se ejecuta al hacer clic en el
     * botón eliminar.
     * @param {string} xvalue Nombre de la función eliminar de javascript.
     */
    setEliminarFunctionName(xvalue) {
        this.__eliminarFunc = xvalue;
    }

    /**
     * Permite generar el componente grilla.
     */
    generateComponent() {
        var strSesion = sessionStorage.getItem("derweb_sesion");
        var url = this.__objApp.getUrlApi("catalogo-pedidos-getPedidoActual");
        this.clearContainer(this.__idContenedor);

        url = url + "?sesion=" + strSesion;
        fetch(url)
            .then(xresponse => xresponse.json())
            .then(xdata => {
                if ((xdata["items"] !== undefined) && (xdata["items"].length != 0)) {
                    this.__grabarEnCache(xdata);
                    this.__crearListaItems(xdata.id_pedido, xdata["items"]);
                    this.__total = xdata["total_pedido"];
                }
            });
    }

    /**
     * Guardar el carrito en localStorage.
     * @param {array} xdata 
     */
    __grabarEnCache(xdata) {
        localStorage.setItem("derweb-mi-carrito", JSON.stringify(xdata));
    }

    /**
     * Permite generar la lista a partir de los items obtenidos desde la API.
     * @param {array} xdatos Items de pedidos a mostrar
     */
    __crearListaItems(xidpedido, xdatos) {
        xdatos.forEach(xItem => {
            var objContenedorFila = document.createElement("div");    
            var objLista = document.createElement("ul");

            objLista.id = this.__idGrilla + "-lista";
            objLista.classList.add("mi-carrito-lista");
    
            objLista.appendChild(this.__agregarColumnaFoto(xItem));
            objLista.appendChild(this.__agregarColumnaInfoArticulo(xItem));
            objLista.appendChild(this.__agregarColumnaPrecioYOperaciones(xidpedido, xItem));
            objContenedorFila.append(objLista);

            objContenedorFila.classList.add("fila-container");
            this.__objContenedorGrilla.appendChild(objContenedorFila);
            document.getElementById(this.__idContenedor).appendChild(this.__objContenedorGrilla);
        });
        let objAnchor = document.createElement("a");
        objAnchor.classList.add("fa-sharp", "fa-solid", "fa-trash-can");
        objAnchor.id = "vaciarCarrito";
        objAnchor.href = "javascript:" + this.__functionNameVaciarCarrito + "();";
    }

    /**
     * Crea la columna foto.
     * @param {array} xitem Item
     * @returns {DOMElement}
     */
    __agregarColumnaFoto(xitem) {
        var objColumna = document.createElement("li");
        var objColumnaContainer = document.createElement("div");
        var objImagen = document.createElement("img");

        objImagen.setAttribute("alt", "");
        if (xitem["archivo"] !== "")
            objImagen.setAttribute("src", xitem["archivo"]);
        else
            objImagen.setAttribute("src", "../admin/ufiles/sinfoto.jpeg");

        objColumnaContainer.classList.add("foto-container");
        objColumnaContainer.appendChild(objImagen);
        objColumna.appendChild(objColumnaContainer);
        return objColumna;        
    }
    
    /**
     * Crea la columna con la información del artículo.
     * @param {array} xitem Item
     * @returns {DOMElement}
     */
    __agregarColumnaInfoArticulo(xitem) {
        var objColumna = document.createElement("li");
        var objColumnaContainer = document.createElement("div");
        var objDescripcion = document.createElement("p");
        var objLinea = document.createElement("hr");
        var objCodigoLabel = document.createElement("label");
        var objCodigo = document.createElement("span");
        var objInputCantidad = document.createElement("input");

        // Trunco la descripción del artículo a 14 caracteres
        objDescripcion.innerText = xitem["descripcion"].substr(0, 14) + " ...";
        objDescripcion.title = xitem["descripcion"];
        objCodigoLabel.innerText = "CODIGO";
        objCodigo.innerHTML = "<br>" + xitem["codigo"] + "<br>";

        objInputCantidad.type = "number";
        objInputCantidad.value = xitem["cantidad"];
        objInputCantidad.readOnly = true;
        
        objColumnaContainer.appendChild(objDescripcion);
        objColumnaContainer.appendChild(objLinea);
        objColumnaContainer.appendChild(objCodigoLabel);
        objColumnaContainer.appendChild(objCodigo);
        objColumnaContainer.appendChild(objInputCantidad);

        objColumnaContainer.classList.add("info-articulo");
        objColumna.appendChild(objColumnaContainer);

        return objColumna;
    }

    /**
     * Crea la columna con el precio y el botón de eliminar.
     * @param {array} xitem Item
     * @returns {DOMElement}
     */
    __agregarColumnaPrecioYOperaciones(xidpedido, xitem) {
        var objColumna = document.createElement("li");
        var objColumnaContainer = document.createElement("div");
        var objCostoLabel = document.createElement("label");
        var objCosto = document.createElement("span");
        var objBotonEliminar = document.createElement("a");
        objBotonEliminar.className = "px-1";
        var objBotonEditar = document.createElement("a");
        objBotonEditar.className = "px-1";

        objBotonEditar.href = "javascript:" + this.__editarFunc + "(" + xitem["id"] + ");";
        objBotonEliminar.href = "javascript:" + this.__eliminarFunc + "(" + xidpedido + "," + xitem["id"] + ");";

        objCostoLabel.innerText = "PRECIO COSTO";
        objCosto.innerHTML = "<br>" +  xitem["costo"] + "<br>";

        objBotonEditar.innerHTML = "<i class='fa-solid fa-pen-to-square'></i>";
        objBotonEliminar.innerHTML = "<i class=\"fa-solid fa-trash\"></i>";

        objColumnaContainer.appendChild(objCostoLabel);
        objColumnaContainer.appendChild(objCosto);
        objColumnaContainer.appendChild(objBotonEditar);
        objColumnaContainer.appendChild(objBotonEliminar);

        objColumnaContainer.classList.add("info-precio");
        objColumna.appendChild(objColumnaContainer);
        return objColumna;
    }
}