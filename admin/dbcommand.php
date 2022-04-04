<?php
/**
 * DBCommand.
 * Esta clase permite ejecutar procedimientos almacenados y asignar
 * los valores a los parámetros teniendo en cuenta el tipo de datos.
 * Heredada de DBObject
 * Por: Leonardo Z.
 */

class DBCommand {
    /**
     * Permite ejecutar un procedimiento almacenado en donde la transacción se maneja
     * internamente en dicho procedimiento.
     * @param mixed $sql
     * Sentencia SQL con la llamada al procedimiento.
     * @return [type]
     */
    public function execute($sql) {
        $result = new BDObject();
        $result->execQuery($sql);
        return $result->getValue("result");
    }

    
    /**
     * Permite ejecutar procedimientos almacenados que no manejan la transacción internamente,
     * sino que la transacción se maneja desde el PHP.
     * @param mixed $xsql
     * Comando SP.
     * @param mixed $xbdObject
     * Instancia BDObject() que contiene la conexión con la base de datos
     * @return [type]
     */
    public function execute_sinT($xsql, $xbdObject) {
        /*$xbdObject->execQuery($xsql);
        return $xbdObject->getValue("result");*/
        return $xbdObject->executeCommand($xsql);
    }

    
    public function setParameter(&$sqlCommand, $parameterName, 
            $value) {
        switch (gettype($value)) {
            case 'boolean':
                $sqlCommand = str_replace($parameterName, 
                        $this->getBooleanValue($value), $sqlCommand);
                break;
            case 'integer':
                $sqlCommand = str_replace($parameterName, $value, 
                        $sqlCommand);
                break;
            case 'double':
                $sqlCommand = str_replace($parameterName, 
                        $this->getDoubleValue($value), $sqlCommand);
                break;
            case 'string':
                $sqlCommand = str_replace($parameterName, 
                        $this->getStringValue($value), $sqlCommand);
                break;
        }	
    }

    private function getBooleanValue($value) {
        return $value == true ? 1 : 0;
    }

    private function getStringValue($value) {
        return "'" . str_replace("'", "''", $value) . "'";
    }

    private function getDoubleValue($value) {
        return str_replace(",", ".", $value);
    }

    public function getRs($sql) {
        $rs = new BDObject();
        $rs->execQuery($sql);
        return $rs;
    }
}