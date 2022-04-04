<?php 
include("funcionesSConsola.php");
checkUsuarioLogueado();

$error = "";
$server = Request("server");

// ftp_sync - Copy directory and file structure
function ftp_sync($dir, $xdirInicial = "")
{
	global $conn_id;
	
	if (!esVacio($xdirInicial))
	{
		echo("\r\ncambiando en ftp a $xdirInicial...");
		if (ftp_chdir($conn_id, $xdirInicial) == false)
		{
			echo ("Change Dir Failed: $xdirInicial<BR>\r\n");
			return;
		}
	}
	
	//la primera vez no crea carpetas, solo mueve dir en el FTP
	if (($dir != ".") && (esVacio($xdirInicial)))
	{
		echo("\r\n\r\ncambiando en ftp a $dir...");
		
		if (ftp_chdir($conn_id, $dir) == false)
		{
			echo ("Change Dir Failed: $dir<BR>\r\n");
			return;
		}
		
		if (!(is_dir($dir)))
			mkdir($dir);
		chdir ($dir);
	}
		
	$contents = ftp_nlist($conn_id, ".");
	foreach ($contents as $file) 
	{
		if ($file == '.' || $file == '..')
			continue;
			
		if (@ftp_chdir($conn_id, $file))
		{
			ftp_chdir ($conn_id, "..");
			ftp_sync ($file);
		}
		else
		{
			echo("\r\ndescargando $file...");
			ftp_get($conn_id, $file, $file, FTP_BINARY);
			//luego de descargar borra archivo fuente del FTP
			ftp_delete($conn_id, $file);
		}
	}
		
	ftp_chdir ($conn_id, "..");
	chdir ("..");
}


?>
<!doctype html>
<html lang="es">
<head>
<title>Bajar version - por SC3</title>

<?php include("include-head.php"); ?>

</head>
<body>

<form method="post" name="form1" id="form1">

  <?php
  $req = new FormValidator();
  ?>

<table class="dlg">
    <tr>
      <td align="center" colspan="2" class="td_titulo">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="center"><?php echo(getOpTitle(Request("opid"))); ?></td>
            <td width="50" align="center"> <?php echo(linkImprimir()); ?> </td>
            <td width="50" align="center"><?php echo(linkCerrar(0)); ?></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>

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
      <td class="td_etiqueta">Server FTP:</td>
      <td class="td_dato">
        <?php
        $ftp_server = getParameter("sc3-ftpversiones", "ftp.elserver.com");
		$txtserver = new HtmlInputText("servidorftp", $ftp_server);
		echo($txtserver->toHtml());
        ?>
      </td>
    </tr>

    <tr>
      <td class="td_etiqueta">Usuario / clave:</td>
      <td class="td_dato">
        <?php 
		$txtuser = new HtmlInputText("userftp", "");
		$txtuser->setSize(20);
		echo($txtuser->toHtml());
		
		$txtpass = new HtmlInputText("passftp", "");
		$txtpass->setSize(20);
		$txtpass->setTypePassword();
		echo($txtpass->toHtml());
		?>
        
      </td>
    </tr>
    
    <tr>
      <td align="right" valign="top" class="td_etiqueta">Carpeta:<br></td>
      <td class="td_dato">
        <?php
        $carpeta = Request("carpeta");
        if (esVacio($carpeta))
        {
        	$dia = getdate(time());
        	$carpeta = "/versiones/update-" . $dia["year"] . "-" .$dia["mon"] . "-31";
        }
		$txtf = new HtmlInputText("carpeta", $carpeta);
		$txtf->setSize(30);
		echo($txtf->toHtml());
        ?>
      </td>
    </tr>
  
    <?php 
    if (enviado())
    {
    ?>
    <tr>
      <td align="right" valign="top" class="td_etiqueta">Resultado:<br></td>
      <td class="td_dato">
      	<textarea rows="20" cols="60">
    
			<?php 
			if (!sonIguales($SITIO, "Sistemas sc3 (desarrollo)"))
			{
				$ftp_server = Request("servidorftp"); 
				$conn_id = ftp_connect ($ftp_server) 
				    or die("Couldn't connect to $ftp_server"); 
				
				$user = Request("userftp");
				$pass = Request("passftp");
				$login_result = ftp_login($conn_id, $user, $pass); 
				if ((!$conn_id) || (!$login_result)) 
				    die("FTP Connection Failed"); 
				
				ftp_pasv($conn_id, true);
				
				$carpeta = Request("carpeta");
				
				// Use "." if you are in the current directory
				ftp_sync($carpeta, $carpeta);     
				
				ftp_close($conn_id);
			}  
			?>
      	</textarea>
      </td>
    </tr>
    <?php 
    }
    ?>

    <tr>
      <td align="right" class="td_etiqueta">&nbsp;</td>
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

</body>
</html>
