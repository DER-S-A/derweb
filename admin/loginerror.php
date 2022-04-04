<?php
require("funcionesSConsola.php");
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">

<head>

	<?php include("include-head.php"); ?>

	<title><?php echo ($SITIO); ?> - usuario no logueado</title>

	<?php
	$nro = date("d");
	?>

	<style type="text/css">
		body {
			background: url("https://www.sc3.com.ar/fotos/f<?php echo ($nro); ?>.jpg");
			background-repeat: no-repeat;
			background-repeat: repeat-y;
			background-position: center;
		}

		.logo {
			max-width: 200px;
		}

		.div-transparente {
			opacity: 0.9;
			background-color: white;
		}
	</style>

	<meta http-equiv="refresh" content="1; url=index.php">

</head>

<body>

	<p></p>

	<div class="w3-card w3-white div-centrada div-transparente" style="width: 70%;">

		<div class="headerTitulo">
			<h1>Usuario no logueado</h1>
		</div>

		<div class="w3-container w3-white">
			<p></p>

			<div class="w3-container w3-margin-left">
				<img src="app/logo.png" alt="" class="w3-image logo">
			</div>

			<ul>
				<li>Su <b>usuario</b> y <b>clave</b> son incorrectos.
				</li>
				<li>Lleva mas de <b>20</b> minutos de inactividad.
				</li>
			</ul>

			<a href="index.php" class="w3-button w3-padding w3-text-white w3-blue-gray">
				<i class="fa fa-sign-in fa-2x"></i> Volver a ingresar
			</a>
			<p></p>

		</div>

	</div>

</body>

</html>