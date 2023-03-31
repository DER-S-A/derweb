<?php
/**
 * Contiene funciones varias para utilizar en cualquier lugar
 * del proyecto.
 */

/**
 * calcular_costo
 * Permite calcular el costo en base al modo seleccionado por el usuario
 * desde el panel de opciones.
 * @param  string $xmodo Modo seleccionado por el usuario.
 * @param  double $xprecioLista Precio de lista.
 * @param  double $xdescuento_1 Descuento 1.
 * @param  double $xdescuento_2 Descuento 2.
 * @return double
 */
function calcular_costo($xmodo, $xprecioLista, $xdescuento_1, $xdescuento_2) {
    $costo = 0.00;
    if (sonIguales($xmodo, "PED"))
        $costo = $xprecioLista - ($xprecioLista * ($xdescuento_1 / 100));
    elseif (sonIguales($xmodo, "PRE"))
        $costo = $xprecioLista - ($xprecioLista * ($xdescuento_2 / 100));
    return $costo;
}

/**
 * calcular_precio_venta
 * Permite calcular el precio de venta en base al costo del cliente.
 * @param  mixed $xcosto
 * @param  mixed $xrentabilidad
 * @return void
 */
function calcular_precio_venta($xcosto, $xrentabilidad) {
    $precio_venta = 0.00;
    $precio_venta = $xcosto + ($xcosto * ($xrentabilidad[0]['rentabilidad_1'] / 100));
    $precio_venta = $precio_venta + ($precio_venta * ($xrentabilidad[0]['rentabilidad_2'] / 100));
    return $precio_venta;
}
?>