<?php
/**
 * Clase: UpdateVersionCentroNoticias
 * Descripción: Permite actualizar el módulo de centros de noticias
 */
class UpdateVersionCentroNoticias extends UpdateVersion {    
    /**
     * actualizar_version
     * Permite actualizar la versiónd el módulo de centro de noticias.
     * @return void
     */
    public static function actualizar() {
        self::instalarTablaNovedades();
        self::instalarNovedadesArticulos();
    }
    
    /**
     * instalarTablaNovedades
     * Permite instalar la tabla de novedades.
     * @return void
     */
    private static function instalarTablaNovedades() {
        $tabla = "novedades";
        $query = getQueryName($tabla);

        if (!sc3existeTabla($tabla)) {
            $sql = "CREATE TABLE $tabla (
                id int not null unique auto_increment,
                descripcion varchar(100) not null,
                fecha datetime not null default current_timestamp,
                imagen varchar(255) not null,
                publicado tinyint(3) not null default 0,
                PRIMARY KEY (id))ENGINE=InnoDB";
            self::ejecutarSQL($sql);
            sc3agregarQuery($query, $tabla, "Novedades", "Panel", "descripcion", 1, 1, 1, "id");
            sc3generateFieldsInfo($tabla);
            sc3AgregarQueryAPerfil($query, "Root");
        }

        sc3updateField($query, "id", "Novedad N°", 0);
        sc3updateField($query, "descripcion", "Descripción", 1);
        sc3updateField($query, "fecha", "Fecha", 1);
        sc3updateField($query, "imagen", "Imagen", 1, "", 1);
        sc3updateField($query, "publicado", "Publicado", 1, "0");

        sc3SetNombreQuery($query, "Centro de noticias");

        $campo = "es_oferta";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo tinyint(3) NOT NULL DEFAULT 0";
            self::ejecutarSQL($sql);
        }

        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $campo, "Es oferta", 1, "0", 0, "Opciones");

        sc3addFilter($query, "Novedades", "es_novedad = 1");
        sc3addFilter($query, "Ofertas", "es_oferta = 1");

        $campo = "mostrar_portada";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo tinyint(3) NOT NULL DEFAULT 0";
            self::ejecutarSQL($sql);
        }

        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $campo, "Mostrar en portada", 1, "0", 0, "Opciones");
        sc3addFilter($query, "Portada", "mostrar_portada = 1");

        $campo = "es_novedad";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo tinyint(3) NOT NULL DEFAULT 0";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
        }
        sc3updateField($query, $campo, "Es novedad", 1, "0", 0, "Opciones");

        $campo = "url";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo varchar(250) NULL";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
        }
        sc3updateField($tabla, $campo, "URL", 0, "");

        // Modifico los campos que muestro en la grilla de ABMs.
        sc3SetQueryFields($query, "id, descripcion, fecha, imagen, publicado, es_oferta, es_novedad, mostrar_portada");
    }
    
    /**
     * instalarNovedadesArticulos
     * Esta tabla permite agrupar los artículos por novedades.
     * @return void
     */
    private static function instalarNovedadesArticulos() {
        $tabla = "articulos_novedades";
        $query = getQueryName($tabla);
        $tabla_novedades = "novedades";
        $query_noveades = getQueryName($tabla_novedades);
        $tabla_articulos = "articulos";
        $query_articulos = $tabla_articulos;

        if (!sc3existeTabla($tabla)) {
            $sql = "CREATE TABLE $tabla (
                        id bigint not null unique auto_increment,
                        id_novedad int not null,
                        id_articulo int not null,
                        habilitado tinyint(3) not null default 0,
                        PRIMARY KEY (id))ENGINE=InnoDB";
            self::ejecutarSQL($sql);
            sc3addFk($tabla, "id_novedad", $tabla_novedades);
            sc3addFk($tabla, "id_articulo", $tabla_articulos);

            sc3agregarQuery($query, $tabla, "Grupo de artículos", "", "id", 1, 1, 1, "id", 4, "", 1);
            sc3generateFieldsInfo($tabla);
            sc3addlink($query, "id_novedad", $query_noveades, 1);
            sc3addlink($query, "id_articulo", $query_articulos);

            sc3AgregarQueryAPerfil($query, "Root");
        }

        sc3updateField($query, "id", "Id. Interno");
        sc3updateField($query, "id_novedad", "Novedad", 1);
        sc3updateField($query, "id_articulo", "Artículo", 1);
        sc3updateField($query, "habilitado", "Habilitado", 1, "1");

        sc3SetQueryFields($query, "id, id_novedad, id_articulo, habilitado");
    }
}
?>