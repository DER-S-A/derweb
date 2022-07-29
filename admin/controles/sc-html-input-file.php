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
		if ($pos1 > 0 || $pos2 > 0 || $pos3 > 0)
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
		if ($this->value != "") {
			if ($this->esImagen($this->value) && $this->showImg) {
				$res .= href(img(getImagesPath() . $this->value, $this->value, $this->widthSmall), getImagesPath() . $this->value, "archivosexternos") . "<br>";
			} else {
				if (endsWith($this->value, "pdf"))
					$icono = "pdficon_large.png";

				//este compacto en la grilla
				$visibleLink = substr(basename($this->value), 0, 14) . "..." . substr(basename($this->value), -4);
				if ($this->mcompact)
					$visibleLink = "";

				$res .= href(img("images/$icono", "") . " " . $visibleLink, getImagesPath() . $this->value, "archivosexternos") . "<br>";
			}
		}
		if (!$this->readonly) {
			$path =  getImagesPath();
			$iframe = $this->miframe;

			//agrupa los directorios por trimestre
			if (getParameterInt("sc3-images-subdir", "1")) {
				$path2 = Sc3FechaUtils::formatFechaPath();
				$e = error_reporting(0);
				mkdir($path . $path2);
				$e = error_reporting($e);
			}

			$res .= "<table cellspacing=\"1\" cellpadding=\"1\" border=\"0\" width=\"270\">
						<tr><td width=\"70%\">";

			$res .= "<input type=\"text\" name=\"" . $id . "\" id=\"" . $id . "\" size=\"35\" ";
			$res .= " value=\"" . $this->value . "\">";

			$res .= "</td><td width=\"40\" align=\"center\">";

			$res .= "\n<a name=\"b_" . $id . "\" title=\"Seleccionar archivo\" href=\"javascript:openWindow('sc-form-enviararchivo.php?control=$id&prefix=" . $this->prefix . "&iframe=$iframe" . "&path=$path2', 'formEnviarArchivo')\" tabindex=\"-1\">
							<i class=\"fa fa-folder-open fa-lg boton-fa-control\"></i>";
			$res .= " </a>";

			$res .= "</td><td width=\"50\" align=\"center\">";

			$res .= "\n<a name=\"borrar_" . $id . "\" title=\"Borrar selecci&oacute;n\" href=\"javascript:borrar_campo('" . $id . "')\" tabindex=\"-1\">
							<i class=\"fa fa-trash-o fa-lg boton-fa-control\"></i>";
			$res .= " </a>";

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
		$pos6 = strpos($xvalue, ".bmp");
		if ($pos1 > 0 || $pos2 > 0 || $pos3 > 0 || $pos4 > 0 || $pos5 > 0 || $pos6 > 0)
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

		if ($this->value != "") {
			if ($this->esImagen($this->value) && $this->showImg) {
				$resultado = href(img(getImagesPath() . $this->value, $this->value, $this->widthSmall), "javascript:sc3DisplayDiv('$zoomId', '');");
			} else {
				if (endsWith($this->value, "pdf"))
					$icono = "pdficon_large.png";

				$esCsv = false;
				if (endsWith($this->value, "csv")) {
					$icono = "excell.jpg";
					$esCsv = true;
				}

				//este compacto en la grilla
				$visibleLink = substr(basename($this->value), 0, 14) . "..." . substr(basename($this->value), -4);
				if ($this->mcompact)
					$visibleLink = "";

				if (!$esCsv)
					$resultado .= href(img("images/$icono", "") . " " . $visibleLink, getImagesPath() . $this->value, "archivosexternos") . "<br>";
				else {
					$url = new HtmlUrl("sc-vercsv.php");
					$url->add("f1", getImagesPath() . $this->value);
					$url->add("csv", basename($this->value));

					$resultado .= linkImgFa($url->toUrl(), "fa-file-excel-o", $visibleLink, "fa-2x verde", "csv2", "");
				}

				if (startsWith($this->value, "https")) {
					if (strContiene($this->value, "youtube"))
						$resultado = linkImgFa($this->value, "fa-youtube", $visibleLink, "fa-2x verde", "yotu", "");
					else
						$resultado = href(img("images/web_16.gif", "") . " " . $visibleLink, $this->value, "_blank");
				}

				if (endsWith($this->value, "mp4")) {
					$resultado = linkImgFa($this->value, "fa-file-video-o", $visibleLink, "fa-2x verde", "", "");
				}
			}
		}

		if (!$this->readonly) {
			$path =  getImagesPath();
			$iframe = $this->miframe;

			//agrupa los directorios por trimestre
			if (getParameterInt("sc3-images-subdir", "1")) {
				$path2 = Sc3FechaUtils::formatFechaPath();
				$e = error_reporting(0);
				mkdir($path . $path2);
				$e = error_reporting($e);
			}

			$res .= "<table cellspacing=\"1\" cellpadding=\"1\" border=\"0\" width=\"270\">";

			$res .= "<input type=\"hidden\" name=\"" . $this->getId() . "\" id=\"" . $this->getId() . "\" size=\"35\" ";
			$res .= " value=\"" . $this->value . "\">";
			$res .= "<tr><td rowspan=2>\r\n";

			if (esVacio($resultado))
				$resultado = "Arrastre aqu&iacute;";
			$res .= $divModal . "<div class=\"drop-zone\" 
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
		} else
			return $divModal . $resultado . "\n";
	}
}


/**
 * Control para subir múltiples imágenes
 * Retorna un valor con los nombres de archivos separados por ;
 */
class HtmlInputFileMultiple
{
	var $id = "";
	var $value = "";
	var $extenciones = ".bmp,.webp,.sql,.txt,.pdf,.ms-excel,.json,.plan,.plain,.gif,.jpeg,.jpg,.png,.svg,.xml,.doc,.dot,.docx,.dotx,.dotm,.xls,.xlt,.xla,.xlsx,.xltx,.xlsm,.xltm,.xlam,.xlsb,.docm,.mdb,.html,.mp4";
	var $createThumbnails = true;
	var $maxFileSize = 10;
	var $autoProcesar = 1;
	var $resizeWidth = null;
	var $resizeHeight = null;
	var $resizeQuality = 0.8;
	var $previewsContainer = null;
	var $ancho = "100%";
	var $alto = "250px";

	function __construct($xid, $xvalue)
	{
		$this->id = $xid;
		$this->setValue($xvalue);
		$this->maxFileSize = intval(ini_get('upload_max_filesize'));
	}

	function setValue($xvalue)
	{
		$this->value = $xvalue;
	}

	/**
	 * @param string $extenciones formatos de archivos permitidos ej: ".jpg,.png,.pdf" separados por coma, defecto jpg,png,jpeg
	 */
	function setExtenciones($extenciones)
	{
		$this->extenciones = $extenciones;
	}

	/**
	 * @param bool $createThumbnails crea una preview de la imagen o el archivo subido, defecto si
	 */
	function setCreateThumbnails($createThumbnails)
	{
		$this->createThumbnails = $createThumbnails;
	}

	/**
	 * @param int $maxFileSize indica el tamaño máximo permitido en MB, defecto 10
	 */
	function setMaxFileSize($maxFileSize)
	{
		$this->maxFileSize = $maxFileSize;
	}

	/**
	 * @param int $autoProcesar (0 no, 1 si) indica si se suben automaticamente al arrastrar, util si se usa sin un boton, defecto si
	 */
	function setAutoProcesar($autoProcesar)
	{
		$this->autoProcesar = $autoProcesar;
	}

	/**
	 * @param int $resizeWidth la imagen se redimensiona al ancho dado, manteniendo el ratio original, defecto null
	 */
	function setResizeWidth($resizeWidth)
	{
		$this->resizeWidth = $resizeWidth;
	}

	/**
	 * @param int $resizeHeight la imagen se redimensiona al alto dado, manteniendo el ratio original, defecto null
	 */
	function setResizeHeight($resizeHeight)
	{
		$this->resizeHeight = $resizeHeight;
	}

	/**
	 * @param float $resizeQuality indica la calidad a la que se redimensiona la imagen, entre 0.1 la menor calidad y 1.0 la calidad original, defecto 0.8
	 */
	function setResizeQuality($resizeQuality)
	{
		$this->resizeQuality = $resizeQuality;
	}

	/**
	 * @param string id del contenedor donde se mostraran las preview de lo que se sube, por defecto se muestran en el mismo controlador
	 * EL CONTENEDOR SE DEVE CREAR ANTES DE LLAMAR AL ELEMENTO
	 */
	function setPreviewsContainer($previewsContainer)
	{
		$this->previewsContainer = $previewsContainer;
	}

	/**
	 * @param string $ancho indica el ancho del controlador, defecto 100%
	 */
	function setAncho($ancho)
	{
		$this->ancho = $ancho;
	}

	/**
	 * @param string $alto indica el alto del controlador, defecto 250px;
	 */
	function setAlto($alto)
	{
		$this->alto = $alto;
	}

	function toHtml()
	{
		//se puede hacer una version reducida, donde no contenga icono ni div contenedor
		$result = '<div class="contenedor-dropzone" style="width:' . $this->ancho . '">
			<div class="contenedor-dropzone-form">  
			<img style="width:75px;height:75px; margin:10px;" src="terceros/dropzone/drop.png" alt="">
			<span style="display: block; font-size:10px; color: grey;">Tamaño máximo: ' . $this->maxFileSize . 'MB</span>
				<div enctype="multipart/form-data" class="dropzone" id="dropzone-' . $this->id . '" style="height:' . $this->alto . ';overflow-y: auto;" >  
					<div style="text-align: center;overflow-y: auto;">  
						
					</div>  
   				</div>
				<input type="hidden" name="' . $this->id . '" id="' . $this->id . '">
	 		 </div>';
		if (!$this->autoProcesar) {
			$result .= '
			 <div class="boton-subir" id="dropzone-' . $this->id . '-boton">Subir</div>';
		}
		$result .= '</div>';
		$result .= '<script type="text/javascript">
	  			Dropzone.autoDiscover = false;

	  			var myDropzone = new Dropzone("#dropzone-' . $this->id . '", {
					url: "sc-html-enviararchivo-ajax.php",
					acceptedFiles: "' . $this->extenciones . '",
					createImageThumbnails: ' . $this->createThumbnails . ',
					autoProcessQueue: ' . $this->autoProcesar . ',
					maxFilesize: ' . $this->maxFileSize . ',	
	  				resizeQuality: ' . $this->resizeQuality . ',
					thumbnailWidth: 80,
					thumbnailHeight: 80,';
		//parametros que si no tienen valor, no van, si no causan error
		if ($this->previewsContainer != null)
			$result .= '
					previewsContainer: ".' . $this->previewsContainer . '",';

		if ($this->resizeWidth != null)
			$result .= '
					resizeWidth: ' . $this->resizeWidth . ',';

		if ($this->resizeHeight != null)
			$result .= '
					resizeHeight: ' . $this->resizeHeight . ',';

		if (!$this->autoProcesar) {
			$result .= '
					parallelUploads: 999,';
		}

		$result .= '
				});';


		$result .= '
				document.onclick = function(event) {
		  			if (event === undefined) event = window.event;
		  				var target = "target" in event ? event.target : event.srcElement;
		  			if (target.id == "dropzone-' . $this->id . '-boton")
			 			myDropzone.processQueue();
	 			};

				myDropzone.on("complete", file => {
			  	if(file["status"] == "success"){
					var archivosSubidos = document.getElementById("' . $this->id . '");
					if (archivosSubidos.value == ""){
						archivosSubidos.value = file.xhr.response;
					}
					else{
						archivosSubidos.value = archivosSubidos.value + ";" + file.xhr.response;
					}
				  }
				});
				  
  		</script>
';
	return $result;
	}
}
