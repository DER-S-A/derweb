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
                    r.id = $xidRendicion";
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
    
    /**
     * confirmarRevision
     * Permite confirmar una rendición que fue confirmada.
     * @param  int $xidRendicion
     * @return string
     */
    public function confirmarRevision($xidRendicion) {
        $linkPDF = $this->generarRendicionPDF($xidRendicion);
        $sql = "UPDATE
                    avp_rendiciones
                SET 
                    avp_rendiciones.revisado = 1,
                    avp_rendiciones.fecha_revision = CURRENT_TIMESTAMP(),
                    avp_rendiciones.archivo_pdf_ok = '$linkPDF'
                WHERE
                    avp_rendiciones.id = $xidRendicion";
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->close();
        return "ufiles/" . $linkPDF;
    }
    
    /**
     * generarRendicionPDF
     * Permite generar la rendición en PDF.
     * @param int $xidRendicion
     * @return string
     */
    private function generarRendicionPDF($xidRendicion) {
        $pdf = new AVPRendicionesPDF();
        $pdf->idRendicion = $xidRendicion;
        $aLink = $pdf->imprimir();
        $linkPDF = $aLink["path_to_update"];
        return $linkPDF;
    }
    
    /**
     * validar_revision
     * Verifica que todos los avisos hayan sido revisados
     * @param  int $xidRendicion
     * @return int
     */
    public function validar_revision($xidRendicion) {
        $ok = false;
        $sql = "SELECT 
                    COUNT(*) AS 'cantidad'
                FROM 
                    avp_movimientos
                WHERE
                    avp_movimientos.id_rendicion = $xidRendicion";
        $rsMovimientos = getRs($sql, true);
        $cantidad_movimientos = $rsMovimientos->getValueInt("cantidad");
        $rsMovimientos->close();
        $sql = "SELECT 
                    COUNT(*) AS 'cantidad'
                FROM 
                    avp_movimientos
                WHERE
                    avp_movimientos.revisado = 1 AND
                    avp_movimientos.id_rendicion = $xidRendicion";
        $rsRevisados = getRs($sql);
        $cantidad_revisado = $rsRevisados->getValueInt("cantidad");
        $rsRevisados->close();

        if (!sonIguales($cantidad_movimientos, $cantidad_revisado))
            return false;

        return true;
    }
}