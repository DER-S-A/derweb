<?php
/**
 * Clase UpdateAvisosDePagos
 * Descripción:
 *  Permite instalar y/o actualizar el módulo de avisos de pagos.
 */

class UpdateAvisosDePagos extends UpdateVersion {    
    /**
     * actualizar
     * Actualiza el módulo de avisos de pagos.
     * @return void
     */
    public static function actualizar() {
        self::crearMenu();
        self::instalarTablaAvpRendiciones();
        self::instalarOActualizarTablaAVPMovimientos();
    }
    
    /**
     * crearMenu
     * Crea el menú donde voy a poner las tablas a modo consulta para controlar.
     * @return void
     */
    private static function crearMenu() {
        sc3AgregarMenu("Avisos de pagos", 5, "fa-ticket");
    }
    
    /**
     * instalarTablaAvpRendiciones
     * Permite instalar o actualizar la tabla de avisos de pagos. Esta tabla se incorpora como cabecera
     * debido a que en este caso tengo que tener en cuenta de los avisos deberán estar separados
     * por vendedores.
     * Cuando se envíe la rendición se adjuntará el archivo PDF.
     * @return void
     */
    private static function instalarTablaAvpRendiciones() {
        $tabla = "avp_rendiciones";
        $query = getQueryName($tabla);
        $tablaEntidades = "entidades";
        $queryEntidades = $tablaEntidades;
        
        if (!sc3existeTabla($tabla)) {
            $sql = "CREATE TABLE $tabla (
                id int not null unique auto_increment,
                id_entidad int not null,
                fecha int not null default current_timestamp,
                fecha_enviado datetime null,
                importe_retiro decimal(20, 2) not null default 0,
                efectivo_depositado decimal(20, 2) not null default 0,
                gastos_transporte decimal(20, 2) not null default 0,
                gastos_generales decimal(20, 2) not null default 0,
                efectivo_entregado decimal(20, 2) not null default 0,
                observaciones text null,
                archivo_pdf varchar(255) null,
                rendido tinyint(3) not null default 0,
                enviado tinyint(3) not null default 0,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB";
            self::ejecutarSQL($sql);
            sc3addFk($tabla, "id_entidad", $tablaEntidades);

            sc3agregarQuery($query, $tabla, "Avisos de pagos", "", "", 0, 0, 0, "", 5);
            sc3generateFieldsInfo($tabla);
            sc3updateField($query, "id", "Aviso N°");
            sc3updateField($query, "id_entidad", "Entidad", 1);
            sc3updateField($query, "fecha", "Fecha", 1);
            sc3updateField($query, "importe_retiro", "Retiró", 1, "0");
            sc3updateField($query, "efectivo_depositado", "Efectivo depositado", 1, "0");
            sc3updateField($query, "gasto_transporte", "Gastos transporte", 1, "0");
            sc3updateField($query, "gastos_generales", "Gastos generales", 1, "0");
            sc3updateField($query, "efectivo_entregado", "Efectivo entregado", 1, "0");
            sc3updateField($query, "archivo_pdf", "Rendición PDF", 0, "", 1);
            sc3updateField($query, "observaciones", "Observaciones");
            sc3updateField($query, "rendido", "Rendido", 1);
            sc3updateField($query, "enviado", "Enviado", 1);
            
            sc3addlink($query, "id_entidad", $queryEntidades, 0);
            sc3AgregarQueryAPerfil($query, "Root");
        }
    }
    
    /**
     * instalarOActualizarTablaAVPMovimientos
     * Contiene los movimientos de los avisos de pagos, es decir, los recibos que entran
     * que se cargan hoy por hoy como aviso de pagos.
     * @return void
     */
    private static function instalarOActualizarTablaAVPMovimientos() {
        $tabla = "avp_movimientos";
        $query = getQueryName($tabla);
        $tablaRendiciones = "avp_rendiciones";
        $queryAvisosPagos = getQueryName($tablaRendiciones);
        $tablaEntidades = "entidades";
        $queryEntidades = $tablaEntidades;
        $tablaSucursales = "sucursales";
        $querySucursales = $tablaSucursales;

        if (!sc3existeTabla($tabla)) {
            $sql = "CREATE TABLE $tabla (
                id bigint not null unique auto_increment,
                id_rendicion int not null,
                id_entidad int not null,
                id_sucursal bigint not null,
                fecha datetime not null default current_timestamp,
                numero_recibo varchar(20) not null,
                importe_efectivo decimal(20, 2) not null default 0,
                importe_cheques decimal(20, 2) not null default 0,
                importe_deposito decimal(20, 2) not null default 0,
                importe_retenciones decimal(20, 2) not null default 0,
                total_recibo decimal(20, 2) not null default 0,
                PRIMARY KEY (id)) ENGINE=InnoDB";

            self::ejecutarSQL($sql);
            sc3addFk($tabla, "id_rendicion", $tablaRendiciones);
            sc3addFk($tabla, "id_entidad", $tablaEntidades);
            sc3addFk($tabla, "id_sucursal", $tablaSucursales);

            sc3agregarQuery($query, $tabla, "Avisos de pagos", "", "", 0, 0, 0, "id", 11, "", 1);
            sc3generateFieldsInfo($tabla);
            sc3updateField($query, "id", "Aviso N°");
            sc3updateField($query, "id_rendicion", "Rendición N°", 1);
            sc3updateField($query, "id_entidad", "Cliente", 1);
            sc3updateField($query, "id_sucursal", "Sucursal", 1);
            sc3updateField($query, "fecha", "Fecha", 1);
            sc3updateField($query, "Número de recibo", "numero_recibo", 1);
            sc3updateField($query, "importe_efectivo", "Efectivo", 1);
            sc3updateField($query, "importe_cheques", "Cheques", 1);
            sc3updateField($query, "importe_deposito", "Depósito", 1);
            sc3updateField($query, "importe_retenciones", "Retenciones", 1);
            sc3updateField($query, "total_recibo", "Importe Recibo", 1);
            
            sc3addlink($query, "id_rendicion", $queryAvisosPagos, 1);
            sc3addlink($query, "id_entidad", $queryEntidades);
            sc3addlink($query, "id_sucursal", $querySucursales);

            sc3AgregarQueryAPerfil($query, "Root");
        }
    }
}