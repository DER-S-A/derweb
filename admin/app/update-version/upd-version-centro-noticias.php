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
                PRIMARY KEY (id))";
            self::ejecutarSQL($sql);
        }

        sc3agregarQuery($query, $tabla, "Novedades", "Panel", "descripcion", 1, 1, 1, "id");
        sc3generateFieldsInfo($tabla);
        sc3updateField($query, "id", "Novedad N°", 0);
        sc3updateField($query, "descripcion", "Descripción", 1);
        sc3updateField($query, "fecha", "Fecha", 1);
        sc3updateField($query, "imagen", "Imagen", 1, "", 1);
        sc3updateField($query, "publicado", "Publicado", 1, "0");
        sc3AgregarQueryAPerfil($query, "Root");

        $campo = "es_oferta";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo tinyint(3) NOT NULL DEFAULT 0";
            self::ejecutarSQL($sql);
        }

        sc3generateFieldsInfo($tabla);
        sc3updateField($query, $campo, "Es oferta", 1, "0");

        sc3SetNombreQuery($query, "Centro de noticias");
        sc3SetQueryFields($query, "id, descripcion, fecha, imagen, publicado, es_oferta");
    }

    
}
?>