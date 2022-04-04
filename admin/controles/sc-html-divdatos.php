<?php

/**
 * Clase que arma un DIV con etiqueta: valor y q es flotante
 * @author marcos
 */
class HtmlDivDatos
{
	var $id = "";
	var $titulo = "";

	var $mExpandible = false;
	var $mExpandida = true;
	var $mClass = "expandingtable";
	var $mTitleClass = "expandingcell";
	var $mDivStyle = "divFlotante";
	var $mInnerTableClass = "";
	var $mAwesomeFont = "";
	var $mVisible = true;
	var $mDatos = array();
	var $tituloBase = "";

	function __construct($xtitulo)
	{
		$this->tituloBase = $xtitulo;
		$this->id = escapeJsNombreVar($xtitulo);
		if (sonIguales($this->id, ""))
			$this->id = getClave(5);

		$this->titulo = $xtitulo;
	}

	function setExpandible($xExpandible = true)
	{
		$this->mExpandible = $xExpandible;
	}

	function setExpandida($xExpandida = true)
	{
		$this->mExpandida = $xExpandida;
	}

	function setStyle($xdivStyle)
	{
		$this->mDivStyle = $xdivStyle;
	}

	function setStyleForm()
	{
		$this->mDivStyle = "divFlotanteForm";
	}

	function setEtiquetaChica()
	{
	}

	function setVisible($xvisible)
	{
		$this->mVisible = $xvisible;
	}

	function setInnerTableClass($xclass)
	{
		$this->mInnerTableClass = $xclass;
	}

	function setAwesomeFont($xfont)
	{
		$this->mAwesomeFont = $xfont;
	}

	function setStyleForm2Cols()
	{
		$this->mDivStyle = "divFlotanteForm ancho100";
	}


	/**
	 * Agrega un par: etiqueta: dato. Si la etiqueta es vacia se muestra una unica celda
	 * @param string $xEtiqueta
	 * @param string $xDato
	 */
	function add($xEtiqueta, $xDato, $xclass = "", $xRowId = "", $xVisible = 1)
	{
		$this->mDatos[] = array($xEtiqueta, $xDato, $xclass, $xRowId, $xVisible);
	}

	function setClassMenu($xclass = "expandingtablemenu")
	{
		$this->mClass = $xclass;
	}

	function setTitleClass($xclass)
	{
		$this->mTitleClass = $xclass;
	}

	/**
	 * Arma DIV con contenido dado
	 * @return string
	 */
	function toHtml()
	{
		$id = $this->id;
		$titulo = $this->titulo;

		if (count($this->mDatos) == 0)
			return "";

		$str = "\n<!-- INICIO HtmlDivDatos -->";
		$str .= "\n<div class=\"" . $this->mDivStyle . "\" id=\"div" . escapeJsNombreVar($titulo) . "\" ";

		if (!$this->mVisible)
			$str .= " style=\"display:none\"";
		$str .= ">";

		$titleAlign = "center";
		if (!esVacio($this->mAwesomeFont))
			$titleAlign = "left";

		$str .= "\n<div width=\"99%\" class=\"" . $this->mClass . "\">";
		$str .= "<div class=\"" . $this->mTitleClass . "\">";

		if (!esVacio($this->mAwesomeFont)) {
			$str .= "<i class=\"" . $this->mAwesomeFont . "\"></i> ";
		}

		if ($this->mExpandible)
			$str .= "\n<a onclick=\"sc3expand2('dr$id','mr$id', '$titulo');\" id=\"mr$id\" style=\"cursor: pointer\">";

		$str .= "$titulo";

		if ($this->mExpandible) {
			if (!$this->mExpandida)
				$str .= " <i class=\"fa fa-angle-double-down fa-lg\"></i>";
			else
				$str .= " <i class=\"fa fa-angle-double-up fa-lg\"></i>";
			$str .= "</a>";
		}

		$str .= "</div></div>";

		$str .= "<div  id=\"dr$id\" ";

		if (!$this->mExpandida)
			$str .= " style=\"display:none\"";

		$str .= ">
				<div class=\"expandingtableinner " . $this->mInnerTableClass . "\">\n
						<div>";

		//contenido
		foreach ($this->mDatos as $par) {
			$str .= "<div class=\"informacion\" ";
			//analiza si hay row id
			if (isset($par[3]) && !esVacio($par[3])) {
				$str .= " id=\"" . $par[3] . "\" ";
			}
			//analiza si se inicia oculta
			if (isset($par[4]) && $par[4] == 0) {
				$str .= " style=\"display:none\"";
			}

			$str .= ">";

			$classDato = "";
			if (isset($par[2]) && !esVacio($par[2])) {
				$classDato = $par[2];
			}

			//si la etiqueta es vacía no se muestra
			if (!esVacio($par[0])) {
				$str .= "<div class=\"info-etiqueta\">";
				$str .= $par[0];
				$str .= "</div>";

				$str .= "\n <div class=\"info-dato $classDato\">";
				$str .= $par[1];
				$str .= "</div>";
			} else {
				//único dato, queda en un DIV sin estilo
				//antes estaba en DIV
				$str .= "\n";
				$str .= $par[1];
			}
			$str .= "</div>";
		}

		$str .= "</div></div></div></div>\n";
		$str .= "\n<!-- FIN HtmlDivDatos -->";
		return $str;
	}

	function toHtmlDraggeable()
	{
		$id = $this->id;
		$titulo = $this->titulo;

		if (count($this->mDatos) == 0)
			return "";

		$str = "\n<!-- INICIO HtmlDivDatos -->";
		$str .= "\n<div class=\"" . $this->mDivStyle . "\" id=\"div" . escapeJsNombreVar($titulo) . "\" ";

		if (!$this->mVisible)
			$str .= " style=\"display:none\"";
		$str .= ">";

		$titleAlign = "center";
		if (!esVacio($this->mAwesomeFont))
			$titleAlign = "left";

		$str .= "\n<div width=\"99%\" class=\"" . $this->mClass . "\">";
		$str .= "<div class=\"" . $this->mTitleClass . "\">";

		if (!esVacio($this->mAwesomeFont)) {
			$str .= "<i class=\"" . $this->mAwesomeFont . "\"></i> ";
		}

		if ($this->mExpandible)
			$str .= "\n<a onclick=\"sc3expand2('dr$id','mr$id', '$titulo');\" id=\"mr$id\" style=\"cursor: pointer\">";

		$str .= "$titulo";

		if ($this->mExpandible) {
			if (!$this->mExpandida)
				$str .= " <i class=\"fa fa-angle-double-down fa-lg\"></i>";
			else
				$str .= " <i class=\"fa fa-angle-double-up fa-lg\"></i>";
			$str .= "</a>";
		}

		$str .= "</div></div>";

		$str .= "<div  id=\"dr$id\" ";

		if (!$this->mExpandida)
			$str .= " style=\"display:none\"";

		$str .= ">
				<div class=\"expandingtableinner " . $this->mInnerTableClass . "\">\n
						<div class=\"droptarget\"  ondragenter=\"dragEnter(event)\" ondragleave=\"dragLeave(event)\" ondrop=\"drop(event, '" . $this->tituloBase . "')\" ondragover=\"allowDrop(event)\">";

		//contenido
		foreach ($this->mDatos as $par) {
			$str .= "<div style=\"width: 90%; margin: 0 auto;\"";
			//analiza si hay row id
			if (isset($par[3]) && !esVacio($par[3])) {
				$str .= " id=\"" . $par[3] . "\" ";
			}
			//analiza si se inicia oculta
			if (isset($par[4]) && $par[4] == 0) {
				$str .= " style=\"display:none\"";
			}

			$str .= ">";

			$classDato = "";
			if (isset($par[2]) && !esVacio($par[2])) {
				$classDato = $par[2];
			}

			//si la etiqueta es vacía no se muestra
			if (!esVacio($par[0])) {
				$str .= "<div class=\"info-etiqueta\">";
				$str .= $par[0];
				$str .= "</div>";

				$str .= "\n <div class=\"info-dato $classDato\">";
				$str .= $par[1];
				$str .= "</div>";
			} else {
				//único dato, queda en un DIV sin estilo
				//antes estaba en DIV
				$str .= "\n";
				$str .= $par[1];
			}
			$str .= "</div>";
		}

		$str .= "</div></div></div></div>\n";
		$str .= "\n<!-- FIN HtmlDivDatos -->";
		return $str;
	}
}
