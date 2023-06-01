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
        $sql .= " ORDER BY descripcion ASC";
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
                    t1.id
                ORDER BY 
                    t1.descripcion";
        return $this->getQuery($sql);
    }
    
    /**
     * upgrade
     * Permite actualizar los datos de la tabla subrubros.
     * @param  string $registro
     * @return array Resultado de la operación
     */
    public function upgrade($registro) {
        $aResult = array();
        $bd = new BDObject();
        try {
            $aRegistro = json_decode($registro, true);
            $strCodigo = $aRegistro["SubRubroCode"];
            $strDescripcion = $aRegistro["SubRubroName"];
            $sql = "CALL sp_subrubros_upgrade(xcodigo, xdescripcion)";
            $this->setParameter($sql, "xcodigo", $strCodigo);
            $this->setParameter($sql, "xdescripcion", $strDescripcion);
            $bd->execQuery($sql);

            $aResult["result_code"] = "OK";
            $aResult["result_message"] = "Subrubros registrado satisfactoriamente"; 
        } catch (Exception $ex) {
            $aResult["result_code"] = "BD_ERROR";
            $aResult["result_message"] = $ex->getMessage();
        } finally {
            $bd->close();
        }

        return $aResult;        
    }
}
?>