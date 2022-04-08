<?php

/**
 * EntidadesModel
 * Clase de acceso a datos para la tabla entidades.
 */
class EntidadesModel extends Model {
    public function get($filter) {
        $sql = "SELECT * FROM entidades ";
        $this->getWhere($sql, $filter);
        return $this->ejecutar_comando($sql);
    }
}
?>