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
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
    
    /**
     * getSubrubrosByRubro
     * Permite obtener los subrubros filtrando por un rubro.
     * @param  int $xid_rubro
     * @return array Conjunto de resultados.
     */
    public function getSubrubrosByRubro($xid_rubro) {
        $sql = "SELECT
                    t1.*
                FROM
                    subrubros t1
                        INNER JOIN articulos t2 ON t2.id_subrubro = t1.id
                WHERE
                    t2.id_rubro = $xid_rubro
                GROUP BY
                    t1.id";
        return $this->getQuery($sql);
    }
}
?>