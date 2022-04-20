<?php
/**
 * Esta clase permite manejar la tabla de rubros
 * 
 */

class SubrubrosModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla rubros.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */
    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM subrubros ";
        $this->getWhere($sql, $xfilter);
        return $this->ejecutar_comando($sql);
    }
}
?>