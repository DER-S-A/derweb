<?php

/**
 * MargenesEspModel
 * Clase de acceso a datos para la tabla margenes especiales.
 */
class MargenesEspModel extends Model {
     /**
     * get
     * Obtiene cualquier tipo de consulta sobre la tabla margenes especiales.
     * @param  string $filter Filtro a aplicar en la consulta
     * @return array
     */
    function get($filter) {
        $sql = "SELECT 
                    * 
                FROM 
                    margenes_especiales ";
        $this->setWhere($sql, $filter);
        return $this->getQuery($sql);
    }

    function cargarMargenesEspeciales($datos) {
        $objBD = new BDObject();
        $datos = json_decode($datos, true);
        $aResult = [];
        $objBD->beginT();
        try {       
            $sql = "INSERT INTO margenes_especiales 
            (id_rubro, id_subrubro, id_marca, id_sucursal, rentabilidad_1, rentabilidad_2, habilitado)
            VALUES (xid_rubro, xid_subrubro, xid_marca, xidsucursal, xrentabilidad_1, xrentabilidad_2, 1)";


            
            $this->setParameter($sql, "xidsucursal", $datos["id_sucursal"]);
            $this->setParameter($sql, "xid_rubro", $datos["id_rubro"]);
            $this->setParameter($sql, "xid_subrubro", $datos["id_subrubro"]);
            $this->setParameter($sql, "xid_marca", $datos["id_marca"]);
            $this->setParameter($sql, "xrentabilidad_1", doubleval($datos["rentabilidad_1"]));
            $this->setParameter($sql, "xrentabilidad_2", doubleval($datos["rentabilidad_2"]));

            $objBD->execInsert($sql);

            $objBD->commitT();

            $aResult["codigo"] = "success";
            $aResult["title"] = "OK";
            $aResult["mensaje"] = "El margen especial se agregó satisfactoriamente";
        } catch (Exception $e) {
            $objBD->rollbackT();
            $aResult["codigo"] = "DB_ERROR";
            $aResult["mensaje"] = $e->getMessage();
        } finally {
            $objBD->close();
        }

        return $aResult;
    }

    function borrarMargenesEspeciales($id) {
        $objBD = new BDObject();
        $aResult = [];
        $objBD->beginT();
        try {       
            $sql = "DELETE FROM margenes_especiales WHERE id=$id";

            $objBD->execInsert($sql);

            $objBD->commitT();

            $aResult["codigo"] = "success";
            $aResult["title"] = "OK";
            $aResult["mensaje"] = "El margen especial se elimino satisfactoriamente";
        } catch (Exception $e) {
            $objBD->rollbackT();
            $aResult["codigo"] = "DB_ERROR";
            $aResult["mensaje"] = $e->getMessage();
        } finally {
            $objBD->close();
        }

        return $aResult;
    }
}