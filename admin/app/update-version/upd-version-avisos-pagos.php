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
        self::instalarOpControlarRecibos();
        self::instalarOpGenerarRendicion();
    }
    
    /**
     * crearMenu
     * Crea el menú donde voy a poner las tablas a modo consulta para controlar.
     * @return void
     */
    private static function crearMenu() {
        sc3AgregarMenu("Administración", 5, "fa-paperclip");
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
                fecha datetime not null default current_timestamp,
                fecha_enviado datetime null,
                total_efectivo decimal(20, 2) not null default 0,
                total_cheques decimal(20, 2) not null default 0,
                total_deposito decimal(20, 2) not null default 0,
                total_retensiones decimal(20, 2) not null default 0,
                total_recibos decimal(20, 2) not null default 0,
                importe_retiro decimal(20, 2) not null default 0,
                efectivo_depositado decimal(20, 2) not null default 0,
                gastos_transporte decimal(20, 2) not null default 0,
                gastos_generales decimal(20, 2) not null default 0,
                efectivo_entregado decimal(20, 2) not null default 0,
                observaciones text null,
                archivo_pdf varchar(255) null,
                enviado tinyint(3) not null default 0,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB";
            self::ejecutarSQL($sql);
            sc3addFk($tabla, "id_entidad", $tablaEntidades);

            sc3agregarQuery($query, $tabla, "Avisos de pagos", "Administración", "", 0, 0, 0, "", 5);
            sc3generateFieldsInfo($tabla);            
            sc3addlink($query, "id_entidad", $queryEntidades, 0);
        }

        if (!sc3existeCampo($tabla, "revisado")) {
            $sql = "ALTER TABLE $tabla ADD revisado tinyint(3) NOT NULL DEFAULT 0";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
        }

        if (!sc3existeCampo($tabla, "fecha_revision")) {
            $sql = "ALTER TABLE $tabla ADD fecha_revision DATETIME NULL";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
        }

        if (!sc3existeCampo($tabla, "archivo_pdf_ok")) {
            $sql = "ALTER TABLE $tabla ADD archivo_pdf_ok varchar(255)";
            self::ejecutarSQL($sql);
            sc3generateFieldsInfo($tabla);
        }

        // Armo la configuración de campos
        sc3updateField($query, "id", "Aviso N°");
        sc3updateField($query, "id_entidad", "Entidad", 1);
        sc3updateField($query, "fecha", "Fecha", 1);
        sc3updateField($query, "total_efectivo", "Total Efvo.", 1, "0", 0, "Totales");
        sc3updateField($query, "total_cheques", "Total Cheques", 1, "0", 0, "Totales");
        sc3updateField($query, "total_deposito", "Total Depósito", 1, "0", 0, "Totales");
        sc3updateField($query, "total_retensiones", "Total Retensiones", 1, "0", 0, "Totales");
        sc3updateField($query, "total_recibos", "Total Recibos", 1, "0", 0, "Totales");
        sc3updateField($query, "importe_retiro", "Retiró", 1, "0", 0, "Importes");
        sc3updateField($query, "efectivo_depositado", "Efectivo depositado", 1, "0", 0, "Importes");
        sc3updateField($query, "gasto_transporte", "Gastos transporte", 1, "0", 0, "Importes");
        sc3updateField($query, "gastos_generales", "Gastos generales", 1, "0", 0, "Importes");
        sc3updateField($query, "efectivo_entregado", "Efectivo entregado", 1, "0", 0, "Importes");
        sc3updateField($query, "archivo_pdf", "Rendición PDF", 0, "", 1, "PDF Recibido");
        sc3updateField($query, "observaciones", "Observaciones", 0, "Observaciones");
        sc3updateField($query, "enviado", "Enviado", 1, "0");
        sc3updateField($query, "revisado", "Revisado", 1, "0", 0, "Control de Administración");
        sc3updateField($query, "fecha_revision", "Revisado el", 0, "", 0, "Control de Administración");
        sc3updateField($query, "archivo_pdf_ok", "Rendición corregida", 0, "", 1, "Control de Administración");
        
        sc3SetMenuAQuery($query, "Administración");
        sc3AgregarQueryAPerfil($query, "Administración");
        sc3SetNombreQuery($query, "Rendiciones");
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
            
            sc3addlink($query, "id_rendicion", $queryAvisosPagos, 1);
            sc3addlink($query, "id_entidad", $queryEntidades);
            sc3addlink($query, "id_sucursal", $querySucursales);
        }

        // Configuro los campos de la tabla
        sc3updateField($query, "id", "Aviso N°");
        sc3updateField($query, "id_rendicion", "Rendición N°", 1);
        sc3updateField($query, "id_entidad", "Cliente", 1);
        sc3updateField($query, "id_sucursal", "Sucursal", 1);
        sc3updateField($query, "fecha", "Fecha", 1);
        sc3updateField($query, "Número de recibo", "numero_recibo", 1);
        sc3updateField($query, "importe_efectivo", "Efectivo", 1, "0", 0, "Importes");
        sc3updateField($query, "importe_cheques", "Cheques", 1, "0", 0, "Importes");
        sc3updateField($query, "importe_deposito", "Depósito", 1, "0", 0, "Importes");
        sc3updateField($query, "importe_retenciones", "Retenciones", 1, "0", 0, "Importes");
        sc3updateField($query, "total_recibo", "Total del recibo", 1, "0", 0, "Importes");

        sc3AgregarQueryAPerfil($query, "Administración");

        /** Agrego campo revisado */
        if (!sc3existeCampo($tabla, "revisado")) {
            $sql = "ALTER TABLE $tabla ADD revisado tinyint(3) NOT NULL DEFAULT 0";
            self::ejecutarSQL($sql);
            sc3updateField($query, "revisado", "Revisado", 1, "0");
        }
    }
    
    /**
     * instalarOpControlarRecibos
     * Instala la operación para controlar los recibos en avisos de pagos.
     * @return void
     */
    public static function instalarOpControlarRecibos() {
        $opid = sc3AgregarOperacion(
            "Controlar recibos", 
            "der-avp-controlar-recibos.php", 
            "ico/check1.ico", 
            "Permite controlar los avisos de pagos.", 
            "avp_rendiciones", 
            "", 
            0, 
            "Administración", 
            "", 
            0, 
            "qavprendiciones");
    }
    
    /**
     * instalarOpGenerarRendicion
     * Permite instalar la operación para generar la rendición desde
     * el área de administración.
     * @return void
     */
    public static function instalarOpGenerarRendicion() {
        $opid = sc3AgregarOperacion(
            "Generar rendición", 
            "der-avp-generar-rendicion.php", 
            "ico/recibo.ico", 
            "Permite generar la rendición después de haber corregido la información.", 
            "avp_rendiciones", 
            "", 
            0, 
            "Administración", 
            "", 
            0, 
            "qavprendiciones");
    }
}