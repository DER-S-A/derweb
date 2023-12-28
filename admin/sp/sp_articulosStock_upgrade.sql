CREATE PROCEDURE `sp_articulosstock_upgrade`(
	IN `ItemCode` varchar(50),
	IN `Cantidad` int
)
BEGIN
    DECLARE IdArticulo int;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
      ROLLBACK;
      GET DIAGNOSTICS CONDITION 1
      @sqlstate = RETURNED_SQLSTATE,
      @errno = MYSQL_ERRNO,
      @text = MESSAGE_TEXT;

      SET @message = CONCAT('Error ', @errno, ': ', @text, ' - SQLSTATE: ', @sqlstate);
      
      INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('articulosStock_Upgrade', @message);
    END;
    START TRANSACTION;
    SET IdArticulo = 0;

    SELECT id INTO IdArticulo FROM articulos WHERE codigo = ItemCode;
    SELECT IdArticulo;
    IF IdArticulo != 0 THEN
      UPDATE articulos SET existencia_stock = Cantidad WHERE id = IdArticulo;
    END IF;

    COMMIT;

END