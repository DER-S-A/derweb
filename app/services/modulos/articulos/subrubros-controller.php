<?php
class SubrubrosController extends APIController {
   
    /**
     * listarPorId
     * Recupera registros de rubros.
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
     * @return void
     */
    private function ejecutarMetodoGet($xfilter = "") {
        $objModel = new SubrubrosModel();
        $a = $objModel->get($xfilter);
        return json_encode($a);
    }

    /**
     * getSubrubrosPorRubro
     * Permite obtener los subrubros en base a un rubro.
     * @return void
     */
    public function getSubrubrosPorRubro() {
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                // Invoco al método getSubrubrosByRubros de la clase SubrubrosModel.
                $id_rubro = intval($this->getURIParameters("id_rubro"));
                $objModel = new SubrubrosModel();
                $responseData = json_encode($objModel->getSubrubrosByRubro($id_rubro));
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
     * upgrade
     * Endpoint para actualizar subrubros
     * @return void
     */
    public function upgrade() {
        // Valido que la llamada venga por método PUT
        if ($this->usePutMethod()) {
            try {
                $registro = $this->getURIParameters("registro");
                $objModel = new SubrubrosModel();
                $responseData = json_encode($objModel->upgrade($registro));
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