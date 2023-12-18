<?php

/**
 * UpdateVersionCuentasCorrientes
 * Permite actualizar el módulo de cuentas corrientes.
 * Fecha: 18/12/2023
 */
class UpdateVersionCuentasCorrientes extends UpdateVersion {
    /**
     * actualzar
     * Ejecuta la actualización del módulo de cuentas corrientes.
     * @return void
     */
    public static function actualzar() {
        self::agregarTablaCuentasCorrientes();
    }
    
    /**
     * agregarTablaCuentasCorrientes
     * Permite agregar la tabla de cuentas corrientes.
     * @return void
     */
    private static function agregarTablaCuentasCorrientes() {
        $tabla = "cuentas_corrientes";
        $query = getQueryName($tabla);

        if (!sc3existeTabla($tabla)) {
            $sql = "CREATE TABLE $tabla (
                        id int not null unique auto_increment,
                        form_code varchar(30) not null,
                        id_entidad int not null,
                        cliente_cardcode int not null,
                        fecha datetime not null,
                        tipo_moneda varchar(3) not null,
                        numero varchar(20) null,
                        doc_total decimal(20, 2) not null,
                        PRIMARY KEY (id),
                        FOREIGN KEY (id_entidad) REFERENCES entidades (id)
                        )Engine=InnoDB";
            self::ejecutarSQL($sql);
            sc3agregarQuery($query, $tabla, "Cuentas Corrientes", "", "", 0, 0, 0);
            sc3generateFieldsInfo($tabla);
        }
    }
}
?>