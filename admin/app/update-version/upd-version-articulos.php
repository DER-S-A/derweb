<?php
/**
 * Clase: UpdateVersionArticulos
 * Descripción:
 *  Permite actualizar el módulo de artículos.
 */

class UpdateVersionArticulos extends UpdateVersion {
    public static function actualziar() {
        self::agregarCampoAMarcas();
        self::instalarTablaUnidadesVentas();
    }

    private static function agregarCampoAMarcas() {
        $tabla = "marcas";
        $query = "marcas";

        $campo = "linkLogo";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo varchar(255) NULL";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
            sc3updateField($query, $campo, "Imagen Logo", 0, "", 1);
        }
    }

    private static function instalarTablaUnidadesVentas() {
        $tabla = "art_unidades_ventas";
        $query = getQueryName($tabla);
        if (!sc3existeTabla($tabla)) {
            $sql = "CREATE TABLE $tabla (
                        id int not null unique auto_increment,
                        id_articulo int not null,
                        descripcion varchar(20) not null,
                        unidad_venta decimal(20, 2) not null,
                        PRIMARY KEY (id))";
            self::ejecutarSQL($sql);
            sc3AddFk($tabla, "id_articulo", "articulos");
            sc3agregarQuery($query, $tabla, "Unidades Ventas", "", "", 1, 1, 1, "", 4, "", 1);
            sc3generateFieldsInfo($tabla);
            sc3updateField($query, "id", "U.V. N°");
            sc3updateField($query, "descripcion", "Descripción", 1);
            sc3updateField($query, "unidad_venta", "Unidad Venta", 1, "1");
            sc3addlink($query, "id_articulo", "articulos", 1);
            sc3AgregarQueryAPerfil($query, "root");
        }
    }
}
?>