<?php

$AJAX_PAGE_SIZE = 90000;

/*
Setea el grupo del campo para la tabla dada
*/
function sc3setGroup($xtabla, $xfield, $xgrupo)
{
	debug("sc3setGroup($xtabla, $xfield, $xgrupo)");
	echo ("<br>set group: $xtabla, <b>$xfield</b>, $xgrupo...");

	$sql = "update sc_fields 
			set grupo='$xgrupo' 
			where field_ like '$xfield' and 
				idquery in (select id from sc_querys where table_ = '$xtabla')";
	$rsq = new BDObject();
	$rsq->execQuery($sql);
}

/*
Cambia el campo a visible o invisible
*/
function sc3setVisible($xtabla, $xfield, $xvisible)
{
	debug("sc3setVisible($xtabla, $xfield, $xvisible)");
	echo ("<br>set visible: $xtabla, <b>$xfield</b>, $xvisible...");

	$sql = "update sc_fields set visible = $xvisible 
			where field_ like '$xfield' and 
				idquery in (select id from sc_querys where table_ = '$xtabla')";
	$rsq = new BDObject();
	$rsq->execQuery($sql);
}


/*
Cambia el campo a visible o invisible, por query
*/
function sc3setVisibleQuery($xquery, $xfield, $xvisible)
{
	debug("sc3setVisible($xquery, $xfield, $xvisible)");
	echo ("<br>set visible: $xquery, <b>$xfield</b>, $xvisible...");

	$sql = "update sc_fields set visible = $xvisible
			where field_ like '$xfield' and
			idquery in (select id from sc_querys where queryname = '$xquery')";
	$rsq = new BDObject();
	$rsq->execQuery($sql);
}



function sc3eliminarPrefijo($xprefix)
{
	debug("sc3eliminarPrefijo($xprefix)");
	$sql = "update sc_fields 
			set show_name = replace(show_name, '$xprefix ', '') 
			where field_ like '" . $xprefix . "_%'";

	$bd = new BDObject();
	$bd->execQuery2($sql);
	echo ("<br>eliminar prefijo: $xprefix...");
}

/**
 * Retorna un combo con los campos
 *
 * @param ScQueryInfo $xqinfo
 * @param string $xcontrolId
 * @param string $xdefault
 * @param string $xvalue
 * @return string
 */
function getComboCamposArray($xqinfo, $xcontrolId, $xdefault, $xvalue, $xclass = "")
{
	$combo = new HtmlCombo($xcontrolId, $xvalue);
	$combo->add("", $xdefault);
	foreach ($xqinfo->getFieldsDef() as $field => $fielddef) {
		$campo = $field;
		$combo->add($campo, substr($xqinfo->getFieldCaption($campo), 0, 15));
	}
	$combo->setClass($xclass);
	return $combo->toHtml();
}


function getFKValue($xidquerymaster, $xcampo, $xvalor)
{
	debug("getFKValue($xidquerymaster, $xcampo, $xvalor)");
	$rsRefInfo = new BDObject();
	$rsRefInfo->execQuery("SELECT sc_referencias.*, sc_querys.* 
							FROM sc_querys 
								INNER JOIN sc_referencias ON sc_querys.idquery = sc_referencias.idquery 
							WHERE ((sc_referencias.idquerymaster=" . $xidquerymaster . ") AND 
								(sc_referencias.campo_ like '" . $xcampo . "'))");
	$retVal = "" . $xvalor;
	if (!$rsRefInfo->EOF()) {
		$rsFKInfo = new BDObject();
		$rsFKInfo->execQuery("select " . $rsRefInfo->getValue("combofield_") . " from " .  $rsRefInfo->getValue("table_") . " where " .  $rsRefInfo->getValue("keyfield_") . "=" . $xvalor);
		if ($rsFKInfo->EOF()) {
			if (strcmp($retVal, "null") == 0)
				$retVal = "";
			else
				$retVal = $xvalor;
		} else {
			$retVal = $xvalor . "-" . $rsFKInfo->getValue(0);
		}
	}
	$rsRefInfo->close();
	return $retVal;
}

function getFKValue2($xcampo, $xvalor, $xfields_ref, &$xfk_cache, $xshowId = TRUE, $xrecord = "")
{
	$retVal = "" . $xvalor;
	if ($xvalor == "")
		return "";

	//prueba en cache primero
	if (isset($xfk_cache[$xcampo][$xvalor]) && $xfk_cache[$xcampo][$xvalor] != "") {
		debug("  CACHE HIT: $xcampo : $xvalor");
		return $xfk_cache[$xcampo][$xvalor];
	}

	//si hay un campo que se llama igual, pero con el sufijo _fk, entonces es un query con left join que tiene el valor
	//del FK en el mismo result set
	$sufixFk = "_fk";

	if (isset($xrecord[$xcampo . $sufixFk]) && !sonIguales($xrecord[$xcampo . $sufixFk], "")) {
		$result = "";
		if ($xshowId)
			$result .= $xvalor . "-";
		$result .= $xrecord[$xcampo . $sufixFk];

		return $result;
	}

	if (isset($xfields_ref[$xcampo]) && $xfields_ref[$xcampo] != "") {
		$rsFKInfo = new BDObject();
		$rsFKInfo->execQuery("select " . $xfields_ref[$xcampo]["combofield_"] . " from " .  $xfields_ref[$xcampo]["table_"] . " where " .  $xfields_ref[$xcampo]["keyfield_"] . " = " . $xvalor);
		if ($rsFKInfo->EOF()) {
			if (strcmp($retVal, "null") == 0)
				$retVal = "";
			else
				$retVal = $xvalor;
		} else {
			$retVal = $rsFKInfo->getValue(0);
			debug("  a CACHE ($xcampo, $retVal)");
			$xfk_cache[$xcampo][$xvalor] = $rsFKInfo->getValue(0);
		}
	}
	return $retVal;
}

/*
Retorna un arreglo con los grupos que componen los campos
*/
function getGruposArray($xfields)
{
	debug("getGruposArray()");

	$grupos = array();
	array_push($grupos, "Datos");
	foreach ($xfields as $field => $fielddef) {
		$grupo = "";
		if (isset($xfields[$field]["grupo"]))
			$grupo = ucfirst($xfields[$field]["grupo"]);

		if (!in_array($grupo, $grupos) && !sonIguales("", $grupo) && $fielddef["visible"] == 1)
			array_push($grupos, $grupo);
	}
	return $grupos;
}


function getFkField($xidquery, $xmquery)
{
	debug("getFkField($xidquery, $xmquery);");
	$rs = new BDObject();
	$sql = 	"SELECT sc_referencias.campo_ ";
	$sql .= " FROM sc_querys INNER JOIN sc_referencias ON sc_querys.id = sc_referencias.idquery";
	$sql .= " WHERE (sc_referencias.idquerymaster=" . $xidquery . ")";
	$sql .= " and (sc_querys.queryname='" . $xmquery . "')";

	$fieldname = "";
	$rs->execQuery($sql);
	if (!$rs->EOF()) {
		$fieldname = $rs->getValue("campo_");
	}
	$rs->close();
	return $fieldname;
}


//Arma un combo con la descripcion de la FK
function getFKCombo($xcampo, $xvalor, $xfields_ref)
{
	debug("getFKCombo($xcampo, $xvalor, $xfields_ref)");
	if ($xfields_ref[$xcampo] == "") {
		return "";
	} else {
		$sel = new HtmlSelector($xcampo, $xfields_ref[$xcampo]["queryname"], $xvalor);
		return $sel->toHtml();
	}
}

/**
 * Retorna si el color debería ser blanco o negro en función del color de fondo
 */
function getColorByBgColor($xbgColor)
{
	if (esVacio($xbgColor))
		return '';

	$colorNum = str_replace("#", "", $xbgColor);
	//16777215 es FFFFFF pasado a numerico
	if ($colorNum > (16777215 / 2))
		return "#000000";
	return "#ffffff";
}


function translateCode($xid, $xquery, $xmfield = "", $xmid = "", $xextendedFilter = "", $xfiltername = "")
{
	debug("translateCode($xid, $xquery)");
	global $EMPTY_SELECTOR;
	$result = $EMPTY_SELECTOR;
	if ($xid == "" || $xquery == "")
		return $result;

	$tc = getCache();
	$query_info = $tc->getQueryInfo($xquery);
	saveCache($tc);

	$qinfo = new ScQueryInfo($query_info, false);

	$rs = new BDObject();
	$sql = "select " .  $qinfo->getComboField();
	$sql .= " from " .  $qinfo->getQueryTable() . " t1 ";
	$sql .= " where ";
	$whereStr = $qinfo->getQueryWhere();
	/*
	MC: debería usarse o traducir directo por ID ?
	if ($whereStr != "")
		$sql .= $whereStr . " and ";
	*/
	$sql .= $qinfo->getKeyField() . " = " . $xid;

	//si hay campo master tambien filtra por el
	//si hay filtro adicional (con una expresión particular, es usa el $mid en ella)
	if (esVacio($xextendedFilter)) {
		if (!sonIguales($xmfield, ""))
			$sql .= " and t1.$xmfield = $xmid";
	} else {
		$extendedWhere = str_replace(":$xmfield", $xmid, $xextendedFilter);
		$sql = addWhere($sql, $extendedWhere);
	}

	$rs->execQuery($sql);
	if (!$rs->EOF()) {
		$result = $rs->getValue(0);
	}
	$rs->close();
	return sinCaracteresEspeciales($result);
}


/*
Invocada desde el selector, cuando el usuario busca por descripcion
Trae con "limit 2" pero traduce solo si hay un unico candidato
*/
function translateDesc($xdescripcion, $xquery, $xmfield = "", $xmid = "")
{
	debug("translateDesc($xdescripcion, $xquery)");
	global $EMPTY_SELECTOR;
	$result = $EMPTY_SELECTOR;
	if ($xdescripcion == "" || $xquery == "") {
		return $result;
	}

	//contempla que sea un ticket, lo trata de manera especial
	//tiene que existir un articulo que arranque como los tickets
	$codigoTicket = getParameter("cja2-prefijo-art-ticket", "99955");
	if (sonIguales($xquery, "qstoarticulos") && startsWith($xdescripcion, $codigoTicket)) {
		$rs = locateRecordWhere("sto_articulos", "codigo = '$codigoTicket'");
		if (!$rs->EOF()) {
			$result = $rs->getValue("id") . "#" . $xdescripcion;
			return sinCaracteresEspeciales($result);
		} else
			return $EMPTY_SELECTOR;
	}

	//contempla que sea una fotocopia
	$codigoFotocopias = getParameter("empresa-codigo", "111");
	if (sonIguales($xquery, "qstoarticulos") && startsWith($xdescripcion, $codigoFotocopias)) {
		$idarticulo1 = getParameterInt("kio-idart-fotocopia", 0);
		if ($idarticulo1 > 0) {
			$rs = locateRecordId("sto_articulos", $idarticulo1);
			if (!$rs->EOF()) {
				$result = $rs->getValue("id") . "#" . $xdescripcion;
				return sinCaracteresEspeciales($result);
			}
		} else
			return $EMPTY_SELECTOR;
	}

	//prefijo de precio producto / importe
	$codigoPPI = getParameter("cja2-prefijo-ppi", "ppi");

	//COD BAR: prefijo (3) + id art (5) + cant (4) + importe (6)
	if (sonIguales($xquery, "qstoarticulos") && startsWith($xdescripcion, $codigoPPI)) {
		$idartPpi = substr($xdescripcion, 3, 5) * 1.0;
		$rs = locateRecordWhere("sto_articulos", "id = $idartPpi");
		if (!$rs->EOF()) {
			$result = $rs->getValue("id") . "#" . $xdescripcion;
			return sinCaracteresEspeciales($result);
		} else
			return $EMPTY_SELECTOR;
	}


	/*
	$tc = getCache();
	$query_info = $tc->getQueryInfo($xquery);
	saveCache($tc);

	$qinfo = new ScQueryInfo($query_info, true);
	*/
	$tc = getCache();
	if ($tc->existsQueryObj($xquery))
		$qinfo = $tc->getQueryObj($xquery);
	else {
		$query_info = $tc->getQueryInfo($xquery);
		$qinfo = new ScQueryInfo($query_info, true);
		$tc->saveQueryObj($xquery, $qinfo);
	}
	saveCache($tc);

	$rs = new BDObject();
	$sql = "select " .  $qinfo->getKeyField() . ", " . $qinfo->getComboField() . ", LENGTH(" . $qinfo->getComboField() . ") as __longitud";
	$sql .= " from " .  $qinfo->getQueryTable() . " t1 ";
	$sql .= " where ";
	$whereStr = $qinfo->getQueryWhere();
	if ($whereStr != "")
		$sql .= $whereStr . " and ";
	$sql .= " t1." . $qinfo->getComboField() . " like '" . $xdescripcion . "%'";

	//si hay campo master tambien filtra por el
	if (!sonIguales($xmfield, ""))
		$sql .= " and t1.$xmfield = $xmid";

	$sql .= " order by LENGTH(" . $qinfo->getComboField() . ")";
	$sql .= " limit 2";

	$rs->execQuery($sql);
	if (!$rs->EOF()) {
		$longResultado = $rs->getValue(2);
		$result = $rs->getValue(0) . "#" . $rs->getValue(1);
		$rs->Next();
		//si hay mas de un candidato y no coinciden exactamente con los buscado, no resuelve
		if (!$rs->EOF() && strlen($xdescripcion) != $longResultado)
			$result = $EMPTY_SELECTOR;
	} else {
		//arranca a buscar cuando tiene 5 o mas
		$minLong = getParameterInt("cja2-vender-longitud", "5");
		if (strlen($xdescripcion) < $minLong)
			$result = $EMPTY_SELECTOR;
		else {
			$fields = $qinfo->getQueryFields();

			//si tiene un campo código, busca por el
			if (strContiene($fields, "codigo")) {
				$sql = "select " .  $qinfo->getKeyField() . ", " . $qinfo->getComboField() . ", LENGTH(codigo) as __longitud";
				$sql .= " from " .  $qinfo->getQueryTable() . " t1 ";
				$sql .= " where ";
				$whereStr = $qinfo->getQueryWhere();
				if ($whereStr != "")
					$sql .= $whereStr . " and ";
				$sql .= " (t1.codigo like '" . $xdescripcion . "%'";

				$descripcion2 = $xdescripcion;
				//por si el artículo está cargado con un valor menos
				$intentaCodigoMenos1 = getParameterInt("cja2-vender-codmenos1", "0");
				if ($intentaCodigoMenos1 > 0) {
					if (strlen($xdescripcion) >= $intentaCodigoMenos1) {
						$descripcion2 = substr($xdescripcion, 0, -1);
						$sql .= " or t1.codigo like '" . $descripcion2 . "%'";
					}
				}
				$sql .= ") ";

				//si hay campo master tambien filtra por el
				if (!sonIguales($xmfield, ""))
					$sql .= " and t1.$xmfield = $xmid";

				$sql .= " order by LENGTH(codigo)";
				$sql .= " limit 2";

				$rs = new BDObject();
				$rs->execQuery($sql);

				if (!$rs->EOF()) {
					$longResultado = $rs->getValue(2);
					$result = $rs->getValue(0) . "#" . $rs->getValue(1);

					$rs->Next();
					//si hay mas de un candidato y no coinciden exactamente con los buscado, no resuelve
					if (!$rs->EOF() && ((strlen($xdescripcion) != $longResultado
						|| (($intentaCodigoMenos1 > 0) && (strlen($descripcion2) != ($longResultado - 1)))))) {
						$result = $EMPTY_SELECTOR;
					}
				}
			}

			//todavia no hay nada encontrado
			if (sonIguales($result, $EMPTY_SELECTOR)) {
				//busca por cualquier campo, si encuentra mejor !
				$sql = "select " .  $qinfo->getKeyField() . ", " . $qinfo->getComboField();
				$sql .= " from " .  $qinfo->getQueryTable() . " t1 ";
				$sql .= " where " . $qinfo->buildSearch("", $xdescripcion, "CON", "t1", true);
				$sql .= " limit 2";
				$rs->execQuery($sql);
				if (!$rs->EOF()) {
					$result = $rs->getValue(0) . "#" . $rs->getValue(1);
					$rs->Next();
					//si hay mas de un candidato, no resuelve
					if (!$rs->EOF())
						$result = $EMPTY_SELECTOR;
				}
			}
		}
	}

	$rs->close();
	return sinCaracteresEspeciales($result);
}

/*
Recupera un valor del query, el primero que encuentre
*/
function getUnValor($xquery)
{
	debug("getUnValor($xquery)");
	$result = "";
	$tc = getCache();
	$query_info = $tc->getQueryInfo($xquery);

	$qinfo = new ScQueryInfo($query_info, false);

	$rs = new BDObject();
	$sql = "select " .  $qinfo->getKeyField();
	$sql .= " from " .  $qinfo->getQueryTable() . " t1 ";
	$sql .= " where ";
	$whereStr = $qinfo->getQueryWhere();
	if ($whereStr != "")
		$sql .= $whereStr;
	$orderStr = $qinfo->getQueryOrder();
	if ($orderStr != "")
		$sql .= " order by " . $orderStr;
	$sql .= " limit 1";

	$rs->execQuery($sql);
	if (!$rs->EOF()) {
		$result = $rs->getValue(0);
	}
	$rs->close();
	return $result;
}

/*
Recupera un valor del query, el primero que encuentre
*/
function getValorDefault($xquery)
{
	debug("getValorDefault($xquery)");
	$result = "";

	$tc = getCache();
	$query_info = $tc->getQueryInfo($xquery);

	$qinfo = new ScQueryInfo($query_info, false);

	$rs = new BDObject();
	$sql = "select " .  $qinfo->getKeyField();
	$sql .= " from " .  $qinfo->getQueryTable();
	$sql .= " where es_default = 1";
	$sql .= " limit 1";
	$rs->execQuery($sql);
	if (!$rs->EOF()) {
		$result = $rs->getValue(0);
	}
	$rs->close();
	return $result;
}

/**
 * Arma el SQL de insert tomando los valores del request pero teniendo prioridad 
 * los que estan en el arreglo de valores. 
 *
 * @param string $xquery
 * @param array $xvalues
 * @return string
 */
function insertIntoTable($xquery, $xvalues, $xincludeId = false)
{
	debug(" insertIntoTable($xquery)");

	$tc = getCache();
	$query_info = $tc->getQueryInfo($xquery);

	$qinfo = new ScQueryInfo($query_info);

	$sql = "select * from " . $qinfo->getQueryTable() . " limit 0";

	$rsMeta = new BDObject();
	$rsMeta->execQuery($sql);
	$sql = "insert into " . $qinfo->getQueryTable() . "(";
	$i = 0;
	$afields = array();
	$avalues = array();
	while ($i < $rsMeta->cantF()) {
		$field = $rsMeta->getFieldName($i);
		$type = $rsMeta->getFieldType($i);

		$valor = "";
		if (isset($xvalues[$field]))
			$valor = $xvalues[$field];

		$valorReq = Request($field);

		if (!$qinfo->isKeyField($field) || ($xincludeId && $valorReq != 0)) {
			array_push($afields, $field);
			if (esCampoStr($type) || esCampoMemo($type)) {
				if (strcmp($valor, "") != 0)
					array_push($avalues, "'" . $valor . "'");
				else
					array_push($avalues, "'" . $valorReq . "'");
			} else	
			if (esCampoFecha($type)) {
				if (strcmp($valor, "") != 0)
					array_push($avalues, $valor);
				else {
					$valorReq = fechaFromRequest($field);
					array_push($avalues, "'" . $valorReq . "'");
				}
			} else {
				if (strcmp($valor, "") != 0)
					array_push($avalues, $valor);
				else {
					if (strcmp($valorReq, "") != 0)
						array_push($avalues, $valorReq);
					else
						array_push($avalues, "null");
				}
			}
		}
		$i++;
	}

	$rsMeta->close();

	$sql .= implode(", ", $afields);
	$sql .= ")\n  values (";
	$sql .= implode(", ", $avalues);
	$sql .= ")";

	return $sql;
}


/**
 * Arma el SQL de insert tomando los valores del request pero teniendo prioridad los que estan en el arreglo de valores. 
 *
 * @param string $xquery
 * @param array $xvalues
 * @return string_sql
 */
function insertIntoTable2($xtable, $xvalues, $xTomaId = false, $xCampoId = "id")
{
	debug(" insertIntoTable2($xtable, " . implode(",", $xvalues) . ")");

	$sql = "select * from " . $xtable . " limit 0";
	$rsMeta = new BDObject();
	$rsMeta->execQuery($sql);
	$sql = "insert into " . $xtable . "(";
	$i = 0;
	$afields = array();
	$avalues = array();
	while ($i < $rsMeta->cantF()) {
		$field = $rsMeta->getFieldName($i);
		$type = $rsMeta->getFieldType($i);
		$valor = "";

		$valorReq = escapeSql(Request($field));

		//valor de arreglo siempre gana, aún vacío
		if (isset($xvalues[$field])) {
			$valor = $xvalues[$field];
			$valorReq = $xvalues[$field];
		}

		if (!sonIguales($field, $xCampoId) || ($xTomaId)) {
			array_push($afields, $field);
			if (esCampoStr($type) || esCampoMemo($type)) {
				if (strcmp($valor, "") != 0)
					array_push($avalues, "'" . escapeSql($valor) . "'");
				else
					array_push($avalues, "'" . $valorReq . "'");
			} else	
			if (esCampoFecha($type)) {
				if (strcmp($valor, "") != 0)
					array_push($avalues, $valor);
				else {
					$valorReq = fechaFromRequest($field);
					array_push($avalues, "'" . $valorReq . "'");
				}
			} else {
				if (strcmp($valor, "") != 0)
					array_push($avalues, $valor);
				else {
					if (strcmp($valorReq, "") != 0)
						array_push($avalues, $valorReq);
					else
						array_push($avalues, "null");
				}
			}
		}
		$i++;
	}

	$rsMeta->close();

	$sql .= implode(", ", $afields);
	$sql .= ") values (";
	$sql .= implode(", ", $avalues);
	$sql .= ")";

	return $sql;
}

/**
 * Arma el SQL del update 
 *
 * @param string $xtable
 * @param array $xvalues
 * @return string_sql
 */
function updateTable($xtable, $xvalues, $xwhere)
{
	$sql = "update $xtable";
	$sql .= " set " . implode_array(" = ", ", ", $xvalues);
	$sql .= " where " . $xwhere;
	return $sql;
}


/**
 * Arma insert into s�lo tomando valores del arreglo, no mira comillas, deben venir tal cual se envían al SQL
 * @param string $xtable
 * @param array $xvalues
 */
function insertIntoTable3($xtable, $xvalues)
{
	$sql = "insert into $xtable(";
	$sql .= implode(", ", array_keys($xvalues)) . ") values (";
	$sql .= implode(", ", array_values($xvalues)) . ")";

	return $sql;
}


function sc3getRsTablasSinQuery()
{
	$sql = "select table_name from information_schema.tables ";
	$sql .= " where table_schema='" . getSession("dbname") . "'";
	$sql .= " and table_name not in (select table_ from sc_querys)";
	$sql .= " order by table_name";
	$rs = new BDObject();
	$rs->execQuery($sql);
	return $rs;
}

function sc3getRsQuerySinTablas()
{
	$sql = "select table_ from sc_querys";
	$sql .= " where table_ not like 'information_schema.tables' ";
	$sql .= " and table_ not in ";
	$sql .= " (select table_name from information_schema.tables where table_schema='" . getSession("dbname") . "')";
	$rs = new BDObject();
	$rs->execQuery($sql);
	return $rs;
}

function sc3deleteQuerySinTablas()
{
	$sql = "delete from sc_querys";
	$sql .= " where table_ not like 'information_schema.tables' ";
	$sql .= " and table_ not in ";
	$sql .= " (select table_name from information_schema.tables where table_schema='" . getSession("dbname") . "')";
	$rs = new BDObject();
	$rs->execQuery($sql);
	$rs->close();
}

/*
Agrega un qry con la tabla dada
*/
function sc3agregarQry($xfieldsquery, $xtabla, $xidmenu, $xdescripcion = "", $xcantcampos = 6, $xicon = "")
{
	debug("sc3agregarQry($xfieldsquery, $xtabla, $xidmenu, $xdescripcion, $xcantcampos, $xicon)");
	$values = array_fill(1, count($xfieldsquery), "null");
	$fields = getFieldsInArray($xtabla, "", $xcantcampos);
	$sql = "insert into sc_querys(";
	$sql .= implode(", ", $xfieldsquery) . ") values(";
	$values[1] = "'q" . str_replace("_", "", $xtabla) . "'";
	$values[2] =  "'" . str_replace("_", " ", $xtabla) . "'";
	$values[3] =  "'" . $xtabla . "'";
	$values[4] = "'" . implode(", ", $fields) . "'";
	$values[5] = "'" . $fields[1] . "'";
	$values[7] = "'" . $fields[0] . "'";

	//insert, update, delete
	$values[11] = "1";
	$values[12] = "1";
	$values[13] = "1";

	//autogestion
	$values[14] = "0";
	//inserta usuario
	$values[15] = "1";
	//borra usuario
	$values[16] = "1";
	$values[19] = $xidmenu;
	//muestra codigo
	$values[21] = "1";
	$sql .= implode(", ", $values) . ")";
	$bd = new BDObject();
	$bd->execQuery($sql);
}

/*
Agrega los querys que falten al menu dado y borra los que sobren
*/
function sc3sincronizar($xidmenu, $xidperfil)
{
	debug("sc3sincronizar($xidmenu, $xidperfil)");
	$rs = sc3getRsTablasSinQuery();
	$fieldsquery = getFieldsInArray("sc_querys", "id");
	while (!$rs->EOF()) {
		$tabla = $rs->getValue("table_name");
		sc3agregarQry($fieldsquery, $tabla, $xidmenu);
		$rs->Next();
	}
	sc3deleteQuerySinTablas();
}


function sc3GrabarAdjunto($xqueryName, $xid, $xfileName)
{
	global $UPLOAD_PATH_SHORT;

	$qinfo = getQueryObj($xqueryName);
	$idquery = $qinfo->getQueryId();

	//Borrar adjunto antes
	$sql = "select * 
			from sc_adjuntos
			where idquery = $idquery and
			iddato = $xid";

	$rs = new BDObject();
	$rs->execQuery($sql);
	if (!$rs->EOF()) {
		$archivoAnt = $rs->getValue("adjunto1");
		if (!esVacio($archivoAnt))
			unlink($UPLOAD_PATH_SHORT . "/" . $archivoAnt);

		$sql = "update sc_adjuntos
				set adjunto1 = '$xfileName'
				where idquery = $idquery and
				iddato = $xid";
	} else {
		$valuesAdj = array();
		$valuesAdj['usuario'] = getCurrentUserLogin();
		$valuesAdj['idquery'] = $idquery;
		$valuesAdj['iddato'] = $xid;
		$valuesAdj['adjunto1'] = $xfileName;
		$sql = insertIntoTable("qscadjuntos", $valuesAdj);
	}

	$bd = new BDObject();
	$bd->execQuery($sql);
}


//-----------------------------------------------CLASES----------------------------------------------------------------------

/**
 * Metadata de un query, Clase que concentra todos los metodos de comportamiento
 **/
class ScQueryInfo
{
	var $mquery_info = array();
	var $mfields_def = array();
	var $mfields_ref = array();
	var $msequence = array();
	var $mfilters = array();

	function __construct($xqueryinfo, $xloadAll = true)
	{
		$this->mquery_info = $xqueryinfo;
		if ($xloadAll) {
			$this->loadFieldsRef();
			$this->loadFieldsDef();
			$this->loadFilters();
		}
	}

	function getQueryId()
	{
		return (int) $this->mquery_info["id"];
	}

	/**
	 * Lista de campos que aparecen en el listado general (sc-selitems.php)
	 */
	function getQueryFields()
	{
		return $this->mquery_info["fields_"];
	}

	function getQueryTable()
	{
		return $this->mquery_info["table_"];
	}

	function getOnInsert()
	{
		return $this->mquery_info["oninsert"];
	}

	function getOnUpdate()
	{
		return $this->mquery_info["onupdate"];
	}

	function getQueryWhere()
	{
		return $this->mquery_info["whereexp"];
	}

	//Retorna si es un query editable o no
	function canEdit()
	{
		if ($this->mquery_info["canedit"] == 1)
			return true;
		else
			return false;
	}

	/**
	 * Retorna si la entidad es debil
	 * @return boolean
	 */
	function isDebil()
	{
		if (((int) $this->mquery_info["debil"]) == 1)
			return true;
		else
			return false;
	}


	function isCacheable()
	{
		if (isset($this->mquery_info["es_cacheable"]) && ($this->mquery_info["es_cacheable"] == 1))
			return true;

		return false;
	}


	function canInsert()
	{
		if ($this->mquery_info["caninsert"] == 1)
			return true;
		return false;
	}

	function canDelete()
	{
		if ($this->mquery_info["candelete"] == 1)
			return true;
		return false;
	}

	/**
	 * Retorna la definicion de campos
	 * @return array
	 */
	function getFieldsDef()
	{
		return $this->mfields_def;
	}

	function getFieldsRef()
	{
		return $this->mfields_ref;
	}

	function getFilters()
	{
		return $this->mfilters;
	}

	function getQueryOrder()
	{
		return $this->mquery_info["order_by"];
	}

	function getKeyField()
	{
		return $this->mquery_info["keyfield_"];
	}

	/**
	 * Inicia una secuencia o cursor
	 *
	 */
	function startCursor()
	{
		$this->msequence = array();
	}

	/**
	 * Agrega un elemento al cursor
	 * @param int $xid
	 */
	function addCursor($xid)
	{
		array_push($this->msequence, $xid);
	}

	/**
	 * Retorna el prox del cursor
	 * @param int $xid
	 */
	function nextCursorId($xid)
	{
		debug("ScQueryInfo::nextCursorId($xid)");

		if (sizeof($this->msequence) == 0)
			return -1;

		$index = array_search($xid, $this->msequence);
		if ($index >= sizeof($this->msequence) - 1)
			return -1;
		return $this->msequence[$index + 1];
	}

	/**
	 * Retorna el previo del cursor
	 * @param int $xid
	 */
	function prevCursorId($xid)
	{
		$index = array_search($xid, $this->msequence);
		if ($index == 0)
			return -1;
		return $this->msequence[$index - 1];
	}

	function hayNextCursor($xid)
	{
		if ($this->nextCursorId($xid) == -1)
			return false;
		return true;
	}

	function hayPrevCursor($xid)
	{
		if ($this->prevCursorId($xid) == -1)
			return false;
		return true;
	}



	/*
	retorna el cambo utilizado para el combo, salvo que sea un FK y en ese caso retorne 
	el campo key
	*/
	function getComboField()
	{
		$comboF = $this->mquery_info["combofield_"];
		if (!array_key_exists($comboF, $this->mfields_ref))
			return $comboF;
		return $this->mquery_info["keyfield_"];
	}

	function getQueryDescription()
	{
		return $this->mquery_info["querydescription"];
	}

	function getQueryIcon()
	{
		$icon = $this->mquery_info["icon"];
		if (sonIguales($icon, ""))
			$icon = "images/table.png";
		return $icon;
	}

	function getQueryName()
	{
		return $this->mquery_info["queryname"];
	}

	/*
	Carga la definicion de campos en un array
	*/
	function loadFieldsDef()
	{
		debug("ScQueryInfo::loadFieldsDef()");

		$idquery = $this->getQueryId();
		$this->mfields_def = array();
		$rsFields = new BDObject();
		$sql = "select * 
				from sc_fields  
				where (idquery = " . $idquery . ")
				order by grupo, id";

		$rsFields->execQuery($sql, true, true);
		$i = 0;
		while (!$rsFields->EOF()) {
			$fieldname = $rsFields->getValue("field_");

			// ANTES: utf8_encode
			$this->mfields_def[$fieldname] = array_map('toUtf8', $rsFields->getRow());
			$rsFields->Next();
		}

		//agrega info de tipo a cada campo
		$str = "select * from " . $this->getQueryTable() . " limit 0";
		$rsFields->execQuery($str);
		$i = 0;
		while ($i < $rsFields->cantF()) {
			$fieldname = $rsFields->getFieldName($i);
			$tipoCampo = $rsFields->getFieldType($i);
			//simula existencia previa de la definicion
			if (!array_key_exists($fieldname, $this->mfields_def)) {
				$this->mfields_def[$fieldname]["show_name"] = $fieldname;
				$this->mfields_def[$fieldname]["field_"] = $fieldname;
				$this->mfields_def[$fieldname]["is_required"] = "0";
				$this->mfields_def[$fieldname]["is_editable"] = "1";
				$this->mfields_def[$fieldname]["encriptado"] = "0";
				$this->mfields_def[$fieldname]["visible"] = "1";
				$this->mfields_def[$fieldname]["ocultar_vacio"] = "0";
				$this->mfields_def[$fieldname]["ancho"] = "2";
			}
			$this->mfields_def[$fieldname]["type"] = $tipoCampo;
			$this->mfields_def[$fieldname]["orientacion"] = $this->getFieldAlignClass($fieldname, $tipoCampo);

			$i++;
		}
		$rsFields->close();
	}

	/**
	 * Carga la definicion de FK en un array
	 */
	function loadFieldsRef()
	{
		debug("ScQueryInfo::loadFieldsRef()");

		$this->mfields_ref = array();
		$idquery = $this->getQueryId();

		$rsFields = new BDObject();
		$sql = "SELECT sc_referencias.*, sc_querys.*
				FROM sc_querys 
						INNER JOIN sc_referencias ON sc_querys.id = sc_referencias.idquery
				WHERE (sc_referencias.idquerymaster = $idquery )
				order by sc_referencias.id";

		$rsFields->execQuery($sql, true, true);
		$alias = 2;
		while (!$rsFields->EOF()) {
			$fieldname = $rsFields->getValue("campo_");
			$aRow = $rsFields->getRow();
			//arma ALAS t2, t3,.... con el que arma el JOIN
			$aRow["alias"] = "t$alias";
			$this->mfields_ref[$fieldname] = array_map('utf8_encode', $aRow);

			//tambien lo guarda con este nombre por si e repiten y un campo referencia a 2 o mas consultas: ej: idpersona en Direcciones
			$mquery = $rsFields->getValue("queryname");
			$this->mfields_ref[$fieldname . "_" . $mquery] = array_map('utf8_encode', $rsFields->getRow());

			$rsFields->Next();
			$alias++;
		}
		$rsFields->close();
	}


	//carga los filtros para futuro uso
	function loadFilters()
	{
		debug("ScQueryInfo::loadFilters()");

		$this->mfilters = array();
		$idquery = $this->getQueryId();

		$rsFilters = new BDObject();
		$sql = "SELECT *, left(descripcion, 20) as descripcion2
				FROM sc_querys_filters
				WHERE idquery = $idquery
				order by descripcion";

		$rsFilters->execQuery($sql, true, true);
		$i = 0;
		while (!$rsFilters->EOF()) {
			$id = $rsFilters->getValue("id");
			$this->mfilters[$id] = $rsFilters->getRow();
			$rsFilters->Next();
		}
		$rsFilters->close();
	}


	//Retorna el nombre a mostrar en el campo
	function getFieldCaption($xfield)
	{
		$caption = "";
		if (isset($this->mfields_def[$xfield]["show_name"]))
			$caption = $this->mfields_def[$xfield]["show_name"];
		if (sonIguales($caption, ""))
			return ucfirst(str_replace("_", " ", $xfield));

		// $caption = str_replace(array("ñ"), array("&nacute;"), $caption);

		return ucfirst($caption);
	}

	/**
	 * Cantidad de decimales en DECIMAL
	 * @param string $xfield
	 * @return int
	 */
	function getFieldAncho($xfield)
	{
		$ancho = 2;
		if (isset($this->mfields_def[$xfield]["ancho"]))
			$ancho = $this->mfields_def[$xfield]["ancho"];
		return $ancho;
	}

	function getFieldHelp($xfield)
	{
		if (!isset($this->mfields_def[$xfield]["field_help"]))
			return "";
		$help = $this->mfields_def[$xfield]["field_help"];
		return $help;
	}

	function getFieldDefaultValue($xfield)
	{
		if (!isset($this->mfields_def[$xfield]["default_value_exp"]))
			return "";
		$help = $this->mfields_def[$xfield]["default_value_exp"];
		return $help;
	}

	/**
	 * Devuelve si un campo es visible, por defecto 1
	 * @param string $xfield
	 * @return boolean
	 */
	function getFieldVisible($xfield)
	{
		if (isset($this->mfields_def[$xfield]["visible"]))
			return $this->mfields_def[$xfield]["visible"];
		return 1;
	}

	/**
	 * Retorna si se oculta el campo en caso de estar vacio.
	 * Por defecto cero
	 */
	function getFieldOcultarVacio($xfield)
	{
		if (isset($this->mfields_def[$xfield]["ocultar_vacio"]))
			return $this->mfields_def[$xfield]["ocultar_vacio"];
		return 0;
	}

	/**
	 * Retorna si es requerido o no.
	 * Por defecto cero
	 */
	function getFieldRequerido($xfield)
	{
		if (isset($this->mfields_def[$xfield]["is_required"]))
			return $this->mfields_def[$xfield]["is_required"];
		return 0;
	}

	/**
	 * Retorna si es editable o no.
	 * Por defecto cero
	 */
	function getFieldEditable($xfield)
	{
		if (isset($this->mfields_def[$xfield]["is_editable"]))
			return $this->mfields_def[$xfield]["is_editable"];
		return 1;
	}

	/**
	 * Retorna si es password o no.
	 * Por defecto cero
	 */
	function getFieldPasswordField($xfield)
	{
		if (isset($this->mfields_def[$xfield]["password_field"]))
			return $this->mfields_def[$xfield]["password_field"];
		return 0;
	}

	/**
	 * Retorna si es tipo archivo o no.
	 * Por defecto cero
	 */
	function getFieldFileField($xfield)
	{
		if (isset($this->mfields_def[$xfield]["file_field"]))
			return $this->mfields_def[$xfield]["file_field"];
		return 0;
	}

	/**
	 * Retorna si es tipo color o no.
	 * Por defecto cero
	 */
	function getFieldColorField($xfield)
	{
		if (isset($this->mfields_def[$xfield]["color_field"]))
			return $this->mfields_def[$xfield]["color_field"];
		return 0;
	}

	/**
	 * Retorna si es rich text o no.
	 * Por defecto cero
	 */
	function getFieldRichText($xfield)
	{
		if (isset($this->mfields_def[$xfield]["rich_text"]))
			return $this->mfields_def[$xfield]["rich_text"];
		return 0;
	}

	/**
	 * Retorna si es tipo google point o no.
	 * Por defecto cero
	 */
	function getFieldGooglePoint($xfield)
	{
		if (isset($this->mfields_def[$xfield]["is_google_point"]))
			return $this->mfields_def[$xfield]["is_google_point"];
		return 0;
	}

	//Retorna el class del campo
	function getFieldClass($xfield, $record)
	{
		$errorR = error_reporting(0);
		$class = $this->mfields_def[$xfield]["class"];
		if (strContiene($class, '='))
			eval($class);
		error_reporting($errorR);

		return $class;
	}


	/**
	 * Retorna la clase que determina la orientacion de un campo
	 * @param string $xnombreCampo
	 * @param string $xtipoCampo
	 * @return string
	 */
	function getFieldAlignClass($xnombreCampo, $xtipoCampo, $xvalor = '')
	{
		//el campo forma parte de un FK, va a la izquiera
		if (isset($this->mfields_ref[$xnombreCampo]))
			return "izquierda";

		if (
			esCampoFecha($xtipoCampo) || esCampoInt($xtipoCampo) || esCampoPorcentaje($xvalor)
			|| esCampoFloat($xtipoCampo) || esCampoConMoneda($xvalor)
		)
			return "derecha";

		if (esCampoColor($xvalor) || esCampoBoleano($xtipoCampo) || startsWith($xvalor, "pdf:"))
			return "centro";

		return "izquierda";
	}

	//Retorna el class del campo
	function getFieldGrupo($xfield)
	{
		$grupo = "";
		if (isset($this->mfields_def[$xfield]["grupo"]))
			$grupo = $this->mfields_def[$xfield]["grupo"];
		return ucfirst($grupo);
	}

	//Retorna el class del campo
	function getFieldSubgrupo($xfield)
	{
		$subgrupo = "";
		if (isset($this->mfields_def[$xfield]["subgrupo"]))
			$subgrupo = $this->mfields_def[$xfield]["subgrupo"];
		return ucfirst($subgrupo);
	}

	/**
	 * Genera la info de los campos de
	 */
	function generateFieldsInfo($xshow = false, $xhideId = true)
	{
		debug("ScQueryInfo::generateFieldsInfo($xshow)");

		$tabla = $this->getQueryTable();
		$str = "select * from $tabla limit 0";

		$rsPpal = new BDObject();
		$rsPpal->execQuery($str);
		$rsIns = new BDObject();
		$i = 0;
		$aCampos = array();

		while ($i < $rsPpal->cantF()) {
			$nombreCampo = $rsPpal->getFieldName($i);

			if (!$this->existsFieldInfo($nombreCampo)) {
				$aCampos[] = $nombreCampo;

				$str = "insert into sc_fields(idquery, field_, show_name) values(";
				$str .= $this->getQueryId();
				$str .= ",'";
				$str .= $nombreCampo;
				$str .= "', '";
				$nombreCampo = str_replace("_", " ", $nombreCampo);
				if ($xhideId && strpos($nombreCampo, "id") !== false && (!sonIguales($nombreCampo, "id")) && strpos($nombreCampo, "id") == 0)
					$nombreCampo = substr($nombreCampo, 2);
				$str .= $nombreCampo;
				$str .= "')";
				$rsIns->execQuery($str);
			}
			$i++;
		}

		//campos existentes en la tabla, toma de la estructura
		$aCamposReales = getFieldsInArray($tabla, "");
		$rsCampos = getRs("select id, field_ 
							from sc_fields
							where idquery = " . $this->getQueryId());

		//elimina campos que no están en la tabla						
		while (!$rsCampos->EOF()) {
			$field = $rsCampos->getValue("field_");
			$idcampo = $rsCampos->getId();
			if (!in_array($field, $aCamposReales)) {
				echo ("<br> eliminando campo $tabla.$field");
				$rsPpal->execQuery("delete from sc_fields where id = $idcampo");
			}

			$rsCampos->Next();
		}
		$rsCampos->close();

		if ($xshow)
			setMensaje("Se crearon los campos: " . implode(", ", $aCampos));

		$rsPpal->close();
	}

	/*
	Retorna si existe la info para este campo
	*/
	function existsFieldInfo($xfield)
	{
		debug("ScQueryInfo::existsFieldInfo($xfield)");
		$idquery = $this->getQueryId();
		$sql = "select * from sc_fields where idquery = $idquery and field_ = '$xfield'";
		$rsq = new BDObject();
		$rsq->execQuery($sql);
		return !$rsq->EOF();
	}


	/***
	arma el sql del tipo:
	
	select t1.*, fk1.esc as fk1_desc, ...
	from TABLE t1
		left join XXX fk1 on (t1.idXXX = fk1.id)
	
	para todos los campos que son FK a otras tablas
	
	@param $xallfields Si son todos los campos o solo los del query
	@param $xcount Si solo va a contar
	@param $xVistaMinima Si solo va a mostar ID, Combo field
	 **/
	function buildSelectLeftJoin($xallfields = false, $xcount = false, $xVistaMinima = false, $xSoloJoin = false)
	{
		debug("ScQueryInfo::buildSelectLeftJoin()");
		if ($xcount)
			$result1 = "select count(*)";
		else		
			if ($xallfields)
			$result1 = "select t1.*";
		else
			//en vista minima (cuando no tiene permisos) solo ve ID y el campo combo
			if ($xVistaMinima)
				$result1 = "select " . $this->setFieldsAlias($this->getKeyField(), "t1") . ", " . $this->setFieldsAlias($this->getKeyField(), "t1") . ", " . $this->setFieldsAlias($this->getComboField(), "t1");
			else
				$result1 = "select " . $this->setFieldsAlias($this->getKeyField(), "t1") . ", " . $this->setFieldsAlias($this->getQueryFields(), "t1");

		$result2 = "\r\n from " . $this->getQueryTable() . " t1 ";

		//busca la info extra (color y notas)
		if (!$xcount) {
			$result2 .= "\r\n left join sc_adjuntos adj ";
			$result2 .= " on (t1." . $this->getKeyField() . " = adj.iddato and adj.idquery = " . $this->getQueryId() . ")";
		}

		$t = 2;
		foreach ($this->mfields_ref as $field => $field_ref) {
			//JIC, valida si el campo esta
			if (array_key_exists($field, $this->mfields_def)) {
				$alias = "t" . $t;
				if (!$xcount)
					$result1 .= ", " . $this->setFieldsAlias($field_ref["combofield_"], $alias) . " as " . $field . "_fk";
				$result2 .= "\r\n left join " . $field_ref["table_"] . " " . $alias;
				$result2 .= " on (t1." . $field . " = " . $alias . "." . $field_ref["keyfield_"] . ")";
				$t++;
			}
		}

		if (!$xcount)
			$result1 .= ", adj.color as color_fk, adj.nota as nota_fk, adj.adjunto1 as adjunto1_fk, adj.usuario as usuario_nota_fk";

		// no interesan los campos
		if ($xSoloJoin)
			return $result2;

		return $result1 . $result2;
	}

	/**
	 * Arma un query para el group by dado
	 *
	 */
	function buildLeftJoinForGroupBy($xg1, $xg2, $xf1)
	{
		debug("ScQueryInfo::buildLeftJoinForGroupBy($xg1, $xg2, $xf1)");

		$sql = $this->buildSelectLeftJoin();
		$asql = explode("from", $sql);
		$asql[0] = "select ";
		$asql[2] = "group by ";
		$aOrder = array();

		$i = 0;
		if (array_key_exists($xg1, $this->mfields_ref)) {
			$findex = $this->getFkIndex($xg1);
			$select[$i] = "ifnull(t" . $findex . "." . $this->mfields_ref[$xg1]["combofield_"] . ", '- sin datos -') as " . $this->getFieldCaption($xg1);
			$aOrder[] = "ifnull(t" . $findex . "." . $this->mfields_ref[$xg1]["combofield_"] . ", '- sin datos -')";
			$groupby[$i] = "t1." . $xg1;
			$i++;
		}

		if (array_key_exists($xg2, $this->mfields_ref)) {
			$findex = $this->getFkIndex($xg2);
			$select[$i] = "ifnull(t" . $findex . "." . $this->mfields_ref[$xg2]["combofield_"] . ", '- sin datos -') as " . $this->getFieldCaption($xg2);
			$aOrder[] = "ifnull(t" . $findex . "." . $this->mfields_ref[$xg2]["combofield_"] . ", '- sin datos -')";
			$groupby[$i] = "t1." . $xg2;
			$i++;
		}
		$select[$i] = $xf1 . " as valor";

		$asql[0] .= implode(", ", $select);
		$asql[1] = " from " . $asql[1];
		$asql[2] .= implode(", ", $groupby);
		$asql[3] = " order by " . implode(", ", $aOrder);
		return implode(" ", $asql);
	}

	/*
	Agrega el alias de la tabla a todos los campos pasados
	Ej: id, nombre, descripcion > t1.id, t1.nombre, t1.descripcion
	*/
	function setFieldsAlias($xfields, $xalias)
	{
		debug("ScQueryInfo::setFieldsAlias($xfields, $xalias)");

		$afields = explode(",", $xfields);
		$result = array();
		foreach ($afields as $i => $field) {
			//si ya tiene alias (ej: t3.email) entonces no agrega alias
			//lo mismo si encuentra un subquery
			if ((strpos(trim($field), ".") == 0) && (strpos(trim($field), "select") == 0))
				array_push($result, $xalias . "." . trim($field));
			else
				array_push($result, trim($field));
		}
		return implode(", ", $result);
	}

	/*
	Dado un query Info, arma un sql con la consulta
	*/
	function getQuerySql($xfilterField, $xfilterValue, $xorderby, $xall, $xcondicion, $xmquery = "", $xmid = "", $xmfield = "", $xfilter = 0)
	{
		debug("ScQueryInfo::getQuerySql($xfilterField, $xfilterValue, $xorderby, $xall, $xcondicion, $xmquery, $xmid, $xmfield, $xfilter)");

		if (!is_array($this->mfields_ref))
			$xfields_ref = array();

		if ($xall == "")
			$xretsql = "select " . $this->getKeyField() . ", " . $this->getQueryFields() . " from " . $this->getQueryTable();
		else
			$xretsql = "select * from " . $this->getQueryTable();

		$wherePart = $this->buildSearch($xfilterField, $xfilterValue, $xcondicion);
		$wherePart = addWhere($wherePart, "$xmfield=$xmid");

		//si hay un filtro, aplica	
		if ($xfilter != 0) {
			//busca el filtro en la cach� interna
			$wherePart = $this->getFilterWherexId($xfilter);
		}

		//agrega los filtros construidos (y quizas la palabra WHERE)
		$xretsql = addFilter($xretsql, $wherePart);

		if ($xorderby != "")
			$xretsql = addOrderby($xretsql, $xorderby);
		else
			$xretsql = addOrderby($xretsql, $this->getQueryOrder());
		return  $xretsql;
	}


	/*
	Arma la busqueda en funcion de los parametros, 
	el fields_ref es para armar la busqueda en los elementos relacionados
	*/
	function buildSearch($xfilterField, $xfilterValue, $xcondicion, $xalias = "", $xuseDefaultWhere = true)
	{
		debug("ScQueryInfo::buildSearch($xfilterField, $xfilterValue, $xcondicion, $xalias)");

		$whereStr = "";
		if ($xuseDefaultWhere)
			$whereStr = $this->getQueryWhere();

		if (strcmp($xfilterValue, "") == 0)
			return $whereStr;

		if (!sonIguales($xfilterField, "")) {
			if (!sonIguales($xalias, ""))
				$campoAlias = $xalias . "." . $xfilterField;
			else
				$campoAlias = $xfilterField;

			//si es un numero, lo busca en el campo
			if (esNumero($xfilterValue))
				$whereStr = addWhere($whereStr, condicionSql($xfilterField, $xcondicion, $xfilterValue, $xalias));
			else {
				//intenta armar la busqueda sobre el FK
				if (array_key_exists($xfilterField, $this->mfields_ref)) {
					$fkfilter = $campoAlias . " in (select " . $this->mfields_ref[$xfilterField]["keyfield_"] . " from ";
					$fkfilter .= $this->mfields_ref[$xfilterField]["table_"] . " where " . $this->mfields_ref[$xfilterField]["combofield_"];
					$fkfilter .= " like '%" . $xfilterValue . "%')";

					$whereStr = addWhere($whereStr, $fkfilter);
				} else
					$whereStr = addWhere($whereStr, condicionSql($campoAlias, $xcondicion, $xfilterValue));
			}
		} else {
			$where2 = "";
			foreach ($this->mfields_def as $field => $fielddef) {
				$nombreCampo = $field;

				if (!sonIguales($xalias, ""))
					$campoAlias = $xalias . "." . $nombreCampo;
				else
					$campoAlias = $nombreCampo;

				$tipo = "";
				if (isset($fielddef["type"]))
					$tipo = $fielddef["type"];

				//No buscaba por valores con decimales
				if (esCampoFloat($tipo) && esNumero($xfilterValue)) {
					$where2 = addWhere($where2, "(" . $campoAlias . "=" . $xfilterValue . ")", "or");
				}

				if (esCampoInt($tipo) || esCampoBoleano($tipo)) {
					if (esNumero($xfilterValue)) {
						$where2 = addWhere($where2, "(" . $campoAlias . "=" . $xfilterValue . ")", "or");
					}

					//intenta armar la busqueda sobre el FK
					if (array_key_exists($nombreCampo, $this->mfields_ref)) {
						$fkfilter = $campoAlias . " in (select " . $this->mfields_ref[$nombreCampo]["keyfield_"] . " from ";
						$fkfilter .= $this->mfields_ref[$nombreCampo]["table_"] . " where " . $this->mfields_ref[$nombreCampo]["combofield_"];
						$fkfilter .= " like '%" . $xfilterValue . "%'";

						//analiza si tiene segundo campo de b�squeda (ej cliente: nombre y nombre de fantasia)
						if (isset($this->mfields_ref[$nombreCampo]["combofield_2"]) && !esVacio($this->mfields_ref[$nombreCampo]["combofield_2"])) {
							$fkfilter .= " or " . $this->mfields_ref[$nombreCampo]["combofield_2"] . " like '%" . $xfilterValue . "%'";
						}

						$fkfilter .= ")";

						$where2 = addWhere($where2, $fkfilter, "or");
					}
				} else {
					//campos de texto
					if (esCampoMemo($tipo) || esCampoStr($tipo)) {
						$where2 = addWhere($where2, $campoAlias . " like '%" . $xfilterValue . "%'", "or");
					}
				}
				//$i++;
			}
			$whereStr = addWhere($whereStr, $where2, "and");
		}

		return $whereStr;
	}

	/**
	 * Dado un query Info, arma un sql con la consulta
	 */
	function getQuerySql2($xfilterField, $xfilterValue, $xorderby, $xall, $xcondicion, $xmquery = "", $xmid = "", $xmfield = "", $xfilter = 0, $xextendedFilter = "", $xallFields = false, $xVistaMinima = false) {

		$result = $this->buildSelectLeftJoin($xallFields, false, $xVistaMinima);

		//si hay filtro no aplica el where original
		$xuseDefaultWhere = true;
		if ($xfilter != 0)
			$xuseDefaultWhere = false;

		//reemplaza espacios por % para mejorar las búsquedas
		if (!esVacio($xfilterValue)) {
			$xfilterValue = str_replace(" ", "%", $xfilterValue);
			//donde hay problemas, quita acentos y reemplaza por _
			$filtraAcentos = getParameterInt("sc3-filtraacentos", 0);
			if ($filtraAcentos == 1)
				$xfilterValue = str_replace(array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'), "%", $xfilterValue);
		}

		$wherePart = $this->buildSearch($xfilterField, $xfilterValue, $xcondicion, "t1", $xuseDefaultWhere);

		//si hay un filtro, aplica (pisando filtro ?)	
		if ($xfilter != 0) {
			if (sonIguales($xfilterValue, ""))
				$wherePart = $this->getFilterWherexId($xfilter);
			else
				$wherePart = addWhere($wherePart, $this->getFilterWherexId($xfilter));
		} else {
			//hay un filtro de un usuario
			if (strlen($xfilter) > 2) {
				if (sonIguales($xfilterValue, ""))
					$wherePart = $xfilter;
				else
					$wherePart = addWhere($wherePart, $xfilter);
			}
		}

		//si hay filtro adicional (con una expresión particular, es usa el $mid en ella)
		if (esVacio($xextendedFilter)) {
			if (!sonIguales($xmfield, ""))
				$wherePart = addWhere($wherePart, "t1.$xmfield=$xmid");
		} else {
			$extendedWhere = str_replace(":$xmfield", $xmid, $xextendedFilter);
			$wherePart = addWhere($wherePart, $extendedWhere);
		}

		//agrega los filtros construidos (y quizas la palabra WHERE)
		$result = addFilter($result, $wherePart);

		if (!sonIguales($xorderby, ""))
			$order = $this->setOrderAlias($xorderby);
		else
			$order = $this->setOrderAlias($this->getQueryOrder());

		$result = addOrderby($result, $order);
		//agrega un orden por el ID para garantizar alg�n orden al usar LIMIT
		$result = addOrderby($result, "t1." . $this->getKeyField() . " desc");

		if ($xallFields)
			$result .= " limit 8000";
		return  $result;
	}

	function getFilterId($xfiltername)
	{
		foreach ($this->mfilters as $id => $filter) {
			if (sonIguales($filter["descripcion"], $xfiltername))
				return (int) $filter["id"];
		}
		return "";
	}


	function getFilterWherexId($xfilterid)
	{
		foreach ($this->mfilters as $id => $filter) {
			if (sonIguales($filter["id"], $xfilterid))
				return $filter["filter"];
		}
		return "";
	}

	function getFilterWhere($xfiltername)
	{
		foreach ($this->mfilters as $id => $filter) {
			if (sonIguales($filter["descripcion"], $xfiltername))
				return $filter["filter"];
		}
		return "";
	}


	/*
	Dado un query Info, arma un sql con la consulta
	*/
	function getRecordCount($xmfield, $xmid)
	{
		debug("ScQueryInfo::getRecordCount($xmfield, $xmid)");

		$sql = "select count(*) as cant from " . $this->getQueryTable();
		$sql .= " where $xmfield=$xmid";
		return  $sql;
	}

	/**
	 * Cuenta los registros que hay, si se aplica el filtro
	 *
	 * @param int $xfilter
	 * @return int
	 */
	function getRecordCountFilter($xfilter)
	{
		debug("ScQueryInfo::getRecordCountFilter($xfilter)");

		$wherePart = $this->getFilterWherexId($xfilter);
		$sql = "select count(*) as cant 
				from " . $this->getQueryTable() . " t1 ";
		$sql = addFilter($sql, $wherePart);

		$rsf = new BDObject();
		$rsf->execQuery($sql);
		$cant = $rsf->getValueInt("cant");
		$rsf->close();
		return $cant;
	}


	/*
	Analiza el alias que le corresponde a cada campo del order by 
	*/
	function setOrderAlias($xorderby)
	{
		if (sonIguales($xorderby, ""))
			return "";

		$aorder = explode(",", $xorderby);
		$result = array();
		foreach ($aorder as $i => $orderPart) {

			//saca los espacios y ademas puede haber un "desc" 
			$orderparts = explode(" ", $orderPart);

			if (sonIguales($orderparts[0], ""))
				array_shift($orderparts);

			//TODO: analizar error
			//print_r($orderparts);

			$field = "";
			if (isset($orderparts[0]))
				$field = trim($orderparts[0]);
			$ascdesc = " ";
			if (count($orderparts) > 1)
				$ascdesc = " " . $orderparts[1];
			if (count($orderparts) > 2)
				$ascdesc .= $orderparts[2];

			//JIC, analiza que exista el campo
			if (array_key_exists($field, $this->mfields_def)) {
				//si tiene FK
				if (array_key_exists($field, $this->mfields_ref)) {
					$index = $this->getFkIndex($field);
					array_push($result, "t" . $index . "." . $this->mfields_ref[$field]["combofield_"] . $ascdesc);
				} else {
					array_push($result, "t1." . $field . " " . $ascdesc);
				}
			} else {
				//el campo no existe en la lista de campos, pero a lo mejor se ha referenciado un campo de una tercera tabla,
				//ej: en mat_computos > order by idobra, idrubro.orden. En este caso se debe buscar el alias de idrubro y traducir a 
				// order by t1.nombre, t2.orden

				$fieldSplit = explode(".", $field);
				$field = $fieldSplit[0];
				if (array_key_exists($field, $this->mfields_def)) {
					//si tiene FK
					if (array_key_exists($field, $this->mfields_ref)) {
						$index = $this->getFkIndex($field);
						array_push($result, "t" . $index . "." . $fieldSplit[1] . $ascdesc);
					}
				}
			}
		}

		return implode(", ", $result);
	}


	/*
	Busca el indice del FK
	*/
	function getFkIndex($xfield)
	{
		$t = 2;
		foreach ($this->mfields_ref as $field => $field_ref) {
			if (sonIguales(trim($field), trim($xfield)))
				return $t;

			//ignora las referencias duplicadas, ej: idpersona, idpersona_qagenda, idpersona_qclientes, ....
			if (array_key_exists($field, $this->mfields_def))
				$t++;
		}
		return 0;
	}

	/*
	Usados por el buscador, retorna cuantos registros hay de una palabra buscada
	*/
	function cantResultados($xcual)
	{
		debug("ScQueryInfo::cantResultados($xcual)");

		$result = $this->buildSelectLeftJoin(false, true);
		$wherePart = $this->buildSearch("", $xcual, "", "t1");
		$sql = addFilter($result, $wherePart);

		$rsPpal = new BDObject();
		$rsPpal->execQuery($sql);

		return (int) $rsPpal->getValue(0);
	}

	/*
	Retorna el nombre de campo que referencia a un query dado
	*/
	function getFieldToFk($xqueryname)
	{
		$result = "";
		foreach ($this->mfields_ref as $field => $field_ref) {
			if (sonIguales(trim($field_ref["queryname"]), trim($xqueryname)))
				return $field;
		}
		return $result;
	}

	/*
	Retorna el nombre del query al que apunta el campo dado
	*/
	function getRelatedQueryToFk($xfield)
	{
		$result = "";
		foreach ($this->mfields_ref as $field => $field_ref) {
			if (sonIguales($field, $xfield))
				return $field_ref["queryname"];
		}
		return $result;
	}

	/*
	Dado un query Info, arma un sql con la consulta
	*/
	function locateRecordSql($xvalue)
	{
		debug("ScQueryInfo::locateRecordSql($xvalue)");
		$sql = "select * from " . $this->getQueryTable();
		$sql .= " where " . $this->getKeyField() . " = " . $xvalue;
		return  $sql;
	}

	/*
	Dado un query Info, arma un sql con la consulta
	*/
	function locateRecordSql2($xvalue)
	{
		debug("ScQueryInfo::locateRecordSql2($xvalue)");
		$result = $this->buildSelectLeftJoin(true);
		$result .= "\r\nwhere t1." . $this->getKeyField() . " = " . $xvalue;
		return  $result;
	}

	function isFileField($xfield, $xvalor = "")
	{
		if (isset($this->mfields_def[$xfield]["file_field"]) && sonIguales($this->mfields_def[$xfield]["file_field"], "1"))
			return true;

		//Menor valor posible: IMG:/ufiles/a.jpg
		if (startsWith($xvalor, "IMG:") && (strlen($xvalor) > 12))
			return true;
		return false;
	}

	function isPasswordField($xfield)
	{
		if (isset($this->mfields_def[$xfield]["password_field"]) && sonIguales($this->mfields_def[$xfield]["password_field"], "1"))
			return true;
		return false;
	}

	function isEncriptedField($xfield)
	{
		if (isset($this->mfields_def[$xfield]["encriptado"]) && sonIguales($this->mfields_def[$xfield]["encriptado"], "1"))
			return true;
		return false;
	}

	/**
	 * Retorna el campo que es foto (archivo)
	 *
	 * @return string
	 */
	function getCampoFoto()
	{
		$result = "";
		foreach ($this->mfields_def as $field => $fielddef) {
			if ($this->isFileField($field))
				$result = $field;
		}
		return $result;
	}

	/**
	 * Retorna un arreglo con todos los campos, propios y ajenos de los querys masters
	 */
	function getListaCamposCompleta($xqueryDesc = "", $xenProfundidad = true, $xfkIndex = 1)
	{
		$ares = array();
		foreach ($this->mfields_def as $field => $fielddef) {
			//analiza si este campo es FL
			$fkindex = $this->getFkIndex($field);
			if ($fkindex == 0)
				array_push($ares, array("t$xfkIndex." . $field, $xqueryDesc . $fielddef["show_name"]));
			else {
				if ($xenProfundidad) {
					$fkquery = $this->mfields_ref[$field]["queryname"];
					$qmaster = getQueryObj($fkquery);
					$arr2 = $qmaster->getListaCamposCompleta($this->getFieldCaption($field) . " - ", false, $fkindex);
					foreach ($arr2 as $i2 => $val2) {
						array_push($ares, $val2);
					}
				}
			}
		}
		return $ares;
	}


	function isColorField($xfield)
	{

		if (isset($this->mfields_def[$xfield]["color_field"]) && sonIguales($this->mfields_def[$xfield]["color_field"], "1"))
			return true;
		return false;
	}


	function getFieldDefault($xfield)
	{
		if (isset($this->mfields_def[$xfield]["default_value_exp"]))
			return $this->mfields_def[$xfield]["default_value_exp"];
		return "";
	}


	function isKeyField($xfield)
	{
		if (strcmp($xfield, $this->getKeyField()) == 0)
			return true;
		else
			return false;
	}

	function isFieldRequired($xnombrecampo)
	{
		if ($this->mfields_def[$xnombrecampo]["is_required"] == 1)
			return true;
		return false;
	}


	function isFieldEditable($xfield)
	{
		if (!sonIguales($this->mfields_def[$xfield]["is_editable"], "1"))
			return false;
		return true;
	}

	function getFieldPrefix($xfield)
	{
		return $this->mfields_def[$xfield]["prefix_field"];
	}

	/**
	 * Arma un preview con los campos principales y un campo abajo del otro
	 * No se usa mas, era la tabla que se armaba en el view item
	 * @param int $xid
	 * @return string
	 */
	function getPreviewTable($xid, $xfield, $xdescripcion)
	{
		if ((int) $xid == 0)
			return "";

		//arma el query para ubicar al dato en cuestion, con los campos que mostraria el sc-selitems.php
		$sql = $this->getQuerySql2($this->getKeyField(), $xid, "", false, "IGU");

		$rsPpal = new BDObject();
		$rsPpal->execQuery($sql);

		$icon = $this->getQueryIcon();

		//si encuentra un campo color, lo usa de fondo de la referencia
		$backgroundColor = "";
		$color = "#010101";
		$objid = $this->getQueryName() . $xfield . $xid;
		$result = "<address onclick=\"expandMaster('det$objid', 'link$objid', '');\" id=\"link$objid\" style=\"cursor:pointer;\">";
		$result .= htmlVisible($xdescripcion);
		$result .= "</address>";
		$result .= "\n<table id=\"det" . $objid . "\" style=\"display:none\" class=\"tabla_detalle\">";

		$result .= "\n<tr>";
		$result .= "<td class=\"td_titulo_detalle\" align=\"center\" colspan=\"2\">";
		$result .= img($icon, "") . " " . $this->getQueryDescription();
		$result .= "</td>";
		$result .= "</tr>";

		$i = 1;
		while ($i < $rsPpal->cantF()) {
			$nombreCampo = $rsPpal->getFieldName($i);
			$record = $rsPpal->getRow();
			$pos = strpos($nombreCampo, "_fk");
			if ($pos === FALSE) {
				$tipoCampo = $rsPpal->getFieldType($i);
				$valorCampo = $rsPpal->getValue($i);
				$etiquetaCampo = $this->getFieldCaption($nombreCampo);

				$result .= "\n<tr>";
				$result .= "<td width=\"30%\" class=\"td_etiqueta_detalle\" align=\"right\">";
				$result .= $etiquetaCampo;
				$result .= ":</td>";
				$result .= "<td width=\"70%\" class=\"td_dato_detalle\">";
				$result .= $this->showField($rsPpal, $nombreCampo, $tipoCampo, true);
				$result .= "</td>";
				$result .= "</tr>";

				//encontró un campo interno que es color, lo usa como color de fondo de la referencia
				if (sonIguales($nombreCampo, "color")) {
					$backgroundColor = $valorCampo;
					$color = getColorByBgColor($backgroundColor);
				}
			}
			$i++;
		}
		$result .= "</table>";

		$result = str_replace("[BG-COLOR]", $backgroundColor, $result);
		$result = str_replace("[COLOR]", $color, $result);
		return $result;
	}

	/**
	 * Arma un preview con los campos principales y un campo abajo del otro
	 *
	 * @param int $xid
	 * @return string
	 */
	function getColorDatoRelacionado($xid)
	{
		if ($xid == 0)
			return ["color" => "", "bgcolor" => ""];

		//Ubica el registro en cuestión por si tiene un color relacionado
		$rsRow = locateRecordId($this->getQueryTable(), $xid, $this->getKeyField());

		//si encuentra un campo color, lo usa de fondo de la referencia
		$backgroundColor = "";
		$color = "";

		$i = 1;
		while ($i < $rsRow->cantF()) {
			$nombreCampo = $rsRow->getFieldName($i);
			$valorCampo = $rsRow->getValue($i);

			//encontró un campo interno que es color, lo usa como color de fondo de la referencia
			if (sonIguales($nombreCampo, "color")) {
				$backgroundColor = $valorCampo;
				$color = getColorByBgColor($backgroundColor);
			}
			$i++;
		}

		$rsRow->close();
		return ["color" => $color, "bgcolor" => $backgroundColor];
	}

	/**
	 * Muestra un campo dado en HTML
	 *
	 * @param BDObject $xrs
	 * @param string $xcampo
	 * @param unknown_type $xtipo
	 * @return string
	 */
	function showField($xrs, $xcampo, $xtipo, $xinpreview = false, $xdecimales = 2)
	{
		global $IMAGE_SIZE;
		$imageSize = "";
		if (getParameterInt("sc3-resize-img-in-view", "1"))
			$imageSize = 200;

		//cuando este inserto lo muestra mas chico
		if ($xinpreview)
			$imageSize = 120;

		$valor = $xrs->getValue($xcampo);

		if (sonIguales($valor, "") || sonIguales($valor, "null"))
			return "";

		if (esCampoStr($xtipo)) {
			if ($this->isColorField($xcampo))
				return "<span title='$valor' style='display:inline-block;padding:3px 3px;border-radius:4px;background-color: $valor;width:60px'>&nbsp;</span>";

			if (sonIguales($xcampo, "clave") || $this->isPasswordField($xcampo))
				return "**********";

			if (strpos($xcampo, "email") !== FALSE) {
				$link = "mailto:" . $valor;
				return href($valor, $link);
			}

			if (strpos($xcampo, "web") !== FALSE)
				return href($valor, $valor, "_blanck");

			//Analiza si el campo es destinado para fotos
			if ($this->isFileField($xcampo)) {
				$file = new HtmlInputFile($xcampo, $valor);
				$file->setWidthPreview($imageSize);
				$file->setReadOnly(true);
				return $file->toHtml();
			}

			//Analiza si es un campo encriptado
			if ($this->isEncriptedField($xcampo)) {
				return href("Valor protegido " . img("images/lock_edit.png", "Campo encriptado"), "javascript:openWindow('sc-showencripted.php?valor=$valor')");
			}

			$strAux = $valor;
			$strAux = str_replace("\n", "<br>", $strAux);
			return htmlVisible($strAux);
		}

		//tipo MEMO
		if (esCampoMemo($xtipo)) {
			$strAux = $valor;
			$strAux = str_replace("\n", "<br>", $strAux);
			$strAux = str_replace("body {", ".boda {", $strAux);
			return htmlVisible($strAux);
		}

		if (esCampoBoleano($xtipo)) {
			return mostrarBooleano($valor);
		}

		if (esCampoFecha($xtipo)) {
			$fechaReg = getdate(toTimestamp($valor));
			return Sc3FechaUtils::formatFecha($fechaReg);
		}

		if (esCampoFloat($xtipo)) {
			return formatFloat($valor, $xdecimales);
		}

		//existe el campo con el FK ?
		$fkvalue = $xrs->getValue($xcampo . "_fk");
		if (!sonIguales($fkvalue, ""))
			return $fkvalue;

		return $valor;
	}


	/**
	 * Retorna la lista de campos para su agrupacion
	 * @param String $xname
	 * @param String $xdefault
	 * @param String $xvalue
	 */
	function getComboCamposAgrupar($xname, $xdefault, $xvalue)
	{
		$combo = new HtmlCombo($xname, $xvalue);
		$combo->add("", $xdefault);

		foreach ($this->mfields_def as $field => $fielddef) {
			/* hasta que funcione
			if (esCampoFecha($fielddef["type"]))
			{
				$combo->add("year($field)", $fielddef["show_name"] . " (año)");
				$combo->add("month($field)", $fielddef["show_name"] . " (mes)");
				$combo->add("day($field)", $fielddef["show_name"] . " (dia)");
			}
			*/
			if (array_key_exists($field, $this->mfields_ref)) {
				$combo->add($field, $fielddef["show_name"]);
			}
			/*
			if (esCampoBoleano($fielddef["type"]))
			{
				$combo->add($field, $fielddef["show_name"]);
			}
			*/
		}

		return $combo->toHtml();
	}

	/**
	 * Lista con que campos se puede promediar o aplicar funci�n agregada
	 *
	 * @param unknown_type $xname
	 * @return unknown
	 */
	function getComboCamposAgrupar2($xname, $xvalor)
	{
		$combo = new HtmlCombo($xname, $xvalor);
		$combo->add("count(*)", "contar elementos");

		foreach ($this->mfields_def as $field => $fielddef) {
			if (isset($fielddef["type"]) && esCampoFloat($fielddef["type"])) {
				$combo->add("sum(ifnull($field, 0))", $fielddef["show_name"] . " (sumar)");
				$combo->add("avg(ifnull($field, 0))", $fielddef["show_name"] . " (promediar)");
				$combo->add("min(ifnull($field, 0))", $fielddef["show_name"] . " (minimo)");
				$combo->add("max(ifnull($field, 0))", $fielddef["show_name"] . " (maximo)");
			}
		}

		return $combo->toHtml();
	}

	//fin clase ScQueryInfo
}

/**
 * Recupera el objeto de la cache
 *
 * @param String $xquery
 * @return ScQueryInfo
 */
function getQueryObj($xquery, $xsaveit = true)
{
	$tc = getCache();
	$query_info = $tc->getQueryInfo($xquery);
	if ($tc->existsQueryObj($xquery))
		$qinfo = $tc->getQueryObj($xquery);
	else {
		$qinfo = new ScQueryInfo($query_info);
		if ($xsaveit)
			$tc->saveQueryObj($xquery, $qinfo);
	}
	if ($xsaveit)
		saveCache($tc);
	return $qinfo;
}

/**
 * Dada una tabla, retorna el query name
 * @param string $xtabla
 * @return string
 */
function getQueryName($xtabla)
{
	return "q" . str_replace("_", "", $xtabla);
}

/**
 * Retorna los inserts para insertar en sc_links como datos relacionados
 * @param string $xquery1
 */
function getSqlLinksInsert($xquery1, $xid1)
{
	$asql = array();

	$i = 1;
	$ref_q = Request("ref_query$i");
	$ref_id = RequestInt("ref_id$i");
	while (!esVacio($ref_q) && $ref_id != 0) {

		$sql = "insert into sc_links (idquery1, id1, idquery2, id2) 
					select q1.id, $xid1, q2.id, $ref_id
					from sc_querys q1, sc_querys q2
					where q1.queryname = '$xquery1' and q2.queryname = '$ref_q'";

		$asql[] = $sql;
		$i++;
		$ref_q = Request("ref_query$i");
		$ref_id = RequestInt("ref_id$i");
	}

	return $asql;
}


/**
 * Retorna el insert para insertar en sc_links y relacionar dos datos
 * @param string $xquery1
 */
function getSqlLinkInsert($xquery1, $xid1, $xquery2, $xid2)
{
	$sql = "insert into sc_links (idquery1, id1, idquery2, id2)
				select q1.id, $xid1, q2.id, $xid2
				from sc_querys q1, sc_querys q2
				where q1.queryname = '$xquery1' and q2.queryname = '$xquery2'";

	return $sql;
}



/**
 * Retorna los objetos relacionados a uno dado
 * @param string $xquery1
 * @param id $xid1
 * @return BDObject
 */
function getSqlLinks1($xquery1, $xid1)
{
	$sql = "select q2.querydescription, q2.queryname, l.id2
			from sc_links l
				inner join sc_querys q1 on (l.idquery1 = q1.id and q1.queryname = '$xquery1')
				inner join sc_querys q2 on (l.idquery2 = q2.id)
			where l.id1 = $xid1
			order by l.id";

	$rslinks = new BDObject();
	$rslinks->execQuery($sql);
	return $rslinks;
}


/**
 * Busca y recupera un objeto relacionado a uno dado
 * @param string $xquery1
 * @param id $xid1
 * @return int
 */
function getLink2Id($xquery1, $xid1, $xquery2)
{
	$sql = "select q2.querydescription, q2.queryname, l.id2
			from sc_links l
				inner join sc_querys q1 on (l.idquery1 = q1.id and q1.queryname = '$xquery1')
				inner join sc_querys q2 on (l.idquery2 = q2.id)
			where l.id1 = $xid1 and q2.queryname = '$xquery2'";

	$rslinks = new BDObject();
	$rslinks->execQuery($sql);
	return $rslinks->getValueInt("id2");
}


/**
 * A un select f1, f2..... from ..........order by ...:
 * Obtiene el select count(*) ......from
 * Quita el ORDER BY, no cambia la cantidad 
 * @param string $xsql
 * @return string
 */
function sc3SqlObtenerCount($xsql, $xtable)
{
	//si hay una sentencia con ORDER BY al final, la quita: no importa el orden al contar
	$aOrders = array();
	$aOrders = explode("order by", $xsql);
	if (count($aOrders) == 2)
		$xsql = $aOrders[0];

	//busca el from principal con el nombre de la tabla
	$aSql = explode("from $xtable ", $xsql);
	$result = array();
	$result[] = "select count(*) as cant_rows";
	$result[] = $aSql[1];

	return implode(" from $xtable ", $result);
}


/**
 * De un query, le agrega el LIMIT 
 * @param string $xsql
 * @param int $xfrom Desde que registro mostrar
 * @param int $xsize TAMAñO del resultado
 * @return string
 */
function sc3SqlObtenerLimitXY($xsql, $xfrom = 1, $xsize = 100)
{
	//si pide del 1 en adelante, el offset es cero
	$offset = $xfrom - 1;
	return $xsql . " LIMIT $offset, $xsize";
}

/**
 * Manda info de metadata en un JSON
 * @param string $xquery
 * @return string[]|unknown[]|NULL[]
 */
function sc3LoadMetadata($xaParams)
{
	$query = $xaParams['query'];
	$qinfo = getQueryObj($query, true);

	$aResult = array();
	$aResult['queryname'] = $query;
	$aResult['querydescription'] = toUtf8($qinfo->getQueryDescription());
	$aResult['table_'] = $qinfo->getQueryTable();
	$aResult['fields_'] = $qinfo->getQueryFields();
	$aResult['combofield_'] = $qinfo->getComboField();
	$aResult['keyfield_'] = $qinfo->getKeyField();
	$aResult['order_by'] = '';
	$aResult['caninsert'] = $qinfo->canInsert();
	$aResult['canedit'] = $qinfo->canEdit();
	$aResult['candelete'] = $qinfo->canDelete();
	$aResult['icon'] = $qinfo->getQueryIcon();
	$aResult['debil'] = $qinfo->isDebil();

	//TODO: array_map('utf8_encode', ....
	$aResult['fieldsdef'] = $qinfo->getFieldsDef();
	$aResult['fieldsref'] = $qinfo->getFieldsRef();

	//arma titulos con la descripcion a mostrar (para facilitar la otra punta)
	$aTitulos = array();
	$aTitulosFields = array();
	$aFields = explode(",", $qinfo->getQueryFields());
	foreach ($aFields as $id => $campo) {
		$campo = trim($campo);
		$titulo = $qinfo->getFieldCaption($campo);
		$aTitulos[] = $titulo;

		if (array_key_exists($campo, $qinfo->getFieldsRef()))
			$campo .= "_fk";
		$aTitulosFields[] = $campo;
	}

	$aResult['titulos'] = array_map('utf8_encode', $aTitulos);
	$aResult['titulos_fields'] = $aTitulosFields;

	return $aResult;
}


function sc3LoadData($xaParams)
{
	global $AJAX_PAGE_SIZE;

	$query = $xaParams['query'];
	$qinfo = getQueryObj($query, true);
	$tabla = $qinfo->getQueryTable();
	$flat = 0;

	if (isset($xaParams["chk"]))
		$chk = $xaParams["chk"];
	else
		$chk = -1;

	//modo flat, no manda el campo _fk, para ser mas chico
	if (isset($xaParams["flat"]))
		$flat = $xaParams["flat"];

	$aResult = array(
		"checksum" => 0,
		"reload" => 1,
		"page_size" => $AJAX_PAGE_SIZE,
		"cant" => 0,
		"status" => "",
		"queryname" => $query,
		"tabla" => $tabla,
		"full_data" => 0,
		"fecha_data" => date("Y-m-d h"),
		"data" => 0
	);

	//determina si necesita un refresh a la version dada por el usuario ($chk)
	$rs = getRs("select table_checksum 
				from sc_querys 
				where table_ = '$tabla'
				limit 1");

	if ($rs->EOF()) {
		$aResult["reload"] = -1;
		$aResult["status"] = "ERROR: No existe la tabla $tabla";
	} else {
		$chk1 = $rs->getValueInt("table_checksum");
		$aResult["checksum"] = $chk1;

		//no hace falta actualizar (chk informado es igual al de la BD)
		if ($chk == $chk1) {
			$aResult["reload"] = 0;
			$aResult["status"] = "No requiere reload";
		} else {
			$aResult["reload"] = 1;
			$aResult["status"] = "Requiere reload";

			$order = $qinfo->getQueryOrder();
			$sql = $qinfo->getQuerySql2("", "", $order, "", "", "", "", "", "", "", false);

			$rs = new BDObject();
			$rs->execQuery($sql, true, true);

			$aData = array();
			$cant = 0;
			while (!$rs->EOF() && $cant <= $AJAX_PAGE_SIZE) {
				$row = $rs->getRow();
				$row = array_map('toUtf8', $row);

				//para reducir rta borra los _fk
				if ($flat == 1) {
					$row2 = array();
					foreach ($row as $key => $valor) {
						if (!strContiene($key, "_fk"))
							$row2[$key] = $valor;
					}
					$row = $row2;
				}

				$aData[] = $row;
				$rs->Next();
				$cant++;
			}

			if ($rs->EOF()) {
				$aResult['full_data'] = 1;
			}
			$aResult['data'] = $aData;
			$aResult['cant'] = $cant;
		}
	}

	return $aResult;
}


/**
 * Manda operaciones posibles para un query y un usuario en un JSON
 * @param string $xquery
 * @return string[]|unknown[]|NULL[]
 */
function sc3LoadQueryOperaciones($xaParams)
{
	$query = $xaParams['query'];

	$qinfo = getQueryObj($query, true);

	$sec = new SecurityManager();
	$rs = $sec->getRsOperacionesQuery($qinfo->getQueryId());

	$aResult = getAjaxResponseArray("sc3LoadQueryOperaciones", 1);
	$aResult['queryname'] = $query;
	$aResult['querydescription'] = toUtf8($qinfo->getQueryDescription());
	$aResult['caninsert'] = $qinfo->canInsert();
	$aResult['canedit'] = $qinfo->canEdit();
	$aResult['candelete'] = $qinfo->canDelete();
	$aResult['icon'] = $qinfo->getQueryIcon();

	while (!$rs->EOF()) {
		$row = array();
		$row['opid'] = $rs->getId();
		$row['nombre'] = $rs->getValue("nombre");
		$row['icon'] = $rs->getValue("icon");
		$row['target'] = $rs->getValue("target");
		$row['url'] = $rs->getValue("url");
		$row['grupal'] = $rs->getValue("grupal");
		$row['emergente'] = $rs->getValue("emergente");
		$row['ayuda'] = $rs->getValueUTF8("ayuda");
		$row['condicion'] = $rs->getValue("condicion");

		$aResult['rs'][] = $row;
		$rs->Next();
	}
	$rs->close();

	return $aResult;
}
