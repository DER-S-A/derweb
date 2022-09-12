<?php

class APISap {
    private $url;
    private $method;
    private $headerOutput;
    private $aHeaders;
    private $data;
    private $ssl_verify;
    private $aInfo;
    private $output;
    private $testMode;
            
    /**
     * __construct
     * Clase para el manejo de las APIs de SAP.
     * @param  string $xurl Establece la URL de la API con la que va a comunicarse.
     * @param  string $xmethod Establece el método a utilizar para enviar los parámetros.
     * @return void
     */
    function __construct($xurl, $xmethod) {
        $this->url = $xurl;
        $this->method = $xmethod;
        $this->headerOutput = false;
        $this->aHeaders = array("Content-Type: application/json");
        $this->data = "";
        $this->ssl_verify = false;
        $this->aInfo = [];
        $this->output = "";
        $this->testMode = false;
    }
    
    /**
     * setTestMode
     * Establece la clase como modo testing.
     * @param  bool $xvalue
     * @return void
     */
    public function setTestMode($xvalue = true) {
        $this->testMode = $xvalue;
    }
        
    /**
     * setHeaderOutput
     * Establece true si se incluye el header en la salida. Valor por defecto false.
     * @param  bool $xvalue
     * @return void
     */
    public function setHeaderOutput($xvalue = true) {
        $this->headerOutput = $xvalue;
    }
    
    /**
     * setHeaders
     * Establece los parámetros del header de solicitud. El valor por defecto es
     * Content-Type: application/json
     * @param  array $xvalue
     * @return void
     */
    public function setHeaders($xvalue = array("Content-Type: application/json")) {
        $this->aHeaders = $xvalue;
    }
    
    /**
     * setData
     * Establece el JSON con los datos de entrada que el API tiene que procesar.
     * @param  array $xdata Array con los datos a procesar.
     * @return void
     */
    public function setData($xdata) {
        $this->data = json_encode($xdata);
        if ($this->testMode)
            file_put_contents("test/enviado-" . date("Ymd", time()) . "json", $this->data);
    }
    
    /**
     * setSSLVerify
     * Establece true si tiene que verificar los certificados de seguridad del API. El valor
     * por defecto es false.
     * @param  boolean $xvalue
     * @return void
     */
    public function setSSLVerify($xvalue = true) {
        $this->ssl_verify = $xvalue;
    }
    
    /**
     * send
     * Envía la solicitud a la API de SAP.
     * @return void
     */
    public function send() {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, $this->url);
        
        if (sonIguales($this->method, "POST"))
            curl_setopt($curlHandler, CURLOPT_POST, true);
        
        curl_setopt($curlHandler, CURLOPT_HEADER, $this->headerOutput);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, $this->ssl_verify);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, $this->ssl_verify);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $this->aHeaders);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curlHandler);
        if (sonIguales($result, "")) {
            $this->aInfo["codigo"] = "API_ERROR";
            $this->aInfo["info"] = curl_getinfo($curlHandler);
            $this->aInfo["mensaje"] = "Error de conexión";
        } else {
            $this->aInfo["codigo"] = "OK";
            $this->aInfo["mensaje"] = json_decode($result, true);
        }
        curl_close($curlHandler);

        $this->output = json_encode($this->aInfo);
        if ($this->testMode)
            file_put_contents("test/recibido-" . date("Ymd", time()) . "json", $this->output);
    }
    
    /**
     * getInfo
     * Obtiene información de ejecución de API.
     * @return string
     */
    public function getInfo() {
        return json_encode($this->aInfo);
    }
        
    /**
     * getData
     * Devuelve los datos enviados en el body.
     * @return string
     */
    public function getData() {
        return $this->data;
    }
    
    /**
     * getTokenETL
     * Establece la conexión con el ETL de SAP. Las definiciones las recupera desde
     * configuracion-sap.php
     * @return void
     */
    public function getTokenETL() {
        $this->url = URL_LOGIN_ETL;
        $this->method = "POST";
        $this->setData(BODY_LOGIN_ETL);
        $this->send();
    }
}
?>