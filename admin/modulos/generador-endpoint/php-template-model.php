<?php

/**
 * Esta clase permite manejar la tabla de nombre_tabla
 * 
 */


class nombre_claseModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla nombre_tabla.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM nombre_tabla ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
}

?>