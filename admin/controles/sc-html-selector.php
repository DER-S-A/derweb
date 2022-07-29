<?php

/**
 * Selector de datos
 **/
class HtmlSelector
{
	var $id = "";
	var $value = "";
	var $query = "";
	var $onkeypressed = "";
	var $onTranslate = "";
	var $readonly = false;
	var $descSize = 33;

	var $ignoreFixedValue = false;
	var $ignoreRequest = false;
	var $ignoreStartingWith = "";

	var $requerido = false;
	var $descripcion = "";
	var $properties = array();
	var $mfilter = "";
	var $showPin = true;
	var $showLupa = true;
	var $autoSuggest = true;
	var $m2Filas = false;

	var $catalogGrid = "";

	//campos para filtrar relacionado a otro selector
	var $masterSelector = "";
	var $masterField = "";
	var $masterDesc = "";
	var $mextendedFilterQuery = "";

	function __construct($xid, $xquery, $xvalue = 0)
	{
		$this->id = $xid;
		$this->query = $xquery;
		if (!sonIguales($xvalue, "0") && ($xvalue != 0))
			$this->value = $xvalue;
		$this->clearExtendedFilter();
	}

	function getDescripcion()
	{
		return $this->descripcion;
	}

	/**
	 * Ignora los códigos que arrancan con el prefijo dado
	 * @param string $xPrefijoIgnorar
	 */
	function setIgnoreStartingWith($xPrefijoIgnorar)
	{
		$this->ignoreStartingWith = $xPrefijoIgnorar;
	}

	function showPin($xshowPin)
	{
		$this->showPin = $xshowPin;
	}

	function set2Filas()
	{
		$this->m2Filas = true;
	}

	function setSizeSmall()
	{
		$this->descSize = 20;
	}

	function showLupa($xshowLupa)
	{
		$this->showLupa = $xshowLupa;
	}

	function setCatalogGrid($xsql)
	{
		$this->catalogGrid = $xsql;
	}

	function setValue($xvalue)
	{
		if (($xvalue != 0) && ($xvalue != "0"))
			$this->value = $xvalue;
		else
			$this->value = "";
	}

	function getValue()
	{
		return $this->value;
	}

	/**
	 * Crea un atributo extra que se agrega a la
	 * @param string $xlabel
	 * @param string $xnombre
	 * @param boolean $xcrear
	 */
	function addPropiedad($xlabel, $xnombre, $xcrear = 1)
	{
		array_push($this->properties, array("label" => $xlabel, "nombre" => $xnombre, "crear" => $xcrear));
	}

	function setRequerido()
	{
		$this->requerido = true;
	}

	function setAutosuggest($xautoSuggest)
	{
		$this->autoSuggest = $xautoSuggest;
	}

	function isRequerido()
	{
		return $this->requerido;
	}

	function setIgnoreFixedValue()
	{
		$this->ignoreFixedValue = true;
	}

	/*
		Toma el valor del request
	*/
	function valueFromRequest($xreq = "")
	{
		if (strcmp($xreq, "") == 0)
			$this->setValue(Request($this->id));
		else
			$this->setValue(Request($xreq));

		//si hay valor del request, se ignora lo guardado (fijado)
		if (!sonIguales($this->value, ""))
			$this->ignoreFixedValue = true;
	}

	/**
	 * Toma el valor por defecto, salvo que se encuentre el valor en el request
	 */
	function setDefault($xvalue)
	{
		$this->valueFromRequest();
		if (sonIguales($this->value, "") && !sonIguales($xvalue, "0"))
			$this->value = $xvalue;
	}

	function setQuery($xquery)
	{
		$this->query = $xquery;
	}

	function setReadOnly($xreadonly = true)
	{
		$this->readonly = $xreadonly;
	}

	function setFilter($xfilter)
	{
		$this->mfilter = $xfilter;
	}

	/**
	 * Permite seleccionar un selector master que tiene que tener un valor antes y permite filtrar por ese valor
	 *
	 * @param string $xselector ID del selector master
	 * @param string $xfield Campo a filtrar con el valor del master
	 */
	function setMasterSelector($xselector, $xfield, $xdescripcion, $xextendedFilterQuery = "")
	{
		$this->masterSelector = $xselector;
		$this->masterField = $xfield;
		$this->masterDesc = $xdescripcion;
		$this->mextendedFilterQuery = $xextendedFilterQuery;
		$this->saveExtendedFilter();
	}

	//guarda el filtro extendido
	function saveExtendedFilter()
	{
		setSession($this->id . "-eqf", $this->mextendedFilterQuery);
	}

	//borra el filtro extendido (subquerys)
	function clearExtendedFilter()
	{
		setSession($this->id . "-eqf", "");
	}

	function ignoreRequest()
	{
		$this->ignoreRequest = true;
	}

	/*
	 Chequea que sin son iguales el master y el query de este selector, le setea el valor
	*/
	function checkMaster($xReadOnly = true, $xMaster = "")
	{
		$master = Request("mquery");
		if (!esVacio($xMaster))
			$master = $xMaster;

		$mid = RequestInt("mid");
		if (sonIguales($this->query, $master) && ($mid > 0)) {
			$this->setValue($mid);
			$this->setReadOnly($xReadOnly);
			$this->ignoreFixedValue = true;
		}
	}

	function setOnKeyPressed($xevent)
	{
		$this->onkeypressed = $xevent;
	}

	function setOnTranslate($xevent)
	{
		$this->onTranslate = $xevent;
	}

	function toHtml()
	{
		$res = "\n";
		$filterSeparator = "|";

		if (sonIguales($this->value, "") && (!$this->ignoreRequest))
			$this->valueFromRequest();

		if ($this->value == 0)
			$this->value = "";

		//intenta recuperar el valor fijado si no hay orden de ignorarlo
		if (!$this->ignoreFixedValue) {
			$valorFijado = getSession("selector-" . $this->query);
			$valorRequest = Request($this->id);
			if (sonIguales($valorRequest, "") && !sonIguales($valorFijado, ""))
				$this->value = $valorFijado;
		}

		//si hay un valor traduce descripcion, si no hay traduccion posible resetea el valor
		if (!sonIguales($this->value, "")) {
			$this->descripcion = translateCode($this->value, $this->query);
			if (sonIguales($this->descripcion, "")) {
				echo ("(imposible traducir codigo " . $this->value . ")");
			}
		}

		if ($this->readonly) {
			if (!sonIguales($this->query, "qmonedas"))
				$res .= "" . $this->value . " - ";
			$res .= "" . $this->descripcion;
			$hid = new HtmlHidden($this->id, $this->value);
			$hid2 = new HtmlHidden($this->id . "desc", $this->descripcion);
			$res .= $hid->toHtml() . $hid2->toHtml();
		} else {
			$qinfo = getQueryObj($this->query);
			if (!$qinfo->isCacheable()) {
				$hayKeyPressedEvent = false;
				//width=\"100%\"
				$res .= "<div class=\"autocomplete-container\">";
				if (!isMobileAgent()) {
					$res .= "\n <input type=\"text\"";
					$res .= " onkeyup=\"javascript:lookUp('" . $this->id . "','" . $this->id . "desc', '" . $this->query . $filterSeparator . $this->mfilter . "', '" . $this->masterSelector . "', '" . $this->masterField . "', '" . $this->masterDesc . "');\" value=\"" . $this->value . "\" maxlength=\"10\" size=\"4\" id=\"" . $this->id . "\" name=\"" . $this->id . "\"";
					if ($this->isRequerido())
						$res .= " class=\"requerido\" ";
					$res .= " autocomplete=\"off\"";
					$res .= " onclick=\"sc3SelectAll('" . $this->id . "')\" />";
				} else {
					$h1 = new HtmlHidden($this->id, $this->value);
					$res .= $h1->toHtml();
					$res .= "<span class=\"autocomplete-code\" id=\"" . $this->id . "code\">" . $this->value . "</span>";
				}
				$res .= "\n <input type=\"text\" maxlength=\"60\" size=\"" . $this->descSize . "\" id=\"" . $this->id . "desc\" name=\"" . $this->id . "desc\" value=\""  . $this->descripcion . "\"";
				$res .= " onkeyup=\"javascript:lookUp2('" . $this->id . "','" . $this->id . "desc', '" . $this->query . $filterSeparator . $this->mfilter . "', '" . $this->masterSelector . "', '" . $this->masterField . "', '" . $this->masterDesc . "', '" . $this->onTranslate . "', '" . $this->ignoreStartingWith . "');\"";
				if (!sonIguales($this->onkeypressed, "")) {
					$res .= " onkeypress=\"" . $this->onkeypressed . "\"";
					$hayKeyPressedEvent = true;
				}
				$res .= " />";

				if ($this->showLupa) {
					//si hay grila especial, abre sc-openCatalagoGrid.php con el SQL enviado
					if (esVacio($this->catalogGrid)) {
						$res .= "\n <a id=\"" .  $this->id . "_oc\" href=\"javascript:openCatalog('" . $this->id . "','" . $this->id . "desc', '" . $this->query . $filterSeparator . $this->mfilter . "', '" . $this->masterSelector . "', '" . $this->masterField . "', '" . $this->masterDesc . "');\" class=\"rptinvisible\" tabindex=\"-1\" title=\"Buscar valor\" >";
					} else {
						$res .= "\n <a id=\"" .  $this->id . "_oc\" href=\"javascript:openCatalogGrid('" . $this->id . "','" . $this->id . "desc', '" . $this->query . "', '" . $this->catalogGrid . "');\" class=\"rptinvisible\" tabindex=\"-1\" title=\"Buscar valor\">";
					}

					$res .= "<i class=\"fa fa-search fa-lg boton-fa-control\"></i>";
					$res .= "</a>";
				}
				$res .= "\n <a href=\"javascript:clearSelector('" . $this->id . "','" . $this->id . "desc');\" class=\"rptinvisible\" tabindex=\"-1\"  title=\"Limpiar valor seleccionado\" >";
				$res .= "<i class=\"fa fa-trash-o fa-lg boton-fa-control\"></i>";
				$res .= "</a>";

				//fijar valor
				if ($this->showPin) {
					$res .= "\n <a href=\"javascript:fijarSelector('" . $this->id . "','" . $this->id . "desc', '" . $this->query . "');\" class=\"rptinvisible\" tabindex=\"-1\" title=\"Fijar valor\">";
					$res .= "<i class=\"fa fa-thumb-tack fa-lg boton-fa-control\"></i>";
					$res .= "</a>";
				}

				//agrega campos con atributos
				foreach ($this->properties as $i => $props) {
					$res .= "<b>" . $props["label"] . ":</b>";
					$res .= " <div id=\"" . $this->id . $props["nombre"] . "\" />";
				}

				$res .= "</div>	";

				if (getParameterInt("sc3-usar-autosuggest", "1") && !$hayKeyPressedEvent && $this->autoSuggest) {
					$maxresults = 25;
					$res .= "\n<script>";
					if (!sonIguales($this->masterField, "")) {
						$res .= "\n	mid = document.getElementById('" . $this->masterSelector . "').value;";
					}
					$res .= "\nvar options_" . $this->id . " = {";

					if (sonIguales($this->masterField, "")) {
						$res .= "script: function (input) { ";
						//TODO: filtrar TAB y teclas de control
						/*
						$res .= "\n if (document.getElementById('". $this->id . "').value.trim() != '')
									return \"\";";
						*/
						$res .= "\n return \"sc-ajax-autosuggest.php?query=" . $this->query . $filterSeparator . $this->mfilter . "&rcontrol1=" . $this->id . "&limit=$maxresults&valor=\" + input; },";
					} else {
						$res .= "script: function (input) { ";
						//$res .= "\n	mid = document.getElementById('" . $this->masterField . "').value;";
						$res .= "\n	mid = document.getElementById('" . $this->masterSelector . "').value;";
						$res .= "\n	if (mid == '')\n	{\n		alert('Ingrese antes un valor en el campo " . $this->masterDesc . "');\n		return;\n	}";
						$res .= "\n	return \"sc-ajax-autosuggest.php?query=" . $this->query . $filterSeparator . $this->mfilter . "&control1=" . $this->id . "&limit=$maxresults" . "&mfield=" . $this->masterField . "&mid=\" + mid + \"&valor=\" + input; },";
					}
					$res .= "\n	varname:\"" . $this->id . "desc\",";
					$res .= "\n	shownoresults:false,";
					$res .= "\n	maxresults:$maxresults,";
					$res .= "\n callback: function (seleccion) {document.getElementById('" . $this->id . "').value = seleccion['id'];
										objCode = document.getElementById('" . $this->id . "code');
										if (objCode != null)
											objCode.innerHTML = seleccion['id'];
										onTranslateFn = \"" . $this->id . "\"" . " + \"_OnTranslate\"; 
										eval('if(typeof ' + onTranslateFn + ' == \'function\') { ' + onTranslateFn + '();}');
							}";

					$res .= "\n};";

					$res .= "\nvar as_xml_" . $this->id . " = new bsn.AutoSuggest('" . $this->id . "desc', options_" . $this->id . ");";
					$res .= "\n</script>";
				}
			} else {
				//es cacheable, arma control nuevo con acceso a cache de sessionStorage
				$res .= "<div class=\"autocomplete-container\">";

				$res .= "<input id=\"" . $this->id . "\" type=\"text\" name=\"" . $this->id . "\" value=\"" . $this->value . "\" placeholder=\"\"";
				$res .= " autocomplete=\"off\"";
				$res .= " onclick=\"sc3SelectAll('" . $this->id . "')\"";
				if ($this->isRequerido())
					$res .= " class=\"requerido\" ";

				$res .= " size=\"4\" onkeyup=\"javascript:traducirCodigoSelector('" . $this->id . "', '" . $this->query . "');\">";

				$divAncho = "310px;";
				if ($this->descSize <= 20)
					$divAncho = "205px;";

				$res .= "<div class=\"autocomplete\" style=\"width:$divAncho\">
							<input id=\"" . $this->id . "desc\" type=\"text\" name=\"" . $this->id . "desc\" value=\""  . $this->descripcion . "\" placeholder=\"\" autocomplete=\"off\" size=\"" . $this->descSize . "\">
						</div>";

				if ($this->showLupa) {
					//si hay grila especial, abre sc-openCatalagoGrid.php con el SQL enviado
					if (esVacio($this->catalogGrid))
						$res .= "<a id=\"\" href=\"javascript:openCatalogDiv('" . $this->id . "', '" . $this->query . "', '', '', '');\" class=\"rptinvisible\" tabindex=\"-1\" title=\"Buscar valor\" ><i class=\"fa fa-search fa-lg boton-fa-control\"></i></a>";
					else
						$res .= "<a id=\"" .  $this->id . "_oc\" href=\"javascript:openCatalogGrid('" . $this->id . "','" . $this->id . "desc', '" . $this->query . "', '" . $this->catalogGrid . "');\" class=\"rptinvisible\" tabindex=\"-1\" title=\"Buscar valor\"><i class=\"fa fa-search fa-lg boton-fa-control\"></i></a>";
				}
				$res .= "<a href=\"javascript:clearSelector('" . $this->id . "', '" . $this->id . "desc');\" class=\"rptinvisible\" tabindex=\"-1\" title=\"Limpiar valor seleccionado\" ><i class=\"fa fa-trash-o fa-lg boton-fa-control\"></i></a>";

				if ($this->showPin)
					$res .= "<a href=\"javascript:fijarSelector('" . $this->id . "', '" . $this->id . "desc', '" . $this->query . "');\" class=\"rptinvisible\" tabindex=\"-1\" title=\"Fijar valor\"><i class=\"fa fa-thumb-tack fa-lg boton-fa-control\"></i></a>";

				$res .= "</div>	";

				$res .= "<script>
						autocomplete('" . $this->id . "', '" . $this->query . "');	
						</script>";
			}
		}
		return $res;
	}
}
