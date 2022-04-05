<?php
/*
 * Clase: DBCommand
 * Desarrollado por: Zulli, Leonardo Diego
 * Descripción: Esta clase permite ejecutar los diferentes comandos SQL.
 * Fecha de creación: 21/10/2016
*/

class DBCommand {
    public static $ErrorMessage;

    public static function getQuery($sql, &$linkDB) {
        return mysqli_query($linkDB, $sql);
    }

    public static function execute($sql, &$linkDB) {
        $sentencia = mysqli_prepare($linkDB, $sql);
        return mysqli_stmt_execute($sentencia);
    }

    public static function fetch_array($result) {
        return mysqli_fetch_array($result);
    }

    public static function fetch_assoc($result) {
        return mysqli_fetch_assoc($result);
    }

    public static function row_count($result) {
        return mysqli_num_rows($result);
    }

    public static function free_result($result) {
        mysqli_free_result($result);
    }

    public static function fetch_all($res) {
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    public static function next_result($linkDB) {
        mysqli_next_result($linkDB);
    }

    public static function currentId($dbLink)
    {
        $result = mysqli_query($dbLink, "SELECT @@IDENTITY AS current_id") 
                or die("Error al sacar el último ID");
        $a = mysqli_fetch_array($result);
        return $a["current_id"];
    }

    public static function setParameter(&$sqlCommand, $parameterName, 
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
        }	
    }

    private static function getBooleanValue($value) {
        return $value == true ? 1 : 0;
    }

    private static function getStringValue($value) {
        return "'" . str_replace("'", "''", $value) . "'";
    }

    private static function getDoubleValue($value) {
        return str_replace(",", ".", $value);
    }	
} 

