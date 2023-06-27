<?php

/**
 * Esta clase permite manejar la tabla de avp_rendiciones
 * 
 */
class Avp_rendicionesModel extends Model {
    /**
     * get
     * Devuelve los registros de la tabla avp_rendiciones.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */

    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM avp_rendiciones ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
    
    /**
     * agregarAvisoPago
     * Permite agregar un aviso de pago
     * @return array
     */
    public function agregarAvisoPago($xdatos) {
        $aResponse = [];
        $aDatos = json_decode($xdatos, true);

        // Valido que los datos de los avisos de pagos sean correctos.
        if (!$this->validarAvisosDePagos($aDatos, $aResponse))
            return $aResponse;

        $sql = "CALL sp_avp_agregarAvisoPago (
            xid_vendedor, xid_entidad, xid_sucursal, xfecha,
            xnumero_recibo, ximporte_efectivo, ximporte_cheques, 
            ximporte_deposito, ximporte_retenciones)";
        $this->setParameter($sql, "xid_vendedor", intval($aDatos["id_vendedor"]));
        $this->setParameter($sql, "xid_entidad", $aDatos["id_cliente"]);
        $this->setParameter($sql, "xid_sucursal", intval($aDatos["id_sucursal"]));
        $this->setParameter($sql, "xfecha", $aDatos["fecha"]);
        $this->setParameter($sql, "xnumero_recibo", $aDatos["numero_recibo"]);
        $this->setParameter($sql, "ximporte_efectivo", $aDatos["importe_efectivo"]);
        $this->setParameter($sql, "ximporte_cheques", doubleval($aDatos["importe_cheques"]));
        $this->setParameter($sql, "ximporte_deposito", doubleval($aDatos["importe_deposito"]));
        $this->setParameter($sql, "ximporte_retenciones", doubleval($aDatos["importe_retenciones"]));
        
        $result = getRs($sql);
        $aResponse["result"] = $result->getValue("result");
        $aResponse["mensaje"] = $result->getValue("mensaje");
        $result->close();
        return $aResponse;
    }
    
    /**
     * validarAvisosDePagos
     * Valida que venga correctamente todos los datos de un aviso de pago.
     * @param  array $aDatos Registro del aviso
     * @param  array $aResponse Array de respuesta.
     * @return bool
     */
    private function validarAvisosDePagos(&$aDatos, &$aResponse) {
        if (sonIguales(gettype($aDatos["id_vendedor"]), "string")) {
            $aResponse["result"] = "PARAMETERS_ERROR";
            $aResponse["mensaje"] = "El campo id_vendedor debe ser un valor numérico";
            return false;
        }

        if (sonIguales(gettype($aDatos["id_cliente"]), "string")) {
            $aResponse["result"] = "PARAMETERS_ERROR";
            $aResponse["mensaje"] = "El campo id_cliente debe ser un valor numérico";
            return false;
        }

        if (sonIguales(gettype($aDatos["id_sucursal"]), "string")) {
            $aResponse["result"] = "PARAMETERS_ERROR";
            $aResponse["mensaje"] = "El campo id_sucursal debe ser un valor numérico";
            return false;
        }

        if ($aDatos["id_vendedor"] == 0) {
            $aResponse["result"] = "PARAMETERS_ERROR";
            $aResponse["mensaje"] = "El id_vendedor no puede ser 0 (cero)";
            return false;
        }

        if ($aDatos["id_cliente"] == 0) {
            $aResponse["result"] = "PARAMETERS_ERROR";
            $aResponse["mensaje"] = "El id_cliente no puede ser 0 (cero)";
            return false;
        }

        if ($aDatos["id_sucursal"] == 0) {
            $aResponse["result"] = "PARAMETERS_ERROR";
            $aResponse["mensaje"] = "El id_sucursal no puede ser 0 (cero)";
            return false;
        }

        if (sonIguales($aDatos["numero_recibo"], "")) {
            $aResponse["result"] = "PARAMETER_ERROR";
            $aResponse["mensje"] = "Falta ingresar el número de recibo";
            return false;
        }

        if (sonIguales(gettype($aDatos["importe_efectivo"]), "string")) {
            $aResponse["result"] = "PARAMETERS_ERROR";
            $aResponse["mensaje"] = "El campo importe efectivo debe ser un valor numérico";
            return false;
        }

        if (sonIguales(gettype($aDatos["importe_cheques"]), "string")) {
            $aResponse["result"] = "PARAMETERS_ERROR";
            $aResponse["mensaje"] = "El campo importe cheque debe ser un valor numérico";
            return false;
        }

        if (sonIguales(gettype($aDatos["importe_deposito"]), "string")) {
            $aResponse["result"] = "PARAMETERS_ERROR";
            $aResponse["mensaje"] = "El campo importe depósito debe ser un valor numérico";
            return false;
        }

        if (sonIguales(gettype($aDatos["importe_retenciones"]), "string")) {
            $aResponse["result"] = "PARAMETERS_ERROR";
            $aResponse["mensaje"] = "El campo importe retenciones debe ser un valor numérico";
            return false;
        }

        return true;
    }
        
    /**
     * generarRendicion
     * Permite generar y enviar la rendición.
     * @param  mixed $xdatos
     * @return array
     */
    public function generarRendicion($xdatos) {
        $aResponse = [];
        $aDatos = json_decode($xdatos, true);

        $sql = "CALL sp_avp_generarRendicion (
            xidRendicion,
            xImporteRetiro,
            xEfectivoDepositado,
            xGastosTransporte,
            xGastosGenerales,
            xObservaciones        
        )";
        $this->setParameter($sql, "xidRendicion", intval($aDatos["idRendicion"]));
        $this->setParameter($sql, "xImporteRetiro", doubleval($aDatos["importe_retiro"]));
        $this->setParameter($sql, "xEfectivoDepositado", doubleval($aDatos["efectivo_depositado"]));
        $this->setParameter($sql, "xGastosTransporte", doubleval($aDatos["gastos_transporte"]));
        $this->setParameter($sql, "xGastosGenerales", doubleval($aDatos["gastos_generales"]));
        $this->setParameter($sql, "xObservaciones", $aDatos["observaciones"]);

        $result = getRs($sql);
        $aResponse["result"] = $result->getValue("result");
        $aresponse["mensaje"] = $result->getValue("mensaje");
        $aResponse["id_rendicion"] = $result->getValueInt("id_rendicion");
        $result->close();
        
        // Recupero los movimientos de la rendición
        $rsMovimientos = $this->getMovimientosByIdRendicion(intval($aDatos["idRendicion"]));
        $aResponse["movimientos"] = $rsMovimientos->getAsArray();
        $aResponse["mensaje"] = $result->getValue("mensaje");
        $rsMovimientos->close();

        return $aResponse;
    }
    
    /**
     * getRendicionByID
     * Permite obtener una rendición por su ID.
     * @param  int $xidRendicion
     * @return BDObject
     */
    public function getRendicionByID($xidRendicion) {
        $sql = "SELECT
                    rend.*,
                    e.nombre AS 'vendedor'
                FROM
                    avp_rendiciones rend
                        INNER JOIN entidades e ON e.id = rend.id_entidad
                WHERE
                    rend.id = $xidRendicion";

        return getRs($sql);
    }
    
    /**
     * getMovimientosByIdRendicion
     * Obtiene los movimientos (avisos de pagos) a partir de un id. de rendición.
     * @param  int $xidRendicion
     * @return BDObject
     */
    public function getMovimientosByIdRendicion($xidRendicion) {
        $sql = "SELECT
                    movs.id,
                    movs.id_rendicion,
                    movs.fecha,
                    movs.id_sucursal,
                    e.cliente_cardcode,
                    e.nombre AS 'cliente',
                    s.codigo_sucursal,
                    s.nombre AS 'sucursal',
                    movs.numero_recibo,
                    movs.importe_efectivo,
                    movs.importe_deposito,
                    movs.importe_cheques,
                    movs.importe_retenciones,
                    movs.total_recibo
                FROM
                    avp_movimientos movs
                        INNER JOIN entidades e ON e.id = movs.id_entidad
                        INNER JOIN sucursales s ON s.id = movs.id_sucursal
                WHERE
                    movs.id_rendicion = $xidRendicion
                ORDER BY
                    movs.fecha ASC";
        return getRs($sql, true);
    }
    
    /**
     * actualizarPDFLink
     * Permite actualizar el link y adjuntar el PDF a la rendición
     * que se generó.
     * @param  int $xidRendicion
     * @param  string $xlink
     * @return void
     */
    public function actualizarPDFLink($xidRendicion, $xlink) {
        $sql = "UPDATE
                    avp_rendiciones
                SET
                    avp_rendiciones.archivo_pdf = '$xlink'
                WHERE
                    avp_rendiciones.id = $xidRendicion";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
    }
    
    /**
     * getRendicionAbiertaPorVendedor
     * Obtiene la rendición abierta por vendedor
     * @param  int $xid_vendedor
     * @return BDObject
     */
    public function getRendicionAbiertaPorVendedor($xid_vendedor) {
        $aResponse = [];
        $sql = "SELECT
                    rend.*,
                    e.nombre AS 'vendedor'
                FROM
                    avp_rendiciones rend
                        INNER JOIN entidades e ON e.id = rend.id_entidad
                WHERE
                    e.id = $xid_vendedor AND
                    rend.enviado = 0";

        $aResponse = getRs($sql, true)->getAsArray();
        $idRendicion = $aResponse[0]["id"];

        $rsMovimientos = $this->getMovimientosByIdRendicion($idRendicion);
        $aResponse["movimientos"] = $rsMovimientos->getAsArray();
        $rsMovimientos->close();
        return $aResponse;
    }
}

?>