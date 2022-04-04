<!-- por marcos c. (info@sc3.com.ar) -->


<?php 
if ($showFooter)
{
?>
<div id="div3"><br />

	<table width="80%" border="0" cellspacing="1" cellpadding="1" align="center" class="footer_table">
	  <tr>
	    <td width="15%" class="td_dato" align="left">
			<a href="http://www.sc3.com.ar" target="_blank">
				<img src="images/sc3-logo45x45.png"  border="0" title="www.sc3.com.ar - Soluciones IT" alt="Visitar www.sc3.com.ar - Soluciones IT" height="45" />
			</a>
	    </td>
	  
	    <td width="60%" class="td_dato" align="center" valign="bottom">
		    <?php
		    $archivoPhp = $_SERVER['REQUEST_URI'];
		    $ventana = "";
		    $helpOpid = Request("opid");
		    if ($helpOpid > 0)
		    	$ventana = getOpTitle2($helpOpid);
	
		    if (isset($qinfo) && is_object($qinfo))
		    	$ventana .= $qinfo->getQueryDescription();
		    
		    $msg = "Soporte: " . $SITIO . " (" . $ventana . " " . $archivoPhp . ")"; 
		    ?>
			<a href="mailto:info@sc3.com.ar?subject=<?php echo($msg); ?>" title="Obtener ayuda, sugerir un cambio o reportar un error sobre esta pantalla">
				<img src="images/scinfo.gif"  border="0" /> Obtener soporte
			</a>
			<br >
			<?php
			if (isset($start_time))
			{ 
				$finish_time = microtime_float();
				$total_time = round($finish_time - $start_time, 2);
				echo("<small>$total_time segs</small>");
			}
			?>
	    </td>
	    
	    
	    <td  width="15%">
	    	<img src="app/logo.png" alt="<?php echo($SITIO);?>" 
	    			title="<?php echo($SITIO);?>" height="45" />
	    </td>
	  </tr>
	</table>
	
</div>
<?php 
}
?>
