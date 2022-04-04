<?php 
include("funcionesSConsola.php"); 

checkUsuarioLogueado();


$error = "";
$mensaje = "";

$idperfil = RequestInt("mid");


if (enviado())
{
	$dHr = Request("desde");
	$hHr = Request("hasta");
	$horaInicio = $dHr;
	$horaFinal = $hHr;
	$bd = new BDObject();
	$i = 0;
	$aDiasSeleccionados = RequestAll('diaSemana');
	
	while ($i < count($aDiasSeleccionados))
	{
		if ($aDiasSeleccionados[$i] == "1")
		{
			$values = array();
			$values["idperfil"] = $idperfil;
			$values["dia"] = "1";
			$values["dia_semana"] = "lun";
			$values["hr_inicio"] = $horaInicio;
			$values["hr_fin"] = $horaFinal;
			$sql = insertIntoTable2("sc_perfiles_horarios", $values);
	 		$bd->execInsert($sql);
		}
		if ($aDiasSeleccionados[$i] == "2")
		{
			$values = array();
			$values["idperfil"] = $idperfil;
			$values["dia"] = "2";
			$values["dia_semana"] = "mar";
			$values["hr_inicio"] = $horaInicio;
			$values["hr_fin"] = $horaFinal;
			$sql = insertIntoTable2("sc_perfiles_horarios", $values);
	 		$bd->execInsert($sql);
		}
		if ($aDiasSeleccionados[$i] == "3")
		{
			$values = array();
			$values["idperfil"] = $idperfil;
			$values["dia"] = "3";
			$values["dia_semana"] = "mierc";
			$values["hr_inicio"] = $horaInicio;
			$values["hr_fin"] = $horaFinal;
			$sql = insertIntoTable2("sc_perfiles_horarios", $values);
	 		$bd->execInsert($sql);
		}
		if ($aDiasSeleccionados[$i] == "4")
		{
			$values = array();
			$values["idperfil"] = $idperfil;
			$values["dia"] = "4";
			$values["dia_semana"] = "jue";
			$values["hr_inicio"] = $horaInicio;
			$values["hr_fin"] = $horaFinal;
			$sql = insertIntoTable2("sc_perfiles_horarios", $values);
	 		$bd->execInsert($sql);
		}
		if ($aDiasSeleccionados[$i] == "5")
		{
			$values = array();
			$values["idperfil"] = $idperfil;
			$values["dia"] = "5";
			$values["dia_semana"] = "vie";
			$values["hr_inicio"] = $horaInicio;
			$values["hr_fin"] = $horaFinal;
			$sql = insertIntoTable2("sc_perfiles_horarios", $values);
	 		$bd->execInsert($sql);
		}
		if ($aDiasSeleccionados[$i] == "6")
		{
			$values = array();
			$values["idperfil"] = $idperfil;
			$values["dia"] = "6";
			$values["dia_semana"] = "sab";
			$values["hr_inicio"] = $horaInicio;
			$values["hr_fin"] = $horaFinal;
			$sql = insertIntoTable2("sc_perfiles_horarios", $values);
	 		$bd->execInsert($sql);
		}
		if ($aDiasSeleccionados[$i] == "7")
		{
			$values = array();
			$values["idperfil"] = $idperfil;
			$values["dia"] = "7";
			$values["dia_semana"] = "dom";
			$values["hr_inicio"] = $horaInicio;
			$values["hr_fin"] = $horaFinal;
			$sql = insertIntoTable2("sc_perfiles_horarios", $values);
	 		$bd->execInsert($sql);
		}
		$i++;
	}

	setMensaje("Se han creado los horarios");
	goOn();
}

?>
<!doctype html>
<html lang="es">
<head>

<title>Generar horarios - Por SC3</title>

<?php include("include-head.php"); ?>

</head>
<body onload="firstFocus();">
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
            <td width="50" align="center"><?php echo(linkCerrar(0)); ?></td>
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

	<?php
	if ($mensaje != "")
	{
	?>
		<tr>
		<td colspan="2" class="td_resaltado"><?php echo($mensaje); ?></td>
		</tr>
    <?php
	}
	?>

    <tr>
      <td class="td_etiqueta">Perfil: </td>
      <td>
		 <?php 
			$selPerfil = new HtmlSelector("idperfil", "qperfiles", $idperfil);
			$selPerfil->checkMaster();
			echo($selPerfil->toHtml());
		 ?> 
      </td>
    </tr>

    <tr>
      <td class="td_etiqueta">Intervalo horario: </td>
      <td class="td_dato">
        <?php 
		$txtDesde = new HtmlInputText("desde", "8");
		$txtDesde->setTypeInt();
		$txtHasta = new HtmlInputText("hasta", "17");
		$txtHasta->setTypeInt();
        echo($txtDesde->toHtml() . "<br>" . $txtHasta->toHtml());
        ?>
      </td>
    </tr>

	
	<tr>
      <td class="td_etiqueta">D&iacute;as: </td>
      <td class="td_dato" id="dias">
		  <label for="1" style="display: inline-block"> Lunes
			  <input type="checkbox" id="1" checked="true" name="diaSemana[]" style="float:left;" value="1">
		  </label>
		  <label for="2" style="display: inline-block"> Martes
			  <input type="checkbox" id="2" checked="true" name="diaSemana[]" style="float:left;"  value="2">
		  </label>
		  <label for="3" style="display: inline-block"> Miercoles
			  <input type="checkbox" id="3" checked="true" name="diaSemana[]" style="float:left;"  value="3">
		  </label>
		  <label for="4" style="display: inline-block"> Jueves
			  <input type="checkbox" id="4" checked="true" name="diaSemana[]" style="float:left;"  value="4">
		  </label>
		  <label for="5" style="display: inline-block"> Viernes
			  <input type="checkbox" id="5" checked="true" name="diaSemana[]" style="float:left;"  value="5">
		  </label>
		  <br/>
		  <br/>
		  <label for="6" style="display: inline-block"> S&aacute;bado
			  <input type="checkbox" id="6" name="diaSemana[]" style="float:left;"  value="6">
		  </label>
		  <label for="7" style="display: inline-block"> Domingo
			  <input type="checkbox" id="7" name="diaSemana[]" style="float:left;"  value="7">
		  </label>

	  </td>
    </tr>

    <tr>
      <td class="td_etiqueta">&nbsp;</td>
      <td>
        	
        	<input type="hidden" name="enviar" value="1" />
	  
			<?php
			$bOkCancel = new HtmlBotonOkCancel();
			echo($bOkCancel->toHtml());
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

	function cancelarOp()
	{
		//TODO
	}
	
	</script>
</form>

</body>
</html>