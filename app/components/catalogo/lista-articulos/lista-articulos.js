/**
 * Clase ListaArticuloComponent
 * Descripción: Contiene la clase que arma el componente de lista de artículos.
 */

class ListaArticuloComponent {
    /**
     * Constructor de clase
     * @param {string} xidContainer Id. del contenedor para el botón lista articulos.
     */
    constructor(xidContainer) {
        this.objContainerListaArticulo = document.getElementById(xidContainer);
        this.idRubroSeleccionado = 0;
    }

    /**
     * Crea el componente Lista de artículos.
     */
    generateComponent() {
        this.__createButton();
        this.__crearOpcionesRubros();

        // Creo el evento click del botón
        document.getElementById("btnPushListaArticulo").addEventListener("click", () => {
            this.abrirCerrarListaArticulos("open");
        })

        // Agrego evento para el botón cerrar
        document.getElementById("btnCerrarListaArticulo").addEventListener("click", () => {
            this.abrirCerrarListaArticulos("close");
        });

        document.getElementById("btnPushListaArticulo").addEventListener("keyup", (e)=>{
            if(e.keyCode === 27){
                this.abrirCerrarListaArticulos("close");
            }
        })
    }

    /**
     * Abre o cierra la lista de rubros.
     * @param {string} xcommand Establece el comando de apertura o cierre. "open": Abrir / "close": cerrar.
     */
    abrirCerrarListaArticulos(xcommand) {
        let objRubrosContainer = document.getElementById("rubros-container");
        if (xcommand === "open")
            objRubrosContainer.style.display = "block";
        else {
            let objListaSubrubroAbierta = document.getElementById("lista-subrubros-container");
            if (objListaSubrubroAbierta !== null)
                document.body.removeChild(objListaSubrubroAbierta);

            objRubrosContainer.style.display = "none";            
        }
    }

    /**
     * Crea el botón lista artículos.
     */
    __createButton() {
        var objButton = document.createElement("button");
        
        objButton.id = "btnPushListaArticulo";
        objButton.name = "btnPushListaArticulo";
        objButton.type = "button";
        objButton.innerHTML = /*<i class='fa-solid fa-list'></i>*/ "<i id='botonHambur' class='fas fa-bars'></i>";
        objButton.classList.add("btn");
        objButton.classList.add("lista-articulos-button");
                
        this.objContainerListaArticulo.appendChild(objButton);

    }

    /**
     * Creo las opciones de rubros
     */
    __crearOpcionesRubros() {
        var objCatalogo = new Catalogo();
        var aRubros = new Array();
        var objUl;
        var objDivRubros = document.createElement("div");
        objDivRubros.id = "rubros-container";
        objDivRubros.classList.add("lista-articulos-opciones-rubros");

        var objLogo = this.__insertHeaderListaArticulos();
        objDivRubros.appendChild(objLogo);

        aRubros = objCatalogo.getRubros();
        objUl = this.crearLista(aRubros, true);

        // Enlazo las etiquetas
        objDivRubros.appendChild(objUl);
        document.body.appendChild(objDivRubros);
    }

    /**
     * Inserta el header de la lista de artículos
     * @returns {DOM} Devuelve el objeto header
     */
    __insertHeaderListaArticulos() {
        var objLogoContainer = document.createElement("div");
        var objImagen = document.createElement("img");
        var objBotonCerrar = document.createElement("button");
        var objIconoCerrar = document.createElement("i");
        
        objBotonCerrar.id = "btnCerrarListaArticulo";
        objBotonCerrar.name = "btnCerrarListaArticulo";
        objBotonCerrar.classList.add("btn");
        objBotonCerrar.classList.add("lista-articulo-boton-cerrar");
        objIconoCerrar.classList.add("fa-solid");
        objIconoCerrar.classList.add("fa-xmark");
        objBotonCerrar.appendChild(objIconoCerrar);

        objLogoContainer.classList.add("lista-articulos-logo-container");
        objImagen.src = "assets/imagenes/logo.png";
        objLogoContainer.appendChild(objImagen);
        objLogoContainer.appendChild(objBotonCerrar);
        return objLogoContainer;
    }

    /**
     * Permite crear la lista UL.
     * @param {array} aDatos Datos a mostrar en la lista
     * @param {boolean} xesRubro Indica si la lista de rubros.
     * @returns 
     */
    crearLista(aDatos, xesRubro) {
        var objUl = document.createElement("ul");
        objUl.classList.add("lista-articulos-ul");
        aDatos.forEach((xrow) => {
            let objLi = document.createElement("li");
            let objDivContainerOption = document.createElement("div");
            let objDivTexto = document.createElement("div");
            let objDivIcono = document.createElement("div");
            let objLink = document.createElement("a");
            

            // Si es la lista de rubros, entonces hago el llamado a la función para desplegar
            // los subrubros del rubro seleccionado, en caso contrario, hago el llamado a la
            // función para redireccionar a la lista de artículos.
            if (xesRubro)
                objLink.href = "javascript:desplegar_subrubros(" + xrow.id + ");";
            else
                objLink.href = "javascript:mostrar_articulos(" + sessionStorage.getItem("derweb_id_rubro_seleccionado") + ", " +  xrow.id + ");";
                //sessionStorage.setItem("derweb_id_rubro_seleccionado", xrow.id);

            objDivContainerOption.classList.add("opcion-rubro-container");
            objDivTexto.classList.add("opcion-rubro-texto-container");
            objDivIcono.classList.add("opcion-rubro-icono-container");

            objDivTexto.innerHTML = "<span>" + xrow.descripcion + "</span>";
            objDivIcono.innerHTML = "<i class=\"fa-solid fa-angle-right\"></i>";

            objDivContainerOption.appendChild(objDivTexto);

            // Si es la lista de rubros, entonces agrego el ícono flechita.
            if (xesRubro)
                objDivContainerOption.appendChild(objDivIcono);

            objLink.appendChild(objDivContainerOption);
            objLi.appendChild(objLink);
            objUl.appendChild(objLi);
        });
        
        return objUl;
    }
}

/**
 * Esta función se ejecuta al hacer click sobre un rubro.
 * @param {int} xid_subrubro 
 */
function desplegar_subrubros(xid_rubro) {
    var objCatalogo = new Catalogo();
    var objListaArticulo = new ListaArticuloComponent();
    var objUl;
    var aSubrubros = objCatalogo.getSubrubrosByRubro(xid_rubro);
    var objDivSubrubrosContainer = document.createElement("div");
    var objTitulo = document.createElement("h5");
    var rubroSeleccionado = objCatalogo.getRubros("id = " + xid_rubro);
    var objListaSubrubroAbierta = document.getElementById("lista-subrubros-container");
    

    sessionStorage.setItem("derweb_id_rubro_seleccionado", xid_rubro);

    // Verifico si se abrió otra lista de subrubro anteriormente para eliminarla
    if (objListaSubrubroAbierta !== null)
        document.body.removeChild(objListaSubrubroAbierta);

    objTitulo.classList.add("titulo-rubro-seleccionado");
    objTitulo.innerHTML = rubroSeleccionado[0].descripcion;
    
    objDivSubrubrosContainer.id = "lista-subrubros-container";
    objDivSubrubrosContainer.classList.add("lista-articulos-subrubros-container");
    objDivSubrubrosContainer.style.display = "block";

    objUl = objListaArticulo.crearLista(aSubrubros, false);

    objDivSubrubrosContainer.appendChild(objTitulo);
    objDivSubrubrosContainer.appendChild(objUl);
    document.body.appendChild(objDivSubrubrosContainer);
    window.scrollTo(0, 0);
}