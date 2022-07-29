<?php

/**
 * Grilla de un RS
 * Author: Marcos C (SC3)
 **/

class HtmlGrid
{
	/**
	 * @var DBObject
	 */
	var $mrs;
	var $mId = "";
	var $mcols;
	var $mtitle;
	var $mcolsStyles = [];
	var $mRowCallbackFunction = "";
	var $mwidth = "";
	var $mcolorNegativo = "red";
	var $mdecimals = 2;
	var $mcantCols = 0;
	var $mcantRows = 0;
	var $msorteable = true;
	var $mautoSort = -1;
	var $mTablaSmall = false;
	var $mClass = "data_table";

	var $mFilters = [];
	var $mtotalizar = [];
	var $mtotales = [];
	var $mtotalesName = [];
	var $mmostrarCantidad = false;

	//Manejo de grupos
	var $magrupadores = -1;
	var $sumary = [];
	var $MAX_CANT = 10;
	var $aCantidades = [];
	var $aNombresGrupos = [];
	var $mClaseGrupo = "";
	var $aNivelesClases = [];

	var $mmostrarHora = FALSE;
	var $mmostrarFecha = TRUE;

	var $macumulador = [];
	var $macumuladorSigno = "";
	var $msaldoAnteriorLabel = "";
	var $msaldoAnterior = 0.0;
	var $mColContador = "";
	var $mostrarDivSum = true;
	var $mostrarFooter = true;
	var $mBrFinal = true;

	//lista de operaciones de cada elemento de la grilla
	var $moperacionVer = [];
	var $moperaciones = [];
	var $mDobleClickEvent = "";
	var $mRowClickEvent = "";

	//multiple check
	var $mCheckName = '';
	var $mCheckField = '';
	var $mCheckValue = '';
	var $mCheckDoble = false;
	var $mCheckFuncion = '';
	var $mAllChecked = false;

	//campos editables
	var $mEditables = [];

	//PDF
	var $showPdf = true;
	var $pdfData = [];
	var $pdfTitles = [];
	var $columnTitles = [];
	var $pdfColumnFormats = [];

	//Info para graficos
	var $mGraphicLabels = [];
	var $mGraphicDatasets = [];
	//limita la cantidad de elementos del gráfico
	var $mGraphicLimit = 25;

	function __construct($xrs)
	{
		$this->mrs = $xrs;
		$this->setWithAll();
		$this->msaldoAnterior = 0.0;
		//completa las cantiades por nivel en cero
		$this->aCantidades = array_fill(0, $this->MAX_CANT, 0);
		$this->aNombresGrupos = array_fill(0, $this->MAX_CANT, "");
	}

	function getPdfColumnFormats()
	{
		return $this->pdfColumnFormats;
	}

	function getPdfData()
	{
		return $this->pdfData;
	}

	/**
	 * Informacion para armar gráfico
	 * @param $xColLabels array
	 * @param $xaDataSeries array
	 */
	function prepareGraphicInfo($xaColLabels, $xaDataSeries)
	{
		foreach ($xaColLabels as $i => $data) {
			$this->mGraphicLabels[$data] = [];
		}

		foreach ($xaDataSeries as $i => $data) {
			$this->mGraphicDatasets[$data] = [];
		}
	}

	function getGraphicLabels()
	{
		return $this->mGraphicLabels;
	}

	function getGraphicDatasets()
	{
		return $this->mGraphicDatasets;
	}

	/**
	 * Va espacio al final ?
	 * @param boolean $xbr
	 */
	function setEspacioFinal($xbr)
	{
		$this->mBrFinal = $xbr;
	}

	function setId($xid)
	{
		$this->mId = $xid;
	}

	function setDobleClickEvent($xevent)
	{
		$this->mDobleClickEvent = $xevent;
	}

	function setRowClickEvent($xevent)
	{
		$this->mRowClickEvent = $xevent;
	}

	function setClass($xclass)
	{
		$this->mClass = $xclass;
	}

	function getPdfTitles()
	{
		return $this->pdfTitles;
	}

	function getColumnTitles()
	{
		return $this->columnTitles;
	}

	function getCantRows()
	{
		return $this->mcantRows;
	}

	function setAutoSortColumn($xindex)
	{
		$this->mautoSort = $xindex;
	}

	function getSaldoAnteriorLabel()
	{
		return $this->msaldoAnteriorLabel;
	}

	function setSaldoAnteriorLabel($xlabel)
	{
		$this->msaldoAnteriorLabel = $xlabel;
	}

	function getTitle()
	{
		return $this->mtitle;
	}

	function setTablaSmall()
	{
		$this->mTablaSmall = true;
	}

	function getSaldoAnterior()
	{
		return $this->msaldoAnterior;
	}

	function setTitle($xtitle)
	{
		$this->mtitle = $xtitle;
	}

	function setDecimals($xdecimals)
	{
		$this->mdecimals = $xdecimals;
	}

	function setMostrarHora($xmostrarHora)
	{
		$this->mmostrarHora = $xmostrarHora;
	}

	function setMostrarFecha($xmostrarFecha)
	{
		$this->mmostrarFecha = $xmostrarFecha;
	}

	function setFilters($xaFilters)
	{
		$this->mFilters = $xaFilters;
	}

	function setTotalizar($xtotales)
	{
		$this->mtotalizar = $xtotales;
	}

	/**
	 * Determina si muestra la cantidad de elementos al final de la grilla
	 */
	function setMotrarCantidad($xMostrar = true)
	{
		$this->mmostrarCantidad = $xMostrar;
	}

	/**
	 * Una columna inicial con el nro de item
	 */
	function setColumnaContador($xColumnName = "#")
	{
		$this->mColContador = $xColumnName;
	}

	function hayColumnaContador()
	{
		return !esVacio($this->mColContador);
	}

	/**
	 * Permite agrupar, 0=una columna, 1=dos columnas
	 * @param int $xagrupador
	 */
	function setAgrupadores($xagrupador)
	{
		$this->magrupadores = $xagrupador;
		$this->mClaseGrupo = "grid_grupo_abierto";
	}

	/**
	 * Campos para editar
	 */
	function setEditables($xEditables)
	{
		$this->mEditables = $xEditables;
	}

	/**
	 * Retorna el agrupador
	 */
	function getAgrupadores()
	{
		return $this->magrupadores;
	}

	function hayAgrupadores()
	{
		return $this->magrupadores >= 0;
	}

	function setMostrarDivSum($xshow)
	{
		$this->mostrarDivSum = $xshow;
	}

	function setMostrarFooter($xshow)
	{
		$this->mostrarFooter = $xshow;
	}

	function hayOperacion()
	{
		if (count($this->moperaciones) > 0)
			return true;
		if (!isset($this->moperacionVer['query']))
			return false;
		return (!sonIguales($this->moperacionVer['query'], ""));
	}

	/*
	 Agrega un sumary (va al pie de la grilla)
	*/
	function addSumary($xtitulo, $xvalor)
	{
		$this->sumary[count($this->sumary)] = array($xtitulo, $xvalor);
	}

	/*
	 Agrega un sumary (va al pie de la grilla)
	*/
	function addSumaryBold($xtitulo, $xvalor)
	{
		$this->addSumary("<b>" . $xtitulo . "</b>", $xvalor);
	}

	function setWithM()
	{
		$this->mwidth = "680px";
	}

	function setWithS()
	{
		$this->mwidth = "500px";
	}

	function setWithL()
	{
		$this->mwidth = "900px";
	}

	function setWithAll()
	{
		$this->mwidth = "99.5%";
	}

	function setSizeF()
	{
		$this->mwidth = "";
	}

	function setColumns($xcols)
	{
		$this->mcols = $xcols;
	}

	function setOperacionIcon($xicon)
	{
		$this->moperaciones[0]['icon'] = $xicon;
	}

	function setOperacionUrl($xurl)
	{
		$this->moperaciones[0]['url'] = new HtmlUrl($xurl);
	}

	function setOperacionLabel($xlabel)
	{
		$this->moperaciones[0]['label'] = $xlabel;
	}

	function setOperacionTarget($xtarget)
	{
		$this->moperaciones[0]['target'] = $xtarget;
	}


	/**
	 * Agrega una operacion a la lista
	 * @param string $xlabel
	 * @param string $xicon
	 * @param string $xurl
	 */
	function addOperacion($xlabel, $xicon, $xurl, $xtarget = "", $xiconSize = "")
	{
		$op = [];
		$op['label'] = $xlabel;
		$op['icon'] = $xicon;
		$op['url'] = new HtmlUrl($xurl);
		$op['target'] = $xtarget;
		if (!esVacio($xiconSize))
			$op["icon-size"] = $xiconSize;
		$this->moperaciones[] = $op;
	}

	function setOperacionVer($xquery, $xfield = "id", $xtarget = "")
	{
		$this->moperacionVer['query'] = $xquery;
		$this->moperacionVer['id'] = $xfield;
		$this->moperacionVer['target'] = $xtarget;
	}

	/**
	 * @param string $xcheckName
	 * @param int $xfieldValue
	 * @param string $xvalueChecked
	 * @param boolean $xdoble
	 */
	function setCheckInfo($xcheckName, $xfieldValue, $xvalueChecked = "", $xdoble = false, $xfn = "", $xAllCheqked = false)
	{
		$this->mCheckName = $xcheckName;
		$this->mCheckField = $xfieldValue;
		$this->mCheckValue = $xvalueChecked;
		$this->mCheckDoble = $xdoble;
		$this->mCheckFuncion = $xfn;
		$this->mAllChecked = $xAllCheqked;
	}

	function getTotales($i)
	{
		return round($this->mtotales[$i], 2);
	}

	function getTotalesName($xfield)
	{
		return $this->mtotalesName[$xfield];
	}

	/**
	 * Acumula saldo, primer columna como debito y otra como credito
	 * 
	 */
	function setAcumulador($xcolumna, $xc1, $xc2, $xsaldoInicial = 0.00)
	{
		$this->macumulador["columna"] = $xcolumna;
		$this->macumulador["c1"] = $xc1;
		$this->macumulador["c2"] = $xc2;
		$this->macumulador["valor"] = 0;
		$this->msaldoAnterior = $xsaldoInicial;
		if ($xsaldoInicial != 0)
			$this->setSaldoAnteriorLabel("Saldo anterior");
	}

	function setAcumuladorSigno($xsigno)
	{
		$this->macumuladorSigno = $xsigno;
	}

	/**
	 * Dado el RS con movimientos ordenados por fecha, calcula el saldo anterior, dejando los ultimos
	 * movs para ser mostrados
	 * @param BDObject $xrs
	 * @param string $xlabel
	 * @param int $xcantMovs
	 */
	function calcularSaldoAnterior($xrs, $xlabel, $xcantMovs)
	{
		$this->msaldoAnteriorLabel = $xlabel;

		$i = 0;
		while ($i < $xrs->cant() - $xcantMovs) {
			$this->msaldoAnterior += splitValorConMoneda($this->mrs->getValue($this->macumulador["c1"]));
			$this->msaldoAnterior -= splitValorConMoneda($this->mrs->getValue($this->macumulador["c2"]));

			$xrs->Next();
			$i++;
		}
	}

	/**
	 * Calcula el saldo anterior hasta una fecha dada
	 * @param BDObject $xrs
	 * @param string $xlabel
	 * @param string $xCampoFecha
	 * @param string $xFecha
	 */
	function calcularSaldoAnteriorFecha($xrs, $xlabel, $xCampoFecha, $xFecha)
	{
		$this->msaldoAnteriorLabel = $xlabel;

		$i = 0;
		while ($xFecha > $xrs->getValueFechaParaComparar($xCampoFecha) && !$xrs->EOF()) {
			$this->msaldoAnterior += splitValorConMoneda($this->mrs->getValue($this->macumulador["c1"]));
			$this->msaldoAnterior -= splitValorConMoneda($this->mrs->getValue($this->macumulador["c2"]));

			$xrs->Next();
			$i++;
		}

		return round($this->msaldoAnterior, 2);
	}

	/**
	 * Avanza el RS para que solo queden los ultimos datos
	 **/
	function showLastRows($xrs, $xrowCount)
	{
		$i = 0;
		while ($i < $xrs->cant() - $xrowCount) {
			$xrs->Next();
			$i++;
		}
	}


	function hayAcumulador()
	{
		if (!isset($this->macumulador["columna"]))
			return false;
		return !sonIguales($this->macumulador["columna"], "");
	}

	/**
	 * arma una division de grupo
	 **/
	function buildGrupo($xnombre, $xnivel, $xcols)
	{
		//TODO: que ocupe toda la fila en el PDF
		$rowPdf = array_fill(0, count($this->pdfTitles), "");

		//iniciamos en cero la cantidad de este nivel
		$this->aCantidades[$xnivel] = 0;
		$this->aNombresGrupos[$xnivel] = $xnombre;
		$this->aNivelesClases[$xnivel] = "niv" . $xnivel . rand(1000, 10000);
		$icono = "icono_" . $this->aNivelesClases[$xnivel];
		$j = 0;
		$clase = "";
		while ($j < $xnivel) {
			$clase .= $this->aNivelesClases[$j] . " ";
			$j++;
		}

		if ($xnivel == 0) {
			$res = "\n<tr class=\"" . $this->mClaseGrupo . "\" onclick=\"abrirCerrarGrupo(event,'" . $this->aNivelesClases[$xnivel] . "', '" . $icono . "')\">";
		} else {
			$res = "\n<tr class=\"" . $this->mClaseGrupo . " " . $clase . "\" onclick=\"abrirCerrarGrupo(event,'" . $this->aNivelesClases[$xnivel] . "','" . $icono . "')\">";
		}

		$i = 0;
		while ($i < $xnivel) {
			$res .= "<td style=\"width: 60px\">";
			$res .= espacio();
			$res .= "</td>";
			$i++;
		}

		$res .= "<td align=\"left\" colspan=\"" . ($xcols - $xnivel) . "\" class=\"grid_grupo$xnivel\">";
		$res .= $xnombre;
		//TODO: iconos de flechas comentados hasta nuevo aviso. Al expandir y contraer
		//      quedan desfasados los niveles interiores
		if ($xnivel == 0)
			$res .= " <i id=\"" . $icono . "\" class=\"fa fa-angle-double-up fa-lg\"></i>";
		$res .= "</td>";

		//TODO: ocupar toda la fila en el PDF
		//corta en 25 para que no se agrande demasiado
		$grupoPDF = wordwrap($xnombre, 25, "\r\n ", false);
		$rowPdf[$i] = "<b>" . $grupoPDF . "</b>";
		$i++;
		$res .= "</tr>";

		//arma el array con los nombres de columnas y el valor del grupo
		$this->pdfData[] = array_combine(array_keys($this->pdfTitles), $rowPdf);

		return $res;
	}

	function cerrarGrupo($xtotales, $xnivel, $xcols, &$xacum)
	{
		debug(" HtmlGrid::cerrarGrupo($xnivel)");

		if (count($this->mtotalizar) == 0)
			return "";

		$rowPdf = array_fill(0, count($this->pdfTitles), "");
		$i = 0;
		$cantNivel = $this->aCantidades[$xnivel];
		$etiquetaNivel = "Cantidad";
		//arma la equiqueta acumulada entre el nivel padre y el actual
		for ($j = 0; $j <= $xnivel; $j++)
			$etiquetaNivel .= " " . $this->aNombresGrupos[$j];

		$j = 0;
		$clase = "";
		while ($j < $xnivel) {
			$clase .= $this->aNivelesClases[$j] . " ";
			$j++;
		}
		$res = "\n<tr class=\"" . $clase . "\">";

		while ($i < $xcols) {
			if ($i < $xnivel) {
				$res .= "<td>" . espacio() . "</td>";
				$rowPdf[$i] = "";
			} else {
				if ($i == $xnivel) {
					$muestraNivel = "#$cantNivel";
					if ($cantNivel < 5)
						$muestraNivel = "";
					$res .= "<td class=\"grid_grupo_cantidad\" title=\"$etiquetaNivel: $cantNivel\">$muestraNivel"  . "</td>";
					//TODO: determinar si se muestra u ocultan
					if ($cantNivel > 20)
						$rowPdf[$i] = "<i>#$cantNivel</i>";
					else
						$rowPdf[$i] = "";
				} else {
					$res .= "<td class=\"grid_grupo_total\"";
					$res .= " onmousedown=\"sumStart(event, this);\" ";
					$res .= ">";

					if ((float) $xtotales[$xnivel][$i] != 0.0) {
						$res .= formatFloatRed($xtotales[$xnivel][$i]);
						$rowPdf[$i] = "<b>" . formatFloat($xtotales[$xnivel][$i], 2, 0, 1) . "</b>";
					} else {
						if ($i < $xcols - 1)
							$res .= espacio();
						else {
							if ($this->hayAcumulador()) {
								$res .= formatFloat(round($this->macumulador["valor"], 2));
							} else
								$res .= espacio();
						}
					}
					$res .= "</td>";
				}
			}
			$i++;
		}
		$res .= "</tr>";

		//TODO: ver si no vuela, porque corta saldo en cambio de grupo ! SIRVE EN LISTADOS DE SALDOS, NECESITAN UN QUIEBRE
		$xacum = 0;

		//arma el array con los nombres de columnas y el valor del grupo
		//DA WARNING DESDE PHP 7.3 por la cantidad de elementos
		//El warning da cuando hay una columna PDF y se saltea en el PDF
		$this->pdfData[] = array_combine(array_keys($this->pdfTitles), $rowPdf);

		return $res;
	}

	function setSorteable($xsorteable = true)
	{
		$this->msorteable = $xsorteable;
	}

	function setColsStyle($xcols)
	{
		$this->mcolsStyles = $xcols;
	}

	function setRowCallbackFunction($xfn)
	{
		$this->mRowCallbackFunction = $xfn;
	}

	/**
	 * Retorna si se puede ordenar, no solo por su propiedad msorteable
	 * sino que tampoco ordena grillas con grupos o saldo anterior
	 */
	function isSorteable()
	{
		if (!$this->msorteable)
			return false;

		if ($this->hayAgrupadores())
			return false;

		if (!sonIguales($this->msaldoAnteriorLabel, ""))
			return false;

		return true;
	}

	function isFiltrable()
	{
		if (!$this->msorteable)
			return false;

		if ($this->hayAgrupadores())
			return false;

		if ($this->mcantRows <= 30)
			return false;

		//mas de 500 se pone lento la muestra de la grilla			
		if ($this->mcantRows > 500)
			return false;

		return true;
	}

	/**
	 * Retorna el ordenador segun el tipo de dato
	 *
	 * @param string $xtipoCampo
	 * @return class_string
	 */
	function getDataSorter($xtipoCampo)
	{
		$result = " table-sortable:";
		if (esCampoInt($xtipoCampo))
			$result .= "numeric";
		else
			if (esCampoFloat($xtipoCampo))
			/*currency"; TODO: no ordena negativos ! */
			$result = "";
		else
				if (esCampoFecha($xtipoCampo))
			$result .= "date";
		else
			$result .= "ignorecase";

		return $result;
	}

	/**
	 * Retorna el ancho recomendado según el tipo de dato
	 * @param string $xtipoCampo
	 * @return string
	 */
	function getDataWidth($xtipoCampo)
	{
		$anchoFloats = getParameter("sc3-grid-ancho-float", "110");
		if (esCampoFloat($xtipoCampo))
			return $anchoFloats;

		return "";
	}


	function getDataFilter($xtipoCampo)
	{
		$result = " ";
		if (esCampoInt($xtipoCampo))
			$result .= "table-filterable";
		else
			if (esCampoFloat($xtipoCampo))
			$result = ""; /* TODO: ver filtros de float o tratarlos como strings */
		else
			if (esCampoFecha($xtipoCampo))
			$result = ""; /* TODO: ver filtros de fecha o strings */
		else
			$result .= "table-filterable";

		return "";
	}


	/**
	 * Arma los encabezados de las columnas de la grilla
	 *
	 */
	function buildColumnHeaders()
	{
		$res = "<tr>";

		//una columna inicial con el nro de item
		if ($this->hayColumnaContador()) {
			$res .= "<th class=\"grid_header table-sortable:ignorecase table-sortable table-sorted-asc\"";
			$res .= " align=\"center\">";
			$res .= ucfirst($this->mColContador);
			$res .= "</th>";

			$this->pdfTitles[$this->mColContador] = "<b>" .  ucfirst($this->mColContador) . "</b>";
			$this->columnTitles[$this->mColContador] = ucfirst($this->mColContador);
		}

		$i = 0;
		while ($i < $this->mcantCols) {
			$field = $this->mrs->getFieldName($i);
			if ($this->isVisibleColumn($field)) {
				$tipoCampo = $this->mrs->getFieldType($i);

				$res .= "<th class=\"grid_header";
				$res .= $this->getDataSorter($tipoCampo);
				$res .= $this->getDataFilter($tipoCampo);
				$res .= "\"";

				//viene el ancho en los estilos o lo decide por el tipo de dato
				if (isset($this->mcolsStyles[$i]["width"])) {
					//pasa el ancho al pdf MALA IDEA; es otra escala
					//$this->pdfColumnFormats[$field]["width"] = $this->mcolsStyles[$i]["width"];
					$res .= " width=\"" . $this->mcolsStyles[$i]["width"] . "\"";
				} else
					$res .= " width=\"" . $this->getDataWidth($tipoCampo) . "\"";

				$res .= " align=\"center\">";

				$this->pdfTitles[$field] = "<b>" .  ucfirst(str_replace("_", "\n", str_replace("__", " ", pdfVisible($field, true)))) . "</b>";
				$this->columnTitles[$field] = ucfirst(str_replace("_", " ", $field));
				if (isset($this->mcols[$i]) && !esVacio($this->mcols[$i]))
					$field = $this->mcols[$i];

				$field = str_replace("_", "<br>", str_replace("__", " ", $field));
				$res .= ucfirst($field);
				$res .= "</th>";
			}
			$i++;
		}
		if ($this->hayAcumulador()) {
			$res .= "<th class=\"grid_header";
			$res .= $this->getDataSorter($tipoCampo);
			$res .= $this->getDataFilter($tipoCampo);
			$res .= "\" align=\"center\">";
			$res .= str_replace("_", "<br>", str_replace("__", " ", $this->macumulador["columna"]));
			$this->pdfTitles[$this->macumulador["columna"]] = "<b>" .  str_replace("_", "\n", str_replace("__", " ", $this->macumulador["columna"])) . "</b>";
			$this->columnTitles[$this->macumulador["columna"]] = str_replace("_", " ", str_replace("__", " ", $this->macumulador["columna"]));
			$res .= "</th>";
		}
		if ($this->hayOperacion()) {
			$res .= "<th class=\"\" align=\"center\">";
			$res .= espacio();
			$res .= "</th>";
		}
		$res .= "</tr>";


		if (count($this->mFilters) > 0)
			$res .= "<tr><th class=\"\">" . implode("</th><th class=\"\">", $this->mFilters) . "</th></tr>";

		return $res;
	}


	/**
	 * Arma los encabezados para los filtros
	 *
	 */
	function buildFilters()
	{
		if (esExcel())
			return "";

		$res = "<tr>";
		if ($this->hayColumnaContador()) {
			$res .= "<th class=\"grid_filter\" align=\"center\">";
			$res .= "<input name=\"filter\" size=\"3\" onkeyup=\"Table.filter(this,this)\" title=\"Ingrese un valor\">";
			$res .= "</th>";
		}
		$i = 0;

		while ($i < $this->mcantCols) {
			$res .= "<th class=\"grid_filter\" align=\"center\">";
			$res .= "<input name=\"filter\" size=\"3\" onkeyup=\"Table.filter(this,this)\" title=\"Ingrese un valor a filtrar\">";
			$res .= "</th>";
			$i++;
		}
		if ($this->hayAcumulador()) {
			$res .= "<th class=\"grid_filter\" align=\"center\">";
			$res .= "<input name=\"filter\" size=\"3\" onkeyup=\"Table.filter(this,this)\" title=\"Ingrese un valor a filtrar\">";
			$res .= "</th>";
		}
		if ($this->hayOperacion()) {
			$res .= "<th class=\"grid_filter\" align=\"center\">";
			$res .= espacio();
			$res .= "</th>";
		}
		$res .= "</tr>";

		return $res;
	}

	/**
	 * Cuenta la cantidad de columnas que no deben mostrarse del recordset
	 *
	 * @param BDObject $xrs
	 * @return int
	 */
	function countNoVisibleColumns($xrs)
	{
		$i = 0;
		$res = 0;
		while ($i < $xrs->cantF()) {
			$fieldName = $xrs->getFieldName($i);
			if (!$this->isVisibleColumn($fieldName))
				$res++;
			$i++;
		}
		return $res;
	}

	/**
	 * Si comienza con __ no es visible
	 *
	 * @param String $xcolumnName
	 * @return Boolean
	 */
	function isVisibleColumn($xcolumnName)
	{
		return !startsWith($xcolumnName, "__");
	}

	/*
	 * Analiza si no coinciden en el nivel dado o en el superior para producir un corte
	*/
	function hayCorteDeGrupo($xgrupos, $xrs, $xnivel)
	{
		return !sonIguales($xgrupos[$xnivel], $xrs->getValue($xnivel)) || (($xnivel > 0) && !sonIguales($xgrupos[$xnivel - 1], $xrs->getValue($xnivel - 1)));
	}


	function esUrlJavascript($xurl)
	{
		return startsWith($xurl, "javascript:");
	}


	/**
	 * Retorna el último valor del acumulador (saldo)
	 * @return number
	 */
	function getAcumulador()
	{
		if (!$this->hayAcumulador())
			return -1;

		//casos en que el saldo anterior es <> 0 y no hay elementos en la grilla
		if ($this->macumulador["valor"] == 0 && $this->mcantRows == 0)
			return round($this->msaldoAnterior, 2);

		return round($this->macumulador["valor"], 2);
	}



	/**
	 * Muestra la grilla. El parametro indica si muestra el sumario (defecto true) 
	 * @param boolean $xshowSumary 
	 * @return string
	 */
	function toHtml($xshowSumary = true)
	{
		$anchoMax = getParameterInt("sc3-grid-trunca-string", 80);

		$this->mcantRows = $this->mrs->cant();
		$this->mtotales = array_fill(0, 20, 0.0);
		$this->mtotalesName = array_fill(0, 20, 0.0);

		$nivel = 0;
		while ($nivel < 5) {
			$totalesGrupo[$nivel] = array_fill(0, 20, 0.0);
			$nivel++;
		}

		$res = "";
		$res = "<div class=\"w3-responsive\"> ";

		$classCebra = "w3-striped ";
		$classHover = "w3-hoverable ";

		$res .= "<table align=\"center\" id=\"" . $this->mId . "\" class=\"sc3-grilla-datos $classCebra $classHover" . $this->mClass;
		if ($this->isSorteable()) {
			$res .= " table-autosort";
			if ($this->mautoSort >= 0)
				$res .= ":" . $this->mautoSort;
			$res .= " sort02";
		}
		if ($this->isFiltrable())
			$res .= " table-autofilter";

		if ($this->mTablaSmall)
			$res .= " table-small ";

		$res .= "\" style=\"width: " . $this->mwidth . "\">";

		$this->mcantCols = $this->mrs->cantF();
		$cols = $this->mcantCols;
		if ($this->hayAcumulador())
			$cols++;
		if ($this->hayOperacion())
			$cols++;
		if ($this->hayColumnaContador())
			$cols++;
		$cols -= $this->countNoVisibleColumns($this->mrs);

		//header
		$res .= "\n<thead>";

		if (!esVacio($this->mtitle)) {
			$res .= "<tr>";
			$res .= "<th align=\"center\" colspan=\"" . $cols . "\" class=\"grid_title\">";
			$res .= $this->mtitle;
			$res .= "</th>";
			$res .= "</tr>";
		}

		$res .= "<tr>";

		//arma la primera linea con el saldo anterior
		if ($this->hayAcumulador() && !sonIguales($this->msaldoAnteriorLabel, "")) {
			$res .= "\n<tr>";
			$res .= "<td align=\"right\" colspan=\"" . ($cols - 1) . "\">";
			$res .= $this->msaldoAnteriorLabel;
			$res .= "</td>";
			$res .= "<td align=\"right\"\" class=\"td_monto\">";
			$valorf = (float) $this->msaldoAnterior;
			$res .= formatFloatRed($valorf);
			$res .= "</td>";
			$res .= "</tr>";
		}

		//arma las cabeceras
		$res .= $this->buildColumnHeaders();

		if ($this->isFiltrable()) {
			$res .= $this->buildFilters();
		}

		$res .= "</thead>";
		$res .= "<tbody>";

		if ($this->mrs->EOF()) {
			$res .= "\n<tr>";
			$res .= "<td align=\"left\" colspan=\"" . $cols . "\">";
			$res .= "<i>(sin datos)</i>";
			$res .= "</td>";
			$res .= "</tr>";
		}

		//contenido
		$acum = $this->msaldoAnterior;
		$nivel = -1;
		if ($this->hayAgrupadores())
			$nivel = 0;
		$grupos = array("", "", "", "");

		$this->mcantRows = 0;
		while (!$this->mrs->EOF()) {
			$i = 0;
			$rowPdf = $this->mrs->getRow();
			$row = $this->mrs->getRow();

			//busca armar un nivel
			if ($this->hayAgrupadores()) {
				debug(" HtmlGrid:toHtml(): grupos de nivel $nivel, getAgrupadores(): " . $this->getAgrupadores() . ",comparando (" . $grupos[$nivel] . ":" . $this->mrs->getValue($nivel) . ")");

				if ($this->hayCorteDeGrupo($grupos, $this->mrs, $nivel)) {
					debug(" HtmlGrid:toHtml(): NO Son iguales, nivel $nivel");

					//avanza todos los niveles que pueda
					if ($nivel != $this->getAgrupadores()) {
						debug(" HtmlGrid:toHtml(): avanzando, nivel=$nivel (" . $grupos[$nivel] . ", " . $this->mrs->getValue($nivel) . ")");

						while ($nivel <= $this->getAgrupadores()) {
							$grupos[$nivel] = $this->mrs->getValue($nivel);
							$grupos[$nivel + 1] = "";
							$res .= $this->buildGrupo($grupos[$nivel], $nivel, $cols);
							$nivel++;
						}
						$nivel--;
					} else {
						//retrocede hasta encontrar uno igual
						while (($nivel >= 0) && ($this->hayCorteDeGrupo($grupos, $this->mrs, $nivel))) {
							debug(" HtmlGrid:toHtml(): retrocediendo, nivel=$nivel (" . $grupos[$nivel] . ", " . $this->mrs->getValue($nivel) . ")");

							if ($this->mcantRows > 0)
								$res .= $this->cerrarGrupo($totalesGrupo, $nivel, $cols, $acum);

							$totalesGrupo[$nivel] = array_fill(0, 20, 0.0);

							$grupos[$nivel] = $this->mrs->getValue($nivel);
							$grupos[$nivel + 1] = "";
							$nivel--;
						}
						$nivel++;

						//avanza hasta los finales del grupo
						while ($nivel <= $this->getAgrupadores()) {
							$grupos[$nivel] = $this->mrs->getValue($nivel);
							$grupos[$nivel + 1] = "";
							$res .= $this->buildGrupo($grupos[$nivel], $nivel, $cols);
							$nivel++;
						}
						$nivel--;
					}
				}
			}

			//recorre todas las operaciones y les borra los parametros
			for ($opid = 0; $opid < count($this->moperaciones); $opid++) {
				$this->moperaciones[$opid]['url']->resetParametros();
			}

			//busca color de dato
			$rowColor = $this->mrs->getValue("__color");
			$j = 0;
			$clase = "";
			while ($j <= $nivel) {
				$clase .= $this->aNivelesClases[$j] . " ";
				$j++;
			}
			$res .= "\n<tr class=\"" . $clase . "\" ";
			if (!esVacio($this->mId))
				$res .= " id=\"" . $this->mId . "_row_" . ($this->mcantRows + 1) . "\" ";

			if (!esVacio($rowColor))
				$res .= " bgcolor=\"$rowColor\" ";

			if (!esVacio($this->mDobleClickEvent))
				$res .= " ondblclick=\"" . $this->mDobleClickEvent . "('" . ($this->mcantRows + 1) . "');\"";
			if (!esVacio($this->mRowClickEvent))
				$res .= " onclick=\"" . $this->mRowClickEvent . "('" . ($this->mcantRows + 1) . "');\"";
			$res .= " >";

			if ($this->hayColumnaContador()) {
				$res .= "<td style=\"width: 40px;text-align:center\">" . ($this->mcantRows + 1) . "</td>";
				$rowPdf[$this->mColContador] = $this->mcantRows + 1;
			}

			while ($i < $this->mcantCols) {
				$field = $this->mrs->getFieldName($i);

				if ($i <= $nivel) {
					$res .= "<td style=\"width: 60px\">" . espacio() . "</td>";
					$rowPdf[$field] = "";
				} else {
					if ($this->isVisibleColumn($field)) {
						$valor = $this->mrs->getValue($i);

						//columna que forma parte de los labels de un grafico
						//limita los elementos del gráfico
						if (in_array($field, array_keys($this->mGraphicLabels)) && (count($this->mGraphicLabels[$field]) < $this->mGraphicLimit))
							$this->mGraphicLabels[$field][] = resumirTexto(escapeJsValor($valor), 45);

						//columna que forma parte de las series de un grafico
						if (in_array($field, array_keys($this->mGraphicDatasets)) && (count($this->mGraphicDatasets[$field]) < $this->mGraphicLimit))
							$this->mGraphicDatasets[$field][] = round($valor, 2);

						$tipoCampo = $this->mrs->getFieldType($i);
						$classTd = "";

						//funcion de CallBack que permite cambiar valores o class 
						if (!esVacio($this->mRowCallbackFunction)) {
							$resultEval = [];
							$evalStr = ' $resultEval = ' . $this->mRowCallbackFunction . '($row, \'' . $field . "');";
							eval($evalStr);
							if (isset($resultEval["class"]))
								$classTd = $resultEval["class"];
							if (isset($resultEval["valor"]))
								$valor = $resultEval["valor"];
							if (isset($resultEval["valor_pdf"]))
								$rowPdf[$field] = pdfVisible($resultEval["valor_pdf"], true);
						}

						//viene estilo en definicion de columnas
						if (isset($this->mcolsStyles[$i]["class"]))
							$classTd .= " " . $this->mcolsStyles[$i]["class"];

						$dataAlign = getDataAlign($field, $tipoCampo, "", $valor, $this->mcolsStyles, $i);

						$topeParams = 3;
						if ($this->hayOperacion() && $i < $topeParams) {
							//recorre todas las operaciones y les pasa el parametro
							for ($opid = 0; $opid < count($this->moperaciones); $opid++) {
								//si es javascript s�lo pasa el primer par�metro
								if ($this->esUrlJavascript($this->moperaciones[$opid]['url']->toUrl())) {
									//MC: abr-2019: era i==0 pero nunca pasaba parámetros javascript
									//if ($i <= 1)
									if ($i == 0) {
										$this->moperaciones[$opid]['param'] = $valor;
									}
								} else
									$this->moperaciones[$opid]['url']->add($field, $valor);
							}
						}

						$celTitle = "";
						//si estás cerca de los títulos no te muestra nombre de columna
						if ($this->mcantRows > 15 && ($this->mcantRows % 2 == 0))
							$celTitle = " title=\"" . str_replace("_", " ", $field) . "\"";

						//muestra celda
						$classAtr = "";
						if (!esVacio($classTd))
							$classAtr = " class=\"$classTd\"";
						$res .= "<td align=\"" . $dataAlign . "\" $classAtr $celTitle";

						//le pone ID a la celda si la grilla tiene ID
						if (!esVacio($this->mId)) {
							$res .= " id=\"" . $this->mId . "_" . escapeJsNombreVar($field) . "_" . ($this->mcantRows + 1) . "\" ";
						}

						//aplica formato PDF
						if (esCampoStr($tipoCampo) || esCampoMemo($tipoCampo)) {
							$rowPdf[$field] = pdfVisible($valor, true);
						}

						if (esCampoFloat($tipoCampo) || (esCampoConMoneda($valor))) {
							$res .= " onmousedown=\"sumStart(event, this);\" ";
							$anchoFloats = getParameterInt("sc3-grid-ancho-float", 100);
							$res .= " width=\"$anchoFloats\" ";
						}
						$res .= ">";

						if (esCampoFloat($tipoCampo) || (esCampoConMoneda($valor))) {
							//campo editable (por ahora solo Decimales)
							if (in_array($field, $this->mEditables)) {
								//si hay check box, usa el ID de la fila para nombrar el campo
								if (!esVacio($this->mCheckField))
									$id = $this->mId . "f_" . escapeJsNombreVar($field) . "_" . ($this->mrs->getValue($this->mCheckField));
								else
									$id = $this->mId . "f_" . escapeJsNombreVar($field) . "_" . ($this->mcantRows + 1);

								$txtValor = new HtmlInputText($id, $valor);
								$txtValor->setTypeFloat($this->mdecimals);
								$txtValor->setSize(8);
								$res .= $txtValor->toHtml();
							} else
								$res .= formatFloatRed($valor, $this->mdecimals);

							$rowPdf[$field] = formatFloat($valor, $this->mdecimals, 0, 1);
							$this->pdfColumnFormats[$field] = array('justification' => 'right');
						} else
							if (esCampoColor($valor)) {
							$res .= "<div style=\"width: 10px;height: 10px; background-color:$valor\">" . espacio() . "</div>";
							$rowPdf[$field] = "";
						} else
							if (esCampoFecha($tipoCampo)) {
								if (sonIguales($valor, "")) {
									$res .= "";
									$rowPdf[$field] = "";
								} else {
									$fecha = getdate(toTimestamp($valor));
									if (!$this->mmostrarFecha)
										$res .= Sc3FechaUtils::formatHora($fecha);
									else
										$res .= Sc3FechaUtils::formatFecha($fecha, $this->mmostrarHora, false);

									$rowPdf[$field] = Sc3FechaUtils::formatFecha2($fecha, false);
								}
								$this->pdfColumnFormats[$field] = array('justification' => 'right');
							} else {
							//columna checkeable
							if (sonIguales($this->mCheckField, $field)) {
								$chk = new HtmlCheckBox($this->mCheckName . "[]", $valor);
								if (sonIguales($valor, $this->mCheckValue) || $this->mAllChecked)
									$chk->setChecked(true);

								//si Hay ID llama a funcion
								if (!esVacio($this->mId) && !esVacio($this->mCheckFuncion))
									$chk->setOnClick($this->mCheckFuncion . "(1, " . ($this->mcantRows + 1) . ");");

								$res .= $chk->toHtml(false);
								if ($this->mCheckDoble) {
									$chk2 = new HtmlCheckBox($this->mCheckName . "2[]", $valor);

									//si Hay ID llama a funcion
									if (!esVacio($this->mId) && !esVacio($this->mCheckFuncion))
										$chk2->setOnClick($this->mCheckFuncion . "(2, " . ($this->mcantRows + 1) . ");");

									$res .= " " . $chk2->toHtml(false);
								}
							}

							if (startsWith($valor, "pdf:")) {
								$rowPdf[$field] = "";
								//elimina la columna del PDF que se va a generar
								/*
										if (isset($this->pdfTitles[$field]))
											unset($this->pdfTitles[$field]);
										*/
								$valor = substr($valor, 4);
								if (!esVacio($valor))
									$res .= href(img("images/pdficon_small.png", "Archivo pdf adjunto"), getImagesPath() . $valor, "_blanck");
							} else {
								//quizás tuvo valor en la funcion CALLBACK
								if (!isset($rowPdf[$field]))
									$rowPdf[$field] = pdfVisible($valor, true);

								//campo editable (textos)
								if (in_array($field, $this->mEditables)) {
									//le pone el nombre del id del check o secuencial si no hay check
									if (!esVacio($this->mCheckField))
										$id = $this->mId . "f_" . escapeJsNombreVar($field) . "_" . ($this->mrs->getValue($this->mCheckField));
									else
										$id = $this->mId . "f_" . escapeJsNombreVar($field) . "_" . ($this->mcantRows + 1);

									$txtValor = new HtmlInputText($id, $valor);
									$txtValor->setSize(35);
									$txtValor->setMaxSize(160);
									$valor = $txtValor->toHtml();
								} else {
									//no es editable, supera el máximo y además no tiene un <i>										    
									if (strlen($valor) > $anchoMax && !strContiene($valor, "i class")) {
										$valorSinComillas = str_replace('"', " ", $valor);
										$valor = "<div title=\"$valorSinComillas\">" . htmlVisible(substr($valor, 0, $anchoMax)) . "...</div>";
									} else
										$valor = htmlVisible($valor);
								}

								$res .= $valor;
							}
						}

						//si esta columna debe sumarse...
						if (esVacio($valor))
							$valor = 0;

						if (in_array($field, $this->mtotalizar)) {
							$this->mtotales[$i] += splitValorConMoneda($valor);

							//suma los totales en este nivel y en todos los superiores
							$nivelSuma = $nivel;
							while ($nivelSuma >= 0) {
								$totalesGrupo[$nivelSuma][$i] += splitValorConMoneda($valor);
								$nivelSuma--;
							}

							debug(" HtmlGrid.toHtml(): totalizador $i=" . $this->mtotales[$i]);
							if (!isset($this->mtotalesName[$field]))
								$this->mtotalesName[$field] = 0.00;
							if (is_numeric($valor))
								$this->mtotalesName[$field] += $valor;
						}
						$res .= "</td>";
					}
				}
				$i++;
			}

			//hay acumulador, lo agrega al final
			if ($this->hayAcumulador()) {
				$anchoFloats = getParameterInt("sc3-grid-ancho-float", 72);
				$res .= "<td align=\"right\" ";
				$res .= " onmousedown=\"sumStart(event, this);\" ";
				$res .= " width=\"$anchoFloats\" >";

				$acum += splitValorConMoneda($this->mrs->getValue($this->macumulador["c1"]));
				$acum -= splitValorConMoneda($this->mrs->getValue($this->macumulador["c2"]));
				$res .= $this->macumuladorSigno . " " . formatFloatRed($acum);

				$rowPdf[$this->macumulador["columna"]] = $this->macumuladorSigno . " " . formatFloat($acum, 2, 0, 1);
				$this->pdfColumnFormats[$this->macumulador["columna"]] = array('justification' => 'right');

				$this->macumulador["valor"] = $acum;
				$res .= "</td>";
			}

			//ACA: varias operaciones
			if ($this->hayOperacion() && !esExcel()) {
				//la operacion de VER es el +1	
				$sizeOp = ((count($this->moperaciones) + 1) * 40) . "px";

				$res .= "<td class=\"td_operaciones\" align=\"center\" style=\"width: $sizeOp\">";
				if (count($this->moperaciones) > 0) {
					//recorre todas las operaciones acumuladas
					foreach ($this->moperaciones as $opid => $op) {
						$target = "";
						if (isset($op['target']))
							$target = $op['target'];
						$url = $op['url']->toUrl();
						$icon = $op['icon'];

						$iconSize = "fa-2x";
						if (isset($op["icon-size"]))
							$iconSize = $op["icon-size"];
						if ($this->esUrlJavascript($url)) {
							if (!isset($op['param']))
								$op['param'] = "";
							$url = str_replace(":PARAM", $op['param'], $url);
							$url = str_replace(":P0", $this->mrs->getValue(0), $url);
							$url = str_replace(":P1", $this->mrs->getValue(1), $url);
							$url = str_replace(":FILA", ($this->mcantRows + 1), $url);
							if (esIconFontAwesome($icon))
								$res .= " " . href(imgFa($icon, $iconSize, "naranja", $op['label']), $url);
							else
								$res .= " " . href(img($op['icon'], $op['label']), $url) . " ";
						} else {
							if (esIconFontAwesome($icon))
								$res .= "" . href(imgFa($op['icon'], $iconSize, "naranja", $op['label']), $url, $target) . " ";
							else
								$res .= "" . href(img($op['icon'], $op['label']), $url, $target) . " ";
						}
					}
				} else {
					$urlVer = new HtmlUrl("sc-viewitem.php");
					$urlVer->add("stackname", "op_" . $this->moperacionVer['query']);
					$urlVer->add("query", $this->moperacionVer['query']);
					$urlVer->add("registrovalor", $row[$this->moperacionVer['id']]);
					$res .= href(imgFa("fa-cogs", "fa-lg", "icono-tools", "Ver " . $row[$this->moperacionVer['id']]), $urlVer->toUrl(), $this->moperacionVer['target']);
				}
				$res .= "</td>";
			}

			//elimino las claves numericas del row
			$this->pdfData[] = array_intersect_key($rowPdf, $this->pdfTitles);

			$res .= "</tr>";
			$this->mrs->Next();
			$this->mcantRows++;
			//incremente las cantidades para todos los niveles
			foreach ($this->aCantidades as $j => $cant) {
				$this->aCantidades[$j]++;
			}
		}

		$res .= "</tbody>";

		//cierra el ultimo grupo: si no hay saldo
		if ($this->hayAgrupadores() && !$this->hayAcumulador()) {
			while ($nivel >= 0) {
				$res .= $this->cerrarGrupo($totalesGrupo, $nivel, $cols, $acum);
				$nivel--;
			}
		}

		//si no hay totales ni acumulador, no arma fila final
		if (count($this->mtotalizar) > 0 || $this->hayAcumulador()) {
			$res .= "<tbody>";
			$res .= "<tr>";

			$rowPdf = [];
			$i = 0;
			while ($i < $this->mcantCols) {
				$field = $this->mrs->getFieldName($i);
				if ($this->isVisibleColumn($field)) {
					$res .= "<td align=\"right\" ";
					$rowPdf[$field] = "";

					//en la primera columna muestra la cantidad
					if ($i == 0 && $this->mmostrarCantidad) {
						$rowPdf[$field] = "<i>" . $this->mcantRows . " elementos </i>";
					}

					if (in_array($field, $this->mtotalizar)) {
						$res .= " class=\"td_monto derecha\" ";
						$res .= " onmousedown=\"sumStart(event, this);\" ";
						$res .= ">" . formatFloatRed($this->mtotales[$i]);
						$rowPdf[$field] = "<b>" . formatFloat($this->mtotales[$i], 2, 0, 1) . "</b>";
					} else
						$res .= " class=\"\">" . espacio();
					$res .= "</td>";
				}
				$i++;
			}

			if (isset($this->macumulador["columna"]))
				$rowPdf[$this->macumulador["columna"]] = "<b>" . formatFloat($acum, 2, 0, 1) . "</b>";

			$this->pdfData[] = $rowPdf;

			if ($this->hayAcumulador()) {
				$res .= "<td align=\"right\" class=\"td_monto\">";
				$res .= formatFloatRed($acum);
				$res .= "</td>";
			}
			if ($this->hayOperacion()) {
				$res .= "<td class=\"\">";
				$res .= espacio();
				$res .= "</td>";
			}

			$res .= "</tr>";
			$res .= "</tbody>";
		}


		if ($xshowSumary)
			$res .= $this->toHtmlSumary();

		return $res;
	}


	/**
	 * Termina la grilla con el sumario al pdf
	 */
	function getSumaryToPdf()
	{
		$res = "";
		foreach ($this->sumary as $sumario) {
			$res .= "\n";
			$res .= "<b>";
			$res .= ucfirst($sumario[0]);
			$res .= "</b> ";
			$valorf = (float) $sumario[1];
			$res .= formatFloat($valorf);
			$res .= "  ";
		}
		return $res;
	}

	/**
	 * Termina la grilla con el sumario
	 */
	function toHtmlSumary()
	{
		$res = "<tbody>";
		$haySumary = false;
		//procesa el sumario
		foreach ($this->sumary as $sumario) {
			$res .= "\n<tr>";
			$res .= "<td align=\"right\" colspan=\"" . ($this->mcantCols - 1) . "\">";
			$res .= ucfirst($sumario[0]);
			$res .= "</td>";
			$res .= "<td align=\"right\"\" class=\"td_monto\">";
			$valorf = (float) $sumario[1];
			$res .= formatFloatRed($valorf);
			$res .= "</td>";
			$res .= "</tr>";
			$haySumary = true;
		}
		$res .= "</tbody>";

		if (!$haySumary && ($this->mcantRows != 1) && $this->mostrarFooter) {
			$colsPanTotal = $this->mcantCols;
			if ($this->hayOperacion())
				$colsPanTotal++;
			if ($this->hayAcumulador())
				$colsPanTotal++;
			if ($this->hayColumnaContador())
				$colsPanTotal++;

			$colsPanMitad1 = 2;
			$colsPanMitad2 = $colsPanTotal - 2;
			//sólo dos columnas
			if ($colsPanTotal == 2) {
				$colsPanMitad1 = 1;
				$colsPanMitad2 = 1;
			}
			$res = "<tfoot>
					<tr>
						<td align=\"left\" colspan=\"" . $colsPanMitad1 . "\" class=\"table-filtered-rowcount:visiblerowspan\">
								<small>" . $this->mcantRows . " elementos </small>
						</td>";
			$res .= "	<td align=\"left\" colspan=\"" . $colsPanMitad2 . "\">";

			//si hay checkbox, arma herramientas para tildar todos
			if (!esVacio($this->mCheckField)) {
				$bid = $this->mId . "_chk_" . $this->mCheckField;
				$fn = $this->mCheckFuncion;
				$res .= "<div class=\"gridtoolbar\" onclick=\"tildarTodos('" . $this->mCheckName . "[]', '$bid', '$fn(1, 1)')\" id=\"$bid\"><i class=\"fa fa-check-square\"></i> todos</div>";
			}

			$res .= " </td>
					</tr>
				</tfoot>";
		}
		$res .= "</table>";

		$res .= "</div>";

		if ($this->mBrFinal)
			$res .= "<br>";

		if ($this->mostrarDivSum) {
			//si no existe dibuja DIV para que se sumen los valores numéricos
			$res .= "\n	<script language=\"javascript\">";
			$res .= "\n	divTotal = document.getElementById('sumtotal');";
			$res .= "\n	if (divTotal == null)";
			$res .= "\n		document.write('<div class=\"div-suma-grilla oculto\" id=\"sumtotal\" title=\"Click para cerrar\" onclick=\"sumEnd(event, this);\"></div>');";
			$res .= "\n	</script>";
		}

		return $res;
	}
}
