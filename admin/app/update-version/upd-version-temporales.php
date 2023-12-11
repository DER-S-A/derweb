<?php
/**
 * Este script se encarga de agregar las tablas temporales que se requieren para
 * actualizar el DERWEB.
 * Fecha: 11/12/2023
 */

class UpdateVersionAgregarTablasTemporales extends UpdateVersion {
        
    /**
     * actualizar
     * Permite ejecutar los métodos que se encargan de agregar las tablas
     * temporales.
     * @return void
     */
    public static function actualizar() {
        self::agregarTmpVistaDocumentos();
    }
    
    /**
     * agregarTmpVistaDocumentos
     * Permite agregar una tabla temporal para cargar los registros que vienen en vista documento
     * que es una réplica de SAP.
     * API relacionada: https://181.119.123.148:50000/b1s/v1/sml.svc/ONESL_SL_Documentos_B1SLQuery
     * @return void
     */
    private static function agregarTmpVistaDocumentos() {
        $tabla = "tmp_vista_documento";
        if (!sc3existeTabla($tabla)) {
            $sql = "CREATE TABLE IF NOT EXISTS $tabla (
                id int not null auto_increment,
                form_code varchar(20),
                doc_entry int,
                doc_num bigint,
                canceled varchar(1),
                doc_status varchar(1),
                obj_type varchar(10),
                doc_date datetime,
                card_code varchar(20),
                doc_cur varchar(3),
                doc_total decimal(20, 2),
                paid_to_date decimal(20, 2),
                comments varchar(100),
                projects varchar(100),
                frm_l_num varchar(20),
                u_onesl_sisori varchar(1),
                u_onesl_sisorinum varchar(20),
                line_num int,
                item_code varchar(20),
                quantity decimal(10, 2),
                open_qty decimal(10, 2),
                line_cur varchar(3),
                price decimal(20, 2),
                line_total decimal(20, 2),
                base_entry int,
                base_type int,
                base_line int,
                cant_asignada decimal(10, 2),
                gestion varchar(1),
                act_message varchar(100),
                id__ int
            )Engine=InnoDB";
            self::ejecutarSQL($sql);
        }
    }
}
?>