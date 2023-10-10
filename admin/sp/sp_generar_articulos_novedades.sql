CREATE PROCEDURE sp_generar_articulos_novedades (
    xidNovedad int,
    xidMarca int,
    xidRubro int,
    xidSubrubro int
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        GET DIAGNOSTICS CONDITION 1 @mensaje = MESSAGE_TEXT;
        SELECT 1 AS 'codigo', @mensaje AS 'mensaje';
    END;

    START TRANSACTION;
    INSERT INTO articulos_novedades (
        id_novedad,
        id_articulo,
        habilitado
    )
        SELECT
            xidNovedad AS 'id_novedad',
            art.id,
            1 AS 'habilitado'
        FROM
            articulos art
        WHERE
            CASE WHEN xidMarca IS NULL THEN 1 ELSE art.id_marca = xidMarca END AND
            CASE WHEN xidRubro IS NULL THEN 1 ELSE art.id_rubro = xidRubro END AND
            CASE WHEN xidSubrubro IS NULL THEN 1 ELSE art.id_subrubro = xidSubrubro END;
    COMMIT;

    SELECT 0 AS 'codigo', 'Los artículos se asignaron satisfactoriamente' AS 'mensaje';
END