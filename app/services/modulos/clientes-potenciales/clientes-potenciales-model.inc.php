<?php

/**
 * Esta clase permite manejar la tabla de rubros
 * 
 */


class ClientesPotencialesModel extends Model 
{
    /**
     * get
     * Devuelve los registros de la tabla nombre_tabla.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */
    public function get($xfilter) 
    {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM clientes_potenciales ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);

    }
    
    /**
     * insert
     * Permite insertar un cliente potencial desde la pantalla de registro.
     * @param  string $xRegistro JSON con el registro a cargar armado.
     * @return array Resultado de la operación.
     */
    public function insert($xRegistro) 
    {
        $aResult = array();
        $aRegistro = json_decode($xRegistro, true);
        $aRubrosSeleccionados = $aRegistro["rubros"];

        // Recupero el ID. del estado inicial para insertar en el
        // registro del cliente.
        $sql = "SELECT 
                    id 
                FROM 
                    clipot_estados 
                WHERE 
                    estado_inicial = 1";
        $rs = $this->getQuery2($sql, false);
        $idEstado = intval($rs->getValue("id"));
        $rs->close();

        // Guardo el registro del cliente potencial.
        $bd = new BDObject();
        $bd->beginT();
        try {
            $sql = "INSERT INTO clipot_registros (
                id_estado,
                apenom,
                telefono,
                email,
                ubicacion)
            VALUES (
                xidEstado,
                xApeNom,
                xTelefono,
                xEmail,
                xUbicacion)";
            $this->setParameter($sql, "xidEstado", $idEstado);
            $this->setParameter($sql, "xApeNom", $aRegistro["apenom"]);
            $this->setParameter($sql, "xTelefono", $aRegistro["telefono"]);
            $this->setParameter($sql, "xEmail", $aRegistro["email"]);
            $this->setParameter($sql, "xUbicacion", $aRegistro["ubicacion"]);
            $ultimoID = $bd->execInsert($sql);

            // Recorro los rubros de venta seleccionado y grabo los datos en la tabla
            // clipot_subrubros.
            foreach ($aRubrosSeleccionados as $rubro) {
                $sql = "INSERT INTO clipot_rubros (
                    id_rubro,
                    id_registro,
                    descripcion)
                VALUES (
                    xidRubro,
                    xidRegistro,
                    xdescripcion)";

                $this->setParameter($sql, "xidRubro", $rubro["id"]);
                $this->setParameter($sql, "xidRegistro", $ultimoID);
                $this->setParameter($sql, "xdescripcion", $rubro["descripcion"]);
                $bd->execInsert($sql);
            }

            // Actualizo el checksum de la tabla.
            sc3UpdateTableChecksum("clipot_registros", $bd);
            sc3UpdateTableChecksum("clipot_rubros", $bd);

            $bd->commitT();

            $aResult["result_code"] = "OK";
            $aResult["result_message"] = "Cliente registrado satisfactoriamente";            
        } catch (Exception $e) {
            $bd->rollbackT();
            $aResult["result_code"] = "BD_ERROR";
            $aResult["result_message"] = $e->getMessage();
        } finally {
            $bd->close();
        }

        return $aResult;
    }
}

?>