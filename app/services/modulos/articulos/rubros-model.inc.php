<?php
/**
 * Esta clase permite manejar la tabla de rubros
 * 
 */

class RubrosModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla rubros.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */
    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM rubros ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
}
?>