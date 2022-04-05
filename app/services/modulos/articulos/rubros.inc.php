<?php
/**
 * Esta clase permite manejar la tabla de rubros
 * 
 */

class RubrosModel extends Model {

    /**
     * Obtiene todos los registros de la tabla rubros.
     */
    public function getAll() {
        $sql = "SELECT
                    *
                FROM
                    rubros
                ORDER BY
                    rubros.descripcion ASC";
        return $this->ejecutar_comando($sql);
    }

    /**
     * getById
     * Devuelve un rubro filtrando por id.
     * @param  int $id
     * @return array $result
     */
    public function getById($id) {
        $sql = "SELECT * FROM rubros WHERE id = $id";
        return $this->ejecutar_comando($sql);
    }
}
?>