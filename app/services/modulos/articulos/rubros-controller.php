<?php
/**
 * Esta clase controla la API Rest para la tabla rubros.
 * Acá se desarrollarán todos los métodos que se requieran para procesar los
 * datos de la tabla rubros.
 */

class RubrosController extends APIController {
   
    /**
     * listarPorId
     * Recupera registros de rubros.
     * Usar método: GET o POST
     * Usar ?filter para filtrar por algún campo. Ej. ?filter="id = 1"
     * @return void
     */
    public function get() {
        $arrQueryStringParams = $this->getQueryStringParams();
        
        // Valido que la llamada venga por método GET o POST.
        if ($this->useGetMethod() || $this->usePostMethod()) {
            try {
                $filter = "";
                if (isset($arrQueryStringParams["filter"]) && $arrQueryStringParams["filter"]) {
                    $filter = $arrQueryStringParams["filter"];
                }

                $rubrosModel = new RubrosModel();
                $arrRubros = $rubrosModel->get($filter);
                $responseData = json_encode($arrRubros);

            } catch (Exception $ex) {
                $this->setErrorFromException($ex);
            }
        } else {
            $this->setErrorMetodoNoSoportado();
        }

        // Envío la salida
        if ($this->isOK())
            $this->sendOutput($responseData, $this->getSendOutputHeaderArrayOKResult());
        else
            $this->sendOutput($this->getOutputJSONError(), $this->getSendOutputHeaderArrayError());
    }
}
?>