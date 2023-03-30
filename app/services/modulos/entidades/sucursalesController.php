<?php
/**
 * Clase para controlar API sucursales
 */
class SucursalesController extends APIController {
    /**
     * listarPorId
     * Recupera registros de sucursales.
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
        $objModel = new SucursalesModel();
        return json_encode($objModel->get($xfilter));
    }

    // TODO: Desarrollar métodos extras acá abajo.

    	/**
     * upgrade
     * Endpoint para actualizar sucursales
     * @return void
     */
    public function upgradeSucursales() {
        // Valido que la llamada venga por método PUT
        if ($this->usePutMethod()) {
            try {
                $registro = $this->getURIParameters("registro");
                $objModel = new SucursalesModel();
                $responseData = json_encode($objModel->upgradeSucursales($registro));
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
    * cambiarPassword
    * Realiza cambio de password del cliente.
    * Parametro $id es el id de entidades (cliente) y el parametro $reset es la password nueva q coloca el cliente
    * @return void
    */

    public function editarRentabilidad() {
        if ($this->usePutMethod()) {
            try {
                $rentabilidad = $this->getURIParameters("renta");
                $id = $this->getURIParameters("id_suc");
                $objModel = new entidadesModel();
                $responseData = json_encode($objModel->updateRentabilidad($id, $rentabilidad));
            } catch (Exception $ex) {
                $this->setErrorFromException($ex);
            }
        } else
            $this->setErrorMetodoNoSoportado();

        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());
    }

}

?>