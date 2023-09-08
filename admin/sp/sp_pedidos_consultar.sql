CREATE PROCEDURE sp_pedidos_consultar (
    xIdSucursal int,
    xfechaDD DATE,
    xfechaHH DATE
)
BEGIN
    SELECT
        p.id AS 'id_pedido',
        p.id_entidad AS 'id_cliente',
        e.cliente_cardcode,
        e.nombre AS 'cliente',
        p.id_sucursal,
        s.codigo_sucursal,
        s.nombre AS 'sucursal',
        p.id_estado,
        ep.descripcion AS 'estado',
        p.fecha_alta,
        p.fecha_modificado,
        p.id_vendedor,
        e1.sales_employee_code,
        e1.nombre AS 'vendedor',
        p.id_formaenvio,
        fe.codigo,
        fe.descripcion AS 'forma_envio',
        p.descuento_1,
        p.descuento_2,
        p.subtotal,
        p.importe_iva,
        p.total
    FROM
        pedidos p
            INNER JOIN entidades e ON p.id_entidad = e.id
            INNER JOIN sucursales s ON e.id = s.id_entidad
            INNER JOIN estados_pedidos ep ON p.id_estado = ep.id
            LEFT JOIN entidades e1 ON s.id_vendedor = e1.id
            LEFT JOIN formas_envios fe ON s.id_formaenvio = fe.id
            LEFT JOIN transportes t ON p.id_transporte = t.id
    WHERE
        CAST(p.fecha_modificado AS date) BETWEEN CAST(xfechaDD AS date) AND CAST(xfechaHH AS date) AND
        p.id_estado = 2 AND
        s.id = xIdSucursal;
END