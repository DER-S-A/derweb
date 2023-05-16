<?php
/**
 * Clase para controlar API avp_rendiciones
 */
class Avp_rendicionesController extends APIController {
    /**
     * listarPorId
     * Recupera registros de avp_rendiciones.
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
        $objModel = new Avp_rendicionesModel();
        return json_encode($objModel->get($xfilter));
    }

    /**
     * agregarAvisoPago
     * Este API permite agregar un aviso de pago a un determinado vendedor.
     * @return void
     */
    public function agregarAvisoPago() {
        if ($this->usePostMethod()) {
            try {
                $datos = $this->getBodyParameter();
                $objModel = new Avp_rendicionesModel();
                $responseData = json_encode($objModel->agregarAvisoPago($datos));
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
    
    /**
     * generarRendicion
     * Este API permite generar la rendición y marcarlo como enviado para
     * que administración lo reciba.
     * @return void
     */
    public function generarRendicion() {
        if ($this->usePostMethod()) {
            try {
                $datos = $this->getBodyParameter();
                $objModel = new Avp_rendicionesModel();
                $responseData = json_encode($objModel->generarRendicion($datos));
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