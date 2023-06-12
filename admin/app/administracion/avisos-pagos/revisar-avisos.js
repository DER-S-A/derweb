/**
 * Contiene la funcionalidad de la pantalla de revisión de avisos de
 * pagos.
 */

/**
 * Acceder a la operación revisar aviso desde la grilla
 * @param {int} xidMovimiento 
 */
function revisar_aviso(xidMovimiento) {
    document.getElementById("id_movimiento").value = parseInt(xidMovimiento);
    fetch("app/administracion/avisos-pagos/api-get-movimientos-by-id.php", {
        method: "POST",
        headers: {
            "Content-Type": "applicaction/json"
        },

        body: JSON.stringify({
            "id": parseInt(xidMovimiento)
        })
    }).then(response => response.json())
        .then(datos => {
            aMovimiento = datos["data"];
            recuperar_datos(aMovimiento)
            document.getElementById("edicion_aviso").style.display = "block";
        });
}

/**
 * Programo el evento load
 */
window.addEventListener("load", () => {
    document.getElementById("txt_importe_efectivo").addEventListener("blur", () => {
        sumar_importes_recibo();
    });

    document.getElementById("txt_importe_cheques").addEventListener("blur", () => {
        sumar_importes_recibo();
    });
    
    document.getElementById("txt_importe_deposito").addEventListener("blur", () => {
        sumar_importes_recibo();
    });

    document.getElementById("txt_importe_retenciones").addEventListener("blur", () => {
        sumar_importes_recibo();
    });

});

/**
 * Suma los importes que se van cargando
 * @returns {double}
 */
function sumar_importes_recibo() {
    var importe_efectivo = parseFloat(document.getElementById("txt_importe_efectivo").value);
    var importe_cheques = parseFloat(document.getElementById("txt_importe_cheques").value);
    var importe_deposito = parseFloat(document.getElementById("txt_importe_deposito").value);
    var importe_retenciones = parseFloat(document.getElementById("txt_importe_retenciones").value);

    document.getElementById("txt_total").value = importe_efectivo + importe_cheques + importe_deposito + importe_retenciones;
}

/**
 * Recupera los datos de los avisos de pagos.
 */
function recuperar_datos(xMovimiento) {
    document.getElementById("txt_fecha").value = xMovimiento[0]["fecha"];
    document.getElementById("txt_cliente").value = xMovimiento[0]["cliente_cardcode"] + " - " + xMovimiento[0]["cliente_nombre"];
    document.getElementById("txt_sucursal").value = xMovimiento[0]["codigo_sucursal"] + " - " + xMovimiento[0]["sucursal_nombre"];
    document.getElementById("txt_numero_recibo").value = xMovimiento[0]["numero_recibo"];
    document.getElementById("txt_importe_efectivo").value = xMovimiento[0]["importe_efectivo"];
    document.getElementById("txt_importe_cheques").value = xMovimiento[0]["importe_cheques"];
    document.getElementById("txt_importe_deposito").value = xMovimiento[0]["importe_deposito"];
    document.getElementById("txt_importe_retenciones").value = xMovimiento[0]["importe_retenciones"];
    document.getElementById("txt_total").value = xMovimiento[0]["total_recibo"];

    if (parseInt(xMovimiento[0]["revisado"]) === 1)
        document.getElementById("chk_revisado").setAttribute("checked", "checked");
    else
        document.getElementById("chk_revisado").removeAttribute("checked");
}

/**
 * Permit grabar los cambios realizados en el aviso de pago
 * @param {int} xidMovimiento 
 */
function grabar_aviso() {
    let aDatos = {
        "id": parseInt(document.getElementById("id_movimiento").value),
        "importe_efectivo": parseFloat(document.getElementById("txt_importe_efectivo").value),
        "importe_cheques": parseFloat(document.getElementById("txt_importe_cheques").value),
        "importe_deposito": parseFloat(document.getElementById("txt_importe_deposito").value),
        "importe_retenciones": parseFloat(document.getElementById("txt_importe_retenciones").value),
        "total_recibo": parseFloat(document.getElementById("txt_total").value),
        "revisado": 1
    };
    console.log(aDatos);
    fetch ("app/administracion/avisos-pagos/api-actualizar-movimiento.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(aDatos)
    }).then(response => response.json())
        .then(datos => {
            alert(datos[0]["mensaje"]);
            document.getElementById("edicion_aviso").style.display = "none";
            location.reload();
        });
}

/**
 * Permite cancelar la edición del aviso de pago.
 */
function cancelar_edicion() {
    document.getElementById("edicion_aviso").style.display = "none";
}