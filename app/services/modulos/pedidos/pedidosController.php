<?php
/**
 * Clase para controlar API pedidos
 */
class PedidosController extends APIController {
    /**
     * listarPorId
     * Recupera registros de pedidos.
     * Usar método: GET o POST
     * Usar ?filter para filtrar por algún campo. Ej. ?filter="id = 1"
     * @return void
     */
    public function get() {        
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $filter = $this->getURIParameters("filter");
                $responseData = $this->ejecutarMetodoGet($filter);
            } catch (Exception $ex) {
                $this->setErrorFromException($ex);
            }
        } else
            $this->setErrorMetodoNoSoportado();

        // Envío la salida
        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());
    }

    /**
     * ejecutarMetodoGet
     * Ejecuta el método get de la clase modelo.
     * @param  string $xfilter
     * @return string
     */
    private function ejecutarMetodoGet($xfilter = "") {
        $objModel = new PedidosModel();
        return json_encode($objModel->get($xfilter));
    }
    
    /**
     * agregarAlCarrito
     * Permite agregar un artículo al carrito de compras.
     * @return void
     */
    public function agregarAlCarrito() {        
        // Valido que la llamada venga por método GET o POST.
        if ($this->usePutMethod()) {
            try {
                $sesion = $this->getURIParameters("sesion");
                $datos = $this->getURIParameters("datos");
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->agregarCarrito($sesion, $datos));
            } catch (Exception $ex) {
                $this->setErrorFromException($ex);
            }
        } else
            $this->setErrorMetodoNoSoportado();

        // Envío la salida
        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());
    }
    
    /**
     * getPedidoActual
     * Permite obtener los ítems del pedido actual para mi carrito.
     * @return void
     */
    public function getPedidoActual() {
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $sesion = $this->getURIParameters("sesion");
                $objModelo = new PedidosModel();
                $responseData = json_encode($objModelo->getPedidoActual($sesion));
            } catch (Exception $ex) {
                $this->setErrorFromException($ex);
            }
        } else
            $this->setErrorMetodoNoSoportado();

        // Envío la salida
        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());
    }

    /**
     * eliminarItem
     * API que permite vaciar carrito.
     * @return void
     */
    public function eliminarItem() {
        // Valido que la llamada venga por método PUT.
        if ($this->usePutMethod()) {
            try {
                $idPedido = $this->getURIParameters("id_pedido");
                $idPedidosItems = $this->getURIParameters("id_pedidos_items");
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->eliminarArt($idPedido, $idPedidosItems));
            } catch (Exception $ex) {
                $this->setErrorFromException($ex);
            }
        } else
            $this->setErrorMetodoNoSoportado();

        // Envío la salida
        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());
    }

    /**
     * vaciarCarrito
     * API que permite vaciar carrito.
     * @return void
     */
    public function vaciarCarrito() {
        // Valido que la llamada venga por método PUT.
        if ($this->usePutMethod()) {
            try {
                $idPedido = $this->getURIParameters("id_pedido");
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->vaciarPedido($idPedido));
            } catch (Exception $ex) {
                $this->setErrorFromException($ex);
            }
        } else
            $this->setErrorMetodoNoSoportado();

        // Envío la salida
        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());
    }
    
    
    /**
     * confirmarPedido
     * API que permite confirmar un pedido.
     * @return void
     */
    public function confirmarPedido() {
        // Valido que la llamada venga por método PUT.
        if ($this->usePutMethod()) {
            try {
                $sesion = $this->getURIParameters("sesion");
                $pedido = $this->getURIParameters("pedido");
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->confirmarPedido($sesion, $pedido));
            } catch (Exception $ex) {
                $this->setErrorFromException($ex);
            }
        } else
            $this->setErrorMetodoNoSoportado();

        // Envío la salida
        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());        
    }
    
    /**
     * getPedidosPendientesByVendedor
     * API que obtiene los pedidos pendientes de confirmar por vendedor.
     * @return void
     */
    public function getPedidosPendientesByVendedor() {
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $sesion = $this->getURIParameters("sesion");
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->getPedidosPendientesByVendedor($sesion));
            } catch (Exception $ex) {
                $this->setErrorFromException($ex);
            }
        } else
            $this->setErrorMetodoNoSoportado();

        // Envío la salida
        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());        
    }

    /**
     * Permite modificar un ítem de un pedido.
     */
    public function modificarItem() {
        // Valido que la llamada venga por método PUT.
        if ($this->usePutMethod()) {
            try {
                $jsonData = $this->getURIParameters("data");
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->modificar_item($jsonData));
            } catch (Exception $ex) {
                $this->setErrorFromException($ex);
            }
        } else
            $this->setErrorMetodoNoSoportado();

        // Envío la salida
        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());        
    }
    
    /**
     * grabarPedido
     * Permite grabar un pedido completo desde la pantalla de pedidos rápidos.
     * @return void
     */
    public function grabarPedido() {
        if ($this->usePostMethod()) {
            try {
                $jsonData = $this->getBodyParameter();
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->grabarPedido($jsonData));
            } catch (Exception $e) {
                $this->setErrorFromException($e);
            }
        } else
            $this->setErrorMetodoNoSoportado();

        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());
    }
    
    /**
     * consultar
     * Obtiene las cabeceras de los pedidos para el listado de mis pedidos.
     * @return void
     */
    public function consultar() {
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $responseData = [];
                $aParametros = json_decode($this->getBodyParameter(), true);
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->consultar($aParametros));
            } catch (Exception $e) {
                $this->setErrorFromException($e);
            }
        }

        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());
    }
    
    /**
     * consultar_item_byid
     * Obtiene los ítems de un pedido para "Mis Pedidos".
     * @return void
     */
    public function consultar_item_byid() {
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $responseData = [];
                $aParametros = json_decode($this->getBodyParameter(), true);
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->consultar_item_byid($aParametros));
            } catch (Exception $e) {
                $this->setErrorFromException($e);
            }
        }

        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());        
    }
}

?>