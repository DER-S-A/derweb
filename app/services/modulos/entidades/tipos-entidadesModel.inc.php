<?php

/**
 * Esta clase permite manejar la tabla de tipos_entidades
 * 
 */
class Tipos_entidadesModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla tipos_entidades.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM tipos_entidades ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
}

?>