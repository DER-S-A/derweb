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
    
    /**
     * getByTipoEntidad
     * Obtiene las operaciones para el menú según el tipo de entidad.
     * @param  mixed $xidTipoEntidad
     * @return void
     */
    public function getByTipoEntidad($xidTipoEntidad) {
        // Armado de la sentencia SQL.
        $sql = "SELECT 
                    t1.*
                FROM 
                    lfw_operaciones t1
                        INNER JOIN lfw_accesos t2 ON t1.id = t2.id_operacion 
                WHERE
                    t2.id_tipoentidad = $xidTipoEntidad";
        return $this->getQuery($sql);        
    }
}

?>