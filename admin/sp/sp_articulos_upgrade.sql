CREATE PROCEDURE sp_articulos_upgrade (
	xrubro_cod varchar(20),
    xsubrubro_cod varchar(20),
    xmarca_cod varchar(20),
    xcodigo varchar(20),
    xcodigo_original varchar(50),
	xdescripcion varchar(100),
	xalicuota_iva decimal(20,2),
	xexistencia_stock decimal(20,2),
	xstock_minimo decimal(20,2)
)
BEGIN
	DECLARE vMensaje TEXT;
    DECLARE vCantReg INT;
    DECLARE vIdArticulo INT;
    DECLARE vIdRubro INT;
    DECLARE vIdSubrubro INT;
    DECLARE vIdMarca INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		ROLLBACK;
        GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
        INSERT INTO log_sp (
			nombre_sp,
            mensaje_error)
		VALUES (
			'articulos_upgrade',
            vMensaje);
    END;
    START TRANSACTION;
    
    -- Levanto el id. de rubro
    SET vIdRubro = (SELECT id FROM rubros WHERE rubros.codigo = xrubro_cod);
        
	-- Levanto el id. de subrubro
    SET vIdSubrubro = (SELECT id FROM subrubros WHERE subrubros.codigo = xsubrubro_cod);
    
    -- Levanto el id. de marca
    SET vIdMarca = (SELECT id FROM marcas WHERE marcas.codigo = xmarca_cod);
    
    -- Verifico si el artículo existe.
    SELECT
		COUNT(*)
	INTO
		vCantReg
	FROM
		articulos
	WHERE
		articulos.codigo = xcodigo;
        
	IF vCantReg = 0 THEN
		-- Si no existe, entonces lo doy de alta
		INSERT INTO articulos (
			id_rubro,
            id_subrubro,
            id_marca,
            codigo,
            codigo_original,
            descripcion,
            alicuota_iva,
            existencia_stock,
            stock_minimo,
            fecha_alta
        ) VALUES (
			vIdRubro,
			vIdSubrubro,
			vIdMarca,
			xcodigo,
			xcodigo_original,
			xdescripcion,
			xalicuota_iva,
			xexistencia_stock,
			xstock_minimo,
            current_timestamp);
		
        -- A Equivalencia le asigno el ID ya que en principio no debe
        -- asociar ninguna equivalencia.
		SET vIdArticulo = (SELECT @@identity);
        UPDATE
			articulos
		SET
			articulos.equivalencia = vIdArticulo
		WHERE
			articulos.id = vIdArticulo;
    ELSE
		-- Si el artículo existe, entonces, actualizo los datos del mismo.
		-- Recupero el id. de artículo que tengo que modificar.
        SET vIdArticulo = (SELECT id FROM articulos WHERE articulos.codigo = xcodigo);
        
        -- Actualizo los datos del artículo.
        UPDATE
			articulos
		SET
			articulos.id_rubro = vIdRubro,
            articulos.id_subrubro = vIdSubrubro,
            articulos.id_marca = vIdMarca,
            articulos.codigo = xcodigo,
            articulos.codigo_original = xcodigo_original,
            articulos.descripcion = xdescripcion,
            articulos.alicuota_iva = xalicuota_iva,
            articulos.existencia_stock = xexistencia_stock,
            articulos.stock_minimo = xstock_minimo,
            articulos.es_nuevo = 0,
            articulos.fecha_modificado = current_timestamp
		WHERE
			articulos.id = vIdArticulo;
    END IF;
    
    COMMIT;
END