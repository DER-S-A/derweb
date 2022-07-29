<? 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$word = Request("word");
$que = $word;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>sc3 - buscar</title>
<link href="sc.css" rel="stylesheet" type="text/css" />
</head>

<body onload="javascript:document.getElementById('word').focus();">

<table width="95%" border="0" cellspacing="5" cellpadding="5" class="dlg" align="center">
  <tr>
    <td height="40" align="center" valign="middle" class="td_dato">
      <form action="sc-help.php" method="get">
        <input type="text" name="word" id="word" size="40" maxlength="80" title="ej: clientes, cheques, presupuestos" value="<? echo($word); ?>" />
        <input type="submit" name="Submit" value="Buscar ayuda" />
      </form>
    </td>
  </tr>
  
  <tr>
    <td align="left" class="td_dato">
    	Buscando ayuda para: <strong><? echo($que); ?></strong>
	</td>
  </tr>

  <?
	if (strlen($que) > 3)
	{
  		//recupera los querys que tiene acceso el usuario
		$sec = new SecurityManager();
		$rs = $sec->getRsQuerysForHelp($que);
		$total = 0;
		while (!$rs->EOF())
		{
			debug("sc-buscar.php: buscando '$cual' en ". $rs->getValue("queryname"));
			$qinfo = getQueryObj($rs->getValue("queryname"), false);
			
			$op = array();
			$op[] = "Ver";
			if ($qinfo->canInsert())
				$op[] = "agregar";
			if ($qinfo->canEdit())
				$op[] = "editar";
			if ($qinfo->canDelete())
				$op[] = "borrar";
				
			$operaciones = " (" . implode(", ", $op) . ")";
				
			$menu = $rs->getValue("menu");
			$tienePermisos = (int) $rs->getValue("tiene_permiso");
			$accesoDirecto = (int) $rs->getValue("acceso_directo");
			if ($tienePermisos)
			{
				if ($accesoDirecto)
				{
					$menu = "<img src=\"img/folder.gif\" border=\"0\" /> " . $menu; 
				}
				$url = new HtmlUrl("sc-selitems.php");
				$url->add("query", $rs->getValue("queryname"));
				$url->add("todesktop", "1");
				$url->add("fstack", "1");
				?>
					<tr>
					  <td align="left" class="td_dato">
					  <br />
					  	<b>Men�:</b> <? echo($menu); ?> / 
					  	<? 
					  	if ($accesoDirecto)
					  	{
					  	?>
						<a href="<? echo($url->toUrl()); ?>">
								<img src="<? echo($qinfo->getQueryIcon()); ?>" border="0" /> <? echo($rs->getValue("querydescription")); ?>
						</a>
						<?
					  	}
					  	else
							echo("<img src=\"" . $qinfo->getQueryIcon() . "\" border=\"0\" /><b> " . $rs->getValue("querydescription") . "</b>");
						echo($operaciones); 
						?>
					  </td>
				  	</tr>
				<?					
			}
			else
			{
			?>
				<tr>
				  <td align="left" class="td_dato">
					<b>Men�</b>: <? echo($menu); ?> / 
							<img src="<? echo($qinfo->getQueryIcon()); ?>" border="0" /> <? echo($rs->getValue("querydescription")); ?>
							<br />
							Ud. no tiene permisos para acceder a esta operacion
				  </td>
			  	</tr>
			<?					
			}
						
			$rs->Next();
		}
		
		//TODO: buscar en operaciones !
		
		
	}
	?>

</table>
<? include("footer.php"); ?>
</body>
</html>
