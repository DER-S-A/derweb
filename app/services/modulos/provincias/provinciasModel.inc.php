<?php

/**
 * Esta clase permite manejar la tabla de provincias
 * 
 */
class ProvinciasModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla provincias.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM provincias ";
        $this->setWhere($sql, $xfilter);
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
            $strCodigo = $aRegistro["EstadoCode"];
            $strCodigoPais = $aRegistro["PaisCode"];
            $strDescripcion = utf8_decode($aRegistro["EstadoName"]);
            $sql = "CALL sp_provincias_upgrade(xcodigo, xcodPais, xdescripcion)";
            $this->setParameter($sql, "xcodigo", $strCodigo);
            $this->setParameter($sql, "xcodPais", $strCodigoPais);
            $this->setParameter($sql, "xdescripcion", $strDescripcion);
            $bd->execQuery($sql);

            $aResult["result_code"] = "OK";
            $aResult["result_message"] = "Provincia registrado satisfactoriamente"; 
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