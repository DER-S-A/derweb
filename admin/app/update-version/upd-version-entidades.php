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

        $campo = "nombre";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo VARCHAR(60) NOT NULL DEFAULT '' AFTER codigo_sucursal";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
            sc3updateField("sucursales", $campo, "Nombre", 1);
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
    
    /**
     * agregarCamposTiposEntidades
     * Agrega campos a la tabla tipos de entidades.
     * @return void
     */
    public static function agregarCamposTiposEntidades() {
        $tabla = "tipos_entidades";
        $query = "tipos_entidades";

        // Agrego el campo para poder identificar el tipo de entidad que se
        // loguea en el DERWEB.
        $campo = "tipo_login";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo VARCHAR(1) NOT NULL DEFAULT 'C'";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
            sc3updateField($query, $campo, "Tipo Login", 1, "C");
            sc3updateFieldHelp($tabla, $campo, "C: Cliente | V: Vendedor | T: Televenta");
        }
    }

    /**
     * cambiarTamanioCampoUsuarioEnEntidades
     * Permite cambiar el tamaño al campo usuario de la tabla entidades.
     * @return void
     */
    public static function cambiarTamanioCampoUsuarioEnEntidades() {
        $sql = "ALTER TABLE entidades CHANGE COLUMN usuario usuario VARCHAR(20) NOT NULL";
        self::ejecutarSQL($sql);
    }
}
?>