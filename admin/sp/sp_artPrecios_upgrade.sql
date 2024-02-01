CREATE PROCEDURE sp_artPrecios_upgrade (
  IN ItemCode text,
  IN PriceList int,
  IN Price decimal(20,2))
BEGIN
    DECLARE vCantReg int;
    DECLARE vIdArticulo int;
    DECLARE vCantRegPrecios int;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
      ROLLBACK;
      GET DIAGNOSTICS CONDITION 1
        @sqlstate = RETURNED_SQLSTATE,
        @errno = MYSQL_ERRNO,
        @text = MESSAGE_TEXT;
      SET @message = CONCAT('Error ', @errno, ': ', @text);
      INSERT INTO log_sp (nombre_sp, mensaje_error)
        VALUES ('artPrecios_upgrade', CONCAT('Artículo: ', ItemCode, ' - ', @message));
    END;

    START TRANSACTION;
    SELECT
      id 
    INTO 
      vIdArticulo
    FROM 
      articulos
    WHERE 
      codigo = ItemCode;

    /* Si el id de artículo no es nulo entonces hago que procese, sino ROLLBACK y listo. */
    IF vIdArticulo IS NOT NULL THEN
      SELECT
        id 
      INTO 
        @vIdPriceList
      FROM 
        listas_precios
      WHERE 
        price_list = PriceList;

      SELECT
        COUNT(*) 
      INTO 
        vCantReg
      FROM 
        articulos_precios
      WHERE 
        id_articulo = vIdArticulo AND
        id_listaprecio = @vIdPriceList;

      IF vCantReg = 0 THEN
        INSERT INTO articulos_precios (id_listaprecio, id_articulo, precio_lista, precio_anterior)
          VALUES (@vIdPriceList, vIdArticulo, Price, 0);
      ELSE
          SELECT
            precio_lista 
          INTO 
            @vPrecioAnterior
          FROM 
            articulos_precios
          WHERE 
            id_articulo = vIdArticulo AND 
            id_listaprecio = @vIdPriceList;

          IF @vPrecioAnterior != Price THEN
            UPDATE articulos_precios
            SET precio_anterior = @vPrecioAnterior,
                precio_lista = Price
            WHERE id_articulo = vIdArticulo
            AND id_listaprecio = @vIdPriceList;
          END IF;
      END IF;
      COMMIT;
    ELSE
      /* Si el id artículo viene en null entonces hago Rollback */
      ROLLBACK;
    END IF;
END
