<?php

/**
 * Esta clase permite manejar la tabla de lfw_operaciones
 * 
 */


class Lfw_operacionesModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla lfw_operaciones.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM lfw_operaciones ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
}

?>