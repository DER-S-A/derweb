CREATE PROCEDURE `sp_sucursales_upgrade`(
	xSucursalCode varchar(65),
    xSucursalName varchar(50),
    xCardCode varchar(15),
    xTipoCode varchar(15),
    xCalle varchar(100),
    xCiudad varchar (100),
    xEstadoCode int,
    xZipCode int,
    xGln int,
    xCardCodeDER int,
    xCreateDate varchar(20)
)
BEGIN

	DECLARE vIdFormaEnvio int;
    DECLARE vIdEntidad int;
    DECLARE vIdProvincia int;
    DECLARE vIdVendedor int;
    DECLARE vIdTransporte int;
    DECLARE vTelefono TEXT;
    DECLARE vEmail TEXT;
    DECLARE vDescuento1 decimal(5,2);
    DECLARE vDescuento2 decimal (5,2);
    DECLARE vCantReg int;
    DECLARE vCantSuc int;
	DECLARE vMensaje TEXT;
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		GET DIAGNOSTICS CONDITION 1 vMensaje = message_text;
        INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('sucursales_upgrade', vMensaje);
    END;
	START TRANSACTION;
    
    SET vIdFormaEnvio = (SELECT id FROM formas_envios WHERE descripcion like 'RETIRA CLIENTE');
    
    SET vIdEntidad = (SELECT id FROM entidades where cliente_cardcode = xCardCode);
    
    SET vIdProvincia = (SELECT id FROM provincias where codigo = xEstadoCode);
    
    SET vIdVendedor = 15571;
    
    SET vIdTransporte = (SELECT id FROM transportes WHERE descripcion like 'RETIRA CLIENTE');
    
    SET vTelefono = (SELECT telefono FROM entidades where cliente_cardcode = xCardCode);
    
	SET vEmail = (SELECT email FROM entidades where cliente_cardcode = xCardCode);
    
    SET vDescuento1 = (SELECT descuento_1 FROM entidades where cliente_cardcode = xCardCode);
	
    SET vDescuento2 = (SELECT descuento_2 FROM entidades where cliente_cardcode = xCardCode);
    
    SELECT 
		COUNT(*)
	INTO
		vCantReg
    FROM 
		Sucursales
    WHERE    
		id_entidad = vIdEntidad;
	
    IF vCantReg = 0 THEN
    
	INSERT INTO Sucursales(
        id_formaenvio,
        id_entidad,
        id_provincia,
        id_vendedor,
        id_transporte,
        codigo_sucursal,
        nombre,
        calle,
        numero,
        departamento,
        ciudad,
        codigo_postal,
        telefono,
        mail,
        global_localizacion_number,
        predeterminado,
        descuento_p1,
        descuento_p2
        )
	VALUES(
        vIdFormaEnvio,
        vIdEntidad,
        vIdProvincia,
        vIdVendedor,
        vIdTransporte,
        xSucursalCode,
        xSucursalName,
        xCalle,
        00,
        00,
        xCiudad,
        xZipCode,
        vTelefono,
        vEmail,
        xGln,
        1,
        vDescuento1,
        vDescuento2
        );
	ELSE 
		SELECT 
			 COUNT(*)
		INTO 
			vCantSuc
		FROM 
			Sucursales
		WHERE 
			id_entidad = vIdEntidad AND codigo_sucursal =  xSucursalCode;
            
		IF vCantSuc = 1 THEN
				
			UPDATE 
				Sucursales 
            SET
				id_formaenvio = vIdFormaEnvio,
				id_provincia = vIdProvincia ,
				id_vendedor = vIdVendedor,
				id_transporte = vIdTransporte,
				calle = xCalle,
				numero = 0,
				departamento = 0,
				ciudad = xCiudad,
				codigo_postal = xZipCode,
				telefono = vTelefono,
				mail = vEmail,
				global_localizacion_number = xGln,
				descuento_p1 = vDescuento1,
				descuento_p2 = vDescuento2
            WHERE 
				id_entidad = vIdEntidad AND codigo_sucursal =  xSucursalCode;
		ELSE 
			INSERT INTO Sucursales(
			id_formaenvio,
			id_entidad,
			id_provincia,
			id_vendedor,
			id_transporte,
			codigo_sucursal,
			nombre,
			calle,
			numero,
			departamento,
			ciudad,
			codigo_postal,
			telefono,
			mail,
			global_localizacion_number,
			predeterminado,
			descuento_p1,
			descuento_p2
			)
		VALUES(
			vIdFormaEnvio,
			vIdEntidad,
			vIdProvincia,
			vIdVendedor,
			vIdTransporte,
			xSucursalCode,
			xSucursalName,
			xCalle,
			00,
			00,
			xCiudad,
			xZipCode,
			vTelefono,
			vEmail,
			xGln,
			0,
			vDescuento1,
			vDescuento2
        );
	  END IF;
  END IF; 
    COMMIT;
END