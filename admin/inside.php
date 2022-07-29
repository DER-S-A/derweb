<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

if (!isset($DESARROLLADOR_NOMBRE))
	$DESARROLLADOR_NOMBRE = "SC3 Sistemas";

$pwa = getParameterInt("pwa-activo", 0);

?>
<!doctype html>
<html lang="es">

<head>
	<title><?php echo (htmlentities($SITIO) . " por " . $DESARROLLADOR_NOMBRE); ?></title>

	<?php
	$SC3_EVITAR_PRECARGA = 1;
	include("include-head.php");
	?>

	<link rel="shortcut icon" href="ico/logo.ico" type="image/x-icon" />

	<?php
	if ($pwa == 1) {
	?>
		<link rel="manifest" href="sc-manifest.json">
	<?php
	}
	?>

	<style>
		#divMenu {
			left: 0px;
			top: 0px;
			height: 100%;
			WIDTH: 240px;
			POSITION: fixed;
		}


		#divContenido {
			left: 240px;
			top: 0px;
			position: fixed;
			width: calc(100vw - 240px);
			height: 100%;
		}

		.frames {
			border: 0px;
			width: 100%;
			height: 100%;
		}

		.noscroll {
			overflow: hidden;
		}

		.nomargin {
			margin: 0px;
		}
	</STYLE>

</head>

<body>

	<div id="divMenu">
		<iframe name="indice" id="indice" class="frames nomargin" src="sc-menu-panel.php?cols=1&innercols=1">
		</iframe>
	</div>

	<div id="divContenido">
		<iframe name="contenido" id="contenido" src="hole.php" class="frames">
		</iframe>
	</div>

	<?php
	if ($pwa == 1) {
	?>
		<script type="text/javascript" src="sc-sw.js"></script>
		<script>
			var miEquipo = sc3LSGet('miEquipo');

			if ('serviceWorker' in navigator) {

				if (miEquipo == 1) {

					window.addEventListener('load', function() {
						navigator.serviceWorker.register('sc-sw.js?' + Math.random() + '')
					});
				} else {

					console.log('NO es mi equipo, desinstalando SW...');

					//elimina los service worker instalados
					navigator.serviceWorker.getRegistrations().then((registrations) => {
						for (let registration of registrations) {
							registration.unregister()
						}
					}).catch(function(err) {
						console.log('Service Worker unregistration failed: ', err);
					});
				}
			}
		</script>
	<?php
	}
	?>

</body>

</html>