// Inicio la aplicación
var app = new App();
var objListaArticulo = new ListaArticuloComponent("lista-articulos-container");
var objMiCarrito = new MiCarritoModalComponent("mi-carrito");

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

    mostrarNumCliente();
    llenarMarcasRepuesto();
    llenarSubrubros();
    llenarMarcasVehiculos();
    llenarModelos();
    llenarMotor();
    llenarAnio();
    generarMenuOperaciones();
    generarBotonListaArticulos();
    generarCarrusel();
    generarCarruselFotter();
    generarBotonMiCarrito();
    iniciarlizarComponenteMiCarrito();
    //esconderHamburguesa();
    setInterval(cerrarSession, 7200000);
}


function mostrarNumCliente() {
    let storage = sessionStorage.getItem("derweb_sesion");
    storage = JSON.parse(storage);
    let objDivNumCli = document.getElementById("num-cliente");
    //objDivNumCli.innerHTML = "<a href='javascript:miPerfil()'><span>" + storage.id_sucursal + "</span></a>";

    let objApp = new App();
    (new APIs()).call(objApp.getUrlApi("app-entidades-getSucursalesByEntidad"), "id_entidad=" + storage.id_cliente, "GET", (xdatos) => {
        xdatos.forEach((xitem) => {
            if(storage.id_sucursal==xitem.id){
                objDivNumCli.innerHTML = "<a href='javascript:miPerfil()'><span>" + xitem.nombre + "</span></a>";
                storage["nombre_suc"] = xitem.nombre;
                sessionStorage.setItem("derweb_sesion", JSON.stringify(storage));
            }
        });
    }); 

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
 * Crea el componente Carrusel.
 */
function generarCarrusel() {  
    (new CarruselComponent("carrusel-container")).generarCarrusel();
}

/**
 * Permite mostrar los artículos seleccionado desde la lista de artículos.
 * @param {int} xidRubro Id. de rubro seleccionado
 * @param {int} xidSubrubro Id. Subrubro seleccionado
 */
function mostrar_articulos(xidRubro, xidSubrubro) {
    document.getElementById("transparente").style = "display:none";
    sessionStorage.setItem("derweb_id_subrubro_seleccionado", xidSubrubro);
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
    if(valor.length > 1){
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
 * Crea el componente Carrusel footer.
 */
function generarCarruselFotter() {  
    // ESTA FUNCION LA LLAMO DENTRO DE LA FUNCION WINDOW.ONLOAD Q ESTA EN LA LINEA 19
    let xId= 'carrusel-container-footer';
    var objCarruselFooter = new CarruselFooterComponent(xId);

   const url_bannerPortada = app.getUrlApi("catalogo-articulos_destacados-get");
   fetch(url_bannerPortada).then(xresponse => xresponse.json()) 
        .then(data => {
            objCarruselFooter.generateComponent(data);

            new Glider(document.querySelector('.carousel__lista'), {
                slidesToShow: 5,
                slidesToScroll: 1,
                draggable: true,  //ESTO HACE Q EL CARRUSEL SEA ARRASTRABLE
                dots: '.carousel__indicadores',
                arrows: {
                    prev: '.carousel__anterior',
                    next: '.carousel__siguiente'
                },
                responsive: [
                    {
                        // screens greater than >= 775px
                        breakpoint: 1080,
                        settings: {
                        // Set to `auto` and provide item width to adjust to viewport
                        slidesToShow: 5,
                        slidesToScroll: 1
                        }
                    },
                    {
                        // screens greater than >= 775px
                        breakpoint: 800,
                        settings: {
                        // Set to `auto` and provide item width to adjust to viewport
                        slidesToShow: 4,
                        slidesToScroll: 1
                        }
                    },
                    {
                        // screens greater than >= 775px
                        breakpoint: 480,
                        settings: {
                        // Set to `auto` and provide item width to adjust to viewport
                        slidesToShow: 3,
                        slidesToScroll: 1
                        }
                    },
                    {
                        // screens greater than >= 775px
                        breakpoint: 400,
                        settings: {
                        // Set to `auto` and provide item width to adjust to viewport
                        slidesToShow: 2,
                        slidesToScroll: 2
                        }
                    },{
                        // screens greater than >= 1024px
                        breakpoint: 200,
                        settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                        }
                    }
                ]
            });
        });
}

/**
 * Crea el botón mi carrito en el toolbar.
 */
function generarBotonMiCarrito() {
    var objBtnMiCarrito = new BotonMiCarritoComponent("boton-mi-carrito-container");
    objBtnMiCarrito.setJSFunction("abrir_mi_carrito");
    objBtnMiCarrito.generateComponent();
}

/**
 * Crea el componente mi carrito pero lo deja oculto.
 */
function iniciarlizarComponenteMiCarrito() {
    objMiCarrito.setFunctionNameVaciarCarrito("vaciar_carrito");
    objMiCarrito.generateComponent();
}

/**
 * Permite vaciar mi carrito al hacer clic en "Vaciar mi carrito"
 */
function eliminar_item_mi_carrito(xidpedido, xId) {
    let url =  app.getUrlApi("catalogo-pedidos-eliminarItem");
    let pedidoStorage = JSON.parse(localStorage.getItem("derweb-mi-carrito"));
    console.log(pedidoStorage.items.length);
    objMiCarrito.close();
    if(pedidoStorage.items.length>1) {
        let objCarrito = new MiCarritoModalComponent;
        objCarrito.eliminar_item_carrito(url, xidpedido, xId);
    } else {
        vaciar_carrito();
    }
    // let objCarrito = new MiCarritoModalComponent;
    // objCarrito.eliminar_item_carrito(url, xidpedido, xId);
 }

/**
 * Permite vaciar mi carrito al hacer clic en "Vaciar mi carrito"
 */
function vaciar_carrito() {
    let idPedidoActual = JSON.parse(localStorage.getItem("derweb-mi-carrito"));
    let url =  app.getUrlApi("catalogo-pedidos-vaciarCarrito");
    objMiCarrito.close();
    let objCarrito = new MiCarritoModalComponent;
    objCarrito.vaciarMiCarrito(url, idPedidoActual.id_pedido);
}

/**
 * Abre el modal para mostrar el pedido actual (mi carrito)
 */
function abrir_mi_carrito() {
    var objGrillaMiCarrito = new CarritoGridComponent("grd-pedido", "mi-carrito-contenido");
    objGrillaMiCarrito.setEliminarFunctionName("eliminar_item_mi_carrito");
    objGrillaMiCarrito.generateComponent();
    objMiCarrito.open();
    /*const aMiCarrito = JSON.parse(localStorage.getItem("derweb-mi-carrito"));
    const objSpan = document.querySelector("#mi-carrito #subtotalMicarrito span");
    objSpan.textContent = "Subtotal _ _ _ _ $ " + aMiCarrito.total_pedido; console.log('pasa 2')*/
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
    let objBotonMenu = document.querySelector("#botonMenu");
    let objBtnPush =  document.querySelector("#btnPushMenu");
    objBotonLista.addEventListener ("click", () => {
        objHambur.style.display = "none";
        document.querySelector("#botonHambur").className = "fas fa-bars";
    })
}

/**
 * Genera el input para poner el nuevo valor cantidad a cambiar. el parametro q recibe es el id item
 */
function editar_carrito(xId_pedItems){
    swal({
        content: {
          element: "input",
          attributes: {
            placeholder: "CANTIDAD",
            type: "number",
            id: "cambiar_cantidad"
          },
        },
    });
    let objbotonCambiarCdad = document.querySelector(".swal-button--confirm");
    objbotonCambiarCdad.addEventListener ("click", () => {
        let xCantidad = document.getElementById("cambiar_cantidad").value;
        if(xCantidad >0) {
            (new MiCarritoModalComponent).editarCarrito(xCantidad, xId_pedItems);
        } else swal({
            icon: "error",
            text: "Cantidad Invalida",
          });
        
    })
}


/**
 * Esta función permite mostrar la pantalla mi perfil.
 */
function miPerfil() {
    let objMiPerfil = new MiPerfil("app-container");
    objMiPerfil.generateComponent();
    let obj = document.getElementById("reset-pass");
    obj.addEventListener("click",cambiarContraseña);  // ESTE EVENTO GENERA EL FORM DE CAMBIO DE CLAVE.
}

/**
 * Esta función permite crear la ficha de articulo, esta funcion es llamada desde el DOM(objAnchorCodigo) que se crea.
 * @param {int} xid Id. Articulo.
 * @param {object} panelesOpciones objeto que lleva el estado de los check de los paneles de opciones de precios.
 */
function crearFicha(xid, oPrecios) {
    let panelesOpciones = {
        precioLista: document.querySelector("#opcion-lista-precio").checked,
        precioCosto: document.querySelector("#opcion-costo").checked,
        precioVenta: document.querySelector("#opcion-venta").checked 
    };
    
    new FichaArticulo().generateComponent(xid, panelesOpciones, oPrecios);
}

/**
 * Esta función permite abrir el centro de noticias.
 */
function abrirCtroNot() {
    const oCtroNot = new CtroNot("#app-container");
    oCtroNot.generateComponent();
}

/**
 * Esta función permite abrir mis pedidos.
 */
function abrirMisPedidos() {
    const oMisPedidos = new MisPedidos("#app-container");
    oMisPedidos.generateComponent();
}

/**
 * Esta función permite abrir detalle de mi pedido.
 */
function detallePedido(jsonPed) {console.log(jsonPed)
    //console.log('llega')
    const miPedidoDet = decodeURIComponent(jsonPed);
    //console.log(JSON.parse(jsonPed))
    //const miPedidoDet = jsonPed;
    sessionStorage.setItem("derweb_detalleMiPed", miPedidoDet);
    const oDetallePedido = new Detalle_MisPedidos("#app-container");
    oDetallePedido.generateComponent();
}