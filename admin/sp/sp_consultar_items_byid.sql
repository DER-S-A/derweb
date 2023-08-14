CREATE PROCEDURE sp_consultar_items_byid (
    xid_pedido int
)
BEGIN
    SELECT
        pit.id,
        pit.id_articulo,
        pit.cantidad,
        a.codigo,
        a.descripcion,
        pit.porcentaje_oferta,
        pit.precio_lista,
        pit.costo_unitario,
        pit.alicuota_iva,
        pit.subtotal,
        pit.importe_iva,
        pit.total
    FROM
        pedidos_items pit
            INNER JOIN articulos a ON pit.id_articulo = a.id
    WHERE
        pit.id_pedido = xid_pedido AND
        pit.anulado = 0;
END