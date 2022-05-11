DELIMITER $$
DROP PROCEDURE IF EXISTS formas_envios_upgrade $$
CREATE PROCEDURE formas_envios_upgrade (
	xcodigo int,
    xdescripcion varchar(100))
BEGIN
	DECLARE vCantReg int;
    DECLARE vMensaje TEXT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		ROLLBACK;
		GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
        INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('formas_envios_upgrade', vMensaje);
    END;
    
    START TRANSACTION;
    
    -- Verifico que la forma de envío no se encuentre cargada.
    SELECT
		COUNT(*)
	INTO
		vCantReg
	FROM
		formas_envios
	WHERE
		formas_envios.codigo = xcodigo;
    
    IF vCantReg = 0 THEN
		INSERT INTO formas_envios (
			codigo,
            descripcion,
            importe_minimo)
		VALUES (
			xcodigo,
            xdescripcion,
            0);
	ELSE
		UPDATE
			formas_envios
		SET
			formas_envios.descripcion = xdescripcion
		WHERE
			formas_envios.codigo = xcodigo;
    END IF;
    COMMIT;
END $$