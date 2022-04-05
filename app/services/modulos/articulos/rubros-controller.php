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
     * Usar método: GET
     * Usar ?filter para filtrar por algún campo. Ej. ?filter="id = 1"
     * @return void
     */
    public function get() {
        $strErrorDesc = '';
        $metodoRequest = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        
        if (strcmp(strtoupper($metodoRequest), "GET") == 0) {
            try {
                $filter = "";
                if (isset($arrQueryStringParams["filter"]) && $arrQueryStringParams["filter"]) {
                    $filter = $arrQueryStringParams["filter"];
                }

                $rubrosModel = new RubrosModel();
                $arrRubros = $rubrosModel->get($filter);
                $responseData = json_encode($arrRubros);

            } catch (Exception $ex) {
                $strErrorDesc = $ex->getMessage() . 'Contactese con soporte.';
                $strErrorHeader = 'HTTP/1.1 500 Error Interno de Servidor';
            }
        } else {
            $strErrorDesc = 'Método no soportado';
            $strErrorHeader = 'HTTP/1.1 422 Entidad no procesable';
        }

        // Envío la salida
        if (!$strErrorDesc)
            $this->sendOutput($responseData, array('Content-Type: application/json', 'HTTP/1.1 200 OK'));
        else
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), array('Content-Type: application/json', $strErrorHeader));
    }
}
?>