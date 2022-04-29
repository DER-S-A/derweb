<?php
/**
 * Esta clase contiene la generación del endpoint.
 */

class GeneradorEndPoint {

    var $endPointPath = "";
    var $modulo = "";
    var $tabla = "";
    var $moduloPath = "";
    
    /**
     * __construct
     * Constructor de clase. Inicializo propiedades.
     * @param  string $xstrModulo Nombre del módulo.
     * @param  string $xstrTabla Nombre de la tabla a procesar.
     * @return void
     */
    function __construct($xstrModulo, $xstrTabla)
    {
        $this->endPointPath = getParameter("endpoints-path", "");
        $this->modulo = $xstrModulo;
        $this->tabla = $xstrTabla;
    }
        
    /**
     * generarEndPoint
     * Proceso que permite crear el código de un end point.
     * @param  string $xstrModulo
     * @param  string $xstrTabla
     * @return void
     */
    public function generarEndPoint() {
        $this->verificarOCrearDirectorio();
        $this->generateModelClass();
        $this->generateControllarClass();
        $this->generateEndPointFile();
    }
    
    /**
     * crearDirectorio
     * Proceso que permite crear el directorio con el nombre del módulo del endpoint.
     * @param  mixed $xstrModulo
     * @return void
     */
    private function verificarOCrearDirectorio() {
        $this->moduloPath = $this->endPointPath . "modulos/" . $this->modulo; 
        if (!file_exists($this->moduloPath))
            mkdir($this->moduloPath);
    }
    
    /**
     * generateModelClass
     * Permite generar el código para la clase modelo.
     * @return void
     */
    private function generateModelClass() {
        $code = $this->generateCode("modulos/generador-endpoint/php-template-model.php");
        file_put_contents($this->moduloPath . "/" . $this->getFileName() . "Model.inc.php", $code);
    }
    
    /**
     * getFileName
     * Devuelve el nombre del archivo formateado a partir del nombre de de la tabla
     * @return string
     */
    private function getFileName() {
        $modulo = str_replace("_", "-", $this->tabla);
        return $modulo;
    }
    
    /**
     * generateCode
     * Obtiene el código del template y sobreescribe el nombre_tabla.
     * @param  string $xtemplate PHP Template.
     * @return void
     */
    private function generateCode($xtemplate) {
        $code = file_get_contents($xtemplate);
        $code = str_replace("nombre_clase", ucfirst($this->tabla), $code);
        $code = str_replace("nombre_tabla", $this->tabla, $code);
        return $code;       
    }
    
    /**
     * generateControllarClass
     * Genera el código de la clase controladora de API.
     * @return void
     */
    private function generateControllarClass() {
        $code = $this->generateCode("modulos/generador-endpoint/php-template-controller.php");
        file_put_contents($this->moduloPath . "/" . $this->getFileName() . "Controller.php", $code);
    }
    
    /**
     * generateEndPointFile
     * Genera una el archivo EndPoint final.
     * @return void
     */
    private function generateEndPointFile() {
        $code = $this->generateCode("modulos/generador-endpoint/php-template-endpoint.php");
        file_put_contents($this->endPointPath . "/" . $this->getFileName() . ".php", $code);
    }
}
?>