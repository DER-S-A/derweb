<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

//registra poder llamar a la funcion sc3UsuariosActivos desde javascript
$ajaxH = sc3GetAjaxHelper();
$ajaxH->registerFunction("sc3UsuariosActivos");
sc3SaveAjaxHelper($ajaxH);


function buildMenuBootstrap()
{
	$sec = new SecurityManager();
	$rsmenu = $sec->getMenuSc3();

	$cant = $rsmenu->cant();

	while (!$rsmenu->EOF()) 
	{
		$item = $rsmenu->getValue("Item");
		$idmenu = $rsmenu->getValue("idItemMenu");
		$img = $rsmenu->getValue("icon");
		$color = $rsmenu->getValue("color");

		$title = $item;


		if (esVacio($img))
			$img = "fa-folder-o";

		echo ("\r\n\r\n<li class=\"nav-item dropdown\">");
	
		echo (" <a title=\"$title\" href=\"#\" class=\"dropdown-toggle mx-auto d-flex align-items-center\" data-toggle=\"dropdown\" style=\"background-color: $color;\">
					<i class=\"fa $img fa-fw fa-2x gris\"></i> <span class=\"d-lg-none\"> $item</span>
				<b class=\"caret\"></b></a>");
		echo (" <ul class=\"dropdown-menu\">");

		$rsTablas = $sec->getRsQuerys($idmenu);

		while (!$rsTablas->EOF()) 
		{
			$url = new HtmlUrl("insideb.php");
			$url->add("action", "sc-selitems.php");
			$url->add("query", $rsTablas->getValue("queryname"));
			$url->add("fstack", "1");
			$url->add("todesktop", "1");
			$item = $rsTablas->getValue("querydescription");
			$icon = $rsTablas->getValue("icon");
			$idquery = $rsTablas->getValue("id");

			if ($icon == "")
				$icon = "images/table.png";
			$target = "contenido";

			echo ("\r\n<li><a class=\"dropdown-item\" href=" . $url->toUrl() . ">" . img($icon, "") . " " . htmlVisible($item) . "</a></li>");

			$rsTablas->Next();
		}

		$rsTablas = $sec->getRsOperaciones($idmenu);
		if (!$rsTablas->EOF())
			echo ("\r\n   <li class=\"divider\"></li>");

		while (!$rsTablas->EOF()) 
		{
			$url = new HtmlUrl("insideb.php");
			$url->add("action", $rsTablas->getValue("url"));
			$url->add("fstack", "1");
			$url->add("opid", $rsTablas->getValue("id"));

			$item = $rsTablas->getValue("nombre");
			$icon = $rsTablas->getValue("icon");
			if ($icon == "")
				$icon = "images/table.png";

			//si abre afuera va directo al URL dado
			$target = $rsTablas->getValue("target");
			if (!esVacio($target)) 
			{
				$url = new HtmlUrl($rsTablas->getValue("url"));
				$url->add("fstack", "1");
				$url->add("opid", $rsTablas->getValue("id"));
			}

			echo ("\r\n<a class=\" dropdown-item\" href=" . $url->toUrl() . " target=\"$target\">" . img($icon, "") . " " . htmlVisible($item) . "</a>");

			$rsTablas->Next();
		}


		echo ("</ul></li>");


		$rsmenu->Next();
	}
	?>

	 
	<div style="display:none" id="usuariosActivos"></div>

	<li class="nav-item">
		<a href="inside.php" title="Men&uacute; de escritorio" class="nav-link d-flex align-items-center"  style="background-color: #84817a;">
			<i class="fa fa-desktop fa-2x fa-fw"></i><span class="d-lg-none">Men&uacute; de escritorio</span>
		</a>
	</li>
	<li class="nav-item">
		<a href="sc-loginerror.php" title="Cerrar sesion" class="nav-link d-flex align-items-center" style="background-color: #84817a;">
			<i class="fa fa-sign-out fa-2x fa-fw"></i><span class="d-lg-none">Salir</span>
		</a>
	</li>

<?php
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

	<?php include("include-headb.php"); ?>

	<title><?php echo (htmlentities($SITIO)); ?> - por SC3</title>

	<script type="text/javascript">
		function resizeIframe(obj) 
		{
			alto = (obj.contentWindow.document.body.scrollHeight) * 1;
			if (alto < 500)
				alto = 500;
			obj.style.height = (alto + 35) + 'px';

			
			obj.style.width = '100%';
		}

		/**
		 * Invoca funcion del server para determina usuarios activos
		 */
		function checkUsuariosActivos() 
		{
			var params = [];
			 sc3InvokeCallback('sc3UsuariosActivos', params, checkUsuariosActivos2);
		}


		function checkUsuariosActivos2() 
		{
			if (xmlhttp2.readyState == 4) {
				rta = xmlhttp2.responseText;
				if (rta != "") {
					ausr = JSON.parse(rta);

					divU = document.getElementById("usuariosActivos");

					usuariosDesc = '';
					i = 0;
					while ((i < ausr.length) && (i < 100)) {
						if ((ausr[i].login != 'undefined') && (isNaN(ausr[i].login)))
							usuariosDesc = usuariosDesc + ausr[i].login + ' | ';

						i++;
					}

					usuarios = "<img alt=\"Usuarios conectados\" title=\"" + usuariosDesc + "\" src=\"images/sc-usuarios.png\" /> ";
					divU.innerHTML = usuarios + " (" + (i - 1) + ')';
				}
			}
		}
	</script>


	<style type="text/css">
		.fa:HOVER 
		{
			color:#2f3640;
		}

		.logo 
		{
			margin: 4px;
			max-height: 42px;
			border-radius: 4px;
		}

		.divPpal 
		{
			overflow-x: none;
		}
		
		.divContenido 
		{
			width: 100%;
			border: none;
			height: 600px;
			display: block;
			background-color: #ffffff;
			overflow: auto;
		}
	</style>

</head>

<body onload="checkUsuariosActivos();" style="background-color: #ced6e0;">
		
			<nav class="navbar navbar-expand-lg navbar-light navbar-fixed-top">
				<a href="insideb.php?fstack=1">
					<img class="logo" title="<?php echo ($SITIO) ?>" src="app/logo.png" />
				</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
					<span class="sr-only">Toggle navigation</span>
				</button>
				<div class="container">
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav mr-auto">
							<?php
							buildMenuBootstrap();
							?>

					</div>
				</div>
			</nav>

	<div class="divCentral">

		<?php
		$actionUrl = new HtmlUrl(Request("action"));
		if (esVacio(Request("action")))
			$actionUrl->setUrl("hole.php");
		$actionUrl->addFromRequestG();
		?>

		<iframe src="<?php echo ($actionUrl->toUrl()); ?>" class="divContenido" onload='javascript:resizeIframe(this);'></iframe>

	</div> 

	<?php
	$showFooter = false;
	include("footerb.php");
	?>

	<script type="text/javascript">
		//instala timer que recupera los usuarios activos (cada minuto y medio)
		window.setInterval(checkUsuariosActivos, 90000);
	</script>

</body>

</html>