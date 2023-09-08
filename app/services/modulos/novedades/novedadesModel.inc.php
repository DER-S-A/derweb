<?php

/**
 * Esta clase permite manejar la tabla de novedades
 * 
 */
class NovedadesModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla novedades.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM novedades ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }

    public function getGrupoArticulosByNovedad($xsesion, $xid) {
        $objArticulosModel = new ArticulosModel();
        // Armo un subquery para que me traiga solo los artículos a mostrar
        // en la grilla de artículos.
        $filtro = "art.id IN ( 
                        SELECT 
                            id_articulo 
                        FROM 
                            articulos_novedades 
                        WHERE 
                            id_novedad = $xid AND
                            habilitado = 1)";
        $aResponse = $objArticulosModel->get($xsesion, $filtro, 0);
        return $aResponse;
    }
}

?>