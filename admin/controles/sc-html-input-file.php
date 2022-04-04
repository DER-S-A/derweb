<?php

/**
 * Controles para subir archivos por FTP
 * SC3 - Marcos C
 * 
 */


/**
 * Subir archivos por FTP, vieja versión
 */
class HtmlInputFile2
{
	var $id = "";
	var $value = "";
	var $readonly = false;
	var $prefix = "-";
	var $width = "";
	var $widthSmall = "80";
	var $mcompact = false;
	var $miframe = "";
	var $showImg = true;

	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->setValue($xvalue);
	}

	function setValue($xvalue)
	{
		$this->value = $xvalue;
	}

	function setIframe($xframe)
	{
		$this->miframe = $xframe;
	}
	
	function setCompact()
	{
		$this->mcompact = true;
	}
	
	function setShowImage($xshowImg)
	{
		$this->showImg = $xshowImg;
	}

	function valueFromRequest()
	{
		$this->setValue(Request($this->id));
	}

	function setPrefix($xprefix)
	{
		$this->prefix = $xprefix;
	}

	function setReadOnly($xreadonly)
	{
		$this->readonly = $xreadonly;
	}

	function esImagen($xvalue)
	{
		$xvalue = strtolower($xvalue);
		$pos1 = strpos($xvalue, ".jpg");
		$pos2 = strpos($xvalue, ".gif");
		$pos3 = strpos($xvalue, ".png");
		if  ($pos1 > 0 || $pos2 > 0 || $pos3 > 0)
			return true;
		return false;
	}

	function setWidth($xwidth)
	{
		$this->width = $xwidth;
	}

	function setWidthPreview($xwidthSmall)
	{
		$this->widthSmall = $xwidthSmall;
	}
	
	function toHtml()
	{
		$icono = "scfile.png";
		
		$id = $this->id;
		$res = "\n<!-- HtmlInputFile -->\n";
		if ($this->value != "")
		{
			if ($this->esImagen($this->value) && $this->showImg)
			{
				$res .= href(img(getImagesPath() . $this->value, $this->value, $this->widthSmall), getImagesPath() . $this->value, "archivosexternos") . "<br>";
			}
			else
			{
				if (endsWith($this->value, "pdf"))
					$icono = "pdficon_large.png";
				
				//este compacto en la grilla
				$visibleLink = substr(basename($this->value), 0, 14) . "..." . substr(basename($this->value), -4);
				if ($this->mcompact)
					$visibleLink = "";
					
				$res .= href(img("images/$icono", "") . " " . $visibleLink, getImagesPath() . $this->value, "archivosexternos") . "<br>";
			}
		}
		if (!$this->readonly)
		{
			$path =  getImagesPath();
			$iframe = $this->miframe;
				
			//agrupa los directorios por trimestre
			if (getParameterInt("sc3-images-subdir", "1"))
			{
				$path2 = Sc3FechaUtils::formatFechaPath();
				$e = error_reporting(0);
				mkdir($path . $path2);
				$e = error_reporting($e);
			}

			$res .= "<table cellspacing=\"1\" cellpadding=\"1\" border=\"0\" width=\"270\">
						<tr><td width=\"70%\">";

			$res .= "<input type=\"text\" name=\"". $id ."\" id=\"". $id . "\" size=\"35\" ";
			$res .= " value=\"" . $this->value . "\">";
				
			$res .= "</td><td width=\"40\" align=\"center\">";
				
			$res .= "\n<a name=\"b_" . $id . "\" title=\"Seleccionar archivo\" href=\"javascript:openWindow('sc-form-enviararchivo.php?control=$id&prefix=" . $this->prefix . "&iframe=$iframe" . "&path=$path2', 'formEnviarArchivo')\" tabindex=\"-1\">
							<i class=\"fa fa-folder-open fa-lg boton-fa-control\"></i>";
			$res .=" </a>";

			$res .= "</td><td width=\"50\" align=\"center\">";

			$res .= "\n<a name=\"borrar_" . $id . "\" title=\"Borrar selecci&oacute;n\" href=\"javascript:borrar_campo('" . $id ."')\" tabindex=\"-1\">
							<i class=\"fa fa-trash-o fa-lg boton-fa-control\"></i>";
			$res .=" </a>";
			
			$res .= "</td></tr></table>\n";
		}
		return $res;
	}
}


/**
 * Control para subir por FTP, con Drag&Drop
 */
class HtmlInputFile
{
	var $id = "";
	var $value = "";
	var $readonly = false;
	var $width = "";
	var $widthSmall = "70";
	var $mcompact = false;
	var $miframe = "";
	var $showImg = true;

	function __construct($xid, $xvalue)
	{ 
		$this->id = $xid;
		$this->setValue($xvalue);
	}

	function getId()
	{
		return $this->id;
	}

	function setValue($xvalue)
	{
		$this->value = $xvalue;
	}

	function setIframe($xframe)
	{
		$this->miframe = $xframe;
	}

	function setCompact()
	{
		$this->mcompact = true;
	}

	function setShowImage($xshowImg)
	{
		$this->showImg = $xshowImg;
	}

	function valueFromRequest()
	{
		$this->setValue(Request($this->id));
	}

	function setReadOnly($xreadonly)
	{
		$this->readonly = $xreadonly;
	}

	function esImagen($xvalue)
	{
		$xvalue = strtolower($xvalue);
		$pos1 = strpos($xvalue, ".jpg");
		$pos2 = strpos($xvalue, ".gif");
		$pos3 = strpos($xvalue, ".png");
		$pos4 = strpos($xvalue, ".svg");
		$pos5 = strpos($xvalue, ".jpeg");
		if ($pos1 > 0 || $pos2 > 0 || $pos3 > 0 || $pos4 > 0 || $pos5 > 0)
			return true;
		return false;
	}

	function setWidth($xwidth)
	{
		$this->width = $xwidth;
	}

	function setWidthPreview($xwidthSmall)
	{
		$this->widthSmall = $xwidthSmall;
	}

	function toHtml()
	{
		$icono = "scfile.png";
		$resultado = "";

		$id = $this->id;
		$res = "\n<!-- HtmlInputFile -->\n";

		//nos aseguramos que sea único, por las grillas
		$zoomId = "zoom$id" . rand(100, 999);
		$divModal = "<div id=\"$zoomId\" class=\"w3-modal\">
						<div class=\"w3-modal-content w3-animate-zoom\">
							<div class=\"w3-padding w3-white w3-center\">
								<span onclick=\"document.getElementById('$zoomId').style.display='none'\"> 
									<img src=\"" . getImagesPath() . $this->value . "\" style=\"max-height:86vh\" title=\"Click para cerrar\"/>
								</span>	
							</div>
						</div>
					</div>\n";

		if ($this->value != "") 
		{
			if ($this->esImagen($this->value) && $this->showImg) 
			{
				$resultado = href(img(getImagesPath() . $this->value, $this->value, $this->widthSmall), "javascript:sc3DisplayDiv('$zoomId', '');");
			} 
			else 
			{
				if (endsWith($this->value, "pdf"))
					$icono = "pdficon_large.png";

				$esCsv = false;
				if (endsWith($this->value, "csv"))
				{
					$icono = "excell.jpg";
					$esCsv = true;
				}

				//este compacto en la grilla
				$visibleLink = substr(basename($this->value), 0, 14) . "..." . substr(basename($this->value), -4);
				if ($this->mcompact)
					$visibleLink = "";

				if (!$esCsv)
					$resultado .= href(img("images/$icono", "") . " " . $visibleLink, getImagesPath() . $this->value, "archivosexternos") . "<br>";
				else
				{
					$url = new HtmlUrl("sc-vercsv.php");
					$url->add("f1", getImagesPath() . $this->value);
					$url->add("csv", basename($this->value));
					
					$resultado .= linkImgFa($url->toUrl(), "fa-file-excel-o", $visibleLink, "fa-2x verde", "csv2", "");
				}					

				if (startsWith($this->value, "https"))
				{
					if (strContiene($this->value, "youtube"))
						$resultado = linkImgFa($this->value, "fa-youtube", $visibleLink, "fa-2x verde", "yotu", "");
					else
						$resultado = href(img("images/web_16.gif", "") . " " . $visibleLink, $this->value, "_blank");
				}
			}
		}

		if (!$this->readonly) 
		{
			$path =  getImagesPath();
			$iframe = $this->miframe;

			//agrupa los directorios por trimestre
			if (getParameterInt("sc3-images-subdir", "1")) 
			{
				$path2 = Sc3FechaUtils::formatFechaPath();
				$e = error_reporting(0);
				mkdir($path . $path2);
				$e = error_reporting($e);
			}

			$res .= "<table cellspacing=\"1\" cellpadding=\"1\" border=\"0\" width=\"270\">";
					
			$res .= "<input type=\"hidden\" name=\"" . $this->getId(). "\" id=\"" . $this->getId() . "\" size=\"35\" ";
			$res .= " value=\"" . $this->value . "\">";
			$res .= "<tr><td rowspan=2>\r\n";

			if (esVacio($resultado))
				$resultado = "Arrastre aqu&iacute;";
			$res .= $divModal . "<div class=\"drop_zone\" 
							id=\"dnd" . $this->getId() . "\" name=\"dnd" . $this->getId() . "\" 
							ondrop=\"upload_file(event, " . $path2 . ", '" . $this->getId() . "', 'previewImagen" . $this->getId() . "');comenzarProgressBar('" . $this->getId() . "');\" ondragover=\"return false\">
						<div id=\"previewImagen" . $this->getId() . "\">
							" . $resultado . "
						</div>						
					</div>\r\n";

			$res .= "</td><td width=\"40\" align=\"center\">";

			$res .= "\n<a name=\"b_" . $this->getId() . "\" title=\"Seleccionar archivo\" href=\"javascript:openWindow('sc-form-enviararchivo.php?control=$id&iframe=$iframe" . "&path=$path2', 'formEnviarArchivo')\" >
							<i class=\"fa fa-folder-open fa-lg boton-fa-control\"></i>";
			$res .= " </a>";

			$res .= "\n<a name=\"link_" . $this->getId() . "\" title=\"Agregar link\" onclick=\"cargaLink('" . $this->getId() . "')\">
							<i class=\"fa fa-link fa-lg boton-fa-control\"></i>";
			$res .= " </a>";			

			$res .= "</tr></td><tr><td width=\"50\" align=\"center\">";

			$res .= "\n<a name=\"borrar_" . $this->getId() . "\" title=\"Borrar selecci&oacute;n\" href=\"javascript:borrar_campo('" . $id . "')\" >
							<i class=\"fa fa-trash-o fa-lg boton-fa-control\"></i>";
			$res .= " </a>";

			$res .= "</td></tr>\n";
			$res .= "</tr>";
			$res .= "<tr><td>
						<div class=\"progress-bar oculto\" id=\"progressBar" . $this->getId() . "\">
							<div class=\"progress-bar-actual\" id=\"progressBarActual" . $this->getId() . "\" style=\"width:10%\"></div>
						</div>
					</td></tr>";
			$res .= "</table>\n";

			return $res;
		}
		else
			return $divModal . $resultado . "\n";
	}
}
