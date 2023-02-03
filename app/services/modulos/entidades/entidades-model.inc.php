<?php

/**
 * EntidadesModel
 * Clase de acceso a datos para la tabla entidades.
 */
class EntidadesModel extends Model {
        
    /**
     * get
     * Obtiene cualquier tipo de consulta sobre la tabla entidades
     * @param  string $filter Filtro a aplicar en la consulta
     * @return array
     */
    public function get($filter) {
        $sql = "SELECT 
                    * 
                FROM 
                    entidades ";
        $this->setWhere($sql, $filter);
        return $this->getQuery($sql);
    }
    
    /**
     * verificarUsuarioYClaveCliente
     * Permite verificar usuario y contraseña de un cliente.
     * @param  string $xusuario Usuario
     * @param  string $xclave Clave
     * @return array Array con los datos de la validación
     */
    public function verificarUsuarioYClaveCliente($xusuario, $xclave) {
        $aResult = array();
        $sql = "SELECT 
                    entidades.id, 
                    cliente_cardcode, 
                    usuario, 
                    clave,
                    id_tipoentidad,
                    tipo_login
                FROM 
                    entidades
                        INNER JOIN tipos_entidades ON tipos_entidades.id = entidades.id_tipoentidad
                WHERE 
                    usuario = '$xusuario'";
        $aDatos = $this->getQuery($sql);
        
        if ($aDatos != null) {
            if (strcmp($aDatos[0]["clave"], $xclave) == 0) {
                $aResult["result"] = "OK";
                $aResult["usuario"] = $aDatos[0]["usuario"];
                // $aResult["clave"] = $aDatos[0]["clave"];
                if (sonIguales($aDatos[0]["tipo_login"], "V")) {
                    $aResult["id_vendedor"] = intval($aDatos[0]["id"]);
                    $aResult["id_cliente"] = null;
                }
                else {
                    $aResult["id_vendedor"] = null;
                    $aResult["id_cliente"] = intval($aDatos[0]["id"]);
                }

                $aResult["codigo"] = $aDatos[0]["cliente_cardcode"];
                $aResult["id_tipoentidad"] = intval($aDatos[0]["id_tipoentidad"]);
                $aResult["tipo_login"] = $aDatos[0]["tipo_login"];

            } else {
                $aResult["result"] = "ERR_CLAVE";
                $aResult["mensaje"] = "Contraseña inválida.";
            }
        } else {
            $aResult["result"] = "ERR_USUARIO";
            $aResult["mensaje"] = "Usuario inválido.";
        }
        return $aResult;
    }

    /**
     * upgrade
     * Permite actualizar los datos de la tabla entidades.
     * @param  string $registro
     * @return array Resultado de la operación
     */
    public function upgradeClientes($registro) {
        $aResult = array();
        $bd = new BDObject();
        try {
            $aRegistro = json_decode($registro, true);
            $strCardCode = $aRegistro["CardCode"];
            $strCuit = esVacio($aRegistro["TaxId"]) ? "" : $aRegistro["TaxId"];
            $strCardName = esVacio($aRegistro["CardName"]) ? "SIN NOMBRE" : $aRegistro["CardName"];
            $strEMail = esVacio($aRegistro["E_Mail"]) ? "" : $aRegistro["E_Mail"];
            $strTelefono = esVacio($aRegistro["Phone1"]) ? "" : $aRegistro["Phone1"];
            $descuento_p1 = doubleval($aRegistro["U_ONESL_DescuentoP1"]);
            $descuento_p2 = doubleval($aRegistro["U_ONESL_DescuentoP2"]);
            $sql = "CALL sp_entidades_upgrade(	
                xid_tipoentidad,
                xcliente_cardcode,
                xnro_cuit,
                xnombre,
                xdireccion,
                xemail,
                xtelefono,
                xdescuento_1,
                xdescuento_2)";
            $this->setParameter($sql, "xid_tipoentidad", 1);
            $this->setParameter($sql, "xcliente_cardcode", $strCardCode);
            $this->setParameter($sql, "xnro_cuit", $strCuit);
            $this->setParameter($sql, "xnombre", $strCardName);
            $this->setParameter($sql, "xdireccion", "");
            $this->setParameter($sql, "xemail", $strEMail);
            $this->setParameter($sql, "xtelefono", $strTelefono);
            $this->setParameter($sql, "xdescuento_1", $descuento_p1);
            $this->setParameter($sql, "xdescuento_2", $descuento_p2);
            $bd->execQuery($sql);

            $aResult["result_code"] = "OK";
            $aResult["result_message"] = "Clientes actualizados satisfactoriamente"; 
        } catch (Exception $ex) {
            $aResult["result_code"] = "BD_ERROR";
            $aResult["result_message"] = $ex->getMessage();
        } finally {
            $bd->close();
        }

        return $aResult;        
    }
    
    /**
     * getBySesion
     * Permite obtener los datos del cliente que inició la sesión.
     * @param  string $xsession JSON con los datos de la sesión actual.
     * @return array
     */
    public function getBySesion($xsesion) {
        $session = json_decode($xsesion, true);
        $id_cliente = $session["id_cliente"];

        $sql = "SELECT
                    *
                FROM
                    entidades
                WHERE
                    entidades.id = $id_cliente";
        $aCliente = getRs($sql, true)->getAsArray();
        return $aCliente;
    }
    
    /**
     * getSucursalesByEntidad
     * Obtiene el listado de sucursales por entidad.
     * @param  int $xidEntidad
     * @return array
     */
    public function getSucursalesByEntidad($xidEntidad) {
        $sql = "SELECT
                    *
                FROM
                    sucursales
                WHERE
                    sucursales.id_entidad = $xidEntidad
                ORDER BY
                    predeterminado DESC";
        return getRs($sql, true)->getAsArray();
    }
    
    /**
     * getClientesByVendedor
     * Obtiene la lista de clientes filtrando por vendedor.
     * Solo recupera los clientes que están habilitados.
     * @param  int $xidVendedor
     * @return array
     */
    public function getClientesByVendedor($xidVendedor) {
        $sql = "SELECT
                    e.id,
                    /*s.id AS 'id_sucursal',*/
                    e.cliente_cardcode as 'codent',
                    /*s.codigo_sucursal AS 'codsuc',*/
                    e.usuario AS 'codusu',
                    e.nombre as 'nombre',
                    e.nro_cuit AS 'cuit'
                FROM
                    entidades e
                        INNER JOIN tipos_entidades t ON t.id = e.id_tipoentidad
                        INNER JOIN sucursales s ON s.id_entidad = e.id
                WHERE
                    t.tipo_login = 'C' AND
                    s.id_vendedor = $xidVendedor AND
                    e.habilitado = 1
                GROUP BY 
                    e.id,cliente_cardcode,nombre,cuit
                ORDER BY
                    nombre;";
        return getRs($sql, true)->getAsArray();
    }

    /**
    * cambiarClave
    * Cambia el password del cliente.
    * Solo recupera los clientes que están habilitados.
    * @param  int $xid (este es el id de entidades, osea el id del cliente).
    * @param  string $xreset (esta es la clave nuevva q pone el cliente).
    * @return array
    */
    public function cambiarClave($xreset, $xid, $xActual) {
        $aResult = array();

        $bd = new BDObject();

        $sql = "SELECT clave FROM entidades WHERE id = $xid";

        $aDatos = $this->getQuery($sql);
        $xActual = json_decode($xActual,true);

        if($aDatos[0]["clave"] == $xActual){

            $bd->beginT();
            try {
                $sql = "UPDATE entidades SET clave = $xreset
                WHERE id = $xid";
                $bd->execInsert($sql);

                // Actualizo el checksum de la tabla.
                sc3UpdateTableChecksum("entidades", $bd);

                $bd->commitT();

                $aResult["result_code"] = "OK";
                $aResult["result_message"] = "Contraseña cambiada satisfactoriamente.";            
            } catch (Exception $e) {
                $bd->rollbackT();
                $aResult["result_code"] = "BD_ERROR";
                $aResult["result_message"] = $e->getMessage();
            } finally {
                $bd->close();
            }

            //return $aResult;
        } else {
            $aResult["result_code"] = "NO";
            $aResult["result_message"] = "Contraseña actual incorrecta.";
        }
        return $aResult;
    }
    public function olvideMiContrasenia($usuario, $mail, $cuit) {

        $sql = "SELECT clave from entidades where id_tipoentidad=1 and usuario='c23900' 
        and email= 'PETRUCCI@TALLERESPETRUCCI.COM.AR' and nro_cuit= '20041149443'";

        $result = getRs($sql, true)->getAsArray();
        if(empty($result)) {
            $result = 'CLIENTE NO ENCONTRADO';
        }
        return $result;
    }
}
?>