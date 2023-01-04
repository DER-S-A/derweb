CREATE PROCEDURE sp_pedidos_grabar_item (
    xidpedido int, xidcliente int, xidarticulo int, xcantidad decimal(20, 2)
)
BEGIN
    DECLARE vMensaje text;
    DECLARE vidListaPrecio int;
    DECLARE vPrecioLista decimal(20, 2);
    DECLARE vAlicuotaIVA decimal(5, 2);
    DECLARE vDescuentoP1 decimal(5, 2);
    DECLARE vDescuentoP2 decimal(5, 2);
    DECLARE vCostoUnitario decimal(20, 2);
    DECLARE vSubtotal decimal(20, 2);
    DECLARE vImporteIVA decimal(20, 2);
    DECLARE vTotal decimal(20, 2);
    DECLARE vPorcentajeOferta decimal(20, 2);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
        SELECT 'BD_ERROR' AS 'codigo_error', vMensaje AS 'result';
    END;

    -- Recupero los datos de la cabecera del pedido que se está generando.
    SELECT
        id_listaprecio,
        descuento_1,
        descuento_2
    INTO
        vidListaPrecio,
        vDescuentoP1,
        vDescuentoP2
    FROM
        entidades
    WHERE
        entidades.id = xidcliente;

    -- Recupero el precio de lista del artículo
    SELECT
        precio_lista
    INTO
        vPrecioLista
    FROM
        articulos_precios ap
    WHERE
        ap.id_articulo = xidarticulo AND
        ap.id_listaprecio = vidListaPrecio;

    -- Recupero los datos del artículo que necesito.
    SELECT
        alicuota_iva
    INTO
        vAlicuotaIVA
    FROM
        articulos
    WHERE
        articulos.id = xidarticulo;

    SET vPorcentajeOferta = 0.00;
    SET vCostoUnitario = vPrecioLista - (vPrecioLista * (vDescuentoP1 / 2));
    SET vImporteIVA = vCostoUnitario + (vCostoUnitario * (vAlicuotaIVA / 100));
    SET vSubtotal = vCostoUnitario * xcantidad;
    SET vTotal = vSubtotal + vImporteIVA;

    INSERT INTO pedidos_items (
        id_pedido, id_articulo, cantidad, porcentaje_oferta,
        precio_lista, costo_unitario, alicuota_iva, subtotal,
        importe_iva, total, anulado)
    VALUES (
        xidpedido, xidarticulo, xcantidad, vPorcentajeOferta,
        vPrecioLista, vCostoUnitario, vAlicuotaIVA, vSubtotal,
        vImporteIVA, vTotal,  0);

    SELECT "OK" AS 'codigo_error', 'El ítem se grabó satisfactoriamente' AS 'result';
END