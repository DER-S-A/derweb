CREATE PROCEDURE `sp_entidades_upgrade`(
	xid_tipoentidad int,
	xcliente_cardcode varchar(20),
    xnro_cuit varchar(15),
    xnombre varchar(100),
    xdireccion varchar(200),
    xemail varchar(200),
    xtelefono varchar(20)
)
BEGIN
	DECLARE vCantReg INT;
    DECLARE vProximoID INT;
    DECLARE vIdEncontrado INT;
	DECLARE vMensaje TEXT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
        INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('entidades_upgrade', vMensaje);
    END;
    START TRANSACTION;
    
    
    SELECT
		COUNT(*)
	INTO
		vCantReg
	FROM
		entidades
	WHERE
		cliente_cardcode = xcliente_cardcode;
	
    
    
	IF vCantReg = 0 THEN
		
        SELECT
			CASE WHEN MAX(id) IS NULL THEN 1 ELSE MAX(id) + 1 END
		INTO
			vProximoID
		FROM
			entidades
		WHERE
			entidades.id_tipoentidad = xid_tipoentidad;
        
		INSERT INTO entidades (
			id,
            id_tipoentidad,
			cliente_cardcode,
            nro_cuit,
            nombre,
			direccion,
			email,
			telefono,
            usuario,
            clave,
            id_listaprecio
            )
		VALUES (
			vProximoID,
            xid_tipoentidad,
			xcliente_cardcode,
			xnro_cuit,
			xnombre,
			xdireccion,
			xemail,
			xtelefono,
            xcliente_cardcode,
            '',
            1
			);
	ELSE
		
		SELECT
			id
		INTO
			vIdEncontrado
		FROM
			entidades
		WHERE
			cliente_cardcode = xcliente_cardcode;
            
		
		UPDATE
			entidades
		SET 
			entidades.nro_cuit = xnro_cuit,
			entidades.nombre = xnombre,
			entidades.direccion = xdireccion,
			entidades.email = xemail,
			entidades.telefono = xtelefono			
        WHERE
			entidades.id = vIdEncontrado AND
            entidades.id_tipoentidad = xid_tipoentidad;
    END IF;
    
	COMMIT;
		
END