CREATE PROCEDURE `sp_entidades_upgrade`(
	xid_tipoentidad int,
	xcliente_cardcode varchar(20),
    xnro_cuit varchar(15),
    xnombre varchar(100),
    xdireccion varchar(200),
    xemail varchar(200),
    xtelefono varchar(20),
    xdescuento_1 decimal(5, 2),
    xdescuento_2 decimal(5, 2)
)
BEGIN
	DECLARE vCantReg INT;
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
    
    -- Verifico si el cliente está cargado en la base, si lo está
    -- entonces actualizo datos en caso contrario lo doy de alta.
    SELECT
		COUNT(*)
	INTO
		vCantReg
	FROM
		entidades
	WHERE
		cliente_cardcode = xcliente_cardcode;
	
	IF vCantReg = 0 THEN
		INSERT INTO entidades (
            id_tipoentidad,
			cliente_cardcode,
            nro_cuit,
            nombre,
			direccion,
			email,
			telefono,
            usuario,
            clave,
            id_listaprecio,
            descuento_1,
            descuento_2
            )
		VALUES (
            xid_tipoentidad,
			xcliente_cardcode,
			xnro_cuit,
			xnombre,
			xdireccion,
			xemail,
			xtelefono,
            xcliente_cardcode,
            '',
            1,
            xdescuento_1,
            xdescuento_2
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
			entidades.telefono = xtelefono,
            entidades.descuento_1 = xdescuento_1,
            entidades.descuento_2 = xdescuento_2		
        WHERE
			entidades.id = vIdEncontrado AND
            entidades.id_tipoentidad = xid_tipoentidad;
    END IF;
    
	COMMIT;
		
END