<?php

/**
 * Esta clase permite manejar la tabla de pedidos
 * 
 */
class PedidosModel extends Model {
    private $idPedido = 0;
    private $aPedido = [];

    /**
     * get
     * Devuelve los registros de la tabla pedidos.
     * @param  string $xfilter Permite establecer el condicional del WHERE para filtrar datos.
     * @return array $result
     */
    public function get($xfilter) {
        // Armado de la sentencia SQL.
        $sql = "SELECT * FROM pedidos ";
        $this->setWhere($sql, $xfilter);
        return $this->getQuery($sql);
    }
    
    /**
     * agregarCarrito
     * Permite grabar un pedido.
     * @param  string $xjsonData
     * @return void
     */
    public function agregarCarrito($xjsonData) {
        $objBD = new BDObject();
        $this->aPedido = json_decode($xjsonData, true);
        $aResult = [];

        $this->calcularTotalPedido();
        $aCabecera = $this->aPedido["cabecera"];
        $aItem = $this->aPedido["item"];

        $objBD->beginT();
        try {
            if (!$this->verificarPendiente()) {        
                $sql = $this->generarInsertCabecera($aCabecera);
                $objBD->execInsert($sql);
                $this->idPedido = $this->obtenerNuevoIdPedido($objBD);
            } else {
                // Actualizar valores en cabecera.
                $sql = $this->generarUpdateCabecera($aCabecera);
                $objBD->execInsert($sql);
            }
            
            // ********************************************
            // TODO: Agregar despues la validación de stock.
            // En caso de que no haya stock avisar pero dejar
            // cargar el ítem de todas formas.

            // ********************************************
            
            // Verifico si el artículo que se está agregando, ya se está cargado
            // en el pedido actual.
            if (!$this->verificarExisteArticuloEnPedido($aItem)) {
                // Inserto el ítem.
                $sql = $this->generarInsertItem($aItem);
                $objBD->execInsert($sql);
            } else {
                // Actualizo la cantidad y los valores del ítem.
                $sql = $this->generarUpdateItem($aItem);
                $objBD->execInsert($sql);
            }

            $objBD->commitT();

            $aResult["codigo"] = "OK";
            $aResult["mensaje"] = "El artículo se agregó al carrito satisfactoriamente";
        } catch (Exception $e) {
            $objBD->rollbackT();
            $aResult["codigo"] = "DB_ERROR";
            $aResult["mensaje"] = $e->getMessage();
        } finally {
            $objBD->close();
        }

        return $aResult;
    }
    
    /**
     * calcularTotalPedido
     * Permite calcular el total del pedido en la cabecera.
     * @return void
     */
    private function calcularTotalPedido() {
        $subtotal = 0.00;
        $importeIVA = 0.00;
        $total = 0.00;

        $subtotal = doubleval($this->aPedido["item"]["costo_unitario"]) * doubleval($this->aPedido["item"]["cantidad"]);
        $importeIVA = $subtotal * ($this->aPedido["item"]["alicuota_iva"] / 100);
        $total = ($subtotal + $importeIVA);

        if ($this->idPedido != 0) {
            // Si el ID de pedido viene dado entonces recupero los totales para sumarlo
            $sql = "SELECT * FROM pedidos WHERE id = " . $this->idPedido;
            $rs = getRs($sql);
            $subtotalActual = $rs->getValueFloat("subtotal");
            $importeIVAActual = $rs->getValueFloat("importeIVA");
            $totalActual = $rs->getValueFloat("total");
            $rs->close();

            $subtotalActual += $subtotal;
            $importeIVAActual += $importeIVA;
            $totalActual += $total;

            $this->aPedido["cabecera"]["subtotal"] = $subtotalActual;
            $this->aPedido["cabecera"]["importe_iva"] = $importeIVAActual;
            $this->aPedido["cabecera"]["total"] = $totalActual;
        } else {
            $this->aPedido["cabecera"]["subtotal"] = $subtotal;
            $this->aPedido["cabecera"]["importe_iva"] = $importeIVA;
            $this->aPedido["cabecera"]["total"] = $total;            
        }
    }
    
    /**
     * verificarPendiente
     * Permite verificar si hay pedido pendiente.
     * En caso de que exista un pedido abierto, recupera el ítem y pone el id en
     * la propiedad $this->idPedido.
     * @return boolean true si hay pedido pendiente abierto, false si hay que generar un pedido nuevo.
     */
    private function verificarPendiente() {
        $result = false;
        $sql = "SELECT 
                    pedidos.id
                FROM 
                    pedidos
                        INNER JOIN estados_pedidos ON estados_pedidos.id = pedidos.id_estado
                WHERE
                    estados_pedidos.estado_inicial = 1";
        $rs = $this->getQuery2($sql);
        $this->idPedido = $rs->getValueInt("id");
        $rs->close();
        if ($this->idPedido == 0)
            $result = false;
        else
            $result = true;

        return $result;
    }
    
    /**
     * generarInsertCabecera
     * Genera la instrucción SQL INSERT para grabar la cabecera del pedido.
     * @param  array $xaCabecera Array con los datos de cabecera del pedido. 
     * @return string Sentencia SQL a ejecutar
     */
    private function generarInsertCabecera($xaCabecera) {
        $sql = "INSERT INTO pedidos (
                    id_entidad,
                    id_estado,
                    descuento_1,
                    descuento_2,
                    subtotal,
                    importe_iva,
                    total,
                    anulado,
                    fecha_alta)
                VALUES (
                    xidentidad,
                    xidestado,
                    xdescuento1,
                    xdescuento2,
                    xsubtotal,
                    ximporte_iva,
                    xtotal,
                    0,
                    current_timestamp)";
        $this->setParameter($sql, "xidentidad", intval($xaCabecera["id_entidad"]));
        $this->setParameter($sql, "xidestado", intval($xaCabecera["id_estado"]));
        $this->setParameter($sql, "xdescuento1", doubleval($xaCabecera["descuento_1"]));
        $this->setParameter($sql, "xdescuento2", doubleval($xaCabecera["descuento_2"]));
        $this->setParameter($sql, "xsubtotal", doubleval($xaCabecera["subtotal"]));
        $this->setParameter($sql, "ximporte_iva", intval($xaCabecera["importe_iva"]));
        $this->setParameter($sql, "xtotal", doubleval($xaCabecera["total"]));

        return $sql;
    }
    
    /**
     * generarUpdateCabecera
     * Genera la sentencia UPDATE en pedidos para actualizar los valores totales
     * del mismo.
     * @param  array $xaCabecera Array con el ítem que se agrega al carrito.
     * @return string Sentencia SQL
     */
    private function generarUpdateCabecera($xaCabecera) {
        $sql = "UPDATE 
                    pedidos 
                SET
                    pedidos.subtotal = pedidos.subtotal + xsubtotal,
                    pedidos.importe_iva = pedidos.importe_iva + ximporteIVA,
                    pedidos.total = pedidos.total + xtotal
                WHERE
                    pedidos.id = xid";

        $subtotal = $xaCabecera["subtotal"];
        $importeIVA = $xaCabecera["importe_iva"];
        $total = $xaCabecera["total"];

        $this->setParameter($sql, "xsubtotal", $subtotal);
        $this->setParameter($sql, "ximporteIVA", $importeIVA);
        $this->setParameter($sql, "xtotal", $total);
        $this->setParameter($sql, "xid", $this->idPedido);

        return $sql;
    }
    
    /**
     * obtenerNuevoIdPedido
     * Obtiene el id de pedido nueo.
     * @param  BDObject $xbdObject Objeto de base de datos con transacción abierta.
     * @return int
     */
    private function obtenerNuevoIdPedido(&$xbdObject) {
        $sql = "SELECT @@IDENTITY AS id";
        $xbdObject->execQuery2($sql);
        $idPedido = $xbdObject->getValueInt("id");
        return $idPedido;
    }
    
    /**
     * verificarExisteArticuloEnPedido
     * Verifica si un artículo se encuentra cargado en el pedido actual.
     * @param  array $xaItem Array con el ítem a agregar al carrito.
     * @return boolean
     */
    private function verificarExisteArticuloEnPedido($xaItem) {
        $result = false;
        $sql = "SELECT
                    COUNT(*) AS cantReg
                FROM
                    pedidos_items
                WHERE
                    pedidos_items.id_pedido = xidpedido AND
                    pedidos_items.id_articulo = xidarticulo";
        $this->setParameter($sql, "xidpedido", $this->idPedido);
        $this->setParameter($sql, "xidarticulo", intval($xaItem["id_articulo"]));
        $rs = getRs($sql);
        if ($rs->getValueInt("cantReg") == 0)
            $result = false;
        else
            $result = true;
            
        return $result;
    }
    
    /**
     * generarInsertItem
     * Genera le insert para agregar el artículo al carrito.
     * @param  array $xaItem Array con los datos del ítem.
     * @return string Sentencia SQL a ejecutar
     */
    private function generarInsertItem($xaItem) {
        $importeIVA = 0.00;
        $subtotal = 0.00;
        $total = 0.00;

        $sql = "INSERT INTO pedidos_items (
                    id_pedido,
                    id_articulo,
                    cantidad,
                    porcentaje_oferta,
                    precio_lista,
                    costo_unitario,
                    alicuota_iva,
                    subtotal,
                    importe_iva,
                    total,
                    anulado)
                VALUES (
                    xidpedido,
                    xidarticulo,
                    xcantidad,
                    xporcentajeOferta,
                    xprecioLista,
                    xcostoUnitario,
                    xalicuotaIVA,
                    xsubtotal,
                    ximporteIVA,
                    xtotal,
                    0)";
        $this->setParameter($sql, "xidpedido", $this->idPedido);
        $this->setParameter($sql, "xidarticulo", intval($xaItem["id_articulo"]));
        $this->setParameter($sql, "xcantidad", doubleval($xaItem["cantidad"]));
        $this->setParameter($sql, "xporcentajeOferta", doubleval($xaItem["porcentaje_oferta"]));
        $this->setParameter($sql, "xprecioLista", doubleval($xaItem["precio_lista"]));
        $this->setParameter($sql, "xcostoUnitario", doubleval($xaItem["costo_unitario"]));
        $this->setParameter($sql, "xalicuotaIVA", doubleval($xaItem["alicuota_iva"]));
        
        $importeIVA = doubleval($xaItem["costo_unitario"]) * ($xaItem["alicuota_iva"] / 100);
        $subtotal = doubleval($xaItem["cantidad"]) * doubleval($xaItem["costo_unitario"]);
        $total = $subtotal + $importeIVA;

        $this->setParameter($sql, "xsubtotal", $subtotal);
        $this->setParameter($sql, "ximporteIVA", $importeIVA);
        $this->setParameter($sql, "xtotal", $total);

        return $sql;
    }
    
    /**
     * generarUpdateItem
     * Genera la instrucción SQL para actualizar el ítem en caso de que exista
     * en el pedido.
     * @param  array $xaItem Array con el ítem a incorporar.
     * @return string
     */
    private function generarUpdateItem($xaItem) {
        // Recupero la cantidad grabada y la sumo a la nueva cantidad ingresada. Luego
        // recalculo los importes en base a la nueva cantidad.
        $cantidad = $this->obtenerCantidadItemActual($xaItem) + $xaItem["cantidad"];
        $importeIVA = doubleval($xaItem["costo_unitario"]) * ($xaItem["alicuota_iva"] / 100);
        $subtotal = $cantidad * doubleval($xaItem["costo_unitario"]);
        $total = $subtotal + $importeIVA;

        $sql = "UPDATE 
                    pedidos_items
                SET
                    pedidos_items.cantidad = xcantidad,
                    pedidos_items.subtotal = xsubtotal,
                    pedidos_items.total = xtotal
                WHERE
                    pedidos_items.id_pedido = xidpedido AND
                    pedidos_items.id_articulo = xidarticulo";
        $this->setParameter($sql, "xcantidad", $cantidad);
        $this->setParameter($sql, "xsubtotal", $subtotal);
        $this->setParameter($sql, "xtotal", $total);
        $this->setParameter($sql, "xidpedido", $this->idPedido);
        $this->setParameter($sql, "xidarticulo", intval($xaItem["id_articulo"]));

        return $sql;
    }
    
    /**
     * obtenerCantidadItemActual
     * Obtiene la cantidad del ítem actual.
     * @param  array $xaItem Array con el ítem a insertar en el carrito.
     * @return double cantidad.
     */
    private function obtenerCantidadItemActual($xaItem) {
        $sql = "SELECT
                    cantidad
                FROM
                    pedidos_items
                WHERE
                    pedidos_items.id_pedido = xidpedido AND
                    pedidos_items.id_articulo = xidarticulo";
        $this->setParameter($sql, "xidpedido", $this->idPedido);
        $this->setParameter($sql, "xidarticulo", intval($xaItem["id_articulo"]));
        $rs = getRs($sql);
        $cantidad = $rs->getValueFloat("cantidad");
        $rs->close();
        return $cantidad;
    }
}

?>