// Inicio la aplicación
var app = new App();
var objListaArticulo = new ListaArticuloComponent("lista-articulos-container");
var objMiCarrito = null;
let objPedidos = null;

app.init();

/**
 * Evento onLoad de la página.
 * Este evento se ejecuta al cargar la página.
 */
window.onload = () => {
    objPedidos = new PedidosVendedoresGUI();   
    // Si la sesión no está en cache entonces vuelvo al login.
    if (!validarSession()) {
        location.href = "index.php";
        return;
    }

    llenarMarcasRepuesto();
    llenarSubrubros();
    llenarMarcasVehiculos();
    llenarModelos();
    llenarMotor();
    llenarAnio();
    generarMenuOperaciones();
    generarBotonListaArticulos();
    generarBotonMiCarrito();
    getClientes();
}

/**
 * Valida si la sesión está activa.
 * @returns bool
 */
function validarSession () {
    let aSesion = sessionStorage.getItem("derweb_sesion");
    if (aSesion == null)
        return false;
    return true;
}


/**
 * Permite llenar el selector con las marcas de repuestos.
 */
function llenarMarcasRepuesto() {
    var objSelectorMarcas = document.getElementById("select_marca_repuesto");
    var objCatalogo = new Catalogo();
    var aMarcas = objCatalogo.getMarcas();
    
    aMarcas.forEach(xElement => {
        addSelectOption(objSelectorMarcas, xElement.id, xElement.descripcion);
    });
}

/**
 * Permite llenar el selector con los subrubros
 */
function llenarSubrubros() {
    var objSelectRepuesto = document.getElementById("select_repuesto");
    var objCatalogo = new Catalogo();
    var aSubrubros = objCatalogo.getSubrubros();

    aSubrubros.forEach(xelement => {
        addSelectOption(objSelectRepuesto, xelement.id, xelement.descripcion);
    });
}

/**
 * Pemite llenar el select de marcas de vehículos
 */
function llenarMarcasVehiculos() {
    var objSelectMarcasVehiculos = document.getElementById("select_marca_vehiculo");
    addSelectOption(objSelectMarcasVehiculos, 0, "En Construccion");
    // TODO: Darle funcionalidad cuando se cargue la base de aplicaciones.
}

/**
 * Permite llenar el select de modelos.
 */
function llenarModelos() {
    var objSelectModelos = document.getElementById("select_modelo");
    addSelectOption(objSelectModelos, 0, "En Construcción")
    // TODO: Darle funcionalidad cuando se cargue la base de aplicaciones.
}

/**
 * Permite llenar el selector de motores.
 */
function llenarMotor() {
    var objSelectMotor = document.getElementById("select_motor");
    addSelectOption(objSelectMotor, 0, "En Construcción");
    // TODO: Darle funcionalidad cuando se cargue la base de aplicaciones.
}

/**
 * Permite llenar el selector de año.
 */
function llenarAnio() {
    var objSelectAnio = document.getElementById("select_anio");
    addSelectOption(objSelectAnio, 0, "En Construcción");
    // TODO: Darle funcionalidad cuando se cargue la base de aplicaciones.
}

/**
 * Genera el menú principal a partir del componente Menus.
 */
function generarMenuOperaciones() {
    var objMenu = new MenuComponent("menu-container");
    objMenu.generarMenu();
}

/**
 * Crea el componente Lista de Artículos.
 */
function generarBotonListaArticulos() {
    objListaArticulo.generateComponent();
}

/**
 * Permite mostrar los artículos seleccionado desde la lista de artículos.
 * @param {int} xidRubro Id. de rubro seleccionado
 * @param {int} xidSubrubro Id. Subrubro seleccionado
 */
function mostrar_articulos(xidRubro, xidSubrubro) {
    let objAppContainer = document.getElementById("app-container");
    objAppContainer.classList.remove("container-miPerfil");
    var objGUI = new CatalogoGUIComponent("app-container");
    var aParametros;
    objGUI.generateComponent();
    objListaArticulo.abrirCerrarListaArticulos("close");

    // Armo el parámetro para buscar por rubro y subrubro.
    aParametros = {
        "api_url": app.getUrlApi("catalogo-articulos-getByRubroAndSubrubro"),
        "values": { 
            "id_rubro": parseInt(xidRubro),
            "id_subrubro": parseInt(xidSubrubro)
        }
    };

    // Traigo los resultados
    objGUI.getArticulosResultadoBusqueda(aParametros);
}

/**
 * Permite la búsqueda por frase.
 */
function buscarPorFrase () {
    let valor = document.getElementById("txtValorBuscado").value;
    if(valor.length > 3){
        var objGUI = new CatalogoGUIComponent("app-container");
        var aParametros;
        objGUI.generateComponent();
    
        aParametros = {
            "api_url": app.getUrlApi("catalogo-articulos-getByFranse"),
            "values": {
                "frase": document.getElementById("txtValorBuscado").value
            }
        };
    
        objGUI.getArticulosResultadoBusqueda(aParametros, true);  
    } else {
        swal("Oops!", "Debe escribir al menos 4 caracteres", "error");
    }
}

/**
 * Crea el botón mi carrito en el toolbar.
 */
function generarBotonMiCarrito() {
    var objBtnMiCarrito = new BotonMiCarritoComponent("boton-mi-carrito-container");
    objBtnMiCarrito.setJSFunction("abrir_mi_carrito");
    objBtnMiCarrito.generateComponent();
}

let objTxtValorBuscado = document.getElementById("txtValorBuscado");
objTxtValorBuscado.addEventListener("keypress",(e) => {
    if(e.keyCode === 13) {
        buscarPorFrase ();
    }
})

function esconderHamburguesa() {
    let objHambur = document.getElementById("menu-options");
    let objBotonLista = document.getElementById("btnPushListaArticulo");
    objBotonLista.addEventListener ("click", () => {
        objHambur.style.display = "none";
        console.log(document.querySelector(".fa-xmark"))
        document.querySelector("#botonHambur").className = "fas fa-bars";
    })

}

/**
 * Esta función permite desplegar mi perfil.
 */
function miPerfil() {
    let objMiPerfil = new MiPerfil("app-container");
    objMiPerfil.generateComponent();
    let obj = document.getElementById("reset-pass");
    obj.addEventListener("click",cambiarContraseña);
}

/**
 * Obtiene la lista de clientes del venededor actualmente logueado.
 */
function getClientes() {
    let objGrid = new LFWDataGrid("app_grid_container", "id");
    let objApp = new App();
    let objCacheUtils = new CacheUtils("derweb", false);
    let url = "";

    objGrid.setAsociatedFormId("formulario");
    objGrid.setPermitirOrden(true);
    objGrid.setPermitirFiltros(true);
    objGrid.setPermitirEditarRegistro(true);
    objGrid.setEditJavascriptFunctionName("entrar_al_cliente");
    objGrid.setIconEditButton("fa-arrow-right-to-bracket");
    objGrid.setEditButtonTitle("Entrar al cliente");

    objGrid.agregarColumna("N° Cliente", "id", "numeric", 100, false);
    objGrid.agregarColumna("Cód. Suc.", "codsuc", "string", 100);
    objGrid.agregarColumna("Razón Social", "nombre", "string");
    objGrid.agregarColumna("C.U.I.T", "cuit", "string", 200);

    aSesion = objCacheUtils.get("sesion");
    url = objApp.getUrlApi("app-entidades-getClientesByVendedor") + "?id_vendedor=" + aSesion["id_vendedor"];
    getApifetch(url, xresponse => {
        xresponse.forEach(xelement => {
            objGrid.agregarFila(xelement);

        });
        objGrid.refresh();
    });
}

/**
 * Permite entrar al cliente seleccionado para hacer pedidos..
 * @param {int} xid Id. de cliente.
 */
function entrar_al_cliente(xid) {
    let objCache = new CacheUtils("derweb");
    let aSesion = objCache.get("sesion");
    console.log(aSesion);
    aSesion["id_cliente"] = xid;
    objCache.set("sesion", aSesion);

    // Abro el derweb con el cliente seleccionado en una pestaña
    // a parte.
    window.open("main-clientes.php", "_blank");
}

/**
 * Permite mostrar la pantalla de los pedidos pendientes de confirmar
 * por clientes a los vendedores.
 */
function ver_pedidos_pendientes() {
    objPedidos.getPedidosPendientes();
}

/**
 * Permite mostrar los ítems del pedido.
 * @param {int} xidpedido 
 */
function entrar_al_pedido(xidpedido) {
    objPedidos.entrarAlPedido(xidpedido);
}

/**
 * Permite editiar un ítem del pedido seleccionado actualmente.
 * @param {int} xidpedido_item 
 */
function editar_pedido(xidpedido_item) {
    objPedidos.editarItem(xidpedido_item);
}