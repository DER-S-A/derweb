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
    }

    /**
     * Crea el componente Lista de artículos.
     */
    generateComponent() {
        this.__createButton();
        this.__crearOpcionesRubros();

        // Creo el evento click del botón
        document.getElementById("btnPushListaArticulo").addEventListener("click", () => {
            this.__abrirCerrarListaArticulos("open");
        })

        // Agrego evento para el botón cerrar
        document.getElementById("btnCerrarListaArticulo").addEventListener("click", () => {
            this.__abrirCerrarListaArticulos("close");
        });
    }

    /**
     * Abre o cierra la lista de rubros.
     * @param {string} xcommand Establece el comando de apertura o cierre. "open": Abrir / "close": cerrar.
     */
    __abrirCerrarListaArticulos(xcommand) {
        let objRubrosContainer = document.getElementById("rubros-container");
        if (xcommand === "open")
            objRubrosContainer.style.display = "block";
        else
            objRubrosContainer.style.display = "none";
    }

    /**
     * Crea el botón lista artículos.
     */
    __createButton() {
        var objButton = document.createElement("button");
        
        objButton.id = "btnPushListaArticulo";
        objButton.name = "btnPushListaArticulo";
        objButton.type = "button";
        objButton.innerHTML = "<i class='fa-solid fa-list'></i> LISTA DE ARTICULOS";
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
        var objDivRubros = document.createElement("div");
        objDivRubros.id = "rubros-container";
        objDivRubros.classList.add("lista-articulos-opciones-rubros");

        var objLogo = this.__insertHeaderListaArticulos();
        objDivRubros.appendChild(objLogo);

        aRubros = objCatalogo.getRubros();

        // Armo la lista de rubros
        var objUl = document.createElement("ul");
        aRubros.forEach((xrubro) => {
            let objLi = document.createElement("li");
            let objDivContainerOption = document.createElement("div");
            let objDivTexto = document.createElement("div");
            let objDivIcono = document.createElement("div");
            let objLink = document.createElement("a");

            objLink.href = "javascript: desplegar_subrubros(" + xrubro.id + ");";

            objDivContainerOption.classList.add("opcion-rubro-container");
            objDivTexto.classList.add("opcion-rubro-texto-container");
            objDivIcono.classList.add("opcion-rubro-icono-container");

            objDivTexto.innerHTML = "<span>" + xrubro.descripcion + "</span>";
            objDivIcono.innerHTML = "<i class=\"fa-solid fa-angle-right\"></i>";

            objDivContainerOption.appendChild(objDivTexto);
            objDivContainerOption.appendChild(objDivIcono);
            objLink.appendChild(objDivContainerOption);
            objLi.appendChild(objLink);
            objUl.appendChild(objLi);
        });

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
}