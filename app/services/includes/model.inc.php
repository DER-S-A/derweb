<?php
/**
 * Clase: Model
 * Descripción:
 *  Esta clase dispone de la funcionalidad de abrir y cerrar la conexión con
 *  el motor de base de datos MySQL.
 */

class Model {
    protected $idCliente = 0;
    protected $idTipoEntidad = 0;
    protected $id_listaprecio = 0;
    protected $descuento_p1 = 0.00;
    protected $descuento_p2 = 0.00;
    protected $rentabilidad = 0.00;
    protected $idSucursal = 0;
    protected $tipoLogin = "";

    /**
     * ejecutar_comando
     * Devuelve un conjunto de resultados a partir de una sentencia SELECT.
     * Nota: El propósito de esta función es cuando se quiere recuperar la información
     *  en un javascript.
     * @param  string $xstrsql Sentencia SQL a ejecutar.
     * @return array $result
     */
    protected function getQuery($xstrsql) {
        $rs = getRs($xstrsql, true);
        $result = $rs->getAsArray();
        $rs->close();
        return $result;
    }
    
    /**
     * getQuery2
     * Retorna el objeto BDObject con los resultados.
     * Nota: El propósito de este método es cuando se va a recuperar información
     *  que se trabajará en el backend solamente.
     * @param  string $xstrsql Sentencia SQL a ejecutar
     * @return BDObject
     */
    protected function getQuery2($xstrsql) {
        return getRs($xstrsql);
    }

    /**
     * setWhere
     * Establece la cláusula WHERE en base al criterio definiro en la URL.
     * @param  string $xsql Establece la sentencia SQL a incluir el WHERE.
     * @param  string $xfilter Contiene el filtro a aplicar.
     * @return string
     */
    protected function setWhere(&$xsql, $xfilter) {
        if (strcmp($xfilter, "") != 0) {
            $xfilter = str_replace("\"", "", $xfilter);
            $xsql .= "WHERE " . $xfilter; 
        }
        return $xsql;
    }
        
    /**
     * setParameter
     * Permite reemplazar los parámetros de una sentencia SQL.
     * @param  string $sqlCommand Comando SQL. Pasa por referencia.
     * @param  string $parameterName $Nombre del parámetro
     * @param  mixed $value valor
     * @return void
     */
    protected static function setParameter(&$sqlCommand, $parameterName, 
            $value) {
        switch (gettype($value)) {
            case 'boolean':
                $sqlCommand = str_replace($parameterName, 
                        self::getBooleanValue($value), $sqlCommand);
                break;
            case 'integer':
                $sqlCommand = str_replace($parameterName, $value, 
                        $sqlCommand);
                break;
            case 'double':
                $sqlCommand = str_replace($parameterName, 
                        self::getDoubleValue($value), $sqlCommand);
                break;
            case 'string':
                $sqlCommand = str_replace($parameterName, 
                        self::getStringValue($value), $sqlCommand);
                break;
            case 'NULL':
                $sqlCommand = str_replace($parameterName, 'NULL', $sqlCommand);
                break;
        }	
    }
    
    /**
     * getBooleanValue
     * Devuelve valor booleano formateado para SQL
     * @param  mixed $value
     * @return bool
     */
    private static function getBooleanValue($value) {
        return $value == true ? 1 : 0;
    }
    
    /**
     * getStringValue
     * Devuelve string formateado para SQL. Contempla apóstrofes.
     * @param  mixed $value
     * @return string
     */
    private static function getStringValue($value) {
        return "'" . str_replace("'", "''", $value) . "'";
    }
    
    /**
     * getDoubleValue
     * Devuelve string formateado para SQL de un valor double.
     * @param  mixed $value
     * @return string
     */
    private static function getDoubleValue($value) {
        return str_replace(",", ".", $value);
    }
    
    /**
     * getClienteActual
     * Levanta los datos de descuento y rentabilidad del cliente acutalmente logueado.
     * @param  string $xsesion JSON con la sesi´pon iniciada en DERWEB.
     * @return Array Datos del cliente
     */
    protected function getClienteActual($xsesion) {
        $objEndidadesModel = new EntidadesModel();
        $aSesion = json_decode($xsesion, true);
        $aCliente = $objEndidadesModel->getBySesion($xsesion);
        $this->id_listaprecio = intval($aCliente[0]["id_listaprecio"]);
        $this->descuento_p1 = doubleval($aCliente[0]["descuento_1"]);
        $this->descuento_p2 = doubleval($aCliente[0]["descuento_2"]);
        $this->rentabilidad = doubleval($aCliente[0]["rentabilidad_1"]);
        $this->idCliente = intval($aCliente[0]["id"]);
        $this->idTipoEntidad = intval($aSesion["id_tipoentidad"]);
        $this->idSucursal = intval($aSesion["id_sucursal"]);
        $this->tipoLogin = $aSesion["tipo_login"];
    }
}
?>