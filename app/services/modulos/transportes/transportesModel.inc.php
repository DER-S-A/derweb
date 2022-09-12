<?php

/**
 * Esta clase permite manejar la tabla de transportes
 * 
 */
class TransportesModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla transportes.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM transportes ORDER BY PREDETERMINADO desc ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
}

?>