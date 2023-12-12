/**
 * Clase CatalogoGridComponent
 * Descripción:
 *  Esta clase contiene el componente grilla para el catálogo teniendo en cuenta
 *  sus funcionalidades.
 */

class CatalogoGridComponent extends ComponentManager {
    /**
     * Creo el contenedor general de la grilla.
     * @param {string} xidgrid Id de contenedor de grilla. 
     *  Formato:    { 
     *                  api_url : "Establece la URL de la API a consultar", 
     *                  tipo_busqueda: "tipo_de_busqueda", 
     *                  values: [
     *                      { id_rubro: "Id. de rubro"},
     *                      { id_subrubro: "Id. de de subrubro"},
     *                  ]
     *              }
     * @param {array} xparametros Array JSON con los parámetros de búsqueda.
     */
    constructor (xidgrid, xparametros, xbuscarPorFrase, xIdGrupoArt = "") {
        super();
        this._aParametros = xparametros;
        this._claveSessionStorage = "derweb_articulos";

        this._objGridContainer = document.createElement("div");
        this._objGridContainer.id = xidgrid;
        this._objGridContainer.classList.add("container","grid-container");
        this._aDatos = [];
        this.__realizarBusquedaPorFrase = xbuscarPorFrase;
        this.__idGrupoArticulo = xIdGrupoArt;

        this.__getData();
    }
    
    /**
     * Establece si se debe mostrar el precio de lista o no.
     * @param {bool} xvalue 
     */
    setVerPrecioLista(xvalue = true) {
           const allboxLista = document.querySelectorAll(".pLista");
           allboxLista.forEach(xbox => {
                if (xvalue)
                    xbox.style.display = "block";
                else
                    xbox.style.display = "none";
           });
    }

    /**
     * Establece si se debe mostrar el precio de costo o no.
     * @param {bool} xvalue 
     */
    setVerPrecioCosto(xvalue = true) {
        const allboxCosto = document.querySelectorAll(".pCosto");
           allboxCosto.forEach(xbox => {
                if (xvalue)
                    xbox.style.display = "block";
                else
                    xbox.style.display = "none";
           });
    }

    /**
     * Establece si se debe mostrar el precio de venta o no.
     * @param {bool} xvalue 
     */
    setVerPrecioVenta(xvalue = true) {
        const allboxVenta = document.querySelectorAll(".pVenta");
           allboxVenta.forEach(xbox => {
                if (xvalue)
                    xbox.style.display = "block";
                else
                    xbox.style.display = "none";
           });

    }

    /**
     * Establece la búsqueda por frase.
     */
    setBuscarPorFrase (xvalue) {
        this.__realizarBusquedaPorFrase = xvalue;
    }

    /**
     * Permite recupera los datos a mostrar en la grilla.
     */
    __getData() {
        var pagina = 0;
        if(this.__idGrupoArticulo != "") {
            this.__grupoArt(pagina, this._claveSessionStorage);
            return;
        }
        if (!this.__realizarBusquedaPorFrase)
            this.__getArticulosByRubroAndSubrubro(pagina, this._claveSessionStorage)
        else
            this.__buscarPorFrase(pagina, this._claveSessionStorage);
    }

    /**
     * Permite recuperar página por página el resultado de artículos en forma
     * asincrónica.
     * @param {int} xpagina Número de página a recuperar
     * @param {string} xclaveSessionStorage Clave de almacenamiento para sessionStorage.
     */
    __getArticulosByRubroAndSubrubro(xpagina, xclaveSessionStorage) {console.log("200")
        var url = this._aParametros["api_url"];
        var url_con_parametros = url + "?sesion=" + sessionStorage.getItem("derweb_sesion")
            + "&parametros=" + JSON.stringify(this._aParametros) + "&pagina=" + xpagina;

        fetch (url_con_parametros)
            .then(xresponse => xresponse.json())
            .then(xdata  => {
                if (xdata["values"].length !== 0) {
                    sessionStorage.setItem(xclaveSessionStorage + "_" + xpagina, JSON.stringify(xdata));
                    xpagina += 40;
                    this.__getArticulosByRubroAndSubrubro(xpagina, xclaveSessionStorage);
                    this.__crearListaArticulos(xpagina - 40);

                }
            });
    }

    /**
     * Permite dibjar la grilla en pantalla
     * @param {int} xpagina Página a levantar
     */
    __crearListaArticulos (xpagina) {
        var aDatos = JSON.parse(sessionStorage.getItem(this._claveSessionStorage + "_" + xpagina));

        aDatos["values"].forEach(xelement => {
            var objRowLista = this.__addBootstrapRow();
            var objColLista = this.__addBoostralColumn(["col-md-12", "col-sm-12"]);
            var objItemRow = this.__addBootstrapRow();
            var objItemCol1 = this.__addBoostralColumn(["col-md-2", "col-sm-2"]);
            var objItemCol2 = this.__addBoostralColumn(["col-md-4", "col-sm-6"]);
            var objItemCol3 = this.__addBoostralColumn(["col-md-4", "col-sm-6"]);
            var objItemCol4 = this.__addBoostralColumn(["col-md-2", "col-sm-2", "cont-semaforo-lista"]);

            objRowLista.classList.add("row-lista");

            console.log(xelement);
            objItemCol1.appendChild(this.__crearColumnaFoto());
            const oPrecios = {Precio_lista: xelement["prlista"], Precio_costo: xelement["cped"], Precio_venta: xelement["vped"]};
            objItemCol2.appendChild(this.__crearColumnaDescripcion(xelement["desc"], xelement["codigo"], xelement["id"], oPrecios));
            objItemCol3.appendChild(this.__crearColumnaPrecios(xelement["prlista"], xelement["cped"], xelement["vped"]));
            objItemCol4.appendChild(this.__crearSemaforo());
            objItemCol4.appendChild(this.__crearColumnaPedido(xelement["id"], xelement["stkd"]));

            objItemRow.appendChild(objItemCol1);
            objItemRow.appendChild(objItemCol2);
            objItemRow.appendChild(objItemCol3);
            objItemRow.appendChild(objItemCol4);
            objColLista.appendChild(objItemRow);
            objRowLista.appendChild(objColLista);
            this._objGridContainer.appendChild(objRowLista);
        });

        sessionStorage.removeItem(this._claveSessionStorage + "_" + xpagina);
    }

    /**
     * Crea la primer columna con la foto.
     * @param {string} xurlFoto 
     * @returns {DOMElement}
     */
    __crearColumnaFoto(xurlFoto) {
        var objContenedorFoto = document.createElement("div");
        var objImg = document.createElement("img");
        
        objContenedorFoto.id = "info-articulo-foto"
        objContenedorFoto.classList.add("info-articulo-foto");

        objImg.src = "../admin/ufiles/sinfoto.jpeg";
        objContenedorFoto.appendChild(objImg);

        return objContenedorFoto;
    }

    /**
     * Crea la segunda columna con la descripción y el código.
     * @param {string} xdescripcion Descripción del artículo.
     * @param {string} xcodigo Código del artículo.
     * @param {int} xid Id. Articulo.
     * @returns {DOMElement}
     */
    __crearColumnaDescripcion(xdescripcion, xcodigo, xid, oPrecios) {
        var objContenedorGeneral = document.createElement("div");
        var objInfoTitulo = document.createElement("div");
        var objInfoCodigo = document.createElement("div");
        var objInfoAplicaciones = document.createElement("div");
        var objSpanDescripcion = document.createElement("span");
        var objTituloCodigo = document.createElement("h4");
        var objSpanCodigo = document.createElement("span");
        var objTituloAplicaciones = document.createElement("h4");
        var objAnchorCodigo = document.createElement("a");

        objContenedorGeneral.id = "info-articulo-general";
        objContenedorGeneral.classList.add("info-articulo-general");
        objAnchorCodigo.href = "javascript:crearFicha(" + xid + "," + JSON.stringify(oPrecios) + ")";

        objSpanDescripcion.textContent = xdescripcion;
        objSpanDescripcion.classList.add("descripcion");
        objInfoTitulo.appendChild(objSpanDescripcion);

        objTituloCodigo.textContent = "CODIGO";
        objSpanCodigo.textContent = xcodigo;
        objInfoCodigo.appendChild(objTituloCodigo);
        
        objInfoCodigo.appendChild(objAnchorCodigo).appendChild(objSpanCodigo);

        objTituloAplicaciones.textContent = "APLICACIONES";
        objInfoAplicaciones.appendChild(objTituloAplicaciones);

        objContenedorGeneral.appendChild(objInfoTitulo);
        objContenedorGeneral.appendChild(objInfoCodigo);
        objContenedorGeneral.appendChild(objInfoAplicaciones);

        
        
        return objContenedorGeneral;
    }

    /**
     * Permite crear la columna precios.
     * @param {double} xprecioLista Precio de lista.
     * @param {double} xcosto Costo
     * @param {double} xventa Precio de venta
     * @returns {DOMElement}
     */
    __crearColumnaPrecios(xprecioLista, xcosto, xventa) {
        var objInfoPrecios = document.createElement("div");
        var objTituloPrecioLista = document.createElement("h5");
        var objSpanPrecioLista = document.createElement("span");
        var objTituloCosto = document.createElement("h5");
        var objSpanCosto = document.createElement("span");
        var objTituloPrecioVenta = document.createElement("h5");
        var objSpanPrecioVenta = document.createElement("span");

        objInfoPrecios.id = "info-articulo-precios";
        objInfoPrecios.classList.add("info-articulo-precios");

        objTituloPrecioLista.textContent = "PRECIO DE LISTA";
        objSpanPrecioLista.textContent = "$ " + xprecioLista;

        objTituloCosto.textContent = "PRECIO DE COSTO";
        objSpanCosto.textContent = "$ " + xcosto;

        objTituloPrecioVenta.textContent = "PRECIO DE VENTA";
        objSpanPrecioVenta.textContent = "$ " + xventa;

        var objDivPrecioLista = document.createElement("div");
        var objDivPrecioCosto = document.createElement("div");
        var objDivPrecioVenta = document.createElement("div");

        objDivPrecioLista.id = "plista-container";
        objDivPrecioCosto.id = "pcosto-container";
        objDivPrecioVenta.id = "pventa-container";

        objDivPrecioLista.classList.add("pLista");
        objDivPrecioCosto.classList.add("pCosto");
        objDivPrecioVenta.classList.add("pVenta");
        
        objDivPrecioLista.appendChild(objTituloPrecioLista);
        objDivPrecioLista.appendChild(objSpanPrecioLista);
        objDivPrecioCosto.appendChild(objTituloCosto);
        objDivPrecioCosto.appendChild(objSpanCosto);
        objDivPrecioVenta.appendChild(objTituloPrecioVenta);
        objDivPrecioVenta.appendChild(objSpanPrecioVenta);

        objInfoPrecios.appendChild(objDivPrecioLista);
        objInfoPrecios.appendChild(objDivPrecioCosto);
        objInfoPrecios.appendChild(objDivPrecioVenta);

        return objInfoPrecios;
    }

    /**
     * Arma la columna de carrito de compras para hacer el pedido.
     * @returns {DOMElement}
     */
    __crearColumnaPedido(xidarticulo, stock_disponible) {
        var habilitarIngresoPedido = false;
        var objPedidoContainter = document.createElement("div");
        var objContainerCarrito = document.createElement("div");
        var objInputCantidad = document.createElement("input");
        objInputCantidad.type = "number";
        objInputCantidad.min = "0";
        var objBotonCarrito = document.createElement("a");
        
        objPedidoContainter.classList.add("pedido-container");
        objContainerCarrito.classList.add("carrito-container");
        
        objInputCantidad.id = "txtcantidad_" + xidarticulo;
        objInputCantidad.name = "txtcantidad_" + xidarticulo;
        
        objBotonCarrito.innerHTML = "&nbsp&nbsp;<i class=\"fa-solid fa-cart-plus\"></i>&nbsp&nbsp;";
        objBotonCarrito.href = "javascript:agregarAlCarrito(" + xidarticulo + ");";

        calcularCantDiasCubiertosStock(xidarticulo, stock_disponible, (dias) => {
            if (parseInt(dias) === 0) {
                objInputCantidad.style.backgroundColor = "red";
                objInputCantidad.style.color = "white";
                objBotonCarrito.style.backgroundColor = "red";
            }

            if ((parseInt(dias) > 0) && (parseInt(dias) <= 2)) {
                objInputCantidad.style.backgroundColor = "yellow";
                objBotonCarrito.style.backgroundColor = "yellow";
            }

            if (parseInt(dias) >= 3) {
                objInputCantidad.style.backgroundColor = "lightgreen";
                objBotonCarrito.style.backgroundColor = "lightgreen";
            }
        })

        objContainerCarrito.appendChild(objInputCantidad);
        objContainerCarrito.appendChild(objBotonCarrito);
        objPedidoContainter.appendChild(objContainerCarrito);

        return objPedidoContainter;
    }

    __crearSemaforo() {
        const objSemaforoContenedor = document.createElement("ul");
        objSemaforoContenedor.className ="semaforo";
        objSemaforoContenedor.innerHTML = `
            <li title="Dispoible"></li>
            <li title="Consulte"></li>
            <li title="Sin stock"></li>
            <li title="Por pedido"></li>
            <li title="Inhabilitado"></li>`;
            return objSemaforoContenedor;
    }

    /**
     * Permite generar el componente datagrid
     */
     generateComponent (xidAppContainer) {
        var objAppContainer = document.getElementById(xidAppContainer);
        objAppContainer.appendChild(this._objGridContainer);
    }

    /**
     * Permite consultar por frase y presentar los resultados en pantalla.
     * @param {int} xpagina 
     * @param {string} xclaveSessionStorage 
     */
    __buscarPorFrase(xpagina, xclaveSessionStorage) {
        var url = this._aParametros["api_url"];
        var url_con_parametros = url + "?sesion=" + sessionStorage.getItem("derweb_sesion")
            + "&frase=" + this._aParametros["values"]["frase"] + "&pagina=" + xpagina;
        fetch (url_con_parametros)
            .then(xresponse => xresponse.json())
            .then(xdata  => {console.log(xdata)
                if (xdata["values"].length !== 0) {
                    sessionStorage.setItem(xclaveSessionStorage + "_" + xpagina, JSON.stringify(xdata));
                    xpagina += 40;
                    this.__buscarPorFrase(xpagina, xclaveSessionStorage);
                    this.__crearListaArticulos(xpagina - 40);
                }
            });        
    }
    __grupoArt(xpagina, xclaveSessionStorage) {
        const url = new App().getUrlApi("artGrupo");
            const objCacheUtils = new CacheUtils("derweb");
            const aSession = objCacheUtils.get("sesion");
            const parametro = "sesion=" + JSON.stringify(aSession) + "&id_novedad=" + this.__idGrupoArticulo;
            new APIs().call(url, parametro, "GET", articulos => {
                if(articulos["values"].length !==0) {
                    sessionStorage.setItem(xclaveSessionStorage + "_" + xpagina, JSON.stringify(articulos));
                    xpagina += 40;
                    //this.__grupoArt(xpagina, xclaveSessionStorage);
                    this.__crearListaArticulos(xpagina - 40);
                }
            });
    }
}
