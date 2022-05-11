CREATE PROCEDURE sp_paises_upgrade (
	xcodigo varchar(10),
    xnombre varchar(100))
BEGIN
	DECLARE vCantReg INT;
    DECLARE vMensaje TEXT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
        INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('paises_upgrade', vMensaje);
    END;
    
    START TRANSACTION;
    
    -- Verifico si el país ya se encuentra cargado.
    SELECT
		COUNT(*)
	INTO
		vCantReg
	FROM
		paises
	WHERE
		codigo = xcodigo;
	
    IF vCantReg = 0 THEN
		INSERT INTO paises (
			codigo,
            nombre)
		VALUES (
			xcodigo,
            xnombre);
    END IF;
    
	COMMIT;
		
END