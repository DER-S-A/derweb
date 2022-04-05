<?php
/**
 * APIController es una clase genérica que se encarga de controlar
 * las APIS Rest que se desarrollen en el sistema.
 */

class APIController {
    
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
     * getUriSegments
     * Obtiene los elementos de la URL.
     * @return void
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
        return parse_str($_SERVER["QUERY_STRING"], $query);
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
}

?>