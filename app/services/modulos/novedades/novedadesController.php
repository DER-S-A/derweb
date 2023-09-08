<?php
/**
 * Clase para controlar API novedades
 */
class NovedadesController extends APIController {
    /**
     * listarPorId
     * Recupera registros de novedades.
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
        $objModel = new NovedadesModel();
        return json_encode($objModel->get($xfilter));
    }
    
    /**
     * getGrupoArticulosByNovedad
     * Obtiene el grupo de artículos a partir del id de novedad seleccionado con
     * el formato para el datagrid.
     * @return void
     */
    public function getGrupoArticulosByNovedad() {
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $sesion = $this->getURIParameters("sesion");
                $id_novedad = $this->getURIParameters("id_novedad");
                $objNovedadesModel = new NovedadesModel();
                $aResponse = $objNovedadesModel->getGrupoArticulosByNovedad($sesion, $id_novedad);
                $responseData = json_encode($aResponse);
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