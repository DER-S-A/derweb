<?php 
require("funcionesSConsola.php");

?>
<html>
<head>
<title>sc3</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="sc.css" type="text/css">

<?php include("include-head.php"); ?>

</head>
<body onload="firstFocus()">

<form method="post" name="form1" id="form1">
  <?
  $req = new FormValidator();
  ?>
  <table width="750" border="0" align="center" cellpadding="1" cellspacing="2" class="dlg">
    <tr>
      <td colspan="2" align="center" class="td_titulo">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="center"><? echo(getOpTitle(Request("opid"))); ?></td>
            <td width="50" align="center"> <? echo(linkImprimir()); ?> </td>
            <td width="50" align="center"><? echo(linkCerrar(1)); ?></td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td align="right" class="td_etiqueta">Resultado: </td>
      <td class="td_dato">
      
      
        <?php
        $start_time = microtime_float();
        
        $i = 1.0;
        while ($i <= 1000000)
        {
			$f = 70099304.38 / $i;
			$m = 383923.3920 * ($i * 34.0);        	
        	$s = 203942.7132 + 29382932.99 + ($i * 7.0);
        	$r = 2938298.87 - $i;
        	$str = "($i $f $m $s $r)";
        	$i++;
        }
        
        $finish_time = microtime_float();
        $total_time = round($finish_time - $start_time, 3);
        echo("$i operaciones (/, *, +, -) en <b>$total_time</b> segs<br><small>$str</small>");
        
        ?>
        
      </td>
    </tr>

  </table>
  
  <script language="JavaScript" type="text/javascript">
	<!--
	<?
	echo($req->toScript());
	?>
	
	function submitForm() 
	{
		//make sure hidden and iframe values are in sync for all rtes before submitting form
		updateRTEs();
		if (validar())
			document.getElementById('form1').submit();
	}
	
	//Usage: initRTE(imagesPath, includesPath, cssFile, genXHTML, encHTML)
	initRTE("./imagesrte/", "./", "", false);
	//-->
	</script>
</form>

<? include("footer.php"); ?>

</body>
</html>
