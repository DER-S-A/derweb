SELECT
                    items.id,
                    items.id_pedido,
                    art.id AS id_articulo,
                    items.cantidad,
                    foto.archivo,
                    art.codigo,
                    art.descripcion AS descripcion_articulo,
                    rub.descripcion AS descripcion_rubro,
                    srb.descripcion AS descripcion_subrubro,
                    precio.precio_lista,
                    art.alicuota_iva
                FROM
                    pedidos_items items
                        INNER JOIN articulos art ON art.id = items.id_articulo
                        INNER JOIN articulos_precios precio ON precio.id_articulo = art.id
                        INNER JOIN listas_precios lpre ON lpre.id = precio.id_listaprecio
                        INNER JOIN pedidos ped ON ped.id = items.id_pedido
                        INNER JOIN estados_pedidos estado ON estado.id = ped.id_estado
                        INNER JOIN rubros rub ON rub.id = art.id_rubro
                        INNER JOIN subrubros srb ON srb.id = art.id_subrubro
                        LEFT OUTER JOIN art_fotos foto ON foto.id_articulo = art.id
                WHERE
                    estado.estado_inicial = 1 AND
                    (foto.predeterminada = 1 OR foto.predeterminada IS NULL) AND
                    ped.id_entidad = 7749 AND
                    lpre.id = 1 AND
                    ped.id_tipoentidad = 3