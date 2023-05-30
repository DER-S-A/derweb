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
                    rend.id = 1";

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
}

?>