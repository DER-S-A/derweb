<?php
/**
 * Clase: RendicionesAdminModel
 * Descripción:
 *  Clase que se encarga de manejar las tablas de rendiciones.
 */

class RendicionesAdminModel extends Model {    
    /**
     * getRendicion
     * Devuelve los datos de la rendición.
     * @param  mixed $xidRendicion Id. Rendición
     * @return BDObject
     */
    public function getRendicion($xidRendicion) {
        $sql = "SELECT
                    r.*,
                    e.nombre
                FROM
                    avp_rendiciones r
                        INNER JOIN entidades e ON e.id = r.id_entidad
                WHERE
                    r.id = 3";
        return getRs($sql, true);
    }

    /**
     * getMovimientosByRendicion
     * Devuelve los avisos de pagos de una dererminada rendición
     * @param  int $xidRendicion
     * @return BDObject
     */
    public function getMovimientosByRendicion($xidRendicion) {
        $sql = "SELECT
                    avisos.id AS 'Numero_aviso',
                    avisos.fecha AS 'Fecha',
                    avisos.numero_recibo AS 'Recibo',
                    ent.cliente_cardcode AS 'Cliente N°',
                    ent.nombre AS 'Razon_social',
                    suc.codigo_sucursal AS 'Codigo_sucursal',
                    suc.nombre AS 'Sucursal',
                    avisos.importe_efectivo,
                    avisos.importe_cheques,
                    avisos.importe_deposito,
                    avisos.importe_retenciones,
                    avisos.total_recibo
                FROM
                    avp_movimientos avisos
                        INNER JOIN entidades ent ON ent.id = avisos.id_entidad
                        INNER JOIN sucursales suc ON suc.id = avisos.id_sucursal
                WHERE
                    avisos.id_rendicion = $xidRendicion
                ORDER BY
                    avisos.numero_recibo";
        return getRs($sql);
    }
    
    /**
     * getMovimientoById
     * Obtiene un movimiento (aviso de pago) por su ID.
     * @param  int $xidMovimiento
     * @return array
     */
    public function getMovimientoById($xidMovimiento) {
        $sql = "SELECT
                    avisos.id,
                    avisos.fecha,
                    avisos.numero_recibo,
                    ent.cliente_cardcode,
                    ent.nombre AS 'cliente_nombre',
                    suc.codigo_sucursal,
                    suc.nombre AS 'sucursal_nombre',
                    avisos.importe_efectivo,
                    avisos.importe_cheques,
                    avisos.importe_deposito,
                    avisos.importe_retenciones,
                    avisos.total_recibo,
                    avisos.revisado
                FROM
                    avp_movimientos avisos
                        INNER JOIN entidades ent ON ent.id = avisos.id_entidad
                        INNER JOIN sucursales suc ON suc.id = avisos.id_sucursal
                WHERE
                    avisos.id = $xidMovimiento";
        return getRs($sql, true)->getAsArray();
    }
    
    /**
     * actualizarMovimiento
     * Actualiza los datos de un movimiento (Aviso de pago)
     * @param  array $aDatos
     * @return array
     */
    public function actualizarMovimiento($aDatos) {
        $sql = "CALL sp_avp_actualizar_movimiento (
            xid, xEfectivo, xCheques, xDeposito, 
            xRetenciones, xTotalRecibo, xRevisado)";
        $this->setParameter($sql, "xEfectivo", doubleval($aDatos["importe_efectivo"]));
        $this->setParameter($sql, "xCheques", doubleval($aDatos["importe_cheques"]));
        $this->setParameter($sql, "xDeposito", doubleval($aDatos["importe_deposito"]));
        $this->setParameter($sql, "xRetenciones", doubleval($aDatos["importe_retenciones"]));
        $this->setParameter($sql, "xTotalRecibo", doubleval($aDatos["total_recibo"]));
        $this->setParameter($sql, "xRevisado", doubleval($aDatos["revisado"]));
        $this->setParameter($sql, "xid", intval($aDatos["id"]));
        return getRs($sql, true)->getAsArray();
    }
}