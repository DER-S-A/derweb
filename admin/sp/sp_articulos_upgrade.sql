CREATE PROCEDURE `sp_articulos_upgrade`(xrubro_cod int,
xsubrubro_cod int,
xmarca_cod int,
xcodigo varchar(20),
xcodigo_original varchar(50),
xdescripcion varchar(100),
xalicuota_iva decimal(20, 2),
xexistencia_stock decimal(20, 2),
xstock_minimo decimal(20, 2),
xhabilitado varchar(2),
xUpdateTime datetime)
BEGIN
  DECLARE vMensaje text;
  DECLARE vCantReg int;
  DECLARE vIdArticulo int;
  DECLARE vIdRubro int;
  DECLARE vIdSubrubro int;
  DECLARE vIdMarca int;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    GET DIAGNOSTICS CONDITION 1 vMensaje = MESSAGE_TEXT;
    INSERT INTO log_sp (nombre_sp,
    mensaje_error)
      VALUES ('articulos_upgrade', vMensaje, vMensaje);
  END;
  START TRANSACTION;


    SET vIdRubro = (SELECT
        id
      FROM rubros
      WHERE rubros.codigo = xrubro_cod);


    SET vIdSubrubro = (SELECT
        id
      FROM subrubros
      WHERE subrubros.codigo = xsubrubro_cod);


    SET vIdMarca = (SELECT
        id
      FROM marcas
      WHERE marcas.codigo = xmarca_cod);


    SELECT
      COUNT(*) INTO vCantReg
    FROM articulos
    WHERE articulos.codigo = xcodigo;

    IF vCantReg = 0 THEN
	
		/*Leonardo: Modifico el INSERT INTO por el parámetro que falta para hacerlo
			funcionar. */
		INSERT INTO articulos (
			id_rubro, id_subrubro, id_marca, codigo, codigo_original,
			descripcion, alicuota_iva, existencia_stock, stock_minimo,
			fecha_alta, habilitado, fecha_modificado)
		VALUES (
			vIdRubro, vIdSubrubro, vIdMarca, xcodigo, xcodigo_original, 
			xdescripcion, xalicuota_iva, xexistencia_stock, xstock_minimo, 
			xUpdateTime, xhabilitado, xUpdateTime
		);

		SET vIdArticulo = (SELECT
		  @@identity);
		UPDATE articulos
		SET articulos.equivalencia = vIdArticulo
		WHERE articulos.id = vIdArticulo;
    ELSE


      SET vIdArticulo = (SELECT
          id
        FROM articulos
        WHERE articulos.codigo = xcodigo);


      UPDATE articulos
      SET articulos.id_rubro = vIdRubro,
          articulos.id_subrubro = vIdSubrubro,
          articulos.id_marca = vIdMarca,
          articulos.codigo = xcodigo,
          articulos.codigo_original = xcodigo_original,
          articulos.descripcion = xdescripcion,
          articulos.alicuota_iva = xalicuota_iva,
          articulos.existencia_stock = xexistencia_stock,
          articulos.stock_minimo = xstock_minimo,
          articulos.es_nuevo = 0,
          articulos.fecha_modificado = xUpdateTime
      WHERE articulos.id = vIdArticulo;
    END IF;

  COMMIT;
END