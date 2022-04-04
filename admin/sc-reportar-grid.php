<?php 
include("funcionesSConsola.php"); 
checkUsuarioLogueado();

$rquery = Request("query");

//busca la definicion y el obj en la cache ----------------------------
$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
if ($tc->existsQueryObj($rquery))
	$qinfo = $tc->getQueryObj($rquery);
else
{
	$qinfo = new ScQueryInfo($query_info);
	$tc->saveQueryObj($rquery, $qinfo);	
}	
saveCache($tc);
//------fin recuperar qinfo ------------------------------------------

$pdf = new HtmlPdf("Reporte " . $qinfo->getQueryDescription(), "a4", true);	
?>
<!doctype html>
<html lang="es">
<head>
<title>Reportes - por SC3</title>

<?php include("include-head.php"); ?>

<script type="text/javascript" src="<?php echo(sc3CacheButer("scripts/sc-reportar.js")); ?>"></script>

<script language="javascript">

var queryname = '<?php echo($rquery);?>';

</script>

<style>

.fondo-blanco
{
	background-color: #ffffff!important;
}
</style>

</head>
<body onload="firstFocus()" class="fondo-blanco">
<form method="post" name="form1" id="form1">

<?php 

	if (enviado())
	{
		$EOF = "\r\n";
		$TAB = "\t";

		//solo para tomar los campos que tildó
		$sql = $qinfo->buildSelectLeftJoin(true);
		$sql .= " limit 0";
		$rsPpal = getRs($sql);
		
		$campoKey = $qinfo->getKeyField();
		$campoKeyAs = "";
		$aFields = [];
		$i = 0;

		$aReferencias = $qinfo->getFieldsRef();

		//recorre todos los campos y solo muestra los del grupo = $grupoActual
		while ($i < $rsPpal->cantF())
		{
			$nombreCampo = $rsPpal->getFieldName($i);
			$valorTilde = RequestInt("muestra_$nombreCampo");
			if ($valorTilde == 1)
			{
				$tipo = $rsPpal->getFieldType($i);
				//busca el nombre que se muestra del campo
				$nombreFantasia = str_replace(["ñ", ".", ",", "?", "-", "/"], "", $qinfo->getFieldCaption($nombreCampo));
				if (strContiene($nombreFantasia, " "))
					$nombreFantasia = comillasSql($nombreFantasia);

				//si está el campo KEY puede poner la operación para ver el registro
				if (sonIguales($nombreCampo, $campoKey))
					$campoKeyAs = $nombreFantasia;

				if (key_exists($nombreCampo, $aReferencias))
				{
					$aFields[] = $aReferencias[$nombreCampo]["alias"] . "." . $aReferencias[$nombreCampo]["combofield_"] . " as $nombreFantasia";
				}					
				else	
				{
					if (esCampoBoleano($tipo))
					{
						$aFields[] = "case when t1.$nombreCampo = 1 then 'Si' else '' end as $nombreFantasia";	
					}
					else
						$aFields[] = "t1.$nombreCampo as $nombreFantasia";
				}
			}

			$i++;
		}
		$rsPpal->close();


		//arma condicones según los filtros
		$aCondiciones = [];
		$indice = 0;
		while ($indice <= 10)
		{
			$campo = Request("busqcampo_$indice");
			$valor = Request("busqvalor_$indice");
			$condicion = Request("busqcondicion_$indice");

			//hay condicion en este índice
			if (!esVacio($campo) && !esVacio($condicion))
			{
				if (strContiene($condicion, "FECHA"))
				{
					$aFecha = RequestFecha("busqvalor_$indice");
					$valor = $aFecha["fecha_sql_0000"];
				}

				if (sonIguales($condicion, "IGUAL"))
					$aCondiciones[] = "t1.$campo = $valor";
				if (sonIguales($condicion, "DISTINTO"))
					$aCondiciones[] = "t1.$campo <> $valor";
				if (sonIguales($condicion, "MAYOR"))
					$aCondiciones[] = "t1.$campo > $valor";
				if (sonIguales($condicion, "MENOR"))
					$aCondiciones[] = "t1.$campo < $valor";
				if (sonIguales($condicion, "NULL"))
					$aCondiciones[] = "t1.$campo IS NULL";
				if (sonIguales($condicion, "NOT_NULL"))
					$aCondiciones[] = "t1.$campo IS NOT NULL";

				if (sonIguales($condicion, "STR_IGUAL"))
					$aCondiciones[] = "t1.$campo = '$valor'";
				if (sonIguales($condicion, "STR_DISTINTO"))
					$aCondiciones[] = "t1.$campo <> '$valor'";
				if (sonIguales($condicion, "STR_CONTIENE"))
					$aCondiciones[] = "t1.$campo like '%$valor%'";
				if (sonIguales($condicion, "STR_VACIO"))
					$aCondiciones[] = "ifnull(t1.$campo, '') = ''";
				if (sonIguales($condicion, "STR_NO_VACIO"))
					$aCondiciones[] = "ifnull(t1.$campo, '') <> ''";
				if (sonIguales($condicion, "STR_MAYOR"))
					$aCondiciones[] = "t1.$campo > '$valor'";
				if (sonIguales($condicion, "STR_MENOR"))
					$aCondiciones[] = "t1.$campo < '$valor'";

				if (sonIguales($condicion, "FECHA_IGUAL"))
					$aCondiciones[] = "t1.$campo = '$valor'";
				if (sonIguales($condicion, "FECHA_DISTINTO"))
					$aCondiciones[] = "t1.$campo <> '$valor'";
				if (sonIguales($condicion, "FECHA_MAYOR"))
					$aCondiciones[] = "t1.$campo > '$valor'";
				if (sonIguales($condicion, "FECHA_MENOR"))
					$aCondiciones[] = "t1.$campo < '$valor'";
			}

			$indice++;
		}

		// arma SELECT con campos tildados
		$sql = "select " . implode(",$EOF$TAB", $aFields);
		$sql .= $qinfo->buildSelectLeftJoin(false, false, false, true);
		if (count($aCondiciones) > 0)
			$sql .= " {$EOF}where " . implode(" and$EOF$TAB", $aCondiciones);
		$sql .= " {$EOF}limit 500";

		$rs = getRs($sql);
		
		$grid = new HtmlGrid($rs);
		$grid->setWithAll();
	
		if (!esVacio($campoKeyAs))
			$grid->setOperacionVer($rquery, $campoKeyAs, "sel_" . $qinfo->getQueryName());
		
		$grilla = $grid->toHtml();

		$fontSize = 8;
		if (count($aFields) > 8)
			$fontSize = 7;
		$pdf->addGrid($grid, $fontSize);
		echo($pdf->toLink() . "<br>" . $grilla);

		if (esRoot())
		{
			$area = new HtmlDivDatos("SQL");
			$area->setExpandible(true);
			$area->setExpandida(false);
			$area->add("", "<textarea rows='20' cols='65'>$sql</textarea>");
			echo($area->toHtml());
		}
	}

?>

<script type="text/javascript">  

</script>
	
</form>
<?php include("footer.php"); ?>
</body>
</html>