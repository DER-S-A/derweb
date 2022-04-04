<?php 
include("funcionesSConsola.php"); 
checkUsuarioLogueado();

$error = "";
if (enviado())
{

}

$idoperacion = RequestIntMaster("idoperacion", "qoperaciones");
$mquery = Request("mquery");
?>
<!DOCTYPE html>
<html>
<head>

<title>SC3 - Operaciones</title>

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
			<td align="center"><?php echo(getOpTitle(Request("opid"))); ?></td>
			<td width="50"><?php echo(linkCerrar(0)); ?></td>
		</tr>
		</table>
	</td>
	</tr>
	<?php
	if ($error != "")
	{
	?>
		<tr>
		<td colspan="2" class="td_error"><?php echo($error); ?></td>
		</tr>
	<?php
	}
	?>
</table>

	<?php 
	$tab = new HtmlTabs2();

	$sql = "select distinct u.nombre, u.login, p.nombre as perfil
			from sc_usuarios u 
				inner join sc_usuarios_perfiles up on (up.idusuario = u.id)
				inner join sc_perfiles p on (up.idperfil = p.id)
				inner join sc_perfiles_operaciones po on (po.idperfil = p.id)                
			where po.idoperacion = $idoperacion
			order by u.nombre";

	$rs = new BDObject();
	$rs->execQuery($sql);

	$grid = new HtmlGrid($rs);
	$grid->setTitle("Usuarios");
	$tab->agregarSolapa("Usuarios", "fa-user", $grid->toHtml());

	$sql = "select distinct p.nombre as perfil
			from sc_perfiles_operaciones po
				inner join sc_perfiles p on (po.idperfil = p.id)
			where po.idoperacion = $idoperacion
			order by p.nombre";
	
	$rs->execQuery($sql);

	$grid = new HtmlGrid($rs);
	$grid->setTitle("Datos");
	$tab->agregarSolapa("Perfiles", "fa-table", $grid->toHtml());

	echo($tab->toHtml());
	?>

<script language="JavaScript" type="text/javascript">

	<?php
	echo($req->toScript());
	?>
	
	function submitForm() 
	{
		if (validar())
		{
			pleaseWait2();
			document.getElementById('form1').submit();
		}	
	}
	</script>

</form>
<?php include("footer.php"); ?>
</body>
</html>
