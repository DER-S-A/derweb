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
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM rubros ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }

    /**
     * upgrade
     * Permite actualizar los datos de la tabla rubros.
     * @param  string $registro
     * @return array Resultado de la operación
     */
    public function upgrade($registro) {
        $aResult = array();
        $bd = new BDObject();
        try {
            $aRegistro = json_decode($registro, true);
            $strCodigo = $aRegistro["RubroCode"];
            $strDescripcion = $aRegistro["RubroName"];
            $sql = "CALL sp_rubros_upgrade(xcodigo, xdescripcion)";
            $this->setParameter($sql, "xcodigo", $strCodigo);
            $this->setParameter($sql, "xdescripcion", $strDescripcion);
            $bd->execQuery($sql);

            $aResult["result_code"] = "OK";
            $aResult["result_message"] = "Rubros actualizados satisfactoriamente"; 
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