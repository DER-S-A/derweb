CREATE PROCEDURE sp_avp_generarRendicion (
    xidRendicion int,
    xImporteRetiro decimal(20, 2),
    xEfectivoDepositado decimal(20, 2),
    xGastosTransporte decimal(20, 2),
    xGastosGenerales decimal(20, 2),
    xObservaciones text
)
BEGIN
    DECLARE vTotalEfectivo decimal(20, 2);
    DECLARE vEfectivoEntregado decimal(20, 2);
    DECLARE vMensaje text;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		ROLLBACK;
		GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
        INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('sp_avp_generarRendicion', vMensaje);
        SELECT 'BD_ERROR' AS 'result', vMensaje AS 'mensaje', xidRendicion AS 'id_rendicion';
    END;

    START TRANSACTION;
    /* Levanto los totales de la rendición */
    SELECT
        total_efectivo
    INTO
        vTotalEfectivo
    FROM
        avp_rendiciones
    WHERE
        avp_rendiciones.id = xidRendicion;

    /* Calculo el efectivo entregado */
    SET vEfectivoEntregado = vTotalEfectivo - xImporteRetiro - xEfectivoDepositado - xGastosTransporte - xGastosGenerales;

    /* Doy cierre a la rendición y lo marco como enviado. */
    UPDATE
        avp_rendiciones
    SET
        avp_rendiciones.fecha_enviado = CAST(current_timestamp AS DATE),
        avp_rendiciones.importe_retiro = xImporteRetiro,
        avp_rendiciones.efectivo_depositado = xEfectivoDepositado,
        avp_rendiciones.gastos_transporte = xGastosTransporte,
        avp_rendiciones.gastos_generales = xGastosGenerales,
        avp_rendiciones.efectivo_entregado = vEfectivoEntregado,
        avp_rendiciones.observaciones = xObservaciones,
        avp_rendiciones.enviado = 1
    WHERE
        avp_rendiciones.id = xidRendicion;

    COMMIT;

    SELECT 'OK' AS 'result', 'Rendición generada con éxito' AS 'mensaje', xidRendicion AS 'id_rendicion';
END