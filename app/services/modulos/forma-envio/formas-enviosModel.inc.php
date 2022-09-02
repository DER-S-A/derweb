<?php

/**
 * Esta clase permite manejar la tabla de formas_envios
 * 
 */
class Formas_enviosModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla formas_envios.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM formas_envios ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
}

?>