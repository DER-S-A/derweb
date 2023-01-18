CREATE PROCEDURE sp_pedidos_grabar (
    xidentidad int, xidtipoentidad int, xidvendedor int, xidsucursal int, 
    xcodSucursal varchar(30), xidTransporte int, xcodigoTransporte varchar(20), xidFormaEnvio int, 
    xsubtotal decimal(20, 2), ximporte_iva decimal(20, 2), xtotal decimal(20, 2))
BEGIN
    /**
     * Este procedimiento almacenado permite grabar la cabecera de pedido
     * que viene dado en la pantalla de pedidos rápidos.
    */

    DECLARE vMensaje TEXT;
    DECLARE vIdEstado INT;
    DECLARE vDescuentoP1 decimal(5, 2);
    DECLARE vDescuentoP2 decimal(5, 2);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
        SELECT 'BD_ERROR' AS 'codigo_error', vMensaje AS 'result';
    END;

    -- Recupero los descuentos del cliente
    SELECT
        descuento_1,
        descuento_2
    INTO
        vDescuentoP1,
        vDescuentoP2
    FROM
        entidades
    WHERE
        entidades.id = xidentidad;

    -- Recupero el ID. que corresponde al estado de pedidos confirmado.
    SELECT
        id
    INTO
        vIdEstado
    FROM
        estados_pedidos
    WHERE
        estados_pedidos.estado_confirmado = 1;

    -- Inserto la cabecera del pedido.
    INSERT INTO pedidos (
        id_entidad, id_tipoentidad, id_estado, id_vendedor,
        id_sucursal, codigo_sucursal, id_transporte, codigo_transporte,
        id_formaenvio, descuento_1, descuento_2, subtotal,
        importe_iva, total, anulado, fecha_alta)
    VALUES (
        xidentidad, xidtipoentidad, vIdEstado, xidvendedor,
        xidsucursal, xcodSucursal, xidTransporte, xcodigoTransporte,
        xidFormaEnvio, vDescuentoP1, vDescuentoP2, xsubtotal,
        ximporte_iva, xtotal, 0, current_timestamp);

    SELECT "OK" AS 'codigo_error', @@IDENTITY AS 'result';
END