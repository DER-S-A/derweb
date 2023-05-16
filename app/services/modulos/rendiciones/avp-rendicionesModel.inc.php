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
     * @return void
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
}

?>