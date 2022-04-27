<?php

/**
 * ClientesPotencialesModel
 * Esta clase contiene la funcionalidad para el Core que permite
 * manipular las tablas del módulo de clientes potenciales.
 */
class ClientesPotencialesModel extends Model {
        
    /**
     * cambiarEstado
     * Cambia el estado de un cliente potencial.
     * @param  int $xidRegistro
     * @param  int $xidEstado
     * @return void
     */
    public function cambiarEstado($xidRegistro, $xidEstado) {
        $sql = "UPDATE 
                    clipot_registros 
                SET 
                    id_estado = $xidEstado 
                WHERE 
                    id = $xidRegistro";
        $bd = new BDObject();
        $bd->beginT();
        try {
            $bd->execQuery2($sql);
            sc3UpdateTableChecksum("clipot_registros", $bd);
            $bd->commitT();
        } catch (Exception $ex) {
            $bd->rollbackT();
            echo $ex->getMessage();
        } finally {
            $bd->close();
        }
    }
    
    /**
     * agregarNota
     * Agrega una nota para poder realizar el seguimiento del proceso de
     * negociación.
     * @param  int $xidRegistro
     * @param  string $xNota
     * @return void
     */
    public function agregarNota($xidRegistro, $xNota) {
        $sql = "INSERT INTO clipot_segumiento (
                    id_registro,
                    notas)
                VALUES (
                    xidRegistro,
                    xNota)";

        $this->setParameter($sql, "xidRegistro", $xidRegistro);
        $this->setParameter($sql, "xNota", $xNota);
        $bd = new BDObject();
        $bd->execQuery2($sql);
        sc3UpdateTableChecksum("clipot_segumiento", $bd);
        $bd->close();
    }
}
?>