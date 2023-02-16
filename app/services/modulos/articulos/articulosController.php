<?php
/**
 * Clase para controlar API articulos
 */
class ArticulosController extends APIController {
    /**
     * listarPorId
     * Recupera registros de articulos.
     * Usar método: GET o POST
     * Usar ?filter para filtrar por algún campo. Ej. ?filter="id = 1"
     * @return void
     */
    public function get() {        
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $sesion = $this->getURIParameters("sesion");
                $pagina = intval($this->getURIParameters("pagina"));
                $filter = $this->getURIParameters("filter");
                $responseData = $this->ejecutarMetodoGet($sesion, $pagina, $filter);
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
    private function ejecutarMetodoGet($xsesion, $xpagina, $xfilter = "") {
        $objModel = new ArticulosModel();
        return json_encode($objModel->get($xsesion, $xfilter, $xpagina));
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
                $objModel = new ArticulosModel();
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
    
    /**
     * getByRubroAndSubrubro
     * Obtiene los artículos para mostrar en el catálogo por rubro y subrubro.
     * @return void
     */
    public function getByRubroAndSubrubro() {
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $sesion = $this->getURIParameters("sesion");
                $filters = $this->getURIParameters("parametros");
                //$id_subrubro = intval($this->getURIParameters("id_subrubro"));
                $pagina = intval($this->getURIParameters("pagina"));
                $objModel = new ArticulosModel();
                $responseData = json_encode($objModel->getByRubroAndSubrubro($sesion, $filters, $pagina));
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

    public function getByFrase() {
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $sesion = $this->getURIParameters("sesion");
                $frase = $this->getURIParameters("frase");
                //$id_subrubro = intval($this->getURIParameters("id_subrubro"));
                $pagina = intval($this->getURIParameters("pagina"));
                $objModel = new ArticulosModel();
                $responseData = json_encode($objModel->getByFrase($sesion, $frase, $pagina));
                //$responseData = json_encode($objModel->getByFrase($sesion, $frase, $pagina));
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

    public function getFicha() {        
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $objArt = new ArticulosModel();
                $xid_art = $this->getURIParameters("id_articulo");
                $xid_cli = $this->getURIParameters("id_cliente");
                $responseData = json_encode($objArt->generarFichaArt($xid_art, $xid_cli));
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