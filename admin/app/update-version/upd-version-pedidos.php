<?php
/**
 * Clase: UPDVersionPedidos
 * Descripción:
 *  Actualiza cambios en las estructuras de la base de datos de pedidos.
 */

 class UPDVersionPedidos extends UpdateVersion {
    /**
     * actualizar
     * Actualiza la versión del módulo de pedidos.
     * @return void
     */
    public static function actualizar() {
        self::incorporarCamposAPedidos();
    }

    /**
     * incorporarCamposAPedidos
     * Agrega campos a la tabla pedidos. Esta tabla no está instalada en el core.
     * @return void
     */
    private static function incorporarCamposAPedidos() {
        $tabla = "pedidos";

        // Agrego campo id_tipoentidad
        $campo = "id_tipoentidad";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo INT NOT NULL AFTER id_entidad";
            self::ejecutarSQL($sql);
            sc3addFk($tabla, $campo, "tipos_entidades");
        }

        // Agrego el campo vendedor.
        $campo = "id_vendedor";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo INT NOT NULL AFTER id_estado";
            self::ejecutarSQL($sql);
            sc3addFk($tabla, $campo, "entidades");
        }

        // Agrego el campo sucursal.
        $campo = "id_sucursal";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo BIGINT(20) NULL AFTER id_vendedor";
            self::ejecutarSQL($sql);
            sc3addFk($tabla, $campo, "sucursales");
        }

        // Agrego el código de la sucursal
        $campo = "codigo_sucursal";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo VARCHAR(30) NULL AFTER id_sucursal";
            self::ejecutarSQL($sql);
        }

        $campo = "id_formaenvio";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo INT NOT NULL AFTER codigo_sucursal";
            self::ejecutarSQL($sql);
            sc3addFk($tabla, $campo, "formas_envios");
        }

        // Agrego campo transportes
        $campo = "id_transporte";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo INT NULL AFTER id_formaenvio";
            self::ejecutarSQL($sql);
            sc3addFk($tabla, $campo, "transportes");
        }

        // Agrego campo código de transporte
        $campo = "codigo_transporte";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo VARCHAR(20) AFTER id_transporte";
            self::ejecutarSQL($sql);
        }

        // Agrego campo id_televenta
        $campo = "id_televenta";
        if (!sc3existeCampo($tabla, $campo)) {
            $sql = "ALTER TABLE $tabla ADD $campo INT NULL AFTER codigo_transporte";
            self::ejecutarSQL($sql);
            sc3addFk($tabla, $campo, "entidades");
        }

        // Agrego campo observaciones.
        if (!sc3existeCampo($tabla, "observacion")) {
            $sql = "ALTER TABLE pedidos ADD COLUMN observacion text";
            self::ejecutarSQL($sql);
        }

        // Agrego si fué transferido o no a SAP
        if (!sc3existeCampo($tabla, "transferido_sap")) {
            $sql = "ALTER TABLE pedidos ADD COLUMN transferido_sap TINYINT(3) NULL DEFAULT 0";
            self::ejecutarSQL($sql);
        }

        // Agrego si fué enviado por el cliente
        if (!sc3existeCampo($tabla, "fecha_enviado")) {
            $sql = "ALTER TABLE pedidos ADD COLUMN fecha_enviado datetime";
            self::ejecutarSQL($sql);
        }

        // Amplio el código de sucursal
        $campo = "codigo_sucursal";
        $sql = "ALTER TABLE $tabla MODIFY COLUMN $campo varchar(100)";
        self::ejecutarSQL($sql);
    }
}