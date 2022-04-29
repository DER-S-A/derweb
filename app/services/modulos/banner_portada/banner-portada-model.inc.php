<?php
/**
 * Esta clase permite manipular los datos de la tabla banner_portada.
 */

class BannerPortadaModel extends Model {
        
    /**
     * get
     * Permite obtener todos los registros del banner de portada.
     * @param  string $xfilter Establece el filtro WHERE
     * @return array Retorna el array para levantarlo desde Javascript.
     */
    public function get($xfilter) {
        $sql = "SELECT * FROM banner_portada ORDER BY id ASC";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
}
?>