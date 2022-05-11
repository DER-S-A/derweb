DELIMITER $$
DROP PROCEDURE IF EXISTS provincias_upgrade $$
CREATE PROCEDURE provincias_upgrade (
	xcodigo varchar(10),
    xcodigo_pais varchar(10),
    xnombre varchar(100))
BEGIN
	DECLARE vCantReg INT;
    DECLARE vId_Pais INT;
    DECLARE vMensaje TEXT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
        INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('provincias_upgrade', vMensaje);
    END;
    START TRANSACTION;
    
    -- Verifico si la provincia existe.
    SELECT
		COUNT(*)
	INTO
		vCantReg
	FROM
		provincias
	WHERE
		codigo = xcodigo;
        
	IF vCantReg = 0 THEN
		/* Si no existe entonces busco el país asociado y
         creo el registro de provincias.*/
        SELECT
			id
		INTO
			vId_Pais
		FROM
			paises
		WHERE
			codigo = xcodigo_pais;
		
        INSERT INTO provincias (
			codigo,
            id_pais,
            nombre)
		VALUES (
			xcodigo,
            vId_Pais,
            xnombre);
    END IF;
    COMMIT;
END $$