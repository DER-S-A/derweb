<?php

/**
 * UpdateVersionMantenimiento
 * Permite mantener actualizado le módulo de mantenimiento del sistema.
 * Fecha: 15/12/2023
 */
class UpdateVersionMantenimiento extends UpdateVersion {
    public static function actualizar() {
        self::instalarOperacionMantenimientos();
    }
    
    /**
     * instalarOperacionMantenimientos
     * Permite instalar la operación para realizar el mantenimiento del sistema.
     * @return void
     */
    private static function instalarOperacionMantenimientos() {
        $opid = sc3AgregarOperacion(
            "Mantenimiento", 
            "der-mantenimiento.php", 
            "ico/toolsc3.ico", 
            "Permite realizar el mantenimiento del sistema para liberear espacio en disco.", 
            "", 
            "Desarrollador", 
            0, 
            "Root", 
            "", 
            0, 
            "");
    }    

}
?>