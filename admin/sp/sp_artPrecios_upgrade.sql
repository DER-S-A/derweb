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
        VALUES ('artPrecios_upgrade', @message);
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
      id_articulo = vIdArticulo;

    IF vCantReg = 0 THEN
      INSERT INTO articulos_precios (id_listaprecio, id_articulo, precio_lista, precio_anterior)
        VALUES (@vIdPriceList, vIdArticulo, Price, 0);
    ELSE
      SELECT
        COUNT(*) 
      INTO 
        vCantRegPrecios
      FROM 
        articulos_precios
      WHERE 
        id_articulo = vIdArticulo AND
        id_listaprecio = @vIdPriceList;

      IF vCantRegPrecios = 0 THEN

        INSERT INTO articulos_precios (id_listaprecio, id_articulo, precio_lista, precio_anterior)
          VALUES (@vIdPriceList, vIdArticulo, Price, 0);

      ELSEIF vCantRegPrecios = 1 THEN

        SELECT
          precio_lista 
        INTO 
          @vPrecioAnterior
        FROM 
          articulos_precios
        WHERE 
          id_articulo = vIdArticulo
        AND 
          id_listaprecio = @vIdPriceList;

        IF @vPrecioAnterior != Price THEN
          UPDATE articulos_precios
          SET precio_anterior = @vPrecioAnterior,
              precio_lista = Price
          WHERE id_articulo = vIdArticulo
          AND id_listaprecio = @vIdPriceList;
        END IF;

      END IF;
    END IF;
    COMMIT;
END
