USE db_derweb;

DROP PROCEDURE IF EXISTS sp_entidades_upgrade;

DELIMITER $$

CREATE
DEFINER = 'root'@'localhost'
PROCEDURE sp_entidades_upgrade (IN xid_tipoentidad int, IN xcliente_cardcode varchar(20), IN xnro_cuit varchar(15), IN xnombre varchar(100), IN xdireccion varchar(200), IN xemail varchar(200), IN xtelefono varchar(20), IN xdescuento_1 decimal(5, 2), IN xdescuento_2 decimal(5, 2), IN xSlpCode varchar(20))
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
      INSERT INTO entidades (id_tipoentidad,
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
      descuento_2,
      codigo_vendedor)
        VALUES (xid_tipoentidad, xcliente_cardcode, xnro_cuit, xnombre, xdireccion, xemail, xtelefono, xcliente_cardcode, '', 1, xdescuento_1, xdescuento_2, xSlpCode);
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
$$

DELIMITER ;