<?php
include("funcionesSConsola.php");
include("sc-zipfile.php");
include("app-ema.php");

$enviarEmail = RequestInt("enviaremail");

if ($enviarEmail == 0)
	checkUsuarioLogueado();
else 
{
	setSession("sc3_logueado", true);
	setSession("idusuario-logueado", 1);
	setSession("login", "1");
	setSession("sc3_clave", "1");
	setSession("email", "info@sc3.com.ar");
	setSession("es_root", "0");
	setSession("idlocalidad", "0");
	setSession("usuario_punto_venta", "");
}
?>
<!doctype html>
<html lang="es">
<head>

<title>Backup en CSV</title>

<?php include("include-head.php"); ?>

</head>

<body onload="firstFocus()">
<table class="dlg">
  <tr>
    <td colspan="3" align="center" class="td_titulo">
      <table width="100%" border="0" cellspacing="1" cellpadding="1">
        <tr>
          <td align="center"><?php echo(getOpTitle(Request("opid"))); ?></td>
          <td width="40" align="center"> <?php echo(linkImprimir()); ?> </td>
          <td width="40" align="right"><?php echo(linkCerrar(0)); ?></td>
        </tr>
      </table>
    </td>
  </tr>
    
	<tr>
	    <td class="td_etiqueta" width="100">Archivo:</td>
	    <td class="td_dato" colspan="2" width="400">
	      <?php

			$afiles = array();
			$aquerys = array();
			$sql = "SELECT sc_querys.queryname FROM `sc_querys` WHERE sc_querys.en_backup = 1";
			$rs = new BDObject();
			$rs->execQuery2($sql);
			while (!$rs->EOF())
			{
				$aquerys[] = $rs->getValue("queryname");
				$rs->next();
			}
			if ($enviarEmail == 0)
			{
				foreach ($aquerys as $query) 
				{
					$rsq = locateRecordWhere("sc_querys", "queryname = '$query'");
					if (!$rsq->EOF())
					{
						$qinfo = getQueryObj($query);
						$desc = $qinfo->getQueryDescription();
						$table = $qinfo->getQueryTable();
						
						if ($rs->existeTabla($table))
						{					
							$fileName = sc3CsvFilename($desc . "-" . $qinfo->getQueryId());
			
							$sql = $qinfo->getQuerySql2("", "", "", "", "", "", "", "", "", "", true);
							
							$rs->execQuery($sql, false, true);
													
							if ($rs->cant() > 0)
							{
								echo("<br>-<b>$desc</b> en archivo " . basename($fileName) . " ...");
								
								$afiles[] = $fileName;
								//Arma encabezado
								$i = 0;
								$encabezados = array();
								while ($i < $rs->cantF())
								{
									$nombreCampo = $rs->getFieldName($i);
									$encabezados[] = $qinfo->getFieldCaption(str_replace("_fk", "", $nombreCampo));
									$i++;
								}
									
								sc3CsvSaveRs($fileName, $encabezados, $rs, 10000);
							}
						}
						//else 
						//	echo("<br>-<b>$query / $table</b> no existe tabla.");
					}
					else 
						echo("<br>-$query no existe.");
				}
			}
			
			//intenta hacer zip y grabar
			$zipName = getZipFilename();
			$zipfile = new zipfile();

			if ($enviarEmail == 0)
				foreach ($afiles as $csvFile)
				{
					$zipfile->add_file(implode("", file($csvFile)), basename($csvFile));
				}
				
			//path vigente de archivos
			$path = getImagesPath();
			if (getParameterInt("sc3-images-subdir", "1"))
			{
				$path .= Sc3FechaUtils::formatFechaPath();
			}
			
			$fileBackupBd = "backups/backup_$BD_DATABASE.sql.gz";
			if (file_exists($fileBackupBd))
			{
				echo("<br>Agregando <b>base de datos (gzip)</b>...");
				$zipfile->add_file(implode("", file("$fileBackupBd")), "basededatos/basededatos_" . date("Y-m-d") . ".sql.gz");
			}
			else
			{
				$fileBackupBd = "backups/backup_$BD_DATABASE.sql";
				if (file_exists($fileBackupBd))
				{
					echo("<br>Agregando <b>base de datos</b>...");
					$zipfile->add_file(implode("", file("$fileBackupBd")), "basededatos/basededatos_" . date("Y-m-d") . ".sql");
				}
				/*
				else 
					echo("<br>No existe <b>$fileBackupBd</b>...");
				*/
			}
				
			$handle = fopen($zipName, "w");
			if ($handle && $enviarEmail == 0)
			{
				fwrite($handle, $zipfile->file());

				$url = new HtmlUrl('backups/' . basename($zipName));
				echo("<br><br>" . href(img("images/zipicon32.gif", "Descargar backup") . " " . basename($zipName), $url->toUrl(), "_blanck"));
			}
			
			if ($enviarEmail == 1)
			{
				$attach = 'backups/' . basename($zipName);
				$subject = "Backup de $SITIO";
				$emailTo = getParameter("email-backup", "marcos.casamayor@gmail.com");
				$body = "<html>Este es su backup semanal.</html>";
				
				enviarEmail("info@sc3.com.ar", $emailTo, "", $subject, $body, $attach, true);
			}
			
			?>
	    </td>
    </tr>
    <tr>
      <td width="200" class="td_etiqueta">&nbsp;</td>
      <td class="td_dato">
        <input name="enviar" type="hidden" id="enviar" value="1" />
		<input type="button" value="Cancelar" name="bcancelar" class="buscar" accesskey="0" onclick="javascript:document.location = 'hole.php?anterior=0'" />
      </td>
    </tr>
  
</table>

</body>
</html>