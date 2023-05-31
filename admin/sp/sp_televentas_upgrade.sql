CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_televentas_upgrade`(IN xTipoEntidad INT, IN xClienteCardCode VARCHAR(20), IN xNombre VARCHAR(100), IN xTelefono VARCHAR(20), IN xDireccion VARCHAR(200), IN xEmail VARCHAR(200), IN xCuit VARCHAR(50))
    COMMENT 'Este Store Procedure permite Actualizar y agregar nuevos televentas a la tabla de entidades'
BEGIN
  DECLARE vCantReg int;
  DECLARE vIdEncontrado int;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    GET DIAGNOSTICS CONDITION 1
    @sqlstate = RETURNED_SQLSTATE,
    @errno = MYSQL_ERRNO,
    @text = MESSAGE_TEXT;

    SET @message = CONCAT('Error ', @errno, ': ', @text, ' - SQLSTATE: ', @sqlstate);

    INSERT INTO log_sp (nombre_sp, mensaje_error)
      VALUES ('televentas_upgrade', @message);
    ROLLBACK;
  END;
  START TRANSACTION;

    SELECT
      COUNT(*) INTO vCantReg
    FROM entidades
    WHERE cliente_cardcode = xClienteCardCode;

    IF vCantReg = 0 THEN
      INSERT INTO entidades (id_tipoentidad,
      cliente_cardcode,
      nro_cuit,
      nombre,
      direccion,
      telefono,
      usuario,
      clave,
      id_listaprecio,
      descuento_1,
      descuento_2,
      fecha_alta,
      fecha_modificado)
        VALUES (2, xClienteCardCode, xCuit, xNombre, xDireccion, xTelefono, cliente_cardcode, 'derweb', 1, 0, 0, NOW(), NOW());
    ELSE
      SELECT
        id INTO vIdEncontrado
      FROM entidades
      WHERE cliente_cardcode = xClienteCardCode;

      UPDATE entidades
      SET entidades.nro_cuit = xCuit,
          entidades.nombre = xNombre,
          entidades.direccion = xDireccion,
          entidades.email = xEmail,
          entidades.telefono = xTelefono,
          fecha_modificado = NOW()
      WHERE entidades.id = vIdEncontrado
      AND entidades.id_tipoentidad = xTipoEntidad;
    END IF;

  COMMIT;

END