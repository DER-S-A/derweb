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
     * confirmarPedido
     * API que permite confirmar un pedido.
     * @return void
     */
    public function confirmarPedido() {
        // Valido que la llamada venga por método GET o POST.
        if ($this->usePutMethod()) {
            try {
                $sesion = $this->getURIParameters("sesion");
                $idPedido = $this->getURIParameters("id_pedido");
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->confirmarPedido($sesion, $idPedido));
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
}

?>