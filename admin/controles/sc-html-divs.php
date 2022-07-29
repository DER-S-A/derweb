<?php

/**
 * Clase que arma un DIV con un arreglo de contenidos
 * @author marcos
 */
class HtmlDivsContainer
{
	var $mClass = "container";
	var $mDatos = [];

	function __construct($xclass)
	{
		$this->mClass = $xclass;
	}

	/**
	 * Agrega un contenido
	 * @param string $xEtiqueta
	 * @param string $xDato
	 */
	function add($xContenido)
	{
		$this->mDatos[] = $xContenido;
	}

	/**
	 * Arma DIV con contenido dado
	 * @return string
	 */
	function toHtml()
	{
		$str = "\n<!-- INICIO HtmlDivsContainer -->";
		$str .= "\n<div class=\"" . $this->mClass . "\" >";

		$str .= implode("\n", $this->mDatos);
		$str .= "</div>\n";
		$str .= "\n<!-- FIN HtmlDivsContainer -->";
		return $str;
	}
}
