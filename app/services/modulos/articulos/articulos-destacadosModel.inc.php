<?php

/**
 * Esta clase permite manejar la tabla de articulos_destacados
 * 
 */
class Articulos_destacadosModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla articulos_destacados.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM articulos_destacados ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
}

?>