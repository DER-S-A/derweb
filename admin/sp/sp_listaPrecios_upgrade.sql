USE db_derweb;

DROP PROCEDURE IF EXISTS sp_listaPrecios_upgrade;

DELIMITER $$

CREATE
DEFINER = 'root'@'localhost'
PROCEDURE sp_listaPrecios_upgrade (IN ListNum int, IN ListName text)
BEGIN
  DECLARE CantReg int;
  DECLARE vMensaje text;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
    INSERT INTO log_sp (nombre_sp,
    mensaje_error)
      VALUES ('listasPrecio_upgrade', vMensaje, message_text);
  END;
  START TRANSACTION;

    SELECT
      COUNT(*) INTO CantReg
    FROM listas_precios
    WHERE price_list = ListNum;
    IF CantReg = 0 THEN
      INSERT INTO listas_precios (price_list, descripcion)
        VALUES (ListNum, ListName);

    ELSE
      UPDATE listas_precios
      SET descripcion = ListName
      WHERE price_list = ListNum;
    END IF;

  COMMIT;

END
$$

DELIMITER ;