<?php

/**
 * Esta clase permite manejar la tabla de sucursales
 * 
 */


class SucursalesModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla sucursales.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */
    public function get($xfilter){
        $aResponse = [];
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM sucursales ";
        $this->setWhere($sql, $xfilter);
        $rsSuc = $this->getQuery2($sql);  
        $i = 0;
        while (!$rsSuc->EOF()) {
            $aResponse[$i]["id"] = $rsSuc->getValueInt("id");
            $aResponse[$i]["id_formaenvio"] = $rsSuc->getValueInt("id_formaenvio");
            $aResponse[$i]["id_entidad"] = $rsSuc->getValueInt("id_entidad");
            $aResponse[$i]["id_provincia"] = $rsSuc->getValueInt("id_provincia");
            $aResponse[$i]["id_vendedor"] = $rsSuc->getValueInt("id_vendedor");
            $aResponse[$i]["id_transporte"] = $rsSuc->getValueInt("id_transporte");
            $aResponse[$i]["codigo_sucursal"] = $rsSuc->getValue("codigo_sucursal");
            $aResponse[$i]["nombre"] = $rsSuc->getValue("nombre");
            $aResponse[$i]["calle"] = $rsSuc->getValue("calle");
            $aResponse[$i]["numero"] = $rsSuc->getValue("numero");
            $aResponse[$i]["departamento"] = $rsSuc->getValue("departamento");
            $aResponse[$i]["ciudad"] = $rsSuc->getValue("ciudad");
            $aResponse[$i]["codigo_postal"] = $rsSuc->getValue("codigo_postal");
            $aResponse[$i]["telefono"] = $rsSuc->getValue("telefono");
            $aResponse[$i]["mail"] = $rsSuc->getValue("mail");
            $aResponse[$i]["global_localizacion_number"] = $rsSuc->getValue("global_localizacion_number");
            $aResponse[$i]["predeterminado"] = $rsSuc->getValueInt("predeterminado");
            $aResponse[$i]["descuento_p1"] = $rsSuc->getValueInt("descuento_p1");
            $aResponse[$i]["descuento_p2"] = $rsSuc->getValueInt("descuento_p2");

            // Levanto el código de transporte predeterminado
            $sql = "SELECT codigo_transporte FROM transportes WHERE id = " . $rsSuc->getValue("id_transporte");
            $rs = getRs($sql, true);
            $aResponse[$i]["codigo_transporte"] = $rs->getValue("codigo_transporte");
            $rs->close();

            // Levanto el código de la forma de envío predeterminada.
            $sql = "SELECT codigo FROM formas_envios WHERE id = " . $rsSuc->getValueInt("id_formaenvio");
            $rs = getRs($sql, true);
            $aResponse[$i]["codigo_forma_envio"] = $rs->getValue("codigo");
            $rs->close();

            $i++;
            $rsSuc->next();
        }
        $rsSuc->close();

        return $aResponse;
    }
    /** 
     * getSucursales 
     * Obtengo la direccion de la sucursal de la session
     * @param  string $xsesion JSON con los datos de la sesión actual.
     * @return direccion del envío 
     */
    public function getNombreSucursal($xsesion){

        $session = json_decode($xsesion, true);
        // $idCliente = intval($session[0]["id_cliente"]);
        $codigoSucursal = intval($session["id_sucursal"]);
        $sql = "SELECT * FROM sucursales WHERE id =" . $codigoSucursal;
        $rs = getRs($sql, true);
        $aNombre = $rs->getValue("nombre");
        $rs->close();

        return $aNombre;
        
    }
     /** 
     * getVendedor 
     * Obtengo el numero de vendedor del cliente
     * @param  string $xsesion JSON con los datos de la sesión actual.
     * @return numero vendedor
     */
    public function getVendedorSucursal($xsesion){
        $session = json_decode($xsesion,true);
        $codigoSucursal = intval($session["id_sucursal"]);
        $sql = "SELECT replace(entidades.cliente_cardcode, 'v', '')as cliente_cardcode ,sucursales.* FROM sucursales inner join entidades on entidades.id = id_vendedor where sucursales.id =". $codigoSucursal;   
        $rs = getRs($sql, true);
        $aVendedor = $rs->getValue("cliente_cardcode");
        $rs->close();

        return $aVendedor;
    }
/** 
     * getDireccionSucursal 
     * Obtengo la direccion completa de la sucursal
     * @param  string $xsesion JSON con los datos de la sesión actual.
     * @return array datos rs
     */
    public function getDireccionSucursal($xsesion){
        $aDireccion = [];
        $session = json_decode($xsesion,true);
        $sql = "SELECT 
                        sucursales.* 
                FROM
                        sucursales
                WHERE 
                        sucursales.id = ". $session["id_sucursal"];
         $rsSuc = $this->getQuery2($sql);
            $aDireccion["ShipToState"] = $rsSuc->getValueInt("id_provincia");
            $aDireccion["ShipToStreet"] = $rsSuc->getValue("calle");
            $aDireccion["ShipToCity"] = $rsSuc->getValue("ciudad");
            $aDireccion["ShipToZipCode"] = $rsSuc->getValue("codigo_postal");
        return $aDireccion;
    }


    /**
     * upgrade
     * Permite actualizar los datos de la tabla entidades.
     * @param  string $registro
     * @return array Resultado de la operación
     */
    public function upgradeSucursales($registro) {
        $aResult = array();
        $bd = new BDObject();
        try {
            $aRegistro = json_decode($registro, true);
            $strSucursalCode = $aRegistro["SucursalCode"];
            $strSucursalName = $aRegistro["SucursalName"];
            $strCardCode = $aRegistro["CardCode"];
            $strTipoCode = $aRegistro["TipoCode"];
            $strCalle = esVacio( $aRegistro["Calle"]) ? "SIN CALLE" : $aRegistro["Calle"];
            $strCiudad = esVacio($aRegistro["Ciudad"]) ? "SIN CIUDAD" : $aRegistro["Ciudad"];
            $intEstadoCode = esVacio($aRegistro["EstadoCode"]) ? 99 : $aRegistro["EstadoCode"];
            $intZipCode = esVacio($aRegistro["ZipCode"]) ? 0 : $aRegistro["ZipCode"];
            $intGln = esVacio($aRegistro["Gln"]) ? 0 : $aRegistro["Gln"];
            $intCardCodeDER = esVacio($aRegistro["ONESL_CardCodeDER"]) ? 1 : $aRegistro["ONESL_CardCodeDER"];
            $strCreateDate = $aRegistro["CreateDate"];
            $sql = "CALL sp_Sucursales_upgrade(	
                xSucursalCode,
                xSucursalName,
                xCardCode,
                xTipoCode,
                xCalle,
                xCiudad,
                xEstadoCode,
                xZipCode,
                xGln,
                xCardCodDER,
                xCreateDate
                )";
            $this->setParameter($sql, "xSucursalCode", $strSucursalCode);
            $this->setParameter($sql, "xSucursalName", $strSucursalName);
            $this->setParameter($sql, "xCardCode", $strCardCode);
            $this->setParameter($sql, "xTipoCode", $strTipoCode);
            $this->setParameter($sql, "xCalle", $strCalle);
            $this->setParameter($sql, "xCiudad", $strCiudad);
            $this->setParameter($sql, "xEstadoCode", $intEstadoCode);
            $this->setParameter($sql, "xZipCode", $intZipCode);
            $this->setParameter($sql, "xGln", $intGln);
            $this->setParameter($sql, "xCardCodDER", $intCardCodeDER);
            $this->setParameter($sql, "xCreateDate", $strCreateDate);
            $bd->execQuery($sql); 
            $aResult["result_code"] = "OK";
            $aResult["result_message"] = "Sucursal actualizada satisfactoriamente";
        } catch (Exception $ex) {
            $aResult["result_code"] = "BD_ERROR";
            $aResult["result_message"] = $ex->getMessage();
        } finally {
            $bd->close();
        }

        return $aResult;        
    }
}

?>