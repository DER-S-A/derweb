/**
 * Contiene funciones correspondiente al módulo de ingreso de pedidos
 * rápidos.
 */

/**
 * Selecciona un artículo de la grilla en la búsqueda de artículos.
 * @param {int} xid Id. de artículo seleccionado.
 */
function seleccionar_articulo(xid) {
    let url = (new App()).getUrlApi("catalogo-articulos-get");
    let sesion_tmp = (new CacheUtils("derweb")).get("sesion_temporal");
    let parametros = "sesion=" + JSON.stringify(sesion_tmp) + "&pagina=0&filter=\"art.id=" + xid + "\"";

    (new APIs()).call(url, parametros, "GET", response => {
        document.getElementById("txtCodArt").value = response.values[0]["codigo"];
        document.getElementById("txtDescripcion").value = response.values[0]["desc"];

        // Pongo el JSON del artículo seleccionado en data-value en txtCodArt
        document.getElementById("txtCodArt").dataset.value = JSON.stringify(response);
        (new CacheUtils("derweb")).remove("sesion_temporal");

        // Cierro el modal
        document.getElementById("main").removeChild(document.getElementById("modal_articulos"));
        document.querySelector("#page-container > div.modal-backdrop.fade.show").remove();
        document.getElementById("txtCantidad").focus();
    });
}
let objIpr = new IngresoPedidosRapidoGUI;
function editarItem(xid) {
    objIpr.editarItem(xid);
}

function eliminarItem(xidpedido, xId) {
    objIpr.borrarItem(xidpedido, xId);
}