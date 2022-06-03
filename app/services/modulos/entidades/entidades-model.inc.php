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
        $sql = "SELECT * FROM entidades ";
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
                    id, 
                    cliente_cardcode, 
                    usuario, 
                    clave,
                    id_tipoentidad
                FROM 
                    entidades 
                WHERE 
                    usuario = '$xusuario'";
        $aDatos = $this->getQuery($sql);
        
        if ($aDatos != null) {
            if (strcmp($aDatos[0]["clave"], $xclave) == 0) {
                $aResult["result"] = "OK";
                $aResult["usuario"] = $aDatos[0]["usuario"];
                $aResult["clave"] = $aDatos[0]["clave"];
                $aResult["id_cliente"] = intval($aDatos[0]["id"]);
                $aResult["codigo"] = $aDatos[0]["cliente_cardcode"];
                $aResult["id_tipoentidad"] = intval($aDatos[0]["id_tipoentidad"]);
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
            $strCuit = esVacio($aRegistro["LicTradNum"]) ? "" : $aRegistro["LicTradNum"];
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
     * @param  mixed $xsession
     * @return void
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
        $aCliente = getRs($sql)->getAsArray();
        return $aCliente;
    }
}
?>