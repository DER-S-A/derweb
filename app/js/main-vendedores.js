// Inicio la aplicación
var app = new App();
var objListaArticulo = new ListaArticuloComponent("lista-articulos-container");
var objMiCarrito = null;

app.init();

/**
 * Evento onLoad de la página.
 * Este evento se ejecuta al cargar la página.
 */
window.onload = () => {
    // Si la sesión no está en cache entonces vuelvo al login.
    if (!validarSession()) {
        location.href = "index.php";
        return;
    }

    //llenarMarcasRepuesto();
    //llenarSubrubros();
    //llenarMarcasVehiculos();
    //llenarModelos();
    //llenarMotor();
    //llenarAnio();
    //generarBotonListaArticulos();
    //generarBotonMiCarrito();
    generarMenuOperaciones();
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
    let objCacheUtils = new CacheUtils("derweb", false);
    var templateDataTable = document.getElementById("app_grid_container");
    templateDataTable.innerHTML = " \
        <table id='tabla-clientes' class='table table-bordered table-hover' style='font-size: 11px; width: 100%' data-page-length='10'> \
            <thead style='background: #009ada; color: white;'>\
                <th>Cliente</th>\
                <th>Razón Social</th>\
                <th>CUIT</th>\
                <th>Acciones</th>\
            </thead>\
        </table>";

    var dataTableClientes = $("#tabla-clientes").DataTable({
        searching: true,
        paging: true,
        responsive: true,
        scrollY: 260
    });

    // Llamo a la API que devuelve la lista de clientes del vendedor y 
    // lleno la grilla.
    aSesion = objCacheUtils.get("sesion");
    url = (new App()).getUrlApi("app-entidades-getClientesByVendedor");
    (new APIs()).call(
        url, 
        "id_vendedor=" + aSesion["id_vendedor"], 
        "GET", 
        response => {
            response.forEach(element => {
                let link = "<a href='javascript:entrar_al_cliente(" + element.id + ")'>\
                    <i class='fa fa-arrow-right-to-bracket fa-lg'></i></a>"
                dataTableClientes.row.add([element.codent, element.nombre, element.cuit, link]);
            });
            dataTableClientes.draw();
        }
    );    
}

/**
 * Permite entrar al cliente seleccionado para hacer pedidos..
 * @param {int} xid Id. de cliente.
 */
function entrar_al_cliente(xid) {
    // Abro el derweb con el cliente seleccionado en una pestaña
    // a parte.   
    let xparametrosxUrl = "filter=id_entidad=" + xid;
    
    (new APIs()).call(app.getUrlApi("app-entidades-sucursales"), xparametrosxUrl, "GET", (xdatos) => {
        console.log(xdatos);
        
        let idSucursal;
        let objCache = new CacheUtils("derweb");
        let aSesion = objCache.get("sesion");
        aSesion.id_cliente = xid;
        console.log(aSesion);
        if(xdatos.length>1) {
            const select = document.createElement("select");
            select.id = 'suc-pantalla-vendedor';
            xdatos.forEach((sucursal) => {
                const option = document.createElement("option");
                option.value = sucursal.id;
                option.text = sucursal.nombre;
                select.append(option);
            });
            swal({
                title: "Selecciona una sucursal",
                content: select,
                icon: "info",
                buttons: true,
            })
            .then(valor => {
                if(valor) {
                    idSucursal = document.getElementById("suc-pantalla-vendedor").value;
                    aSesion.id_sucursal = parseInt(idSucursal);
                    objCache.set("sesion", aSesion);
                    console.log(idSucursal);
                    window.open("main-clientes.php", "_blank");
                }
            })
        } else {
            idSucursal = xdatos[0].id;
            aSesion.id_sucursal = idSucursal;
            objCache.set("sesion", aSesion);
            console.log(idSucursal);
            window.open("main-clientes.php", "_blank");
        }
    });     
}

/**
 * Permite mostrar la pantalla de los pedidos pendientes de confirmar
 * por clientes a los vendedores.
 */
function ver_pedidos_pendientes() {
    let objPedidosPendientes = new PedidosPendientes();
    objPedidosPendientes.getPedidosPendientes(response => {
        objPedidosPendientes.mostrarGrillaPedidosPendientes(response);        
    });
}

/**
 * Permite mostrar los ítems del pedido.
 * @param {int} xidpedido 
 */
function entrar_al_pedido(xidpedido) {
    (new EdicionPedidosPendientes()).entrarAlPedido(xidpedido);
}

/**
 * 
 * Permite editiar un ítem del pedido seleccionado actualmente.
 * @param {int} xidpedido_item 
 */
function editar_pedido(xidpedido_item) {
    (new EdicionPedidosPendientes()).editarItem(xidpedido_item);
}

/**
 * Permite ingresar a la operación de pedidos rápidos.
 */
function ingresar_pedidos_rapido() {
    reset_cli_suc();
    sessionStorage.removeItem('ipr_grid_items_rows');
    let objIngresoPedidosRapido = new IngresoPedidosRapidoGUI("app-container");
    objIngresoPedidosRapido.generateComponent();
}

function reset_cli_suc() {
    let aSesion = new CacheUtils("derweb", false).get("sesion");
    if(aSesion.id_cliente != null || aSesion.id_sucursal != null) {
        aSesion.id_cliente = null;
        aSesion.id_sucursal = null;
        new CacheUtils("derweb", false).set("sesion", aSesion);
    }
}