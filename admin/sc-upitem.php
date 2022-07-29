<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

//variables del Request
$rorderby = RequestStr("orderby");
$rfilter = RequestStr("filter");
$rquery = RequestStr("query");
$rpalabra = RequestStr("palabra");

//si hay master query
$rmquery = RequestStr("mquery");
$rmid = RequestInt("mid");
$rmfield = RequestStr("mfield");
$modoCatalogo = RequestInt("modocatalog");

//una pila con otro nombre indica que est� en una solapa con su propia pila
$stackname = RequestStr("stackname");

$primerValorStr = "";

if (strcmp($rquery, "") == 0)
	echo ("<h3>Falta parametro: query</h3> Ej: sc-selitems.php<b>?query=propiedades_sin_borrar</b>");

$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
saveCache($tc);

$qinfo = new ScQueryInfo($query_info);

//siempre que se actualiza o edita algun dato se borra la cache
$fileCache = new ScFileCache();
$fileCache->clear();

//Determina si estamos ante una insercion
function esInsert()
{
	if (strcmp(RequestInt("insert"), 1) == 0)
		return true;
	else
		return false;
}

//Determina si debe continuar
function hayGoon()
{
	if (RequestInt("goon") == 1)
		return true;
	else
		return false;
}

/**
 * Compara los campos del RS con los del Request para determinar cuales cambiaron
 */
function getCamposCambiados($xrs, $xqinfo)
{
	$cambios = array();
	$i = 0;
	while ($i < $xrs->cantF()) {
		$campo = $xrs->getFieldName($i);
		$viejo = $xrs->getValue($campo);
		$nuevo = RequestStr($campo);
		$tipo = $xrs->getFieldType($i);
		if (esCampoFecha($tipo)) {
			$nuevo =  RequestInt($campo . "_a");
			$nuevo .= "-" . str_pad(RequestInt($campo . "_m"), 2, "0", STR_PAD_LEFT);
			$nuevo .= "-" . str_pad(RequestInt($campo . "_d"), 2, "0", STR_PAD_LEFT);
			$nuevo .= " " . str_pad(RequestInt($campo . "_h"), 2, "0", STR_PAD_LEFT);
			$nuevo .= ":" . str_pad(RequestInt($campo . "_n"), 2, "0", STR_PAD_LEFT);
			$nuevo .= ":" . str_pad(RequestInt($campo . "_s"), 2, "0", STR_PAD_LEFT);

			if (sonIguales($nuevo, "0-00-00 00:00:00"))
				$nuevo = "";
		}
		if (esCampoFloat($tipo)) {
			$nuevo = formatFloat($nuevo);
			$viejo = formatFloat($viejo);
		}

		if (!sonIguales($viejo, $nuevo) && !$xqinfo->isEncriptedField($campo)) {
			$dif = ": ...";
			if (!esCampoStr($tipo) && !esCampoMemo($tipo))
				$dif = ": $viejo, $nuevo";
			$campo = $xqinfo->getFieldCaption($campo);
			array_push($cambios, $campo . $dif);
		}
		$i++;
	}
	$result = "";
	if (sizeof($cambios) > 0)
		$result =  "<br>" . implode("<br>", $cambios);
	$result = substr($result, 0, 220);
	return $result;
}

$valoresHidden = "";
$str = "select * from " . $qinfo->getQueryTable();
if (!esInsert())
	$str .= " where " . $qinfo->getKeyField() . "=" . RequestInt("registrovalor");
$str .= " limit 1";

$rsPpal = new BDObject();
$rsPpal->execQuery($str);
$oldRecord = $rsPpal->getRow();

$cod_operacion = "EDICION";
$cambios = "";

if (($rsPpal->EOF()) && (!esInsert()))
	echo ("No se ha encontrado el registro a editar.");
else {
	if (!esInsert()) {
		$cambios = getCamposCambiados($rsPpal, $qinfo);
		$strUp = "update " . $qinfo->getQueryTable() . " set ";
		$i = 0;
		$hayCampo = 0;
		while ($i < $rsPpal->cantF()) {
			$campo = $rsPpal->getFieldName($i);
			//No actualiza campos claves ni encriptados
			if (!$qinfo->isKeyField($campo) && !$qinfo->isEncriptedField($campo)) {
				if (esCampoStr($rsPpal->getFieldType($i)) || esCampoMemo($rsPpal->getFieldType($i))) //es un string (o memo)
				{
					$valorStr = RequestStr($campo);
					if ($hayCampo)
						$strUp .= ", ";
					$strUp .= $campo . "='" . $valorStr . "'";
					$hayCampo = 1;
				} else
					//135 es el tipo fecha
					if (esCampoFecha($rsPpal->getFieldType($i))) {
						if ($hayCampo)
							$strUp .= ", ";

						//OJO cuando cambiemos e control
						$aFecha = RequestFecha($campo);

						$ano = RequestInt($campo . "_a");
						if ($ano == 0)
							$strUp .= $campo . " = null";
						else {
							$strUp .= $campo . " = '" . $aFecha["fecha_sql"] . "'";
						}
						$hayCampo = 1;
					} else {
						//si es int controla que no se encuentre vacioo
						if (strcmp(Request($rsPpal->getFieldName($i)), "") != 0) {
							if ($hayCampo)
								$strUp .= ", ";
							$strUp .= $rsPpal->getFieldName($i) . " = " . RequestStr($rsPpal->getFieldName($i));
							$hayCampo = 1;
						} else {
							//caso de actualizar a null
							if ($hayCampo)
								$strUp .= ", ";
							$strUp .= $rsPpal->getFieldName($i) . " = null";
							$hayCampo = 1;
						}
					}
			}
			$i++;
		}
		$strUp .= " where " . $qinfo->getKeyField() . " = " . RequestInt("registrovalor");
	} else {
		//es un insert
		$cod_operacion = "ALTA";
		$strUp = "insert into " . $qinfo->getQueryTable() . "(";
		$i = 0;
		$hayCampo = 0;
		while ($i < $rsPpal->cantF()) {
			if (!$qinfo->isKeyField($rsPpal->getFieldName($i))) {
				if ($hayCampo)
					$strUp .= ", ";
				$strUp .= $rsPpal->getFieldName($i);
				$hayCampo = 1;
			}
			$i++;
		}

		$strUp .= ") values(";

		$i = 0;
		$hayCampo = 0;
		while ($i < $rsPpal->cantF()) {
			$campo = $rsPpal->getFieldName($i);

			$hid = new HtmlHidden($campo, "");
			$hid->valueFromRequest();
			$valoresHidden .= $hid->toHtml();

			if (!$qinfo->isKeyField($campo)) {
				//es un string (o memo)
				if (esCampoStr($rsPpal->getFieldType($i)) || esCampoMemo($rsPpal->getFieldType($i))) {
					if ($hayCampo)
						$strUp .= ", ";
					$valorStr = RequestStr($rsPpal->getFieldName($i));
					$strUp .= "'" . $valorStr . "'";
					$hayCampo = 1;

					if (sonIguales($primerValorStr, ""))
						$primerValorStr = substr($valorStr, 0, 20);
				} else	
				if (esCampoFecha($rsPpal->getFieldType($i))) {
					if ($hayCampo)
						$strUp .= ", ";

					$ano = RequestInt($campo . "_a");
					if ($ano == 0)
						$strUp .= "null";
					else {
						$strUp .= "'" . RequestInt($campo . "_a") . "-" . RequestInt($campo . "_m") . "-" . RequestInt($campo . "_d");
						$strUp .= " " . RequestInt($campo . "_h") . ":" . RequestInt($campo . "_n") . "'";
					}
					$hayCampo = 1;
				} else
					//si es int controla que no se encuentre vacío
					if (strcmp(Request($rsPpal->getFieldName($i)), "") != 0) {
						if ($hayCampo)
							$strUp .= ", ";
						$strUp .= Request($rsPpal->getFieldName($i));
						$hayCampo = 1;
					} else {
						if ($hayCampo)
							$strUp .= ", ";
						$strUp .= "null";
						$hayCampo = 1;
					}
			}
			$i++;
		}
		$strUp .= ")";
	}
	$rsPpal->execQuery($strUp, false, false, false);

	//actualiza checksum
	sc3UpdateTableChecksum($qinfo->getQueryTable(), $rsPpal);

	//guarda en el log	
	$id = RequestInt("registrovalor");
	if (esInsert()) {
		$id = findMaxId($qinfo->getQueryTable(), $qinfo->getKeyField());

		//analiza el tablaOnInsert()
		//carga los modulos app-*.php que encuentre
		sc3LoadModules();
		$triggerFunction = $qinfo->getQueryTable() . "OnInsert";
		if (function_exists($triggerFunction)) {
			eval($triggerFunction . "(" . $id . ");");
		}

		eval($qinfo->getOnInsert());
	} else {
		$id = RequestInt("registrovalor");

		//analiza el tablaOnInsert()
		//carga los modulos app-*.php que encuentre por si encuentra la funcion 
		sc3LoadModules();
		$triggerFunction = $qinfo->getQueryTable() . "OnUpdate";
		if (function_exists($triggerFunction)) {
			eval($triggerFunction . '(' . $id . ', $oldRecord);');
		}

		eval($qinfo->getOnUpdate());
	}

	//marca el ultimo registro visitado
	setSession("sc3-last-$rquery", $id);
	logOp($cod_operacion, $rquery, $id, "ip: " . getRemoteIp() . "$cambios");
}
?>
<!doctype html>
<html lang="es">

<head>

	<title>
		<?php echo ($qinfo->getQueryDescription()); ?>
	</title>

	<?php include("include-head.php"); ?>

</head>

<body onload="document.getElementById('form1').submit()">

	<br><br>

	<div class="info-update">

		<?php
		if (!sonIguales($cambios, ""))
			echo ("<b>Cambios</b><br>$cambios...<br>");

		echo ("<br /><br /><img border='0' src='" . $qinfo->getQueryIcon() . "' /> <b>" . $qinfo->getQueryDescription() . "</b> ha sido actualizada <b>satisfactoriamente</b>.")
		?>

	</div>


	<?php

	$scriptCerrar = "";
	$method = "get";
	$action = "hole.php";
	if ($qinfo->isDebil())
		$action = "sc-showgrid.php";
	else 
	if ($modoCatalogo == 1) {
		$action = "sc-opencatalog.php";
	}
	//abrió emergente de un modal 
	elseif ($modoCatalogo == 2) {
		$scriptCerrar = "<script>window.close();</script>";
	} else {
		//luego de insertar, se ve el registro
		$action = "sc-viewitem.php";
	}

	if (hayGoon())
		$action = "sc-edititem.php";

	if (!esVacio($scriptCerrar))
		echo ($scriptCerrar);
	?>

	<form action="<?php echo ($action); ?>" name="form1" id="form1" method="<?php echo ($method); ?>">

		<input type="hidden" name="insert" value="1" />
		<input type="hidden" name="query" value="<?php echo ($rquery); ?>" />
		<input type="hidden" name="mquery" value="<?php echo ($rmquery); ?>" />
		<input type="hidden" name="stackname" value="<?php echo ($stackname);  ?>" />
		<input type="hidden" name="mid" value="<?php echo ($rmid);  ?>" />
		<input type="hidden" name="mfield" value="<?php echo ($rmfield);  ?>" />
		<input type="hidden" name="registrovalor" value="<?php echo ($id);  ?>" />

		<?php
		if ($modoCatalogo == 1) {
		?>
			<input type="hidden" name="search" value="<?php echo ($primerValorStr);  ?>" />
		<?php
		}
		echo ($valoresHidden);
		?>

	</form>
	<br />
	<br />
	<br />
	<br />

	<?php include("footer.php"); ?>

</body>

</html>