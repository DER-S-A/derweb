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

}
?>