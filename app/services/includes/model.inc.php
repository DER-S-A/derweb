<?php
/**
 * Clase: Model
 * Descripción:
 *  Esta clase dispone de la funcionalidad de abrir y cerrar la conexión con
 *  el motor de base de datos MySQL.
 */

class Model {
    private $activeConnection;
    private $objDbConnection;

    /**
     * Establece la conexión con la base de datos.
     */
    protected function open() {
        $this->objDbConnection = new DBConnection();
        $this->activeConnection = $this->objDbConnection->Connect();
    }

    /**
     * Cierra la conexión establecida con la base de datos
     */
    protected function close() {
        $this->objDbConnection->Close($this->activeConnection);
    }

    /**
     * Devuelve la conexión activa para utilizarla dentro de la
     * clase modelo hija.
     */
    protected function getActiveConnection() { 
        return $this->activeConnection;
    }

    /**
     * ejecutar_comando
     * Permite establecer la conexión, ejecutar el comando y cerrar la conexión.
     * Devuelve directamente el result en array.
     * @param  string $sql
     * @return array $result
     */
    protected function ejecutar_comando($sql) {
        $db = new DBCommand();
        $this->open();
        $rs = $db->getQuery($sql, $this->getActiveConnection());
        $result = $db->fetch_all($rs);
        $this->close();
        return $result;
    }
    
    /**
     * setWhere
     * Establece la cláusula WHERE en base al criterio definiro en la URL.
     * @param  string $xsql Establece la sentencia SQL a incluir el WHERE.
     * @param  string $xfilter Contiene el filtro a aplicar.
     * @return string
     */
    protected function getWhere(&$xsql, $xfilter) {
        if (strcmp($xfilter, "") != 0) {
            $xfilter = str_replace("\"", "", $xfilter);
            $xsql .= "WHERE " . $xfilter; 
        }
        return $xsql;
    }

}
?>