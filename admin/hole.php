<?php
require("funcionesSConsola.php");
include("sc-cal.php");

checkUsuarioLogueado();

//analiza si hay anterior porque si no lo hay no usa la cache
//esto es porque al invocar al goOn() no se manda el parametro, por lo que viene de presionar el [ Aceptar ]
$ant = Request("anterior");
$useCache = true;
if (sonIguales($ant, "")) {
	$useCache = false;
}

$stackname = Request("stackname");
$anterior = RequestInt("anterior");
//nuevos para los reportes
$mid = RequestInt("mid");
$opid = RequestInt("opid");
$file = Request("file");
$mensaje = getMensaje();
$warning = getWarning();
$loc = "";

$fileCache = new ScFileCache();

//Si hay opid es que tiene que ejecutar una operacion, no desapila
if (($opid == 0) && esVacio($mensaje) && esVacio($warning) && esVacio($file)) {
	//recupera tope de pila,
	$stack = getStack($stackname);
	$loc = $stack->getUrlTope();
	$stackKey = $stack->getKeyTope();

	//si encuentra el archivo grabado en archivo, lo retorna
	$cachefile = $fileCache->fileExists($stack->getCount(), $stackname);
	if (!sonIguales($cachefile, "") && ($anterior != 1) && $useCache) {
		//echo("Ant: $ant");
		readfile($cachefile);
		exit;
	}

	//MC: DUDA !!! estaba sin el IF
	if ($anterior == 1)
		$stack->desapilar();

	if ($anterior == 1) {
		$loc = $stack->getUrlTope();
		$stackKey = $stack->getKeyTope();
	}
	saveStack($stack, $stackname);
}


//deriva si hay algo en el stack y no hay pedido de ejecutar una operacion
if ((strcmp($loc, "") != 0) && ($opid == 0) &&
	esVacio($mensaje) &&
	esVacio($warning) &&
	esVacio($file)
) {

	$cachefile = $fileCache->fileExists($stack->getCount(), $stackname);
	if (!sonIguales($cachefile, "") && $useCache) {
		readfile($cachefile);
		exit;
	} else {
		header("Location:" . $loc);
		exit;
	}
}


//recupera si hay Calendar y eventos del día
$aCalInfo = calInicializar();
$hayCalendar = $aCalInfo["hay_calendar"];
$cantEventos = $aCalInfo["eventos_hoy"];
?>
<!DOCTYPE html>
<html>

<head>

	<title>SC3 Sistemas</title>

	<?php include("include-head.php"); ?>

	<script type="text/javascript">
		function focusBuscar() {
			word = document.getElementById('word');
			if (word != null)
				word.focus();
		}
	</script>


	<style type="">

		.tabla_menu_escritorio 
{ 
	padding: 2px;
	font-variant: small-caps;
	font-weight: bold;
}

.tabla_escritorio
{
	width: 90%;
	font-size: 14px;
	border: 0px; 
	padding: 5px;
}

.tabla_escritorio td
{
	padding: 8px;
}

.td_ultimos
{
	padding: 6px;
	text-align: left;
}

.divDeskContainer {
	margin: 10px;
	padding: 10px;
}

/* Cada div del escritorio */
.divDesk
{
	float: left;
	width: 48%; 
	margin: 6px;
	border-radius: 3px;
	background-color:#ffffff!important;
	opacity: 0.90;
	padding: 8px;
	text-align: center;
}

@media screen and (max-width: 850px) 
{
	.divDesk
	{
		width: 99%;
	}

	.escritorio-nota
	{
		max-width: 100%;
	}
}

@media screen and (min-width: 1600px) 
{
	.divDesk
	{
		width: 31%;
	}
}

.iconoDesk
{
	color:#cb769e;
}

.escritorio-op
{
	min-height: 500px;
	width: 100%;
}

</style>
</head>

<?php
$nro = date("d");

if (!isset($DESARROLLADOR_NOMBRE))
	$DESARROLLADOR_NOMBRE = "SC3 Sistemas";
if (!isset($DESARROLLADOR_WEB_SITE))
	$DESARROLLADOR_WEB_SITE = "https://www.sc3.com.ar";
if (!isset($DESARROLLADOR_LOGO))
	$DESARROLLADOR_LOGO = "images/sc3-logo.png";
?>

<body background="<?php echo ($DESARROLLADOR_WEB_SITE) ?>/fotos/f<?php echo ($nro); ?>.jpg" onload="javascript:focusBuscar();">

	<?php
	if (($opid == 0) && esVacio($mensaje) && esVacio($warning) && esVacio($file)) {
	?>
		<div class="historial-horizontal">
			<?php
			//agrega las filas de tabla con los ultimos querys realizados
			$desk = getEscritorio();
			echo ($desk->showQuerys(false, false, "boton", "", $stackname));
			?>
		</div>

		<div class="divDeskContainer">

			<div class="divDesk w3-center">

				<form action="sc-buscar.php" method="get">
					<input type="text" name="word" id="word" size="25" maxlength="60" placeholder="ej: gonzales, cliente gonzales, cheque 2398765, etc." />
					<input type="submit" name="Submit" value="Buscar" class="boton-buscar" />
				</form>

			</div>

			<?php
			// ------------------------ FAVORITOS --------------------------------------------
			$desk = getEscritorio();
			$sec = new SecurityManager();
			$rsTablas = $sec->getRsFavoritos();
			if (!$rsTablas->EOF()) {
				echo ('<div class="divDesk">
					<div class="historial-vertical">');

				//si hay Calendar instalado muestra acceso y cantidad de eventos del día	
				if ($hayCalendar) {

					$spanCant = "";
					if ($cantEventos > 0)
						$spanCant = span($cantEventos, "span-cantidad");

					echo ("<div class=\"boton\">
							<a href='sc-citanova.php' target='calendar' class='boton-fa'>
								<i class='fa fa-calendar fa-lg'> </i> <br>Calendar<br>$spanCant
							</a>
						</div>");
				}

				echo ($desk->showFavoritos($rsTablas));

				echo ('</div></div>');
			}
			$rsTablas->close();

			// -------------------------- OPERACIONES PANTALLA INICIAL ------------------------
			$rsIniciales = $sec->getRsOperacionesPantallaInicial();
			if (!$rsIniciales->EOF()) {
				while (!$rsIniciales->EOF()) {
					$url = new HtmlUrl($rsIniciales->getValue("url"));
					$url->add("pantallainicial", 1);
					$urlfull = $url->toUrl();
					echo ("<div class=\"divDesk\">
						<iframe src=\"$urlfull\" frameborder=\"0\" class=\"escritorio-op\"></iframe>
					</div>");
					$rsIniciales->Next();
				}
			}
			$rsIniciales->close();

			// ------------------------------ NOTAS --------------------------------------------
			$rsNotas = $sec->getRsMisNotas();
			if (!$rsNotas->EOF()) {
				echo ('<div class="divDesk">
					<div class="escritorio-notas">');
				while (!$rsNotas->EOF()) {
					$notaColor = $rsNotas->getValue("color");
					$notaNota = $rsNotas->getValue("nota");
					$notaUser = $rsNotas->getValue("login");
					$notaImg =  $rsNotas->getValue("icon");
					$notaUrl = "<div class=\"escritorio-nota\" style=\"background-color:$notaColor\">
								<a href=\"sc-viewitem.php?query=" . $rsNotas->getValue("queryname") . "&registrovalor=" . $rsNotas->getValue("iddato") . "&fstack=1\">
									<img src=\"./" . $notaImg . "\" border=\"0\" />
								<b>$notaUser</b>: $notaNota</a>
							</div>";
					echo ($notaUrl);
					$rsNotas->Next();
				}
				echo ('</div></div>');
			}
			$rsNotas->close();
			?>

			<div class="divDesk" style="text-align: center;">

				<a href="<?php echo ($DESARROLLADOR_WEB_SITE); ?>" target="_blank">
					<img src="<?php echo ($DESARROLLADOR_LOGO); ?>" title="<?php echo ($DESARROLLADOR_NOMBRE); ?>" />
				</a>

			</div>

		</div>

	<?php
	} else {
	?>

		<div class="w3-card w3-white w3-margin-top dlg-75" style="width: 70%;">

			<div class="headerTitulo">
				<h1>Operaci&oacute;n realizada</h1>
			</div>

			<div class="w3-container w3-white">
				<p>
					<?php
					if ($opid != 0) {
						$record = array();
						$rsoperacion = locateRecordId("sc_operaciones", $opid);
						$aoperacion = $rsoperacion->getRow();
						$aoperacion["condicion"] = "";
						echo (botonToolbar($aoperacion, "", $mid, $record));
					}

					if (!esVacio($mensaje)) {
						echo ("<i class=\"fa fa-check-circle fa-4x verde\"></i><h4>$mensaje</h4>");
					}
					if (!esVacio($warning)) {
						echo ("<i class=\"fa fa-exclamation-triangle fa-4x amarillo\"></i><h4>$warning</h4>");
					}
					if (!esVacio($file)) {
						$icono = "fa-file";

						if (endsWith($file, "pdf"))
							$icono = "fa-file-pdf-o";

						echo (href(imgFa($icono, "fa-2x", "verde") . " " . basename($file), $file, "reporter"));
					}
					?>
				</p>

				<a class="w3-button w3-blue-gray" href="javascript:document.location='hole.php?anterior=<?php echo ($ant); ?>&stackname=<?php echo ($stackname); ?>'">
					<i class="fa fa-check fa-2x"></i>Continuar
				</a>

				<p></p>
			</div>
		</div>

	<?php
	}
	?>

</body>

</html>