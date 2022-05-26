<?php

/**
 * Esta clase permite manejar la tabla de paises
 * 
 */
class PaisesModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla paises.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM paises ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }

    /**
     * upgrade
     * Permite actualizar los datos de la tabla paises.
     * @param  string $registro
     * @return array Resultado de la operación
     */
    public function upgrade($registro) {
        $aResult = array();
        $bd = new BDObject();
        try {
            $aRegistro = json_decode($registro, true);
            $strCodigo = $aRegistro["PaisCode"];
            $strDescripcion = $aRegistro["PaisName"];
            $sql = "CALL sp_paises_upgrade(xcodigo, xdescripcion)";
            $this->setParameter($sql, "xcodigo", $strCodigo);
            $this->setParameter($sql, "xdescripcion", $strDescripcion);
            $bd->execQuery($sql);

            $aResult["result_code"] = "OK";
            $aResult["result_message"] = "País registrado satisfactoriamente"; 
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