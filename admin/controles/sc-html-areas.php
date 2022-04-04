<?php

/**
 * Clase adaptada de https://www.w3schools.com/howto/howto_js_accordion.asp
 * Autor: Marcos C
 * Fecha: nov-2019
 */
class HtmlAccordeon
{
	var $mId = "";
	var $mTitulo = "";
	var $mClass = "sc3-accordion-panel";
	var $mTitleClass = "sc3-accordion";
	var $mTitleStyle = "";
	var $mScrolleable = false;
	var $mAwesomeFont = "";
    var $aContent = array();

    function __construct($xtitle, $xfontAwasomeIcon = "")
    {
        $this->mAwesomeFont = $xfontAwasomeIcon;
		$this->mTitulo = $xtitle;
		$this->mId = "m" . substr(md5($xtitle), 0, 6);
    }

	function setHeaderClass($xclass)
	{
		$this->mTitleClass = $xclass;
	}

	function setHeaderStyle($xStyle)
	{
		$this->mTitleStyle = $xStyle;
	}

	function setScrolleable($xscrolleable)
	{
		$this->mScrolleable = $xscrolleable;
	}

    function addP($xcontent)
    {
        $this->aContent[] = '<p>' . $xcontent . '</p>';
    }

	function addDiv($xcontent, $xclass = "")
    {
        $this->aContent[] = "<div class=\"$xclass\">" . $xcontent . '</div>';
    }

    function addEtiquetaValor($xEtiqueta, $xValor, $xclassDiv = "")
    {
		$this->addDiv("<div class=\"info-etiqueta\">$xEtiqueta</div>
						<div class=\"info-dato\">$xValor</div>", "informacion $xclassDiv");
    }

	/**
	 * Muestra el panel, funciona siempre expandido en false
	 */
    function toHtml($xexpandida = false, $idBoton = 0)
    {
		$icono = "<i class=\"fa fa-angle-double-down fa-lg\"></i>";
		$estiloPanel = "sc3-accordion-panel";
		$active = "";
		if ($xexpandida)
		{
			$icono = " <i class=\"fa fa-angle-double-up fa-lg\"></i>";
			$estiloPanel = "sc3-accordion-panel-abierta";
			$active = " active";
		}
		if ($this->mScrolleable) { 
			$scroll = 'scrollGradual('.$idBoton.');';
		}else {
			$scroll = '';
		}
		$icono = "<span id=\"" . $this->mId . "\" class=\"sc3-accordion-flecha\">$icono</span>";
        $result = "<div><button id='$idBoton' class=\"" . $this->mTitleClass . " $active\" style=\"" . $this->mTitleStyle . "\" onclick=\"javascript: accordionToggle(this, '" . $this->mId . "'); $scroll return false; \">";
        if (!esVacio($this->mAwesomeFont))
            $result .= imgFa($this->mAwesomeFont, "fa-2x");
        $result .= " " . $this->mTitulo . " $icono </button>\r\n";

        $result .= "<div class=\"$estiloPanel\">";
          
        foreach ($this->aContent as $i => $txt)
            $result .= $txt . "\r\n";

        $result .= "</div></div>";
        return $result;
    }

}


class HtmlExpandingArea
{
	var $id = "";
	var $titulo = "";
	var $mInTabla = true;
	var $mExpandible = true;
	var $width = "100%";
	var $mClass = "expandingtable";
	var $mTitleClass = "expandingcell";
	var $mTitleAlign = "center";
	var $mAwesomeFont = "";
	var $mIconUrl = array();

	function __construct($xtitulo)
	{
		$this->id = escapeJsNombreVar($xtitulo);
		if (sonIguales($this->id, ""))
			$this->id = getClave(5);

		$this->titulo = $xtitulo;
	}

	function setAwesomeFont($xfont)
	{
		$this->mAwesomeFont = $xfont;
	}
	
	function setIconURL($xurl, $xtarget)
	{
		$this->mIconUrl["url"] = $xurl;
		$this->mIconUrl["target"] = $xtarget;
	}
	
	function setExpandible($xExpandible)
	{
		$this->mExpandible = $xExpandible;
	}

	function setWidth($xwidth)
	{
		$this->width = $xwidth;
	}

	function setInTable($xintable)
	{
		$this->mInTabla = $xintable;
	}

	function setClassMenu($xclass = "expandingtablemenu")
	{
		$this->mClass = $xclass;
	}

	function setTitleClass($xclass)
	{
		$this->mTitleClass = $xclass;
	}
	
	function setTitleAlign($xalign)
	{
		$this->mTitleAlign = $xalign;
	}
	
	/*
	 Comienza un fieldset con el legend dado
	*/
	function start($xexpandida = false, $xfiltrable = false)
	{
		$id = $this->id;
		$titulo = htmlVisible($this->titulo);

		$str = "\n<!-- INICIO AREA -->";
		if ($this->mInTabla)
		{
			$str .= "<tr><td colspan=\"2\">";
		}
		$str .= "\n<table width=\"" . $this->width . "\" class=\"" . $this->mClass . "\">";

		$str .= "<tr>";
		if (!esVacio($this->mAwesomeFont))
		{
			$str .= "	<td width=\"35\" align=\"left\" class=\"" . $this->mTitleClass . "\">";
			if (isset($this->mIconUrl["url"]))
				$str .= href("<i class=\"" . $this->mAwesomeFont ." gris\"></i>", $this->mIconUrl["url"], $this->mIconUrl["target"]);
			else
				$str .= "<i class=\"" . $this->mAwesomeFont ." fa-fw gris\"></i> ";
			$str .= "</td>";
		}
		$str .= "	<td align=\"" . $this->mTitleAlign . "\" class=\"" . $this->mTitleClass . "\">";
		
		if ($this->mExpandible)
			$str .= "\n<address onclick=\"sc3expand2('dr$id','mr$id', '$titulo');\" id=\"mr$id\" style=\"cursor: pointer\">";
		
		$str .= "$titulo ";

		if ($this->mExpandible)
		{
			if (!$xexpandida)
				$str .= " <i class=\"fa fa-angle-double-down fa-lg\"></i>";
			else
				$str .= " <i class=\"fa fa-angle-double-up fa-lg\"></i>";
			$str .= "</address>";
		}
		$str .= "</td></tr>";

		$str .= "<tr id=\"dr$id\" ";
		if (!$xexpandida && ($this->mExpandible))
			$str .= " style=\"display:none\"";
		$str .= "><td class=\"expandingtableinner\" colspan=\"2\">\n";
		
		$class = "";
		if ($xfiltrable)
			$class = "table-autofilter";
		$str .= "<table width=\"100%\" cellpadding=\"2\" class=\"$class\">";
		
		return $str;
	}

	function end()
	{
		$str = "</table></td></tr></table>\n";
		if ($this->mInTabla)
		{
			$str .= "</td></tr>\n";
		}
		return $str;
	}
}

class HtmlExpandingAreaDiv
{
	var $id = "";
	var $titulo = "";
	var $mMinWidth = "380";
	var $mClass = "expandingtable";
	var $mTitleClass = "expandingcell";
	var $mDivStyle = "";

	function __construct($xtitulo, $xdivStyle)
	{
		$this->id = escapeJsNombreVar($xtitulo);
		if (sonIguales($this->id, ""))
			$this->id = getClave(5);

		$this->titulo = $xtitulo;
		$this->mDivStyle = $xdivStyle;
	}

	function setWidth($xwidth)
	{
		$this->width = $xwidth;
	}

	function setClassMenu($xclass = "expandingtablemenu")
	{
		$this->mClass = $xclass;
	}

	function setTitleClass($xclass)
	{
		$this->mTitleClass = $xclass;
	}

	/*
	 Comienza un fieldset con el legend dado
	 */
	function start($xexpandida = false)
	{
		$id = $this->id;
		$titulo = $this->titulo;

		$str = "\n<!-- INICIO AREA DIV -->";
		$str .= "\n<div class=\"" . $this->mDivStyle . "\">";
		$str .= "\n<table width=\"" . $this->mMinWidth . "\" class=\"" . $this->mClass . "\">";
		
		$str .= "<tr><td align=\"center\" class=\"" . $this->mTitleClass . "\">";

		$str .= "\n	<address onclick=\"sc3expand2('dr$id','mr$id', '$titulo');\" id=\"mr$id\" style=\"cursor: pointer\">";
		$str .= "$titulo ";
		if (!$xexpandida)
			$str .= " <img src=\"images/sc3expand.png\" border=\"0\" />";
		else
			$str .= " <img src=\"images/sc3contract.png\" border=\"0\" />";
		$str .= "</address>";
		$str .= "</td></tr>";

		$str .= "<tr id=\"dr$id\"";
		if (!$xexpandida)
			$str .= " style=\"display:none\"";
		$str .= "><td class=\"expandingtableinner\">\n
					<table width=\"100%\" cellpadding=\"2\">";
		return $str;
	}

	function end()
	{
		$str = "</table></td></tr></table>\n";
		$str .= "</div>\n";
		$str .= "\n<!-- FIN AREA DIV -->";
		return $str;
	}
}


?>