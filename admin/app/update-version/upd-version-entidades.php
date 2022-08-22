<?php
/**
 * Clase: UpdateVersionEntidades
 * Descripción:
 *  Permite realizar la actualización de entidades.
 */

class UpdateVersionEntidades extends UpdateVersion {    
    /**
     * actualizarTablaSucursales
     * Permite actualizar la estructura de la tabla sucursales
     * @return void
     */
    public static function actualizarTablaSucursales() {
        $tabla = "sucursales";
        $campo = "telefono";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo varchar(20) AFTER codigo_postal";
            self::ejecutarSQL($sql);

            sc3generateFieldsInfo($tabla);
            sc3updateField("sucursales", $campo, "Teléfono", 0);
        }

        $campo = "mail";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo varchar(60) AFTER telefono";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
            sc3updateField("sucursales", $campo, "E-Mail", 0);
        }
    }
}
?>