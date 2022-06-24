// Inicio la aplicación
var app = new App();
var objListaArticulo = new ListaArticuloComponent("lista-articulos-container");

app.init();

/**
 * Evento onLoad de la página.
 * Este evento se ejecuta al cargar la página.
 */
window.onload = () => {   
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

    const url_bannerPortada = "services/banner_portada/get";
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
        "api_url": "http://localhost/derweb/app/services/articulos/getByRubroAndSubrubro",
        "values": { 
            "id_rubro": parseInt(xidRubro),
            "id_subrubro": parseInt(xidSubrubro)
        }
    };

    // Traigo los resultados
    objGUI.getArticulosResultadoBusqueda(aParametros);
}

function buscarPorFrase () {
    var objGUI = new CatalogoGUIComponent("app-container");
    var aParametros;
    objGUI.generateComponent();

    aParametros = {
        "api_url": "http://localhost/derweb/app/services/articulos/getByFrase",
        "values": {
            "frase": document.getElementById("txtValorBuscado").value
        }
    };

    objGUI.getArticulosResultadoBusqueda(aParametros, true);
}

/**
 * Crea el componente Carrusel footer.
 */
 function generarCarruselFotter() {  
    // ESTA FUNCION LA LLAMO DENTRO DE LA FUNCION WINDOW.ONLOAD Q ESTA EN LA LINEA 19
    let xId= 'carrusel-container-footer';
    var objCarruselFooter = new CarruselFooterComponent(xId);

   const url_bannerPortada = "services/articulos-destacados/get";
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
