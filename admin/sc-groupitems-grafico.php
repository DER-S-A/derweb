<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

$rquery = RequestSafe("query");
$f1 = Request("f1");
$field1 = RequestSafe("field1");
$field2 = RequestSafe("field2");

if (!esVacio($rquery))
{
    $query_info = Array();
    $tc = getCache();
    $query_info = $tc->getQueryInfo($rquery);
    saveCache($tc);

    $qinfo = new ScQueryInfo($query_info);

}

$sql = "";
if (enviado())
{
	$sql = $qinfo->buildLeftJoinForGroupBy(RequestSafe("field1"), RequestSafe("field2"), $f1);	
}
?>
<!doctype html>
<html lang="es">
<head>
<title>SC3 - analisis de <?php echo($qinfo->getQueryDescription()); ?></title>

<?php include("include-head.php"); ?>

<script language="javascript">

	function buscar()
	{
		f = document.getElementById('form1');
		palabra = document.getElementById('palabra');
		f.submit();
	}

function generarGrafico()
{
	var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var color = Chart.helpers.color;
	var barChartData = {
		labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
		datasets: [{
			label: 'Dataset 1',
			backgroundColor: color("red").alpha(0.5).rgbString(),
			borderColor: "red",
			borderWidth: 1,
			data: [1, 2, 4, 55, 8, 19, 5, 6, 10				]
			}]

		};

		var ctx = document.getElementById('grafico').getContext('2d');
		window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Chart.js Bar Chart'
					}
				}
			});
		


}

</script>

</head>
<body onload="generarGrafico()">

<?php 
if (enviado())
{  
?>  
	<canvas id="grafico">


	</canvas>	

	<div class="div-grilla">
		<?php 
		$rs = new BDObject();
		$rs->execQuery($sql);
		$grid = new HtmlGrid($rs);
		$grid->setWithAll();
		if (!esVacio($field2))
			$grid->setAgrupadores(0);

		$grid->setTotalizar(array("valor"));
		$grid->setTitle("Anl&aacute;lisis de " . $qinfo->getQueryDescription());
		echo($grid->toHtml());
		?>
	</div>

<?php 
} 
?>

</form>

<?php include("footer.php"); ?>

</body>
</html>