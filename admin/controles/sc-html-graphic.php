<?php

class HtmlGraphic
{
	var $id = "";
	var $sql = "";
	var $title = "";
	var $mgtype = "column_3d";

	function __construct($xid, $xtitle)
	{
		$this->id = $xid;
		$this->title = $xtitle;
	}

	function setSql($xsql)
	{
		$this->sql = $xsql;
	}

	function setGraphicType2d()
	{
		$this->mgtype = "column_2d";
	}

	function setGraphicTypePie3d()
	{
		$this->mgtype = "pie_3d";
	}

	function setGraphicTypeFromRequest($xgtypeParam = "gtype")
	{
		$this->mgtype = Request($xgtypeParam);
	}

	function toHtml()
	{
		$url = new HtmlUrl("sc-showgraphic.php");
		$url->add("gtype", $this->mgtype);
		$url->add("sql", saveSessionStr($this->sql));
		$url->add("title", $this->title);

		$result = "\n<!-- HtmlGraphic -->";
		$result .= "<img ";
		$result .= " name=\"" . $this->id . "\"";
		$result .= " id=\"" . $this->id . "\"";
		$result .= " alt=\"" . $this->title . "\"";
		$result .= " src=\"" . $url->toUrl() . "\"";
		$result .= " />\n";
		return $result;
	}

	/**
	 * Guarda el resultado en archivo temporal y retorna nombre
	 */
	function saveTempFile()
	{
		include 'terceros/plchart/v1/class.plchart.php';


		$title = $this->title;
		$gtype = $this->mgtype;
		$etiquetas = array();
		$data = array();

		$rs = new BDObject();
		$rs->execQuery($this->sql);

		$tmpFile = "./tmp/" . strtolower(escapeJsNombreVar($title)) . ".jpg";

		$i = 0;
		$maximo = -100000;
		$minimo =  100000;
		$serie = $rs->getFieldName(0);

		$longEtiquetas = 7;
		if (sonIguales($gtype, "pie_3d")) {
			$longEtiquetas = 15;
		}

		while (!$rs->EOF()) {
			$valory = (float) splitValorConMoneda($rs->getValue(1));
			$etiquetas[$i] = substr($rs->getValue(0), 0, $longEtiquetas);
			$data[$i] = round($valory);
			if ($valory > $maximo)
				$maximo = $valory;
			if ($valory < $minimo)
				$minimo = $valory;

			$rs->Next();
			$i++;
		}

		//con tipo jpg y un nombre de archivo temporal, no manda resultado al browser, escribe archivo
		$demo = new plchart($data, $gtype, 650, 450, 'jpg', $tmpFile);
		$demo->set_title($title, 12, 0, 100);
		if ($minimo > 0)
			$minimo = 0;

		if (sonIguales($gtype, "pie_3d")) {
			$demo->set_scale($etiquetas);
			$demo->set_desc(400);
			$demo->set_graph(10, 30, 350, 350, 0.1);
		} else {
			$demo->set_color('plchart/bg.png', 'line_scatter');
			$escalax = array($minimo, round(($maximo - $minimo) / 4), round(($maximo - $minimo) / 2), round(($maximo - $minimo) * 3 / 4), round($maximo + 1));
			$demo->set_scale($escalax, $etiquetas);
			$demo->set_desc(220);
			$demo->set_graph(10, 30, 550, 400, 0.1);
		}
		$demo->output();

		return $tmpFile;
	}
}


/**
 * Version de graficos con ChartJS
 */
class HtmlGraphic2
{
	var $id = "";
	var $title = "";
	var $mType = "bar";
	var $mStacked = false;
	var $mHideLegend = false;
	var $mLabels = array();
	var $mDatasets = array();

	function __construct($xid, $xtitle)
	{
		$this->id = escapeJsNombreVar($xid);
		$this->title = $xtitle;
	}

	function setLabels($xLabels)
	{
		$this->mLabels = $xLabels;
	}

	function setTypeBar($xStacked = false)
	{
		$this->mType = 'bar';
		$this->mStacked = $xStacked;
	}

	function setTypePie()
	{
		$this->mType = 'pie';
		$this->mStacked = false;
	}

	function setTypeLine()
	{
		$this->mType = 'line';
		$this->mStacked = false;
	}

	function setTypeDona()
	{
		$this->mType = 'doughnut';
		$this->mStacked = false;
	}

	function ocultarLeyenda()
	{
		$this->mHideLegend = true;
	}

	/**
	 * Agrega un conjunto de datos, con nombre
	 * @param $xNombre string
	 * @param $xDataSet array
	 */
	function addDataSet($xNombre, $xDataSet)
	{
		$this->mDatasets[] = array("nombre" => $xNombre, "data" => $xDataSet);
	}

	function setDataSets($xaDataSets)
	{
		foreach ($xaDataSets as $i => $ds) {
			$serie = str_replace("_", " ", $i);

			$this->addDataSet($serie, $ds);
		}
	}

	/**
	 * Arma canvas y dibuja el gráfico
	 */
	function toHtml()
	{
		$aColors = array(
			'window.chartColors.green',
			'window.chartColors.yellow',
			'window.chartColors.red',
			'window.chartColors.blue',
			'window.chartColors.orange',
			'window.chartColors.grey',
			'window.chartColors.black',
			'window.chartColors.white',
			'window.chartColors.purple',
			'window.chartColors.green2',
			'window.chartColors.grey2',
			'window.chartColors.blue2',
			'window.chartColors.white',
			'window.chartColors.green2',
			'window.chartColors.grey',
			'window.chartColors.blue2',
			'window.chartColors.green2',
			'window.chartColors.grey',
			'window.chartColors.blue2'
		);

		$result = '<div class="div-grafico">
						<canvas id="' . $this->id .  '">
						</canvas>
				</div> ';

		$result .= '<script>
					window.chartColors = {
								red: \'rgb(231, 76, 60)\',
								orange: \'rgb(243, 156, 18)\',
								yellow: \'rgb(241, 196, 15)\',
								green: \'rgb(26, 188, 156)\',
								blue: \'rgb(41, 128, 185)\',
								purple: \'rgb(155, 89, 182)\',
								grey: \'rgb(149, 165, 166)\',
								white: \'rgb(255, 255, 255)\',
								black: \'rgb(53, 59, 72)\',
								green2: \'rgb(186, 220, 88)\',
								grey2: \'rgb(83, 92, 104)\',
								blue2: \'rgb(64, 115, 158)\'
							};

					var barChartData = {
						labels: [\'' . implode("', '", $this->mLabels) . '\'], 
						datasets: [{ ';

		foreach ($this->mDatasets as $i => $dataset) {
			$label = $dataset["nombre"];
			$data =  $dataset["data"];

			if ($i > 0)
				$result .= "},\r\n {";

			//en todos los gráficos, cada serie con su color. En las donas elije de a uno
			$colorFondo = "backgroundColor: " . $aColors[$i] . ",";
			if (sonIguales($this->mType, "doughnut") || sonIguales($this->mType, "pie"))
				$colorFondo = "backgroundColor: [" . implode(", ", $aColors) . "],";

			$result .= " label: '$label', 
						fill: false,
						$colorFondo
						borderColor: window.chartColors.grey,
						borderWidth: 0, 
						data: [" . implode(", ", $data) . "]";
		}

		$result .= "}]};";
		$result .= "\r\n";

		$scale = "";
		if ($this->mStacked) {
			$scale = "scales: {
							xAxes: [{
								stacked: true,
								}],
							yAxes: [{
								stacked: true
								}]},";
		}

		$legend = "legend: {position: 'top'	}";

		if ($this->mHideLegend)	
			$legend = " legend: {display: false}";

		$result .= "\r\n";
		$result .= ' var ctx' . $this->id . ' = document.getElementById("' . $this->id . '").getContext("2d"); ';
		$result .= "\r\n window.myBar" . $this->id . " = new Chart(ctx" . $this->id . ", {
							type: '" . $this->mType . "',
							data: barChartData,
							options: {
								responsive: true,
								$scale
								$legend,
								title: {
									display: true,
									text: '" . $this->title . "'
								}
							}
						});
					
					</script>";

		return $result;
	}
}
