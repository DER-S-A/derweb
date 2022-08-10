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
    esconderHamburguesa();
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
     // ESTA FUNCION LA LLAMO DENTRO DE LA FUNCION WINDOW.ONLOAD Q ESTA EN LA LINEA 9
    const idComp = "carrusel-container"
     var objCarrusel = new CarruselComponent(idComp);

    const url_bannerPortada = "services/banner_portada.php/get";
    fetch(url_bannerPortada).then(xresponse => xresponse.json()) 
    .then(data => {
        objCarrusel.generateComponent(data, idComp);
    })
}

/**
 * Permite mostrar los artículos seleccionado desde la lista de artículos.
 * @param {int} xidRubro Id. de rubro seleccionado
 * @param {int} xidSubrubro Id. Subrubro seleccionado
 */
function mostrar_articulos(xidRubro, xidSubrubro) {
    var objGUI = new CatalogoGUIComponent("app-container");
    var aParametros;
    objGUI.generateComponent();
    objListaArticulo.abrirCerrarListaArticulos("close");

    // Armo el parámetro para buscar por rubro y subrubro.
    aParametros = {
        "api_url": "services/articulos.php/getByRubroAndSubrubro",
        "values": { 
            "id_rubro": parseInt(xidRubro),
            "id_subrubro": parseInt(xidSubrubro)
        }
    };

    // Traigo los resultados
    objGUI.getArticulosResultadoBusqueda(aParametros);
}

function buscarPorFrase () {
    let valor = document.getElementById("txtValorBuscado").value;
    if(valor.length>3){
        var objGUI = new CatalogoGUIComponent("app-container");
        var aParametros;
        objGUI.generateComponent();
    
        aParametros = {
            "api_url": "services/articulos.php/getByFrase",
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

   const url_bannerPortada = "services/articulos-destacados.php/get";
   fetch(url_bannerPortada).then(xresponse => xresponse.json()) 
        .then(data => {
            objCarruselFooter.generateComponent(data);

                new Glider(document.querySelector('.carousel__lista'), {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    draggable: true,  //ESTO HACE Q EL CARRUSEL SEA ARRASTRABLE
                    dots: '.carousel__indicadores',
                    arrows: {
                        prev: '.carousel__anterior',
                        next: '.carousel__siguiente'
                    },
                    responsive: [
                        {
                            // screens greater than >= 775px
                            breakpoint: 450,
                            settings: {
                            // Set to `auto` and provide item width to adjust to viewport
                            slidesToShow: 2,
                            slidesToScroll: 2
                            }
                        },{
                            // screens greater than >= 1024px
                            breakpoint: 800,
                            settings: {
                            slidesToShow: 4,
                            slidesToScroll: 4
                            }
                        }
                    ]
                });
        })
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
    objMiCarrito = new MiCarritoModalComponent("mi-carrito");
    objMiCarrito.generateComponent();
}

/**
 * Abre el modal para mostrar el pedido actual (mi carrito)
 */
function abrir_mi_carrito() {
    var objGrillaMiCarrito = new CarritoGridComponent("grd-pedido", "mi-carrito-contenido");
    objGrillaMiCarrito.setEliminarFunctionName("eliminar_item_mi_carrito");
    objGrillaMiCarrito.generateComponent();
    objMiCarrito.open();

    // Agrego el evento click de finalizar pedido
    document.getElementById("btn-finalizar-pedido").addEventListener("click", () => {
        // Mando a marcar el pedido como confirmado.
        let aPedidoActual = JSON.parse(localStorage.getItem("derweb-mi-carrito"));
        let url =  "services/pedidos.php/confirmarPedido";
        let parametros = "?sesion=" + sessionStorage.getItem("derweb_sesion") + "&id_pedido=" + parseInt(aPedidoActual["id_pedido"]);

        objMiCarrito.close();
        url = url + parametros;
        console.log(url);
        
        fetch(url, {
            method: "PUT",
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
    }, false);
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

/*
* DESPLIEGA MENU PERFIL
*/
let objBPerfil = document.getElementById("btn_perfil");
let abrirCerrar=0;

objBPerfil.addEventListener("click",()=> {
    let objMPerfil = document.querySelector(".menu-perfil");
    if(abrirCerrar===0){
        objMPerfil.style.display = "flex";
        abrirCerrar=1;
    } else {
        objMPerfil.style.display = "none";
        abrirCerrar=0;
    }
})

let objCierreSession = document.getElementById("cierreSession");
objCierreSession.addEventListener("click",()=> {
    sessionStorage.removeItem("derweb_sesion");
})

/**
 * Esta función permite desplegar mi perfil.
 */
function miPerfil() {
    let objMiPerfil = new MiPerfil("app-container");
    objMiPerfil.generateComponent();
}
