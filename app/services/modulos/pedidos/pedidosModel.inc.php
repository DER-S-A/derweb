<?php

/**
 * Esta clase permite manejar la tabla de pedidos
 * 
 */
class PedidosModel extends Model {
    private $idPedido = 0;
    private $aPedido = [];

    // Estas propiedades se completan a partir de los datos
    // de la sesión actual
    protected $id_listaprecio;
    protected $descuento_p1;
    protected $descuento_p2;
    protected $rentabilidad;  

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
    public function agregarCarrito($xsesion, $xjsonData) {
        $objBD = new BDObject();
        $this->aPedido = json_decode($xjsonData, true);
        $aResult = [];
        $this->getClienteActual($xsesion);

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

        $this->calcularCostoUnitario($this->aPedido["item"]);
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
                    estados_pedidos.estado_inicial = 1 AND
                    pedidos.id_entidad = " . $this->idCliente . " AND 
                    pedidos.id_tipoentidad = " . $this->idTipoEntidad;
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
                    id_tipoentidad,
                    id_estado,
                    id_vendedor,
                    id_sucursal,
                    codigo_sucursal,
                    id_transporte,
                    codigo_transporte,
                    id_formaenvio,
                    descuento_1,
                    descuento_2,
                    subtotal,
                    importe_iva,
                    total,
                    anulado,
                    fecha_alta)
                VALUES (
                    xidentidad,
                    xidtipoentidad,
                    xidestado,
                    xidvendedor,
                    xidsucursal,
                    xcodSucursal,
                    xidTransporte,
                    xcodigoTransporte,
                    xidFormaEnvio,
                    xdescuento1,
                    xdescuento2,
                    xsubtotal,
                    ximporte_iva,
                    xtotal,
                    0,
                    current_timestamp)";
        $this->setParameter($sql, "xidentidad", intval($xaCabecera["id_entidad"]));
        $this->setParameter($sql, "xidtipoentidad", intval($xaCabecera["id_tipoentidad"]));
        $this->setParameter($sql, "xidestado", intval($xaCabecera["id_estado"]));
        $this->setParameter($sql, "xidvendedor", intval($xaCabecera["id_vendedor"]));
        $this->setParameter($sql, "xidsucursal", intval($xaCabecera["id_sucursal"]));
        $this->setParameter($sql, "xcodSucursal", $xaCabecera["codigo_sucursal"]);
        $this->setParameter($sql, "xidTransporte", intval($xaCabecera["id_transporte"]));
        $this->setParameter($sql, "xcodigoTransporte", intval($xaCabecera["codigo_transporte"]));
        $this->setParameter($sql, "xidFormaEnvio", intval($xaCabecera["id_formaenvio"]));
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
        $this->calcularCostoUnitario($xaItem);
        $importeIVA = doubleval($xaItem["costo_unitario"]) * ($xaItem["alicuota_iva"] / 100) * doubleval($xaItem["cantidad"]);
        $subtotal = doubleval($xaItem["cantidad"]) * doubleval($xaItem["costo_unitario"]);
        $total = $subtotal + $importeIVA;      

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
        $this->setParameter($sql, "xsubtotal", $subtotal);
        $this->setParameter($sql, "ximporteIVA", $importeIVA);
        $this->setParameter($sql, "xtotal", $total);

        return $sql;
    }
    
    /**
     * calcularPrecioUnitario
     * Permite calcular el precio de costo unitario.
     * @param  array $xaItem Array con los ítems.
     * @return void
     */
    private function calcularCostoUnitario(&$xaItem) {
        // Tengo que hacer que aplique los descuentos que tiene el cliente para calcular
        // el costo unitario.
        $xaItem["costo_unitario"] = $xaItem["precio_lista"];        
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

        $this->calcularCostoUnitario($xaItem);     
        $importeIVA = doubleval($xaItem["costo_unitario"]) * ($xaItem["alicuota_iva"] / 100) * $cantidad;
        $subtotal = $cantidad * doubleval($xaItem["costo_unitario"]);
        $total = $subtotal + $importeIVA;

        $sql = "UPDATE 
                    pedidos_items
                SET
                    pedidos_items.cantidad = xcantidad,
                    pedidos_items.subtotal = xsubtotal,
                    pedidos_items.importe_iva = ximporteIVA,
                    pedidos_items.total = xtotal
                WHERE
                    pedidos_items.id_pedido = xidpedido AND
                    pedidos_items.id_articulo = xidarticulo";
        $this->setParameter($sql, "xcantidad", $cantidad);
        $this->setParameter($sql, "xsubtotal", $subtotal);
        $this->setParameter($sql, "ximporteIVA", $importeIVA);
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
    
    /**
     * getPedidoActual
     * Obtiene el detalle de mi pedido actual para mostrar en Mi Carrito.
     * @param  string $xsesion JSON con los datos de la sesión actual.
     * @return array
     */
    public function getPedidoActual($xsesion) {
        $aResponse = [];
        $totalPedido = 0.00;
        $totalPedidoConIVA = 0.00;
        $id_pedido = 0;

        // Recupero los datos que necesito del cliente.
        $objEntidad = new EntidadesModel();
        $aSesion = json_decode($xsesion, true);
        $this->idTipoEntidad = intval($aSesion["id_tipoentidad"]);
        $aCliente = $objEntidad->getBySesion($xsesion);
        $id_cliente = intval($aCliente[0]["id"]);
        $id_precio_lista = intval($aCliente[0]["id_listaprecio"]);
        $descuento_p1 = doubleval($aCliente[0]["descuento_1"]);
        $descuento_p2 = doubleval($aCliente[0]["descuento_2"]);

        $sql = "SELECT
                    items.id,
                    items.id_pedido,
                    art.id AS id_articulo,
                    items.cantidad,
                    foto.archivo,
                    art.codigo,
                    art.descripcion AS descripcion_articulo,
                    rub.descripcion AS descripcion_rubro,
                    srb.descripcion AS descripcion_subrubro,
                    precio.precio_lista,
                    art.alicuota_iva
                FROM
                    pedidos_items items
                        INNER JOIN articulos art ON art.id = items.id_articulo
                        INNER JOIN articulos_precios precio ON precio.id_articulo = art.id
                        INNER JOIN listas_precios lpre ON lpre.id = precio.id_listaprecio
                        INNER JOIN pedidos ped ON ped.id = items.id_pedido
                        INNER JOIN estados_pedidos estado ON estado.id = ped.id_estado
                        INNER JOIN rubros rub ON rub.id = art.id_rubro
                        INNER JOIN subrubros srb ON srb.id = art.id_subrubro
                        LEFT OUTER JOIN art_fotos foto ON foto.id_articulo = art.id
                WHERE
                    estado.estado_inicial = 1 AND
                    (foto.predeterminada = 1 OR foto.predeterminada IS NULL) AND
                    ped.id_entidad = $id_cliente AND
                    lpre.id = $id_precio_lista AND
                    ped.id_tipoentidad = " . $this->idTipoEntidad;
        
        $rs = getRs($sql);
        $indice = 0;
        while (!$rs->EOF()) {
            $id_pedido = $rs->getValueInt("id_pedido");
            $aResponse["items"][$indice]["id"] = $rs->getValueInt("id");
            $aResponse["items"][$indice]["id_articulo"] = $rs->getValueInt("id_articulo");
            $aResponse["items"][$indice]["cantidad"] = $rs->getValueFloat("cantidad");
            $aResponse["items"][$indice]["archivo"] = $rs->getValue("archivo");
            $aResponse["items"][$indice]["codigo"] = $rs->getValue("codigo");
            $aResponse["items"][$indice]["descripcion"] = $rs->getValue("descripcion_articulo");
            $aResponse["items"][$indice]["rubro"] = $rs->getValue("descripcion_rubro");
            $aResponse["items"][$indice]["subrubro"] = $rs->getValue("descripcion_subrubro");
            $aResponse["items"][$indice]["precio_lista"] = $rs->getValueFloat("precio_lista");
            $aResponse["items"][$indice]["alicuota_iva"] = $rs->getValueFloat("alicuota_iva");
            $aResponse["items"][$indice]["costo"] = calcular_costo("PED", $rs->getValueFloat("precio_lista"), $descuento_p1, $descuento_p2);
            $aResponse["items"][$indice]["costo_final"] = $aResponse["items"][$indice]["costo"] * (1 + ($rs->getValueFloat("alicuota_iva") / 100));
            $aResponse["items"][$indice]["subtotal"] = $rs->getValueFloat("cantidad") * $aResponse["items"][$indice]["costo"];
            $aResponse["items"][$indice]["subtotal_final"] = $aResponse["items"][$indice]["costo_final"] * $rs->getValueFloat("cantidad");

            $totalPedido += $aResponse["items"][$indice]["subtotal"];
            $totalPedidoConIVA += $aResponse["items"][$indice]["subtotal_final"];
            
            $indice++;
            $rs->next();
        }

        $aResponse["id_pedido"] = $id_pedido;
        $aResponse["total_pedido"] = $totalPedido;
        $aResponse["total_con_iva"] = $totalPedidoConIVA;
        $rs->close();

        return $aResponse;
    }

    /**
     * vaciarPedido
     * vacia el pedido actual por su ID.
     * @param  int $xid_pedido
     * @return array
     */
    public function vaciarPedido($xid_pedido) {
        /* $aResponse = [];
        $ok = false;

        $sql = "DELETE
                FROM pedidos_items
                WHERE
                    id_pedido = $xid_pedido";

        $sql2 = "DELETE
                FROM pedidos
                WHERE
                id = $xid_pedido";
        
        $bd = new BDObject();
        $bd->execQuery($sql);
        $bd->execQuery($sql2);
        $aResponse["codigo"] = "OK";
        $aResponse["mensaje"] = "Se vacio el carrito";
                
        $bd->close();

        return $aResponse; */

        //$aResult = array();

        $bd = new BDObject();
        $bd->beginT();
        try {
            $sql = "DELETE
                FROM pedidos_items
                WHERE
                    id_pedido = $xid_pedido";

             $sql2 = "DELETE
                    FROM pedidos
                    WHERE
                    id = $xid_pedido"; 
            $bd->execInsert($sql);
            $bd->execInsert($sql2);

            // Actualizo el checksum de la tabla.
            sc3UpdateTableChecksum("pedidos_items", $bd);
            //sc3UpdateTableChecksum("pedidos", $bd);

            $bd->commitT();

            $aResult["codigo"] = "OK";
            $aResult["mensaje"] = "Se vacio el carrito.";            
        } catch (Exception $e) {
            $bd->rollbackT();
            $aResult["codigo"] = "BD_ERROR";
            $aResult["mensaje"] = "No se vacio el carrito";
        } finally {
            $bd->close();
        }
        return json_encode($aResult);
    }
    
    /**
     * confirmarPedido
     * Confirma el pedido actual por su ID.
     * @param  string $xsesion JSON con la sesión
     * @param  string $xid_pedido JSON con los datos del pedido a confirmar.
     * @return array
     */
    public function confirmarPedido($xsesion, $xpedido) {
        $idEstado = 0;
        $ok = false;
        $this->getClienteActual($xsesion);
        $aPedidoConfirmar = json_decode($xpedido, true);
        
        $aResponse = [];

        // Transfiero el pedido a SAP.
        $aResponse["result-sap"] = $this->enviarPedido_a_SAP($xsesion, intval($aPedidoConfirmar["id_pedido"]));
        if ($aResponse["result-sap"] == null)
            $aResponse["codigo-result-sap"] = "API_SAP_ERROR";

        $sql = "SELECT
                    id
                FROM
                    estados_pedidos
                WHERE
                    estados_pedidos.estado_confirmado = 1";
        $rsEstado = getRs($sql);
        $idEstado = $rsEstado->getValueInt("id");
        $rsEstado->close();

        $sql = "SELECT 
                    id, 
                    codigo_sucursal
                FROM
                    sucursales
                WHERE
                    sucursales.id = " . intval($aPedidoConfirmar["id_sucursal"]);
        $rsSucursal = getRs($sql, true);
        $idSucursal = $rsSucursal->getValueInt("id");
        $codigoSucursal = $rsSucursal->getValue("codigo_sucursal");
        $rsSucursal->close();

        $sql = "SELECT mostrar_transporte FROM formas_envios WHERE id = " . intval($aPedidoConfirmar["id_formaenvio"]);
        $rsFormaEnvio = getRs($sql, true);
        $grabar_transporte = $rsFormaEnvio->getValueInt("mostrar_transporte") == 1 ? true : false;
        $rsFormaEnvio->close();

        if ($grabar_transporte) {
            $id_transporte = intval($aPedidoConfirmar["id_transporte"]);
            $sql = "SELECT
                        codigo_transporte
                    FROM
                        transportes
                    WHERE
                        id = " . $id_transporte;
            $rsTransporte = getRs($sql, true);
            $codigoTransporte = "'" . $rsTransporte->getValue("codigo_transporte") . "'";
            $rsTransporte->close();
        } else {
            $id_transporte = "NULL";
            $codigoTransporte = "NULL";
        }

        $sql = "UPDATE
                    pedidos
                SET
                    pedidos.id_estado = $idEstado,
                    pedidos.id_sucursal = $idSucursal,
                    pedidos.codigo_sucursal = '$codigoSucursal',
                    pedidos.id_formaenvio = " . intval($aPedidoConfirmar["id_formaenvio"]) . ",
                    pedidos.id_transporte = $id_transporte,
                    pedidos.codigo_transporte = $codigoTransporte, 
                    pedidos.fecha_modificado = current_timestamp
                WHERE
                    pedidos.id = " . intval($aPedidoConfirmar["id_pedido"]) . " AND
                    pedidos.id_entidad = " . $this->idCliente;
        
        $bd = new BDObject();
        $bd->execQuery($sql);
        if ($bd->affectedRows > 0)
            $ok = true;        
        $bd->close();

        if (!$ok) {
            $aResponse["codigo"] = "BD_ERROR";
            $aResponse["mensaje"] = "No se confirmó el pedido";
        } else {
            $aResponse["codigo"] = "OK";
            $aResponse["mensaje"] = "Pedido confirmado satisfactoriamente";
        }

        return $aResponse;
    }
    
    /**
     * enviarPedido_a_SAP
     * Permite enviar un pedido a SAP.
     * @param  mixed $xid_pedido
     * @return void
     */
    private function enviarPedido_a_SAP($xsesion, $xid_pedido) {
        $aBody = [];
        $aPedidoEnviar = [];
        $aItems = [];
        $objAPISap = new APISap(URL_ENVIAR_PEDIDO, "POST");
        $aPedidoActual = [];
        
        // Establezco la comunicación con el ETL.
        $this->getToken();
        $objAPISap->setTestMode(); // Modo testing
        
        $aBody["connectorCode"] = CONNECTOR_CODE;
        $aBody["functionalityCode"] = FUNCIONALITY_CODE;
        
        $aPedidoEnviar["DocDate"] = date("Y-m-d", time());
        $aPedidoEnviar["DocDueDate"] = date("Y-m-d", time());
        $aPedidoEnviar["CardCode"] = $this->idCliente;
        $aPedidoEnviar["NumAtCard"] = "DERWEB-" . $xid_pedido;

        // Recupero el pedido actual
        $aPedidoActual = $this->getPedidoActual($xsesion);
        for ($i = 0; $i < sizeof($aPedidoActual["items"]); $i++) {
            $aItems[$i]["ItemCode"] = $aPedidoActual["items"][$i]["codigo"];
            $aItems[$i]["Quantity"] = doubleval($aPedidoActual["items"][$i]["cantidad"]);
            $aItems[$i]["Price"] = doubleval($aPedidoActual["items"][$i]["precio_lista"]);
        }

        $aPedidoEnviar["DocumentLines"] = json_encode($aItems);
        $aBody["data"] = json_encode($aPedidoEnviar);

        $objAPISap->setData($aBody);
        $objAPISap->send();

        return $objAPISap->getInfo();
    }
    
    /**
     * getToken
     * Obtiene el token para enviar pedidos al ETL.
     * @return void
     */
    private function getToken() {
        $objAPISap = new APISap(URL_LOGIN_ETL, "POST");
        $objAPISap->getTokenETL();
    }
    
    /**
     * getPedidosPendientesByVendedor
     * Obtiene los pedidos actuales pendientes de confirmar por vendedor.
     * @param  string $xsesion
     * @return array
     */
    public function getPedidosPendientesByVendedor($xsesion) {
        $aSesion = json_decode($xsesion, true);
        $idVendedor = intval($aSesion["id_vendedor"]);
        $idTipoEntidad = intval($aSesion["id_tipoentidad"]);
        $aResponse = [];

        $sql = "SELECT
                    p.id,
                    p.fecha_alta,
                    ent.cliente_cardcode,
                    ent.nombre,
                    p.codigo_sucursal,
                    p.total
                FROM
                    pedidos p
                        INNER JOIN estados_pedidos est ON est.id = p.id_estado
                        INNER JOIN entidades ent ON ent.id = p.id_entidad
                WHERE
                    p.id_vendedor = $idVendedor AND
                    est.estado_inicial = 1 AND
                    p.id_tipoentidad = $idTipoEntidad";
        $rs = getRs($sql, true);
        $i = 0;
        while(!$rs->EOF()) {
            $aResponse[$i]["id"] = $rs->getValueInt("id");
            $aResponse[$i]["fecha_alta"] = $rs->getValueFechaFormateada("fecha_alta");
            $aResponse[$i]["cliente_cardcode"] = $rs->getValue("cliente_cardcode");
            $aResponse[$i]["nombre"] = $rs->getValue("nombre");
            $aResponse[$i]["codigo_sucursal"] = $rs->getValue("codigo_sucursal");
            $aResponse[$i]["total"] = $rs->getValueFloat("total");

            $sql = "SELECT
                        item.id,
                        item.id_pedido,
                        item.cantidad,
                        art.codigo,
                        art.descripcion,
                        item.precio_lista,
                        item.costo_unitario,
                        item.subtotal,
                        item.total
                    FROM
                        pedidos_items item
                            INNER JOIN articulos art ON art.id = item.id_articulo
                    WHERE
                        item.id_pedido = " . $rs->getValueInt("id");
            $aResponse[$i]["items"] = getRs($sql, true)->getAsArray();

            $i++;
            $rs->next();
        }

        $rs->close();

        return $aResponse;
    }
}

?>