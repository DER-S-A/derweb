<?php
require("funcionesSConsola.php");

if (usuarioLogueado()) {
	$menuB = "";
	if (isMobileAgent())
		header("location:insideb.php");
	else
		header("location:inside.php");
	exit;
}

if (!isset($DESARROLLADOR_NOMBRE))
	$DESARROLLADOR_NOMBRE = "SC3 Sistemas";
if (!isset($DESARROLLADOR_WEB_SITE))
	$DESARROLLADOR_WEB_SITE = "https://www.sc3.com.ar";
if (!isset($DESARROLLADOR_LOGO))
	$DESARROLLADOR_LOGO = "images/sc3-logo45x45.png";

?>
<!DOCTYPE html>
<html>

<head>

	<?php
	$SC3_EVITAR_PRECARGA = 1;
	include("include-head.php");
	?>

	<title><?php echo (htmlentities($SITIO) . " por " . $DESARROLLADOR_NOMBRE); ?> </title>

	<meta http-equiv="refresh" content="900; url=<?php echo $DESARROLLADOR_WEB_SITE; ?>">

	<link rel="shortcut icon" href="ico/logo.ico">

	<style type="text/css">
		<?php
		$nro = date("d");
		?>body {
			background: url("<?php echo $DESARROLLADOR_WEB_SITE; ?>/fotos/f<?php echo ($nro); ?>.jpg");
			background-repeat: no-repeat;
			background-repeat: repeat-y;
			background-position: center;
		}

		.form-login {
			max-width: 340px;
			opacity: 0.9;
			background-color: white;
			box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
		}

		.titulo-login {
			padding: 20px !important;
		}

		.form-login .logo {
			display: flex;
			align-items: center;
			margin: 20px;
		}

		.form-login-controles {
			display: flex;
			flex-direction: column;
			gap: 5px;
			margin: 10px;
		}

		.boton-clave {
			color: #616161;
			padding: 5px;
		}

		.boton-login {
			display: block;
			border: none;
			padding: 8px 16px !important;
			color: white;
			background-color: #393c3d;
			border-radius: 4px;
		}

		.boton-login:hover {
			cursor: pointer;
		}
	</style>

	<script>
		/**
		 * Oculta o muestra la clave ingresada
		 */
		function mostrarClave() {

			ctrlClave = document.getElementById('clave');
			iFa = document.getElementById('mostrar-clave');
			if (ctrlClave.type == 'password') {
				iFa.className = "fa fa-asterisk fa-lg";
				ctrlClave.type = 'text';
			} else {
				ctrlClave.type = 'password';
				iFa.className = "fa fa-eye fa-lg";
			}
		}
	</script>

</head>

<body onload="sc3SSClear();">

	<p></p>

	<form action="dologin.php" method="post" target="_top" id="form1">

		<div class="form-login div-centrada">

			<div class="headerTitulo titulo-login">
				<i class="fa fa-users fa-lg"></i> Bienvenido
			</div>

			<div class="w3-display-container w3-margin" style="height:150px;">
				<div class="w3-display-middle">
					<img src="app/logo.png" style="max-width:330px;max-height:145px;">
				</div>
			</div>

			<div class="form-login-controles">

				<input class="w3-input" type="text" name="login" id="login" placeholder="Usuario" required autofocus />

				<div class="grupo-control-boton">

					<input class="w3-input" type="password" name="clave" id="clave" placeholder="Clave" required />
					<a class="boton-clave" href="javascript:mostrarClave()" title="Ver clave">
						<i id="mostrar-clave" class="fa fa-eye fa-lg"></i>
					</a>
				</div>

				<button class="boton-login" type="submit">
					<i class="fa fa-sign-in fa-2x"></i> Ingresar
				</button>

				<div class="w3-display-container w3-margin" style="height:50px;">
					<div class="w3-display-middle">
						<a href="<?php echo $DESARROLLADOR_WEB_SITE; ?>" target="_blank">
							<img src="<?php echo $DESARROLLADOR_LOGO; ?>" title="Ir a sitio web del desarrollador" height="45" />
						</a>
					</div>
				</div>

			</div>
		</div>

	</form>

</body>

</html>