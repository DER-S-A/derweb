<?php
include("funcionesSConsola.php");
include("app-ema.php");
include("sc-zipfile.php");

checkUsuarioLogueado();

$to = getParameter("email-backup", "email-backup@servidor.com");

$error = "";
if (enviado())
{
	$prueba = RequestInt("prueba");
	$cc = RequestStr("cc");

	$idcomplejo = RequestInt("idcomplejo");
	$idfilter = RequestInt("idfilter");
	$rsFilters = locateRecordId("sc_querys_filters", $idfilter);
	
	$smtp_host = requestOrParameter("smtp_host");
	$smtp_user = requestOrParameter("smtp_user");
	$smtp_pass = requestOrParameter("smtp_pass");

	$rs = new BDObject();
	$rs->execQuery($sql);

	$error = emaEnviarEmaiBackup($rs, $idtemplate, $cc, $prueba, 0, $smtp_host, $smtp_user, $smtp_pass);
}


function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) 
{
	$file = $path.$filename;
	$file_size = filesize($file);
	$handle = fopen($file, "r");
	$content = fread($handle, $file_size);
	fclose($handle);
	$content = chunk_split(base64_encode($content));
	$uid = md5(uniqid(time()));
	$name = basename($file);
	$header = "From: ".$from_name." <".$from_mail.">\r\n";
	$header .= "Reply-To: ".$replyto."\r\n";
	$header .= "MIME-Version: 1.0\r\n";
	$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
	$header .= "This is a multi-part message in MIME format.\r\n";
	$header .= "--".$uid."\r\n";
	$header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
	$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
	$header .= $message."\r\n\r\n";
	$header .= "--".$uid."\r\n";
	$header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use diff. types here
	$header .= "Content-Transfer-Encoding: base64\r\n";
	$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
	$header .= $content."\r\n\r\n";
	$header .= "--".$uid."--";
	if (mail($mailto, $subject, "", $header))
		return true;
	else
		return false;
}

#First do a local backup at web host
//ini_set ('error_reporting', E_ALL); //show all errors
function backupDump()
{
	echo "Inicio " . date("r"). "<BR>";
	$F = "backups/backup-" . $SITIO . "-" . Sc3FechaUtils::formatFecha3() . "-" . getClave(10) . ".sql";
	$F = str_replace(" ", "-", $F);
	echo($F . "\n");
	$bd = getParameter("dbname", getSession("dbname"));
	$command = "mysqldump --skip-extended-insert --user=" . $BD_USER . " --password=" . $BD_PASSWORD . " " . $bd . " > ".$F; //| gzip 
	$return_value ='execute failed';
	echo($command . "\n");

	ini_set ("safe_mode", false); 
	system($command, $return_value);
	ini_set ("safe_mode", true); 
	
	echo "MySql Backup code: " . $return_value. "<BR>";
}


function backupDetail()
{
	global $SITIO;
	$file = $SITIO . "-" . Sc3FechaUtils::formatFecha3() . "-" . getClave(20) . ".sql";
	$file = str_replace(" ", "-", $file);
	$file = str_replace("--", "-", $file);
	$file = str_replace("+", "", $file);
	$file = str_replace("(", "", $file);
	$file = str_replace(")", "", $file);
	
	$result = path() . "/backups/$file";
	
	$bd = new BDObject();
	
	$askipFiles = array("sc_fields", "sc_querys", "sc_operaciones", "sc_perfiles_operaciones", "sc_referencias", "sc_perfiles_querys");
	$bd->backupToFile($result, true, $askipFiles);
	
	$result = "./backups/$file";
	return $result;
}

/*
esta estrategia tiene el problema que al hacer el implode del registro con el separador TAB duplica los campos.
Esto sucede porque el arreglo est� doblemente indexado, por orden y por nombre de campo
*/
function backupSelect()
{
	$rsTables = new BDObject();
	$rsTables->execQuery("select distinct table_ from sc_querys order by table_");
	while (!$rsTables->EOF())
	{
		$table = $rsTables->getValue("table_");
		$file = path() . "/backups/$table-$sufix.sql";
		$sql = "select * from $table";
		$bd->execQuery($sql);
		while (!$bd->EOF())
		{
			print_r($bd->getRow());
			$row = implode("\t", $bd->getRow()) . "\n";
	    	error_log($row, 3, $file);
			$bd->Next();
		}
		
		echo("\n$table");
		$bd->execQuery($sql);
		$rsTables->Next();
	}
}

function backupEmail($xfile)
{
	global $SITIO;
	$my_path = null;
	$my_name = "Backup de $SITIO";
	$my_mail = getParameter("email-backup", "marcos.casamayor@gmail.com");
	$my_replyto = $my_mail;
	$my_subject = "Backup de " . $SITIO . " " . Sc3FechaUtils::formatFecha3();
	$my_message = "Backup adjunto\r\n\r\n---\nSoluciones www.sc3.com.ar";
	
	enviarEmail($my_mail, $my_mail, "", $my_subject, $my_message, $xfile, true);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo($titulo); ?></title>

<?php include("include-head.php"); ?>

</head>

<body onload="firstFocus()">
<table width="600" border="0" align="center" cellpadding="1" cellspacing="2" class="dlg">
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
      <td align="center" colspan="2" class="td_titulo2">resultado</td>
	</tr>   

	<tr>
	    <td align="right" class="td_etiqueta" width="200">Archivo:</td>
	    <td class="td_dato" colspan="2" width="400">
	      <?php
			$f = backupDetail();
			$url = new HtmlUrl($f);
			
			$urlz = new HtmlUrl("sc-zipfile.php");
			$urlz->add("content", $f);
			
			$urlz->add("zip", "$f.zip");
			echo(href(img("images/backups.png", "grabar backup") . " backup", $url->toUrl(), "_blanck"));
			//echo(href(img("images/sczip.gif", "grabar compactado") . " backup compactado", $urlz->toUrl(), "_blanck"));
			
			//intenta hacer zip y grabar
			$zip = "$f.zip";
			$zip = str_replace("./", "", $zip);
			$zipfile = new zipfile();
			$zipfile->add_file(implode("", file($f)), str_replace("/", "", str_replace("./", "", $f)));
			 
			echo("<br><br>enviando por email...");
			$handle = fopen($zip, "w");
			if ($handle)
			{
				fwrite($handle, $zipfile->file());
				//unlink($f);
				backupEmail($zip);
			}
			?>
	    </td>
    </tr>
    <tr>
      <td width="200" align="right" class="td_etiqueta">&nbsp;</td>
      <td class="td_dato">
        <input name="enviar" type="hidden" id="enviar" value="1" />
		<input type="button" value="Cancelar" name="bcancelar" class="buscar" accesskey="0" onclick="javascript:document.location = 'hole.php?anterior=0'" />
      </td>
    </tr>
  
</table>

</body>
</html>