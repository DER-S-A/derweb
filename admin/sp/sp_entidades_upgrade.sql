CREATE PROCEDURE sp_entidades_upgrade (
  xid_tipoentidad int, 
  xcliente_cardcode varchar(20), 
  xnro_cuit varchar(15), 
  xnombre varchar(100), 
  xdireccion varchar(200), 
  xemail varchar(200), 
  xtelefono varchar(100), 
  xdescuento_1 decimal(5, 2), 
  xdescuento_2 decimal(5, 2), 
  xSlpCode varchar(20))
BEGIN
	DECLARE vCantReg int;
	DECLARE vIdEncontrado int;
	DECLARE vMensaje text;
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
	ROLLBACK;
	GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
	INSERT INTO log_sp (nombre_sp, mensaje_error)
	  VALUES ('entidades_upgrade', vMensaje);
	END;
	START TRANSACTION;

	SELECT
	  COUNT(*) INTO vCantReg
	FROM entidades
	WHERE cliente_cardcode = xcliente_cardcode;

	IF vCantReg = 0 THEN
		/* Levanto la lista de precio predeterminada*/
		SELECT
			id
		INTO
			@idListaPrecio
		FROM
			listas_precios
		WHERE
			listas_precios.price_list = 1;

		INSERT INTO entidades (
			id_tipoentidad, cliente_cardcode, nro_cuit, nombre,
			direccion, email, telefono, usuario, clave,
			id_listaprecio, descuento_1, descuento_2, codigo_vendedor)
		VALUES (
			xid_tipoentidad, xcliente_cardcode, xnro_cuit, xnombre, 
			xdireccion, xemail, xtelefono, xcliente_cardcode, '', 
			@idListaPrecio, xdescuento_1, xdescuento_2, xSlpCode);
	ELSE
		SELECT
		id INTO vIdEncontrado
		FROM entidades
		WHERE cliente_cardcode = xcliente_cardcode;

		UPDATE entidades
		SET entidades.nro_cuit = xnro_cuit,
		  entidades.nombre = xnombre,
		  entidades.direccion = xdireccion,
		  entidades.email = xemail,
		  entidades.telefono = xtelefono,
		  entidades.descuento_1 = xdescuento_1,
		  entidades.descuento_2 = xdescuento_2,
		  entidades.codigo_vendedor = xSlpCode
		WHERE entidades.id = vIdEncontrado
		AND entidades.id_tipoentidad = xid_tipoentidad;
	END IF;

	COMMIT;

END