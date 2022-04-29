/**
 * Contiene la clase para generar el menú hamburguesa desplegable.
 */

class MenuComponent {
    constructor() {
        this.menuContainer = document.getElementById("menu-container");
    }

    generarMenu() {
        getTemplate("components/menus/menus.html", (xhtml) => {
            this.menuContainer.innerHTML = xhtml;
            this.__leerOpciones();

            document.getElementById("btnPushMenu").addEventListener("click", () => {
            if (document.getElementById("menu-options").style.display === "none" 
                    || document.getElementById("menu-options").style.display === "")
                document.getElementById("menu-options").style.display = "block";
            else
                document.getElementById("menu-options").style.display = "none";
            });
        });
    }

    __leerOpciones() {
        var objMenuOptions = document.getElementById("menu-options");
        var objAPIs = new APIs();
        var objCache = new CacheUtils("derweb", false);
        var sesion = objCache.get("sesion");
        var idTipoEntidad = sesion.id_tipoentidad;
        var aOperaciones = objAPIs.getFromAPI("services/lfw-operaciones/getByTipoEntidad?idTipoEntidad=" + idTipoEntidad);
        aOperaciones.forEach(xElement => {
            let objLink = document.createElement("a");
            objLink.href = xElement.url;
            objLink.innerHTML = "<i class='" + xElement.icono + "'></i>&nbsp;" + xElement.nombre;
            objMenuOptions.appendChild(objLink);
        });
    }
}
