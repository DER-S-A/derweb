CREATE PROCEDURE sp_listaPrecios_upgrade (IN ListNum int, IN ListName text)
BEGIN
  DECLARE CantReg int;
  DECLARE vMensaje text;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    GET DIAGNOSTICS CONDITION 1
    @sqlstate = RETURNED_SQLSTATE,
    @errno = MYSQL_ERRNO,
    @text = MESSAGE_TEXT;
    SET @message = CONCAT('Error ', @errno, ': ', @text);
    INSERT INTO log_sp (nombre_sp, mensaje_error)
     VALUES ('listaPrecios_upgrade', @message);
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