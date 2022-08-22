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

        $campo = "descuento_p1";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo DECIMAL(5, 2) NOT NULL DEFAULT 0";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
            sc3updateField("sucursales", $campo, "Descuento P1", 0, "0");
        }

        $campo = "descuento_p2";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo DECIMAL(5, 2) NOT NULL DEFAULT 0";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
            sc3updateField("sucursales", $campo, "Descuento P2", 0, "0");
        }
    }
    
    /**
     * actualizarFormaEnvio
     * Permite actualizar la estructura de la tabla formas de envío.
     * @return void
     */
    public static function actualizarFormaEnvio() {
        $tabla = "formas_envios";
        $query = "formas_envios";
        $campo = "mostrar_transporte";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo TINYINT(3) NOT NULL DEFAULT 0";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
            sc3updateField($query, $campo, "Mostrar transporte", 1, "0");
        }
    }
}
?>