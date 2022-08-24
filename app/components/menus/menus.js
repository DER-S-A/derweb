/**
 * Contiene la clase para generar el menú hamburguesa desplegable.
 */

class MenuComponent {
    /**
     * Constructor de clase
     * @param {string} xidContainer Id. del contenedor en donde se debe crear el componente.
     */
    constructor(xidContainer) {
        this.menuContainer = document.getElementById(xidContainer);
        this.__objApp = new App();
    }

    /**
     * Genera el menú hamburguesa principal.
     */
    generarMenu() {
        getTemplate("components/menus/menus.html", (xhtml) => {
            this.menuContainer.innerHTML = xhtml;
            this.__leerOpciones();

            document.getElementById("btnPushMenu").addEventListener("click", () => {
            if (document.getElementById("menu-options").style.display === "none" 
                    || document.getElementById("menu-options").style.display === "") {
                document.getElementById("menu-options").style.display = "block";
                document.getElementById("btnPushMenu").innerHTML = "<i id='botonHambur' class='fa-solid fa-xmark'></i>";
            }
            else {
                document.getElementById("menu-options").style.display = "none";
                document.getElementById("btnPushMenu").innerHTML = "<i id='botonHambur' class='fas fa-bars'></i>";
            }
            });
            document.getElementById("btnPushMenu").addEventListener("keyup", (e)=>{
                if(e.keyCode === 27){
                    document.getElementById("menu-options").style.display = "none";
                    document.getElementById("btnPushMenu").innerHTML = "<i id='botonHambur' class='fas fa-bars'></i>";
                }
            })
        });
    }

    /**
     * Permite leer las opciones de menú desde la API de DERWEB Operaciones.
     */
    __leerOpciones() {
        var objMenuOptions = document.getElementById("menu-options");
        var objAPIs = new APIs();
        var objCache = new CacheUtils("derweb", false);
        var sesion = objCache.get("sesion");
        var idTipoEntidad = sesion.id_tipoentidad;

        // Recupero las opciones de menú según el tipo de entidad para mostrar solo
        // a lo que puede acceder.
        var aOperaciones = objAPIs.getFromAPI(this.__objApp.getUrlApi("app-operaciones-getByTipoEntidad") 
            + "?idTipoEntidad=" + idTipoEntidad);

        aOperaciones.forEach(xElement => {
            let objLink = document.createElement("a");
            objLink.href = xElement.url;
            objLink.innerHTML = "<i class='" + xElement.icono + "'></i>&nbsp;" + xElement.nombre;
            objMenuOptions.appendChild(objLink);
        });
    }
}
