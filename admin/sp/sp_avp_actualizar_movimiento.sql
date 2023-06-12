CREATE PROCEDURE sp_avp_actualizar_movimiento (
    xid bigint,
    xEfectivo decimal(20, 2),
    xCheques decimal(20, 2),
    xDeposito decimal(20, 2),
    xRetenciones decimal(20, 2),
    xTotalRecibo decimal(20, 2),
    xRevisado tinyint(3)
)
BEGIN
    DECLARE vIdRendicion int;
    DECLARE vTotalEfectivo decimal(20, 2);
    DECLARE vTotalCheques decimal(20, 2);
    DECLARE vTotalDeposito decimal(20, 2);
    DECLARE vTotalRetenciones decimal(20, 2);
    DECLARE vTotalRecibos decimal(20, 2);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
		GET DIAGNOSTICS CONDITION 1 @mensaje = MESSAGE_TEXT;
        INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('sp_avp_actualizar_movimiento', @mensaje);
        SELECT 'error' AS 'result', @mensaje AS 'mensaje';        
    END;

    START TRANSACTION;

    /* Obtengo el id de rendición asociado al movimiento */
    SELECT
        id_rendicion
    INTO
        vIdRendicion
    FROM
        avp_movimientos
    WHERE
        avp_movimientos.id = xid;

    /* Actualizo las correcciones del movimiento */
    UPDATE
        avp_movimientos
    SET
        avp_movimientos.importe_efectivo = xEfectivo,
        avp_movimientos.importe_cheques = xCheques,
        avp_movimientos.importe_deposito = xDeposito,
        avp_movimientos.importe_retenciones = xRetenciones,
        avp_movimientos.total_recibo = xTotalRecibo,
        avp_movimientos.revisado = xRevisado
    WHERE
        avp_movimientos.id = xid;

    /* Calculo y actualizo los totales en la tabla rendición */
    SELECT
        SUM(importe_efectivo),
        SUM(importe_cheques),
        SUM(importe_deposito),
        SUM(importe_retenciones),
        SUM(total_recibo)
    INTO
        vTotalEfectivo,
        vTotalCheques,
        vTotalDeposito,
        vTotalRetenciones,
        vTotalRecibos
    FROM
        avp_movimientos
    WHERE
        avp_movimientos.id_rendicion = vIdRendicion
    GROUP BY
        avp_movimientos.id_rendicion;

    UPDATE
        avp_rendiciones
    SET
        avp_rendiciones.total_efectivo = vTotalEfectivo,
        avp_rendiciones.total_cheques = vTotalCheques,
        avp_rendiciones.total_deposito = vTotalDeposito,
        avp_rendiciones.total_retensiones = vTotalRetenciones,
        avp_rendiciones.total_recibos = vTotalRecibos
    WHERE
        avp_rendiciones.id = vIdRendicion;

    COMMIT;
    SELECT 'success' AS 'result', 'Las modificaciones se realizadon satisfactoriamente' AS 'mensaje';
END