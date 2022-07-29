<?php 
include("funcionesSConsola.php");

//una pila con otro nombre indica que est� en una solapa con su propia pila
$stackname = Request("stackname");

$error = "";
if (strcmp(Request("enviar"), "1") == 0)
{
	goOn($stackname);
}

$mquery = Request("query");
$mid = RequestInt("registrovalor");

//Setea las variables del query actual
$tc = getCache();
$query_info = $tc->getQueryInfo($mquery);

$qinfo = new ScQueryInfo($query_info);

function getRsLogItem($xquery, $xid)
{

	$sql = "SELECT l.id, l.fecha, u.login as usuario, 
				q.querydescription as dato, 
				codigo_operacion as op, l.descripcion
			FROM sc_logs l
				left join sc_usuarios u on (l.idusuario = u.id)
				left join sc_querys q on (q.queryname = '$xquery')
			where l.objeto_operado = '$xquery' and 
				id_operado = $xid
			order by l.fecha
			limit 100";

	$bd = new BDObject();
	$bd->execQuery($sql);
	return $bd;
}

?>
<!doctype html>
<html lang="es">
<head>
<title>sc3 - Auditoria</title>

<?php include("include-head.php"); ?>

</head>
<body onload="firstFocus()">

<form method="post" name="form1" id="form1">
<?php
$req = new FormValidator();
?>
<table class="dlg">
	<tr>
	<td colspan="2" align="center" class="td_titulo">
		<table width="100%" border="0" cellspacing="1" cellpadding="1">
		<tr>
			<td align="center"><img src="images/scauditar.gif" width="16" height="16" />Auditoria de cambios</td>
			<td width="50" align="center"> <?php echo(linkImprimir()); ?> </td>
			<td width="50" align="center"><?php echo(linkCerrar(0, $stackname)); ?></td>
		</tr>
		</table>
	</td>
	</tr>
	
	<tr>
		<td colspan="2" class="td_dato" align="left"><strong>Cambios de</strong>: <?php echo($qinfo->getQueryDescription() . " (" . $mid . ")"); ?></td>
	</tr>
	
	<tr>
	<td colspan="2" class="td_dato">
	<?php
		$rs = getRsLogItem($mquery, $mid);
		$grid = new HtmlGrid($rs);
		$grid->setTitle("Cambios");
		$grid->setMostrarHora(TRUE);
		$grid->setWithAll();
		echo($grid->toHtml());
		?>
	</td>
	</tr>
	<tr>
	<td width="200" class="td_etiqueta">&nbsp;</td>
	<td class="td_dato">
		<input name="enviar" type="hidden" id="enviar" value="1" />
		<input type="button" value="Cancelar" name="bcancelar" class="buscar" accesskey="0" onclick="javascript:document.location = 'hole.php?anterior=0&stackname=<?php echo($stackname); ?>'">
	</td>
	</tr>
</table>
<script language="JavaScript" type="text/javascript">

	<?php
	echo($req->toScript());
	?>
	
	function submitForm() 
	{
		if (validar())
			document.getElementById('form1').submit();
	}
	
	</script>

</form>
<?php include("footer.php"); ?>
</body>
</html>