<?php
require("funcionesSConsola.php");
?>
<!DOCTYPE html>
<html lang="es">

<head>

	<?php include("include-head.php"); ?>

	<title><?php echo ($SITIO); ?> - estamos offline</title>

	<style type="text/css">
		.logo {
			max-width: 200px;
		}

		.div-transparente {
			opacity: 0.9;
			background-color: white;
		}
	</style>

</head>

<body>

	<p></p>

	<div class="div-centrada div-transparente" style="width: 75%;">

		<div class="headerTitulo">
			<h3>Estamos sin conexión
				<i class="fa fa-plug "></i>
			</h3>
		</div>

		<div class="historial-vertical w3-white w3-padding">
			<?php
			// ------------------ Operaciones marcadas como de acceso offline --------------------------------------------
			$desk = getEscritorio();
			$sec = new SecurityManager();
			$rsOps = $sec->getRsOperacionesOffline();
			$str = "";
			$i = 0;
			while (!$rsOps->EOF()) {

				$str .= "\n<div class=\"boton\">";

				$url = new HtmlUrl($rsOps->getValue("url"));
				$url->add("opid", $rsOps->getValueInt("id"));

				$icon = $rsOps->getValue("icon");
				$favicon = favIconBuild($icon, true);
				$url->add("favicon", $favicon);

				$target = $rsOps->getValue("target");
				//abre en ventana aparte
				if (esVacio($target)) {
					$target = "contenido";
					$url->add("fstack", "1");
				} else {
					//abre en ventana aparte, en otra pila
					$url->add("stackname", "op_" . $target);
				}

				$url->add("pwacache", 1);
				$item = $rsOps->getValue("nombre");

				$str .= href(img($icon, $item) . " " . $item, $url->toUrl(), "op$i", "", "");
				$str .= "</div>";

				$i++;
				$rsOps->Next();
			}
			$rsOps->close();

			echo ($str);
			?>

		</div>
	</div>
</body>

</html>