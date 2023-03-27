<?php

/**
 * MargenesEspController
 * Contiene el controlador del end point de la tabla margenes especiales.
 */
class MargenesEspController extends APIController {
    /**
     * listarPorId
     * Recupera registros de margenes especiales.
     * Usar método: GET o POST
     * Usar ?filter para filtrar por algún campo. Ej. ?filter="id = 1"
     * @return void
     */
    public function get() {        
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $objMargenesEspModel = new MargenesEspModel();
                $filter = $this->getURIParameters("filter");
                $aMargenesEsp = $objMargenesEspModel->get($filter);
                $responseData = json_encode($this->leerRegistros($aMargenesEsp));
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
     * leerRegistros
     * Carga los registros completos de margenes especiales.
     * @param  array $xrecord
     * @return array
     */
    private function leerRegistros($xrecord) {
        $aRegistro = [];
        for ($i = 0; $i < sizeof($xrecord); $i++) {
            $aRegistro[$i]["id"] = $xrecord[$i]["id"];
            $aRegistro[$i]["rubro"] = $xrecord[$i]["id_rubro"] == '' ? 'TODAS' : $xrecord[$i]["id_rubro"];
            $aRegistro[$i]["rubroNom"] = $xrecord[$i]["rubroNom"];
            $aRegistro[$i]["subrubro"] = $xrecord[$i]["id_subrubro"] == '' ? 'TODAS' : $xrecord[$i]["id_subrubro"];
            $aRegistro[$i]["subrubroNom"] = $xrecord[$i]["subrubroNom"];
            $aRegistro[$i]["marca"] = $xrecord[$i]["id_marca"] == '' ? 'TODAS' : $xrecord[$i]["id_marca"];
            $aRegistro[$i]["marcaNom"] = $xrecord[$i]["marcaNom"];
            $aRegistro[$i]["id_sucursal"] = $xrecord[$i]["id_sucursal"];
            $aRegistro[$i]["habilitado"] = $xrecord[$i]["habilitado"];
            $aRegistro[$i]["margen1"] = $xrecord[$i]["rentabilidad_1"];
            $aRegistro[$i]["margen2"] = $xrecord[$i]["rentabilidad_2"];
        }

        return $aRegistro;
    }

    /**
     * cargarMargenesEspeciales
     * Permite agregar margen especiales de la pantalla rentabilidad.
     * @return void
     */
    function cargarMargenesEspeciales() {        
        // Valido que la llamada venga por método GET o POST.
        if ($this->usePostMethod()) {
            try {
                $datos = $this->getURIParameters("datos");
                $id_suc = $this->getURIParameters("id_suc");
                $objModel = new MargenesEspModel();
                $responseData = json_encode($objModel->cargarMargenesEspeciales($datos, $id_suc));
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
     * cargarMargenesEspeciales
     * Permite agregar margen especiales de la pantalla rentabilidad.
     * @return void
     */
    function borrarMargenesEspeciales() {        
        // Valido que la llamada venga por método GET o POST.
        if ($this->useDeleteMethod()) {
            try {
                $datos = $this->getURIParameters("datos");
                $objModel = new MargenesEspModel();
                $responseData = json_encode($objModel->borrarMargenesEspeciales($datos));
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