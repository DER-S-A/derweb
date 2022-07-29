<!-- por marcos c. (info@sc3.com.ar) -->

<?php
$total_time = -1;
if (isset($start_time))
{ 
	$finish_time = microtime_float();
	$total_time = round($finish_time - $start_time, 2);
}
?>

<script type="text/javascript">

var t = <?php echo($total_time); ?>;
var hrMin = ' (<?php echo(date("s")); ?>)';
hrMin = '';

divPer = document.getElementById("divperformance");
if (divPer != null && t >= 0)
{
	divPer.innerHTML = htmlImgFa('fa-server fa-lg fa-fw', 'velocidad del servidor') + ' ' + t + ' seg' + hrMin;
}
  
</script>

<div class="boxejecutando" id="divejecutando" style="visibility: hidden;">
	
	<div class="w3-center">
    	<i class="fa fa-cog fa-spin fa-4x fa-fw w3-text-blue"></i>
    	procesando...
	</div>
	
</div>
