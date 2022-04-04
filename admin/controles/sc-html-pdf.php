<?php


/**
 *  Generacion de reportes en pdf (usa ezpdf)
 * @author marccos
 */
class HtmlPdf
{
	public static $TABLA = 500;
	public static $TABLA_2 = 250;
	public static $TABLA_3_1 = 166;
	public static $TABLA_3_2 = 334;
	public static $TABLA_4_1 = 125;

	var $titulo = "";
	var $pdf;
	var $apaisanada = false;
	var $filename = "";
	var $emailDefault = '';
	var $celularDefault = '';
	var $mEnviarATodos = false;
	var $gridWidth = "500";
	var $metiquetas = false;
	var $mImprimeFecha = true;
	var $mEspacioAntesDeGrilla = true;
	var $blanckPage = false;
	var $defFontSize = 9;
	var $mLineCol = array();
	var $defFontSizeText = 9;
	var $aCsvFiles = array();
	var $mMostrarNroHoja = true;
	var $mKey = "";

	function __construct($xtitulo, $xtamanio = "a4", $xapaisanada = false, $xblanckPage = false, $xmostrarLogo = true, 
								$xMostrarNroHoja = true, $xTablaconImagenes = false)
	{

		global $SITIO;

		$fecha = date("d/m/Y");
		$footer = "$SITIO  -  $fecha  -  Hoja {PAGENUM} de {TOTALPAGENUM}";
		$this->mMostrarNroHoja = $xMostrarNroHoja;

		$this->setTitulo($xtitulo);
		$this->blanckPage = $xblanckPage;
		if ($xapaisanada) {
			$this->setApaisanada();
			$this->pdf = new Cezpdf($xtamanio, "landscape");
			if ($xMostrarNroHoja)
				$this->pdf->ezStartPageNumbers(800, 21, 7, "right", $footer);
		} else {
			$this->pdf = new Cezpdf($xtamanio);

			if ($xMostrarNroHoja)
				$this->pdf->ezStartPageNumbers(550, 21, 7, "right", $footer);
		}

		if ($xTablaconImagenes) {
			$ext = './terceros/cezpdf/extensions/CezTableImage.php';
			if (!file_exists($ext)) {
				die('This class requires the CezTableImage.php extension');
			}

			include $ext;
			if ($xapaisanada) {
				$this->pdf = new CezTableImage($xtamanio, "landscape");
				if ($xMostrarNroHoja)
					$this->pdf->ezStartPageNumbers(800, 21, 7, "right", $footer);
			} else {
				$this->pdf = new CezTableImage($xtamanio);
				if ($xMostrarNroHoja)
					$this->pdf->ezStartPageNumbers(550, 21, 7, "right", $footer);
			}
		}

		$font = getParameter("sc3-font-pdf", "Helvetica.afm");
		$this->pdf->selectFont('./terceros/cezpdf/src/fonts/' . $font);

		//Font de Gonzalo L
		//$this->pdf->selectFont('./pdf/fonts/Squ721Rm.afm');

		//si no muestra el logo, asume hoja preimpresa y baja el margen
		if ($xmostrarLogo)
			$this->pdf->ezSetCmMargins(1, 1, 2, 1.5);
		else
			$this->pdf->ezSetCmMargins(5, 1, 2, 1.5);

		global $SITIO;
		$datacreator = array(
			'Title' => $xtitulo,
			'Author' => $SITIO,
			'Subject' => $xtitulo,
			'Creator' => 'info@sc3.com.ar',
			'Producer' => 'http://www.sc3.com.ar/'
		);

		$this->pdf->addInfo($datacreator);
		if (!$this->blanckPage)
			$this->writeHeader($xmostrarLogo);

		$this->defFontSize = getParameterInt("pdf-font-size", 9);
		$this->defFontSizeText = getParameterInt("pdf-font-size-text", 9);
		$this->setLineCol(array(176 / 255, 196 / 255, 222 / 255));
	}

	function setCmMargins($t, $b, $l, $r)
	{
		$this->pdf->ezSetCmMargins($t, $b, $l, $r);
	}

	function setEspacioAntesDeGrilla($xEspacio)
	{
		$this->mEspacioAntesDeGrilla = $xEspacio;
	}

	function setLineColGray()
	{
		$this->setLineCol(array(0.9, 0.9, 0.9));
	}

	function setLineColDarkGray()
	{
		$this->setLineCol(array(0.8, 0.8, 0.8));
	}

	function setLineColBlack()
	{
		$this->setLineCol(array(0.1, 0.1, 0.1));
	}

	function setLineCol($xLineCol)
	{
		$this->mLineCol = $xLineCol;
	}

	function setEnviarATodos($xenviar = true)
	{
		$this->mEnviarATodos = $xenviar;
	}

	/**
	 * @return Cezpdf
	 */
	function getPdfObj()
	{
		return $this->pdf;
	}

	/**
	 * Imprime puntos X,Y para orientar las coordenadas del documento
	 */
	function imprimirCoordenadas($xrango = 50)
	{
		$topX = 500;
		if ($this->apaisanada)
			$topX = 1000;
		$x = 50;
		while ($x < $topX) {
			$y = 50;
			while ($y < 800) {
				$this->pdf->addText($x, $y, 5, ".($x,$y)");
				$y = $y + $xrango;
			}
			$x = $x + $xrango;
		}
	}

	function setGridWidth($xgridWidth)
	{
		$this->gridWidth = $xgridWidth;
	}

	function setGridWidthDefault()
	{
		$this->gridWidth = 495;
	}

	function setTitulo($xtitulo)
	{
		$this->titulo = $xtitulo;
	}

	function setEmailDefault($xemailDefault)
	{
		$this->emailDefault = $xemailDefault;
	}

	function setCelularDefault($xcelDefault)
	{
		$this->celularDefault = $xcelDefault;
	}

	/**
	 * Puntos a centímetros
	 */
	function puntos_cm($medida, $resolucion = 72)
	{
		return ($medida / (2.54)) * $resolucion;
	}

	/**
	 * Inversa de puntos a Centimetros
	 */
	function cm_a_puntos($cm, $resolucion = 72)
	{
		return ($cm * 2.54) / $resolucion;
	}

	function getFileName()
	{
		return $this->filename;
	}

	function writeHeader($xmostrarLogo = true)
	{
		global $SITIO;
		$anchoLogo = getParameter("sc3-ancho-logo", 150);
		$altoLogo  = getParameter("sc3-alto-logo", 55);

		$diff = 0;
		if (!$xmostrarLogo)
			$diff = -1.2;

		$logoAncho = getParameter("sc3-pdf-logo-ancho", 0);

		$rsempresa = getRsEmpresa();
		$empresa = $rsempresa->getValue("nombre");
		if (!sonIguales($rsempresa->getValue("direccion"), ""))
			$empresa .= " - " . pdfVisible($rsempresa->getValue("direccion"), true);
		if (!sonIguales($rsempresa->getValue("telefono"), ""))
			$empresa .= " - Tel: " . $rsempresa->getValue("telefono");
		if (!sonIguales($rsempresa->getValue("email"), ""))
			$empresa .= " - " . $rsempresa->getValue("email");
		if (!esVacio($rsempresa->getValue("condicion_iva")))
			$empresa .= " - " . $rsempresa->getValue("condicion_iva");
		if (!esVacio($rsempresa->getValue("cuit")))
			$empresa .= " - CUIT: " . $rsempresa->getValue("cuit");
		if (!esVacio($rsempresa->getValue("fecha_alta")))
			$empresa .= " - F. Inicio Act.: " . $rsempresa->getValueFechaFormateada("fecha_alta");

		if (!sonIguales($rsempresa->getValue("web"), ""))
			$empresa .= " - " . $rsempresa->getValue("web");

		$tamFontTitulo = 12;
		if (!$this->apaisanada) {
			if ($xmostrarLogo) {
				//el logo ancho muestra todo y no lleva el titulo
				if ($logoAncho == 1) {
					$this->addImgJpg("./app/logo-ancho.jpg", 1.2, 25.2, 530, 80);
					$this->pdf->ezSetY($this->puntos_cm(25 + $diff));
					$this->addText($this->titulo, $this->defFontSizeText + 2, "left");
					$this->addHorizontalLine(1, false, 1);
				} else {
					$this->pdf->addJpegFromFile("./app/logo.jpg", $this->puntos_cm(2), $this->puntos_cm(26.5), $anchoLogo, $altoLogo);
					$txttit = "<b>" . $this->titulo . "</b>\n";
					$leftTitulo = 9.5;
					if (strlen($this->titulo) > 40) {
						$tamFontTitulo = 9;
						$leftTitulo = 7.5;
					}

					$this->pdf->addText($this->puntos_cm($leftTitulo), $this->puntos_cm(27.5), $tamFontTitulo, $txttit);
					$this->pdf->addText($this->puntos_cm(2), $this->puntos_cm(26.2 + $diff), 6, pdfVisible($empresa, true));
					$this->pdf->line($this->puntos_cm(2), $this->puntos_cm(26 + $diff), $this->puntos_cm(19.4), $this->puntos_cm(26 + $diff));
					$this->pdf->ezSetY($this->puntos_cm(25.5 + $diff));
				}
			}
		} else {
			if ($xmostrarLogo) {
				//el logo ancho muestra todo y no lleva el titulo
				if ($logoAncho == 1) {
					$this->addImgJpg("./app/logo-ancho-apaisado.jpg", 1.5, 17, 750, 80);
					$this->pdf->ezSetY($this->puntos_cm(17 + $diff));
					$this->addText($this->titulo, $this->defFontSizeText + 2, "left");
					$this->addHorizontalLine(1, false, 1);
				} else {
					$this->pdf->addJpegFromFile("./app/logo.jpg", $this->puntos_cm(1.8), $this->puntos_cm(18.5), $anchoLogo, $altoLogo);
					$txttit = "<b>" . $this->titulo . "</b>\n";
					$this->pdf->addText($this->puntos_cm(12), $this->puntos_cm(19.4), 12, $txttit);
					$this->pdf->addText($this->puntos_cm(1.9), $this->puntos_cm(18.2), 7, $empresa);
					$this->pdf->line($this->puntos_cm(1.9), $this->puntos_cm(18 + $diff), $this->puntos_cm(28.3), $this->puntos_cm(18 + $diff));
					$this->pdf->ezSetY($this->puntos_cm(17.5 + $diff));
				}
			}
		}
	}

	/**
	 * Agrega imagen en x,y
	 * @param string $ximg
	 * @param string $x
	 * @param string $y
	 * @param string $xancho
	 * @param string $xalto
	 */
	function addImgJpg($ximg, $x, $y, $xancho, $xalto)
	{
		$this->pdf->addJpegFromFile("./" . $ximg, $this->puntos_cm($x), $this->puntos_cm($y), $xancho, $xalto);
	}

	function addImgPng($ximg, $x, $y, $xancho, $xalto)
	{
		$this->pdf->addPngFromFile("./" . $ximg, $this->puntos_cm($x), $this->puntos_cm($y), $xancho, $xalto);
	}
	
	function addSpace($xlines = 1)
	{
		while ($xlines > 0) {
			$this->pdf->ezText("\n", $this->defFontSize);
			$xlines--;
		}
	}

	function addHorizontalLine($xspacesBefore = 1, $xpunteada = false, $xspacesAfter = 0)
	{
		$this->pdf->setLineStyle(0.8, '', '', array());
		if ($xpunteada)
			$this->pdf->setLineStyle(0.8, '', '', array(5, 3));

		$this->addSpace($xspacesBefore);
		if (!$this->apaisanada) {
			$this->pdf->line($this->puntos_cm(2), $this->pdf->y, $this->puntos_cm(19), $this->pdf->y);
		} else {
			$this->pdf->line($this->puntos_cm(1.5), $this->pdf->y, $this->puntos_cm(28), $this->pdf->y);
		}
		$this->addSpace($xspacesAfter);
		$this->pdf->setLineStyle(0.8, '', '', array());
	}

	function addArea($x, $y, $ancho, $alto, $xlineColor = "")
	{
		if (!is_array($xlineColor))
			$xlineColor = array(0.1, 0.1, 0.1);

		$this->getPdfObj()->setStrokeColor($xlineColor[0], $xlineColor[1], $xlineColor[2]);
		$this->getPdfObj()->rectangle($x, $y, $ancho, $alto);
	}

	/**
	 * Agrega un recuadro de toda la hoja
	 */
	function addAreaMargenes($xlineColor = "")
	{
		if (!is_array($xlineColor))
			$xlineColor = array(0.1, 0.1, 0.1);

		$this->getPdfObj()->setStrokeColor($xlineColor[0], $xlineColor[1], $xlineColor[2]);
		$this->addArea(56, 45, 502, 755);
	}

	/**
	 * Escribe footer. Si ya hay nro de hoja, está incluido
	 */
	function writeFooter()
	{
		if ($this->mMostrarNroHoja)
			return;

		global $SITIO;
		$cols = array();
		$cols["izq"] = array('justification' => 'left');
		$cols["der"] = array('justification' => 'right');

		$fecha = "";
		if ($this->mImprimeFecha)
			$fecha = "<b>Fecha:</b> " . date("d/m/Y") . " " . date("H:i:s");

		$data = array();
		$data[] = array("izq" => $SITIO, "der" => $fecha);
		$this->addTable($data, "", 0, $cols);
	}

	function setApaisanada()
	{
		$this->apaisanada = true;
		$this->gridWidth = "750";
	}

	function setImprimeFecha($ximprimeFecha)
	{
		$this->mImprimeFecha = $ximprimeFecha;
	}

	function getCsvFileName($xtitulo)
	{
		$arep = array(" ", "--", "(", ")", ":", "[", "]", ".", "/", "*", chr(164), chr(165));
		$tit = sinCaracteresEspeciales(str_replace($arep, "-", $xtitulo));
		$pattern = strtolower($tit);

		$pattern2 = $pattern . "-" . getCurrentUser() . "-" . date("His") . "-";
		return "./tmp/" . $pattern2 . $this->getFechaPrefix() . "-" . substr(md5($pattern2 . $this->getFechaPrefix()), 1, 5) . ".csv";
	}

	/**
	 * Hace un newPage() si queda poco espacio para grillas y/o texto
	 * Retorna si hico newpage()
	 * @param number $xLimiteYCambioHoja
	 */
	function newPageIfNeeded($xLimiteYCambioHoja = 0)
	{
		if ($xLimiteYCambioHoja == 0)
			$xLimiteYCambioHoja = getParameterInt("sc3-pdf-limiteY_hojanueva", 160);

		if ($this->getPdfObj()->ezGetY() < $xLimiteYCambioHoja)
		{
			$this->newPage();
			return true;
		}

		return false;
	}

	/**
	 * Avanza hasta la mitad de la hoja
	 */
	function avanzarAMitadDeHoja()
	{
		while (round($this->getPdfObj()->ezGetY()) > round($this->getPdfObj()->ez['pageHeight'] / 2)) {
			$this->addSpace();
		}
	}

	/**
	 * Crea una nueva página
	 */
	function newPage()
	{
		$this->pdf->ezNewPage();
	}

	/**
	 * Agrega la grilla como tabla del pdf
	 *
	 * @param HtmlGrid $xgrid
	 */
	function addGrid($xgrid, $xfontSize = 8, $xshaded = 1, $xrowGap = 2, $xcolGap = 5, $xOrientation = "center")
	{
		$verticalLines = getParameterInt("sc3-grid-verticalLines", 0);

		/*
		$options = array(
					'shadeCol' => array(0.9, 0.9, 0.9),
					'lineCol' => array(0.4, 0.5, 0.6),
					'shaded' => $xshaded,
					'xOrientation' => 'center',
					'cols' => $xgrid->getPdfColumnFormats(),
					//'fontSize' => max($this->defFontSize, $xfontSize),
					'fontSize' => $xfontSize,
					'innerLineThickness' => 0.4,
					'outerLineThickness' => 0.4,
					'showLines' => 1,
					'rowGap' => $xrowGap,
					'colGap' => $xcolGap,
					'verticalLines' => $verticalLines,
					'width' => $this->gridWidth,

					// Light Steel Blue	176-196-222	b0c4de
					'shadeHeadingCol'=> array(176/255, 196/255, 222/255)
				);
		*/

		// Light Steel Blue	176-196-222	b0c4de
		$r = getParameter("sc3-pdf-colorgrid-r", 176);
		$g = getParameter("sc3-pdf-colorgrid-g", 196);
		$b = getParameter("sc3-pdf-colorgrid-b", 222);

		//el centrado sirve si hay mas de 5 columnas
		$alignHeadings = "center";
		if (count($xgrid->getPdfColumnFormats()) <= 4)
			$alignHeadings = "";

		$options = array(
			'shadeCol' => array(0.9, 0.9, 0.9),
			'lineCol' => $this->mLineCol,
			'shaded' => $xshaded,
			'xOrientation' => $xOrientation,
			'cols' => $xgrid->getPdfColumnFormats(),
			'fontSize' => $xfontSize,
			'innerLineThickness' => 0.4,
			'outerLineThickness' => 0.4,
			'rowGap' => $xrowGap,
			'colGap' => $xcolGap,
			'width' => $this->gridWidth,
			'titleFontSize' => ($xfontSize + 2),

			//alineacion del header, independientemente de cada columna
			'alignHeadings' => $alignHeadings,
			//20 es un bitmask para mostrar las lineas de header y footer, ver tables.php
			'gridlines' => 20,

			'shadeHeadingCol' => array($r / 255, $g / 255, $b / 255)
		);

		if ($this->mEspacioAntesDeGrilla)
			$this->pdf->ezText("\n", $this->defFontSize);

		//control si existe lugar para otra grilla o arranca en hoja nueva
		$limiteYCambioHoja = getParameterInt("sc3-pdf-limiteY_hojanueva", 160);
		if ($this->pdf->ezGetY() < $limiteYCambioHoja && !$this->blanckPage)
			$this->newPage();

		//por ahora, agrega texto con saldo anterior
		if ($xgrid->hayAcumulador() && !sonIguales($xgrid->getSaldoAnteriorLabel(), "")) {
			$saldoAnterior = $xgrid->getSaldoAnteriorLabel();
			$valorf = (float) $xgrid->getSaldoAnterior();
			$saldoAnterior .= ": <b>" . formatFloat($valorf) . "</b>";
			$this->pdf->ezText($saldoAnterior, $xfontSize);
			$this->pdf->ezText("", $this->defFontSizeText);
		}

		$adata = $xgrid->getPdfData();
		$this->pdf->ezTable($adata, $xgrid->getPdfTitles(), pdfVisible($xgrid->getTitle(), true), $options);
		$this->addText($xgrid->getSumaryToPdf(), $this->defFontSize, "right");

		//guarda en CSV
		$titCsv = $xgrid->getTitle();
		if (esVacio($titCsv)) {
			$titCsv = "Datos";
		}
		$csvFile = $this->getCsvFileName($titCsv);
		$this->aCsvFiles[] = array('titulo' => $titCsv, "archivo" => $csvFile);
		sc3CsvSaveArray($csvFile, $xgrid->getColumnTitles(), $adata);
	}

	/**
	 * Agrega la tabla al pdf
	 */
	function addTable($xdata, $xtitulo, $xshowLines = 1, $xcols = "", $xfontSize = 8, $xrowGap = 2, $xshaded = 0, $xverticalLines = 0, $xOrientation = 'left')
	{
		$verticalLines = getParameterInt("sc3-grid-verticalLines", 0) + $xverticalLines;

		/*
		$options = array(
					'shadeCol' => array(0.9, 0.9, 0.9),
					'lineCol' => array(0.4, 0.5, 0.6),
					'shaded' => $xshaded,
					'xOrientation' => $xOrientation,
					'fontSize' => max($this->defFontSize, $xfontSize),
					'cols' => $xcols,
					'rowGap' => $xrowGap,
					'innerLineThickness' => 0.4,
					'outerLineThickness' => 0.4,
					'showHeadings' => 0,
					'showLines' => $xshowLines,
					'verticalLines' => $verticalLines,
					'width' => $this->gridWidth);
		*/

		//20 es un bitmask para mostrar las lineas de header y footer, ver tables.php
		/*
		$gridLines = 20;
		if ($xverticalLines)
			$gridLines = 27;
		*/


		/*
		 * 'xPos' indica el eje de la grilla. 'xOrientation' es como se distribuye la grilla respecto de ese eje
		 * EJ: xPos: left, xOrientation: right ==> la grilla se muestra desde la izquierda hacia la derecha
		 */

		//default para left
		$pos = "left";
		$orientation = "right";
		if (sonIguales($xOrientation, "right")) {
			$pos = "right";
			$orientation = "left";
		}

		$options = array(
			'shadeCol' => array(0.9, 0.9, 0.9),
			'lineCol' => $this->mLineCol,
			'shaded' => $xshaded,

			//xpos es el eje, la orientacion es sobre ese eje. EJ: xpos left y luego orientacion left hace que se muestre a la izquierda de la izquierda
			'xPos' => $pos,
			'xOrientation' => $orientation,

			//mínima cantidad de filas a mostrar con encabezado
			'protectRows' => 3,

			'cols' => $xcols,
			'fontSize' => $xfontSize,
			'innerLineThickness' => 0.4,
			'outerLineThickness' => 0.4,
			'rowGap' => $xrowGap,
			'width' => $this->gridWidth,

			'showBgCol' => 1,

			'showHeadings' => 0,
			'showLines' => $xshowLines,

			//alineacion del header, independientemente de cada columna
			'alignHeadings' => 'center',
			'gridlines' => 20,

			// Light Steel Blue	176-196-222	b0c4de
			'shadeHeadingCol' => array(176 / 255, 196 / 255, 222 / 255)
		);

		//analiza si hay espacio entre las tablas
		if ($this->mEspacioAntesDeGrilla)
			$this->pdf->ezText("\n", $this->defFontSize);

		//control si existe lugar para otra grilla o arranca en hoja nueva. Si es una hoja en blanco, es una FC o algo que administra
		//su propio espacio
		$limiteYCambioHoja = getParameterInt("sc3-pdf-limiteY_hojanueva", 160);
		if ($this->pdf->ezGetY() < $limiteYCambioHoja && !$this->blanckPage)
			$this->newPage();

		$this->pdf->setLineStyle(1);

		$this->pdf->ezTable($xdata, "", $xtitulo, $options);
	}

	/**
	 * Agrega un selector
	 * @param HtmlSelector $xsel
	 */
	function addSelector($xetiqueta, $xsel, $xextra = "")
	{
		$id = $xsel->getValue();
		$descripcion = $xsel->getDescripcion();
		if (sonIguales($descripcion, ""))
			$this->pdf->ezText("<b>$xetiqueta</b>: (todos)", 9);
		else
			$this->pdf->ezText("<b>$xetiqueta</b>: $descripcion $xextra", $this->defFontSizeText);
	}

	/**
	 * Agrega un combo al PDF, escapa acentos
	 * @param string $xetiqueta
	 * @param HtmlCombo $xcombo
	 */
	function addCombo($xetiqueta, $xcombo)
	{
		$etiqueta = pdfVisible($xetiqueta, true);
		$valor = pdfVisible($xcombo->getDescripcionSeleccionada(), true);

		$this->pdf->ezText("<b>$etiqueta</b>: " . $valor,  $this->defFontSizeText);
	}

	function addTextoConEtiqueta($xetiqueta, $xtexto)
	{
		$etiqueta = pdfVisible($xetiqueta, true);
		$texto = pdfVisible($xtexto, true);

		$this->pdf->ezText("<b>$etiqueta</b>: " . $texto, $this->defFontSizeText);
	}


	/**
	 * Agrega un texto plano o html, si no viene el tama�o toma el default (8)
	 * @param string $xtext
	 * @param int $xsize
	 * @param string $xalign
	 */
	function addText($xtext, $xsize = 0, $xalign = "left")
	{
		$quitar = array("<br>", "&nbsp;", "<span style=\"font-weight: bold;\">", "</span>", "<span style=\"font-weight: bold; text-decoration: underline;\">", "<div style=\"text-align: right;\">", "</div>", "<style type=\"text/css\">", "body ", "{", "background:", "#FFF;", "}", "</style>", "<span style=\"font-style: italic;\">");
		$poner = array("\n", " ", "<b>", "</b>", "", "", "\n", "", "", "", "", "", "", "", "", "");
		$texto = str_replace($quitar, $poner, $xtext);

		if ($xsize == 0)
			$xsize = $this->defFontSize;

		$options = array('justification' => $xalign);
		$this->pdf->ezText($texto, $xsize, $options);
	}

	function addTitulo($xtext, $xlinesBefore = 0)
	{
		if ($xlinesBefore > 0)
			$this->addText(str_repeat("\n", $xlinesBefore), $this->defFontSizeText, "center");
		$this->addText("<b>$xtext </b>\n", $this->defFontSizeText + 2, "center");
	}

	function addDates($xdate1, $xdate2)
	{
		$this->pdf->ezText("<b>Fechas</b>: " . $xdate1->toPdf() . " al " . $xdate2->toPdf(), $this->defFontSizeText);
	}

	function addDateRange($xdate)
	{
		$this->pdf->ezText("<b>Fechas</b>: " . $xdate->toPdf(), $this->defFontSizeText);
	}

	/**
	 * Agrega una fecha
	 * @param string $xtext
	 * @param HtmlDate $xdate1
	 */
	function addDate($xtext, $xdate1)
	{
		$this->pdf->ezText("<b>$xtext</b>: " . $xdate1->toPdf(), $this->defFontSizeText);
	}

	function setEtiquetas($xancho = 8.7, $xalto = 2.7)
	{
		$this->metiquetas = true;
		$size = array($xancho, $xalto);
		$this->pdf = new Cezpdf($size);

		$this->pdf->selectFont('./pdf/fonts/Courier.afm');
		$this->pdf->ezSetCmMargins(0.2, 0.2, 0.2, 0.2);

		global $SITIO;
		$datacreator = array(
			'Title' => "titulo",
			'Author' => $SITIO,
			'Subject' => "etiqueta",
			'Creator' => 'info@sc3.com.ar',
			'Producer' => 'http://www.sc3.com.ar/'
		);

		$this->pdf->addInfo($datacreator);
	}

	function addTextEtiqueta($xtext, $xsize = 9, $xline = 1)
	{
		$quitar = array("<br>", "&nbsp;", "<span style=\"font-weight: bold;\">", "</span>", "<span style=\"font-weight: bold; text-decoration: underline;\">", "<div style=\"text-align: right;\">", "</div>", "<style type=\"text/css\">", "body ", "{", "background:", "#FFF;", "}", "</style>", "<span style=\"font-style: italic;\">");
		$poner = array("\n", " ", "<b>", "</b>", "", "", "\n", "", "", "", "", "", "", "", "", "");
		$texto = str_replace($quitar, $poner, $xtext);

		if ($xline == 1)
			$y = 60;
		if ($xline == 2)
			$y = 50;
		if ($xline == 3)
			$y = 40;
		$this->pdf->addText(15, $y, $xsize, $texto, 0);
	}


	/**
	 * Guarda en el dir ./tmp/ para ser referenciado por el PDF
	 *
	 * @param string $xcode
	 * @return string
	 */
	function saveToTmpFile($xcode)
	{
		$width = 320;
		$height = 50;

		$fontsize = 32;
		$text = "*" . $xcode . "*";

		$img_handle = imagecreate($width, $height);
		$white = imagecolorallocate($img_handle, 255, 255, 255);
		$black = imagecolorallocate($img_handle, 0, 0, 0);

		imagettftext($img_handle, $fontsize, 0, 15, $height - 10, -$black, "FREE3OF9.TTF", $text);
		$filename = "./tmp/$xcode.jpg";
		imagejpeg($img_handle, $filename);
		return $filename;
	}

	function saveToTmpFile39($xcode, $xwidth = 300, $xheight = 70)
	{
		// barcode center
		$x        = 100;
		$x        = $xwidth / 2 - 10;
		// barcode center
		$y        = 30;
		// barcode height in 1D ; module size in 2D
		$height   = 55;
		// barcode height in 1D ; not use in 2D
		$width    = 1;
		// rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation
		$angle    = 0;

		$type     = 'code39';

		$im     = imagecreatetruecolor($xwidth, $xheight);
		$black  = ImageColorAllocate($im, 0x00, 0x00, 0x00);
		$white  = ImageColorAllocate($im, 0xff, 0xff, 0xff);
		imagefilledrectangle($im, 0, 0, $xwidth, $xheight, $white);

		$data = Barcode::gd($im, $black, $x, $y, $angle, $type, array('code' => $xcode));

		$filename = "./tmp/$xcode-39.gif";
		imagegif($im, $filename);
		return $filename;
	}

	function saveToTmpFileI2de5($xcode, $ximgWidth = 300)
	{
		$type     = 'code128';
		// barcode center
		$y        = 50;
		// barcode height in 1D ; module size in 2D
		$height   = 50;
		// barcode height in 1D ; not use in 2D
		$width    = 2;
		if (strlen($xcode) > 50)
			$width = 1;
			
		// rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation 
		$angle    = 0;
		// barcode center
		$x        = 250;

		$im     = imagecreatetruecolor(500, 100);
		$black  = ImageColorAllocate($im, 0x00, 0x00, 0x00);
		$white  = ImageColorAllocate($im, 0xff, 0xff, 0xff);
		imagefilledrectangle($im, 0, 0, 500, 100, $white);
		$data = Barcode::gd($im, $black, $x, $y, $angle, $type, array('code' => $xcode), $width, $height);

		$filename = "./tmp/$xcode-code128-$ximgWidth.gif";
		imagegif($im, $filename);
		return $filename;
	}

	function addCodeBarEtiqueta($xcodigo)
	{
		$filename = $this->saveToTmpFile($xcodigo);
		$this->pdf->addJpegFromFile($filename, 5, 20, 320, 35);
		$this->pdf->addText(50, 15, 7, $xcodigo, 0);
	}


	function addCodeBarEtiquetaI2de5($xcodigo, $x = 5, $y = 20, $w = 300, $h = 35)
	{
		$filename = $this->saveToTmpFileI2de5($xcodigo, $w);
		$this->pdf->addGifFromFile($filename, $x, $y, $w, $h);
		$this->pdf->addText($x + 45, $y - 5, 7, $xcodigo, 0);
	}

	function addCodeBarEtiqueta39($xcodigo, $x = 5, $y = 20, $w = 300, $h = 35, $xEtiquetaExtraLarge = false)
	{
		$sizeFont = 8;
		if (strlen($xcodigo) <= 10)
			$sizeFont = 15;

		$diffY = 8;
		if ($xEtiquetaExtraLarge) {
			$sizeFont = 25;
			$diffY = 16;
		}

		$filename = $this->saveToTmpFile39($xcodigo, $w - 20);
		$this->pdf->addGifFromFile($filename, $x, $y, $w, $h);
		$this->pdf->addText($x + 45, $y - $diffY, $sizeFont, $xcodigo, 0);
	}

	//TODO: agregar el dia ?
	function getFechaPrefix()
	{
		$hoy = getdate(time());
		$fecha = "";
		$fecha .= $hoy["year"] . "-" .  $hoy["mon"];
		return $fecha;
	}

	/*
	 * Genera archivo temporal y crea link
	*/
	function toLink($xkey = "", $xusekeyOnly = false, $xShowCsv = true)
	{
		if (esExcel())
			return "";

		$this->mKey = $xkey;

		if ((!$this->metiquetas) && (!$this->blanckPage))
			$this->writeFooter();

		$tit = str_replace('\$', 's', $this->titulo);
		$arep = array(" ", "--", "(", ")", ":", "[", "]", ".", "/", "*", ",", chr(164), chr(165), chr(36));
		$tit = sinCaracteresEspeciales(str_replace($arep, "-", $tit));
		if ($xusekeyOnly)
			$tit = $xkey;

		if (!sonIguales($xkey, ""))
			$xkey .= "-";

		if ($xusekeyOnly)
			$pattern = strtolower($tit) . "-" . date('H');
		else
			$pattern = strtolower($tit) . "-" . date('H') . $xkey;

		$pattern2 = $pattern . "-" .  getCurrentUser() . "-";
		$this->filename = "./tmp/" . $pattern2 . $this->getFechaPrefix() . "-" . substr(md5($pattern2 . date("hs")), 1, 5) . ".pdf";
		$handle = fopen($this->filename, "w+");
		fwrite($handle, $this->pdf->ezOutput());

		$titHtml = htmlVisible($this->titulo);
		$result = "<div class=\"w3-container tabla_resultado\"><b>" . $titHtml . "</b>";
		$result .= "<div class=\"w3-container td_resultado\">" . linkImgFa($this->filename, "fa-file-pdf-o", "Ver o imprimir", "fa-2x", "_blank", "a_resultado");

		//enviar email
		$url = new HtmlUrl("sc-emailpdf.php");
		$url->add("titulo", $this->titulo);
		$url->add("filename", $this->filename);
		$url->add("email", $this->emailDefault);
		$result .= "<a class=\"a_resultado\" href=\"javascript:openWindow('" . $url->toUrl() . "','browsepdf');\" title=\"Enviar este reporte por email\">";
		$result .= imgFa("fa-envelope-o", "fa-2x");
		$result .= " Email</a>";

		if ($this->mEnviarATodos) {
			$url = new HtmlUrl("app-ema-emailing.php");
			$url->add("titulo", $this->titulo);
			$url->add("cerrar", 1);
			$url->add("adjunto1", $this->filename);
			$result .= "<a class=\"a_resultado\" href=\"javascript:openWindow('" . $url->toUrl() . "','browsepdf');\" title=\"Enviar este reporte por email\">";
			$result .= imgFa("fa-envelope-open-o", "fa-2x");
			$result .= " Enviar a todos</a>";
		}

		//enviar whatsapp	
		$sec = new SecurityManager();
		if ($sec->tienePerfil('WhatsApp Cliente')) {
			$url = new HtmlUrl("sc-wsppdf.php");
			$url->add("titulo", $this->titulo);
			$url->add("celular", $this->celularDefault);
			$url->add("filename", $this->filename);
			$result .= "<a class=\"a_resultado\" href=\"javascript:openWindow('" . $url->toUrl() . "','browsepdf');\" title=\"Enviar este reporte por whatsapp\">";
			$result .= imgFa("fa-whatsapp", "fa-2x");
			$result .= " WhatsApp</a>";
		}

		if ($xShowCsv) {
			foreach ($this->aCsvFiles as $i => $csvFile) {
				$f1 = $csvFile['archivo'];
				$csv = $csvFile['titulo'];
				$url = new HtmlUrl("sc-vercsv.php");
				$url->add("f1", $f1);
				$url->add("csv", $csv);

				$result .= linkImgFa($url->toUrl(), "fa-file-excel-o", $csvFile['titulo'], "fa-lg", "csv1", "a_resultado td_resultado_csv");
			}
		}

		$result .= "</div></div>";

		//apila archivo para ser borrado
		$stack = getStack();
		$stack->addFileToDelete($this->filename);
		saveStack($stack);

		return $result;
	}

	/**
	 * Enviar el PDF directo al output
	 */
	function toBrowser()
	{
		$this->pdf->ezStream();
	}

	/**
	 * Copia el archivo de /tmp a /ufiles/YYYYM/ y retorna el nombre final
	 *
	 * @return nombreDeArchivo
	 */
	function saveDoc($xpath = "")
	{
		$docName = basename($this->filename);
		$path =  getImagesPath();
		//agrupa los directorios por trimestre
		if (getParameterInt("sc3-images-subdir", "1")) {
			$path .= Sc3FechaUtils::formatFechaPath();

			// Crea carpeta si no existe
			$e = error_reporting(0);
			mkdir($path);
			$e = error_reporting($e);

			$docName = getImagesPath() . Sc3FechaUtils::formatFechaPath() . "/" . $docName;
		}

		copy($this->filename, $xpath . $docName);
		$docName = str_replace(getImagesPath(), "", $docName);
		return $docName;
	}


	function saveAsCsv($xindex = 0, $xprefix = "")
	{
		$csvFile = $this->aCsvFiles[$xindex];

		$xlsName = "csv" . $this->mKey . "-" . date('Hi') . basename($csvFile['archivo']);
		if (!esVacio($xprefix))
			$xlsName = $xprefix . date('Hi') . "-" . basename($csvFile['archivo']);

		$path =  getImagesPath();
		//agrupa los directorios por trimestre
		if (getParameterInt("sc3-images-subdir", "1")) {
			$path .= Sc3FechaUtils::formatFechaPath();

			// Crea carpeta si no existe
			$e = error_reporting(0);
			mkdir($path);
			$e = error_reporting($e);

			$docName = getImagesPath() . Sc3FechaUtils::formatFechaPath() . "/" . $xlsName;
		}

		copy($csvFile['archivo'], $docName);
		$docName = str_replace(getImagesPath(), "", $docName);
		return $docName;
	}
}
