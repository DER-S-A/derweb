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
                $datos = $this->getURIParameters("datos");
                $objModel = new PedidosModel();
                $responseData = json_encode($objModel->agregarCarrito($datos));
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