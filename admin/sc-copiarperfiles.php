<?php 
include("funcionesSConsola.php"); 
checkUsuarioLogueado();

$error = "";
$idorigen = RequestIntMaster("idusuario", "sc_usuarios");

if (enviado())
{
	$contador = 0;
	$iddestino = Request("usuario");
	if (!empty($iddestino)){
		$nombreOrigen = Request("idusuariodesc");
		$bd = new BDObject();

		$sql = "SELECT nombre
				FROM sc_usuarios
				WHERE id = $iddestino";
		$bd->execQuery($sql);
		$nombreDestino = $bd->getValue("nombre");
		$sql = "";

		$sql = "SELECT idperfil
				FROM sc_usuarios_perfiles
				WHERE idusuario = $idorigen";
		$bd->execQuery($sql);

		$rs = new BDObject();

		while (!$bd->EOF()){

			$idperfil = $bd->getValueInt("idperfil");
			$sql = "SELECT idperfil AS encontrado
					FROM sc_usuarios_perfiles
					WHERE idusuario = $iddestino AND 
						idperfil = $idperfil";
			$rs->execQuery($sql);
			$perfilEncontrado = $rs->getValueInt("encontrado");

			if ($perfilEncontrado == 0) {
				$sql = "INSERT INTO sc_usuarios_perfiles(idusuario, idperfil)
						VALUES ($iddestino, $idperfil)";
				$rs->execQuery($sql);
				$contador = $contador + $rs->getAffectedRows();
			}	
			$bd->next();
		}
		
		if ($contador == 0) {
			setMensaje("El usuario $nombreDestino ya tenía todos los perfiles del usuario $nombreOrigen");
		} else if ($contador == 1){
			setMensaje("Se copio $contador perfil");
		} else {
			setMensaje("Se copiaron $contador perfiles");
		}		
		goOn();
	}
}

?>
<!DOCTYPE html>
<html>
<head>

<title>SC3 - Copiar perfil</title>

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
						<td align="center"><img src="images/lock_edit.png" alt="Copiar perfil" />  Copiar perfiles </td>
						<td width="50" align="center"> <?php echo(linkImprimir()); ?> </td>
						<td width="50" align="center"><?php echo(linkCerrar()); ?></td>
					</tr>
				</table>
			</td>
		</tr>
	<?php
		if ($error != "")
		{
		?>
			<tr>
				<td colspan="2" align="left" class="td_error"><?php echo($error); ?></td>
			</tr>
			<?php
			}
			?>  
		<tr>
			<td class="td_etiqueta">Usuario origen: </td>
			<td class="td_dato">
				<?php
					$selUsr = new HtmlSelector("idusuario", "sc_usuarios", $idorigen);
					$selUsr->setReadOnly(true); 
					echo($selUsr->toHtml());
				?>
			</td>
		</tr>
		
		<tr>
			<td class="td_etiqueta">Usuario destino: </td>
			<td class="td_dato">
				<?php 
					$bd = new BDObject();
					$sql = "SELECT id, nombre, habilitado
									FROM sc_usuarios
									WHERE id <> $idorigen AND habilitado = 1
									order by nombre";
					$bd->execQuery($sql);
					$cboUsuario = new HtmlCombo("usuario", "", "");
					$cboUsuario->addSeleccione();
					$cboUsuario->cargarRs($bd, "id", "nombre");
					echo($cboUsuario->toHtml()); 
					$req->add("usuario", "Usuario destino");
				?>
		</td>
		</tr>
		
		<tr>
			<td class="td_etiqueta">&nbsp;</td>
			<td class="td_dato">
				<?php 
					$bok = new HtmlBotonOkCancel();      	
					echo($bok->toHtml());
				?>
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