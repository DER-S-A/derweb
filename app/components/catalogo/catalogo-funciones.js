/**
 * Script: catalogo-funciones.js
 * Descripción:
 *  Contiene las funciones que van a ser invocadas desde un hipervínculo <a src="javascript:...">
 *  o dentro de un evento de un botón siempre y cuando los controles correspondan al
 *  módulo de catálogo.
 */


/**
 * Permite agregar al carrito un articulo.
 * @param {int} xidarticulo Id. del artículo a insertar
 */
 function agregarAlCarrito(xidarticulo) {
    let cantidad = document.getElementById("txtcantidad_" + xidarticulo).value;
    let aSesion = JSON.parse(sessionStorage.getItem("derweb_sesion"));
    let objCatalogo = new Catalogo();

    if (cantidad == "" || cantidad<1) {
        alert("Cantidad vacia o valor incorrecto");
        return;
    }

    // Recupero los datos de la sucursal predeterminada
    objCatalogo.getSucursalPredeterminadaByCliente(aSesion["id_cliente"], (xaSucursal) => {
        let acabecera = {
            "id_cliente": parseInt(aSesion["id_cliente"]),
            "id_tipoentidad": parseInt(aSesion["id_tipoentidad"]),
            "id_vendedor": parseInt(xaSucursal[0]["id_vendedor"]),
            "id_sucursal": parseInt(xaSucursal[0]["id"]),
            "codigo_sucursal": xaSucursal[0]["codigo_sucursal"],
            "id_transporte": xaSucursal[0]["id_transporte"],
            "codigo_transporte": xaSucursal[0]["codigo_transporte"],
            "id_formaenvio": xaSucursal[0]["id_formaenvio"],
            "codigo_forma_envio": xaSucursal[0]["codigo_forma_envio"]
        };

        if (xaSucursal === undefined) {
            alert("Usted no tiene sucursal asignada, por favor comuníquese con sistemas para resolver este problema");
            return;
        }
        
        objCatalogo.agregarArticuloEnCarrito(aSesion, xidarticulo, cantidad, acabecera);
    });
 }
