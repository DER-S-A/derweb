<?php
/**
 * APIController es una clase genérica que se encarga de controlar
 * las APIS Rest que se desarrollen en el sistema.
 */

class APIController {
    private $errorMessage;
    private $errorHeader;
    private $ocurrioError;
    private $arrOutputHeader;
    private $jsonOutputError;

    function __construct() {
        $this->ocurrioError = false;
    }
    
    /**
     * setErrorMetodoNoSoportado
     * Permite establecer el mensaje de error y el header cuando un método
     * de solicitud invocado no es soportado por el End Point.
     * @return void
     */
    public function setErrorMetodoNoSoportado() {
        $this->errorMessage = "Método no soportado";
        $this->errorHeader = "HTTP/1.1 422 Entidad no procesable";
        $this->setErrorProperties();
    }
    
    /**
     * setErrorProperties
     * Setea las propiedades de los errores.
     * @return void
     */
    protected function setErrorProperties() {
        $this->ocurrioError = true;
        $this->arrOutputHeader = array('Content-Type: application/json', $this->getErrorHeader());
        $this->jsonOutputError = json_encode(array('error' => $this->getErrorMessage()));
    }
    
    /**
     * setErrorFromException
     * Permite establecer el mensaje de error y el header cuando el error surge
     * a partir de una excepción try - catch.
     * @param  mixed $xmessage
     * @return void
     */
    public function setErrorFromException($xmessage = "") {
        $this->errorMessage = $xmessage . " Comunicarse con soporte";
        $this->errorHeader = "HTTP/1.1 500 Error Interno de Servidor";
        $this->setErrorProperties();
    }
    
    /**
     * getErrorMessage
     * Devuelve el mensaje de error.
     * @return void
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }
    
    /**
     * getErrorHeader
     * Devuelve el header del error.
     * @return void
     */
    public function getErrorHeader() {
        return $this->errorHeader;
    }

    /**
     * isOK
     * Verifica si el End Point funcionó correctamente.
     * @return bool true: todo salió bien / false: hubo errores.
     */
    public function isOK() {
        return !$this->ocurrioError;
    }
    
    /**
     * getSendOutputArrayOKResult
     * Devuelve el array con las cabaceras para cuando el End Point se ejecutó correctamente.
     * @return array
     */
    public function getSendOutputHeaderArrayOKResult() {
        $this->arrOutputHeader = array('Content-Type: application/json', 'HTTP/1.1 200 OK');
        return $this->arrOutputHeader;
    }
    
    /**
     * getSendOutputHeaderArrayError
     * Devuelve el header de salida en caso de que haya ocurrido un error.
     * @return array
     */
    public function getSendOutputHeaderArrayError() {
        return $this->arrOutputHeader;
    }
    
    /**
     * getOutputJSONError
     * Devuelve el string JSON para la salida del error.
     * @return string
     */
    public function getOutputJSONError() {
        return $this->jsonOutputError;
    }
    
    /**
     * __call
     * Método de llamada
     * @param  string $name
     * @param  mixed $arguments
     * @return void
     */
    public function __call($name, $arguments) {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }

    /**
     * getMethdoName
     * Obtiene el nombre del método que se está invocando según la
     * URL recibida.
     * @return void
     */
    public function getMethdoName() {
        $uri = $this->getUriSegments();
        return $uri[sizeof($uri) - 1];
    }
    
    /**
     * getUriSegments
     * Obtiene los elementos de la URL.
     * @return array
     */
    protected function getUriSegments() {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $uri = explode('/', $uri);
        return $uri;
    }
    
    /**
     * getQueryStringParams
     * Obtiene los parámetros de la cadena de consulta.
     * @return void
     */
    protected function getQueryStringParams() {
        parse_str($_SERVER["QUERY_STRING"], $query);
        return $query;
    }
        
    /**
     * sendOutput
     * Envía salida de la API.
     * @param  mixed $xdata
     * @param  mixed $xHeaders
     * @return void
     */
    protected function sendOutput($xdata, $xHeaders = array()) {
        header_remove('Set-Cookie');

        if (is_array($xHeaders) && count($xHeaders)) {
            foreach ($xHeaders as $header)
                header($header);
        }
        
        echo $xdata;
    }

    /**
     * Verifica si se está usando el método GET.
     */
    public function useGetMethod() {
        $metodoRequest = $_SERVER["REQUEST_METHOD"];
        return sonIguales(strtoupper($metodoRequest), "GET");
    }

    /**
     * Verifica si se está usando el método POST
     */
    public function usePostMethod() {
        $metodoRequest = $_SERVER["REQUEST_METHOD"];
        return sonIguales(strtoupper($metodoRequest), "POST");
    }
    
    /**
     * usePutMethod
     * Verifica si se debe usar el método PUT.
     * @return bool
     */
    public function usePutMethod() {
        $metodoRequest = $_SERVER["REQUEST_METHOD"];
        return sonIguales(strtoupper($metodoRequest), "PUT");
    }

    /**
     * useDeleteMethod
     * Verifica si se debe usar el método DELETE.
     * @return bool
     */
    public function useDeleteMethod() {
        $metodoRequest = $_SERVER["REQUEST_METHOD"];
        return sonIguales(strtoupper($metodoRequest), "DELETE");
    }
    
    /**
     * getURIParameters
     * Verifica si vino algún parámetro en la URL y en lo devuelve
     * @param string $xparamName Nombre del parámetro de URL a obtener.
     * @return string
     */
    protected function getURIParameters($xparamName) {
        $arrQueryStringParams = $this->getQueryStringParams();
        $filter = "";

        // Verifico si el parámetro filter viene seteado en la URL.
        if (isset($arrQueryStringParams[$xparamName]) && $arrQueryStringParams[$xparamName])
            $filter = $arrQueryStringParams[$xparamName];
            
        return $filter;
    }

    /**
     * Permite recuperar un parámetro desde el body
     */
    protected function getBodyParameter() {
        $datos = file_get_contents("php://input");
        return $datos;
    }
}

?>