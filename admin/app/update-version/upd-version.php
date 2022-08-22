<?php

/**
 * UpdateVersion
 * Contiene métodos útiles para crear updversions.
 */
class UpdateVersion {    
    /**
     * ejecutarSQL
     * Permite ejecutar un comando SQL.
     * @param  string $sql
     * @return void
     */
    public static function ejecutarSQL($sql) {
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
    }
}
?>