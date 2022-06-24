CREATE PROCEDURE sp_articulos_getByRubroAndRubro (
    xid_preciolista int,
    xid_rubro int,
    xid_subrubro int,
    xpagina int
)
BEGIN
    SELECT
        art.id,
        art.descripcion,
        art.codigo,
        pre.precio_lista,
        art.existencia_stock,
        art.alicuota_iva
    FROM
        articulos art
            INNER JOIN articulos_precios pre ON pre.id_articulo = art.id
    WHERE
        art.eliminado = 0 AND
        art.habilitado = 1 AND
        art.id_rubro = xid_rubro AND
        art.id_subrubro = xid_subrubro AND
        pre.id_listaprecio = xid_preciolista
    ORDER BY 
        id ASC
    LIMIT 40 OFFSET xpagina;
END