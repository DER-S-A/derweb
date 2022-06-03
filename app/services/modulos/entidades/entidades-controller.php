<?php

/**
 * EntidadesController
 * Contiene el controlador del end point de la tabla entidades.
 */
class EntidadesController extends APIController {
   
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
     * loginCliente
     * Este End Point permite verificar el usuario y contraseña de un cliente.
     * @return void
     */
    public function loginCliente() {
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $usuario = $this->getURIParameters("usuario");
                $clave = $this->getUriParameters("clave");
                $responseData = $this->clienteVerificarUsuarioYClave($usuario, $clave);
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
        $objEntidadesModel = new EntidadesModel();
        $arrEntidades = $objEntidadesModel->get($xfilter);
        return json_encode($arrEntidades);
    }
    
    /**
     * clienteVerificarUsuarioYClave
     * Ejecuta la verificación de login de cliente del modelo.
     * @param  string $xusuario
     * @param  string $xclave
     * @return void
     */
    private function clienteVerificarUsuarioYClave($xusuario, $xclave) {
        $objEntidadesModel =new EntidadesModel();
        $arrEntidades = $objEntidadesModel->verificarUsuarioYClaveCliente($xusuario, $xclave);
        return json_encode($arrEntidades);
    }

	/**
     * upgrade
     * Endpoint para actualizar subrubros
     * @return void
     */
    public function upgradeClientes() {
        // Valido que la llamada venga por método PUT
        if ($this->usePutMethod()) {
            try {
                $registro = $this->getURIParameters("registro");
                $objModel = new EntidadesModel();
                $responseData = json_encode($objModel->upgradeClientes($registro));
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

    public function getBySesion() {
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $sesion = $this->getURIParameters("sesion");
                $objModel = new EntidadesModel();
                $responseData = json_encode($objModel->getBySesion($sesion));
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