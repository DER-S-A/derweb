<?php
/**
 * Clase para controlar API lfw_operaciones
 */

class Lfw_operacionesController extends APIController {
    /**
     * listarPorId
     * Recupera registros de lfw_operaciones.
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
        $objModel = new Lfw_operacionesModel();
        return json_encode($objModel->get($xfilter));
    }
    
    /**
     * getByTipoEntidad
     * Obtiene las operaciones por tipo de entidad.
     * @return void
     */
    public function getByTipoEntidad() {        
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $idTipoEntidad = $this->getURIParameters("idTipoEntidad");
                $objModel = new Lfw_operacionesModel();
                $responseData = json_encode($objModel->getByTipoEntidad($idTipoEntidad));                
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