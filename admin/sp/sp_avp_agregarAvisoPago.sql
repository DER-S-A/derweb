CREATE PROCEDURE sp_avp_agregarAvisoPago (
    xid_vendedor int,
    xid_entidad int,
    xid_sucursal int,
    xfecha date,
    xnumero_recibo varchar(20),
    ximporte_efectivo decimal(20, 2),
    ximporte_cheques decimal(20, 2),
    ximporte_deposito decimal(20, 2),
    ximporte_retenciones decimal(20, 2)
)
BEGIN
    DECLARE vTotalRecibo decimal(20, 2);
    DECLARE vMensaje text;
    DECLARE vRendicionAbierta decimal(20, 2);
    DECLARE vIdRendicion int;

    /* Validaciones */
    DECLARE vReciboDuplicado int;

    /* Totales para grabar en la tabla avp_rendiciones. */
    DECLARE vtotal_efectivo decimal(20, 2);
    DECLARE vtotal_cheques decimal(20, 2);
    DECLARE vtotal_deposito decimal(20, 2);
    DECLARE vtotal_retensiones decimal(20, 2);
    DECLARE vtotal_recibos decimal(20, 2);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		ROLLBACK;
		GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
        INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('sp_avp_agregarAvisoPago', vMensaje);
        SELECT 'error' AS 'result', vMensaje AS 'mensaje';
    END;

    SET vtotal_efectivo = 0.00;
    SET vtotal_cheques = 0.00;
    SET vtotal_deposito = 0.00;
    SET vtotal_retensiones = 0.00;
    SET vtotal_recibos = 0.00;

    /* Valido que no se pueda enviar un recibo duplicado */ 
    SELECT
        COUNT(*)
    INTO
        vReciboDuplicado
    FROM
        avp_movimientos mov
            INNER JOIN avp_rendiciones rend ON mov.id_rendicion = rend.id
    WHERE
        rend.id_entidad = xid_vendedor AND
        mov.numero_recibo = xnumero_recibo;

    IF vReciboDuplicado > 0 THEN
        /* Si el recibo está duplicado genero una Excepción para que salga por SQLEXCEPTION */
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El número de recibo se encuentra duplicado';
    END IF;

    START TRANSACTION;

    /* Verifico si la cabecera ya está creada */
    SELECT
        COUNT(*)
    INTO
        vRendicionAbierta
    FROM
        avp_rendiciones
    WHERE
        avp_rendiciones.id_entidad = xid_vendedor AND
        avp_rendiciones.enviado = 0;

    IF vRendicionAbierta = 0 THEN
        /* Si no hay cabecera genero una nueva con todo los importes en cero.
         La fecha pone la actual como predeterminada. */
        INSERT INTO avp_rendiciones (
            id_entidad, fecha
        ) VALUES (
            xid_vendedor, CAST(current_timestamp AS DATE));

        SET vIdRendicion = LAST_INSERT_ID();
    ELSE
        /* Si hay una cabecera creada, entonces tomo el ID. de la
            cabecera abierta. */
        SELECT 
            id, total_efectivo, total_cheques, total_deposito,
            total_retensiones, total_recibos            
        INTO
            vIdRendicion, vtotal_efectivo, vtotal_cheques, vtotal_deposito,
            vtotal_retensiones, vtotal_recibos
        FROM
            avp_rendiciones
        WHERE
            avp_rendiciones.id_entidad = xid_vendedor AND
            avp_rendiciones.enviado = 0;         
    END IF;

    /* Calculo el total del recibo. */
    SET vTotalRecibo = ximporte_efectivo + ximporte_cheques + ximporte_deposito + ximporte_retenciones;
    SET vtotal_efectivo = vtotal_efectivo + ximporte_efectivo;
    SET vtotal_cheques = vtotal_cheques + ximporte_cheques;
    SET vtotal_deposito = vtotal_deposito + ximporte_deposito;
    SET vtotal_retensiones = vtotal_retensiones + ximporte_retenciones;
    SET vtotal_recibos = vtotal_recibos + vTotalRecibo;

    /* Agrego el movimiento (aviso de pago). */
    INSERT INTO avp_movimientos (
        id_rendicion, id_entidad, id_sucursal, fecha,
        numero_recibo, importe_efectivo, importe_cheques, importe_deposito,
        importe_retenciones, total_recibo)
    VALUES (
        vIdRendicion, xid_entidad, xid_sucursal, xfecha,
        xnumero_recibo, ximporte_efectivo, ximporte_cheques, ximporte_deposito,
        ximporte_retenciones, vTotalRecibo);

    /* Actualizo los totales en la cabecera de rendiciones */
    UPDATE
        avp_rendiciones
    SET
        avp_rendiciones.total_efectivo = vtotal_efectivo,
        avp_rendiciones.total_cheques = vtotal_cheques,
        avp_rendiciones.total_deposito = vtotal_deposito,
        avp_rendiciones.total_retensiones = vtotal_retensiones,
        avp_rendiciones.total_recibos = vtotal_recibos
    WHERE
        avp_rendiciones.id = vIdRendicion;

    COMMIT;

    SELECT 'success' AS 'result', 'El aviso de pago se agregó con éxito' AS 'mensaje';
END