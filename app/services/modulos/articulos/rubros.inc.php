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
        if (strcmp($xfilter, "") == 0)
            // Si el filtro viene vacío entonces muestro todos los registros.
            $sql = "SELECT 
                        * 
                    FROM 
                        rubros 
                    ORDER BY descripcion ASC";
        else {
            // Si viene algún filtro en la petición, entonces,
            // lo concateno al WHERE de la sentencia SQL.
            $xfilter = str_replace("\"", "", $xfilter);
            $sql = "SELECT 
                        * 
                    FROM 
                        rubros 
                    WHERE " . $xfilter;    
        }
        return $this->ejecutar_comando($sql);
    }
}
?>