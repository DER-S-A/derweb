<?php
/**
 * Clase: UpdateVersionArticulos
 * Descripción:
 *  Permite actualizar el módulo de artículos.
 */

class UpdateVersionArticulos extends UpdateVersion {
    public static function actualziar() {
        self::agregarCampoAMarcas();
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
}
?>