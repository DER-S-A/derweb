<?php
/**
 * Control para poder realizar una búsqueda genérica por cualquier campo.
 * 
 * Autor: Leonardo D. Zulli
 * Fecha: 23/07/2021
 */

class HtmlBuscador {
    private $id;
    private $fnExactSearch;
    private $query;
    private $keyFieldName;
    private $descFieldName;
    
    /**
     * __construct
     *
     * @param  string   $xid                    Id. de control
     * @param  string   $xfnExactSearch         Nombre de función que obtiene el dato exacto
     * @param  bool     $xquery                 Nombre del query para dibujar la tabla
     * @return void
     */
    public function __construct($xid, $xfnExactSearch, $xquery)
    {
        $this->id = $xid;
        $this->fnExactSearch = $xfnExactSearch;
        $this->query = $xquery;
    }
    
    /**
     * Establece el nombre del campo ID. a devolver o primario
     * después de haber realizado la búsqueda
     *
     * @param  mixed $xKeyFieldName
     * @return void
     */
    public function setKeyFieldName($xKeyFieldName) {
        $this->keyFieldName = $xKeyFieldName;
    }
    
    /**
     * Establece el campo descripción o campo secundario a devolver
     * después de haber realizado la búsqueda.
     *
     * @param  mixed $xDescFieldName
     * @return void
     */
    public function setDescFieldName($xDescFieldName) {
        $this->descFieldName = $xDescFieldName;
    }

    /**
     * Arma el código HTML y dibuja el control en pantalla.
     */
    public function toHtml() {
        $txtValorBuscado = new HtmlInputText($this->id, "");
        $txtValorBuscado->setSize(10);
        $txtDescripcion = new HtmlInputText($this->id . "desc", "");
        $txtDescripcion->setSize(30);
        $txtDescripcion->setReadOnly(true);

        $jsSetVariables = "<script>"
                        . " keyFieldName = " . $this->keyFieldName . "; \n"
                        . " descFieldName = " . $this->descFieldName . "; \n"
                        . "</script>";

        $html   = "<div class='w3-row'>"
                . " <div class='w3-col m3 w3-margin-right'>" . $txtValorBuscado->toHtml() . "</div>"
                . " <div class='w3-col m9'>" . $txtDescripcion->toHtml();
        $html   .= "<a href=\"javascript:sc_open_modal_buscador('" . $this->query . "');\" title='Buscar valor'><i class='fa fa-search fa-lg boton-fa-control'></i></a>";
        $html   .= "<a href='javascript:limpiar_html_buscador();' title='Limpiar'><i class='fa fa-trash-o fa-lg boton-fa-control'></i></a>";
        $html   .= "</div></div>";
        
        // Asigno un evento para que haga la búsqueda cuando pierde el foco.
        $js = "<script>";
        //$js .= "var " . $this->id . "_modal = new lfw_modal('" . $this->id . "_modal', 'Buscar');";
        $js .= "document.getElementById('" . $this->id . "').addEventListener('blur', " . $this->id . "_blur, false);";
        $js .= "function " . $this->id . "_blur() { \n"
             . "    searchData('" . $this->id . "', '" . $this->fnExactSearch . "', '" . $this->query . "',"
             . "            '" . $this->keyFieldName . "', '" . $this->descFieldName . "'); \n"
             . "}";

        $js .= "</script>";

        return $html . $jsSetVariables . $js;
    }
}

?>