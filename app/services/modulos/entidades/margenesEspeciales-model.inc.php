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
                    marcas.descripcion AS marcaNom, rubros.descripcion AS rubroNom, subrubros.descripcion AS subrubroNom, m.* 
                FROM 
                    margenes_especiales AS m
                LEFT JOIN marcas ON marcas.id = m.id_marca
                LEFT JOIN rubros ON rubros.id = m.id_rubro
                LEFT JOIN subrubros ON subrubros.id = m.id_subrubro ";
        $this->setWhere($sql, $filter);
        return $this->getQuery($sql);
    }

    function cargarMargenesEspeciales($datos, $id_suc) {
        $objBD = new BDObject();
        $datos = json_decode($datos, true);
        $contar =0;
        //return $datos;
        $aResult = [];
        $objBD->beginT();
        try {
            foreach ($datos as $dato) {       
                if($dato['id'] == '') {
                    $sql = "INSERT INTO margenes_especiales 
                (id_rubro, id_subrubro, id_marca, id_sucursal, rentabilidad_1, rentabilidad_2, habilitado)
                VALUES (xid_rubro, xid_subrubro, xid_marca, xid_sucursal, xrentabilidad_1, xrentabilidad_2, 1)";


                $dato["rubro"] != 'TODAS' ? $this->setParameter($sql, "xid_rubro", $dato["rubro"]) : $this->setParameter($sql, "xid_rubro", null);
                $dato["subrubro"] != 'TODAS' ? $this->setParameter($sql, "xid_subrubro", $dato["subrubro"]) : $this->setParameter($sql, "xid_subrubro", null);
                $dato["marca"] != 'TODAS' ? $this->setParameter($sql, "xid_marca", $dato["marca"]) : $this->setParameter($sql, "xid_marca", null);
                $this->setParameter($sql, "xid_sucursal", $id_suc);
                $this->setParameter($sql, "xrentabilidad_1", doubleval($dato["margen1"]));
                $this->setParameter($sql, "xrentabilidad_2", doubleval($dato["margen2"]));

                $objBD->execInsert($sql);
                }

            } 
            
            $objBD->commitT();

            $aResult["codigo"] = "success";
            $aResult["title"] = "OK";
            $aResult["mensaje"] = "Operacion realizada satisfactoriamente";
        } catch (Exception $e) {
            $objBD->rollbackT();
            $aResult["codigo"] = "DB_ERROR";
            $aResult["mensaje"] = $e->getMessage();
        } finally {
            $objBD->close();
        }

        return $aResult;
    }

    function borrarMargenesEspeciales($datos) {
        $objBD = new BDObject();
        $aResult = [];
        $datos = json_decode($datos, true);
        $objBD->beginT();
        try {
            foreach ($datos as $dato) {
                $id = $dato[0]['id'];
                $sql = "DELETE FROM margenes_especiales WHERE id=$id";

                $objBD->execInsert($sql);
            }
            

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