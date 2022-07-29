<?php
require("funcionesSConsola.php");
checkUsuarioLogueado();

//registra poder llamar a la funcion sc3UsuariosActivos desde javascript
$ajaxH = sc3GetAjaxHelper();
$ajaxH->registerFunction("sc3UsuariosActivos");
sc3SaveAjaxHelper($ajaxH);

$pwa = getParameterInt("pwa-activo", 0);

//arma logo.ico
favIconBuild("app/logo.png");

//arreglo con todas las operaciones que puede acceder el usuario
$aOperaciones = [];

if (function_exists('lcfirst') === false) {
	function lcfirst($str)
	{
		$str[0] = strtolower($str[0]);
		return $str;
	}
}

function buildMenuExpanding($xid, $xAcordion, $xmenu)
{
	global $aOperaciones;

	$ANCHO_MAX = 23;
	$res = "";

	$sec = new SecurityManager();
	$rsTablas = $sec->getRsQuerys($xid);

	$cant = $rsTablas->cant();
	$rsOp = $sec->getRsOperaciones($xid);
	$cant += $rsOp->cant();

	$i = 0;
	while (!$rsTablas->EOF()) {

		$url = new HtmlUrl("sc-selitems.php");
		$url->add("query", $rsTablas->getValue("queryname"));
		$url->add("fstack", "1");
		$url->add("todesktop", "1");
		$item = $rsTablas->getValue("querydescription");
		$icon = $rsTablas->getValue("icon");
		if ($icon == "")
			$icon = "images/table.png";
		$target = "contenido";

		$itemShort = substr($item, 0, $ANCHO_MAX);
		$line = span("<a href=\"" . $url->toUrl() . "\" target=\"$target\" class=\"td_toolbarIzq\">
						<img src=\"$icon\" border=0 title=\"$item\"> $itemShort
					</a>", "menu-item-izq");

		//acumula operacion accesible
		$aOperaciones[] = [$item, $icon, $url->toUrl(), $target, "/ $xmenu"];

		//abre en ventana aparte, en otra pila
		$url->add("stackname", "sel_" . lcfirst(escapeJsNombreVar($item)));

		$line .= span("<a href=\"" . $url->toUrl() . "\" target=\"sel_" . escapeJsNombreVar($item) . "\">
						<i class=\"fa fa-window-restore fa-lg iconoOtraSolapa\"></i>
					</a>", "menu-item-der");
		$xAcordion->addDiv($line, "menu-item");

		$rsTablas->Next();
		$i++;
	}

	$rsTablas = $sec->getRsOperaciones($xid);

	$i = 0;
	while (!$rsTablas->EOF()) {
		$url = new HtmlUrl($rsTablas->getValue("url"));
		$url->add("opid", $rsTablas->getValue("id"));

		$item = $rsTablas->getValue("nombre");
		$icon = $rsTablas->getValue("icon");
		$accesoOffline = $rsTablas->getValueInt("acceso_offline");

		if (esVacio($icon))
			$icon = "images/table.png";
		$favicon = favIconBuild($icon, true);
		$url->add("favicon", $favicon);

		$ayuda = $rsTablas->getValue("ayuda");
		$target = $rsTablas->getValue("target");
		if (esVacio($target)) {
			$target = "contenido";
			$url->add("fstack", "1");
		} else {
			//abre en ventana aparte, en otra pila
			$url->add("stackname", "op_" . $target);
		}

		if ($accesoOffline == 1)
			$url->add("pwacache", 1);

		$itemShort = substr($item, 0, $ANCHO_MAX);
		$str = span("<a href=\"" . $url->toUrl() . "\" target=\"$target\" class=\"td_toolbarIzq\">
						<img src=\"$icon\" border=0 title=\"$item\"> $itemShort
					</a>", "menu-item-izq");

		//acumula operacion accesible			
		$aOperaciones[] = [$item, $icon, $url->toUrl(), $target, $ayuda];

		//abre en ventana aparte, en otra pila
		$url->add("stackname", "op_" . lcfirst(escapeJsNombreVar($item)));
		$url->add("fstack", "1");

		$str .= span("<a href=\"" . $url->toUrl() . "\" target=\"sel_" . escapeJsNombreVar($item) . "\">
						<i class=\"fa fa-window-restore fa-lg iconoOtraSolapa\"></i>
					</a>", "menu-item-der");

		$xAcordion->addDiv($str, "menu-item");

		$rsTablas->Next();
		$i++;
	}
}

?>
<!DOCTYPE html>
<html>

<head>

	<title>SC3 - Menú</title>

	<?php include("include-head.php"); ?>

	<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-menu-panel.js")); ?>"></script>

	<script language="javascript">
		//cuantas veces voy al server
		let gAccesosServer = 0;

		var aOperacionesOffline = [];

		var ANCHO_CHICO = '60';
		var ANCHO_GRANDE = '235';

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var response = xmlhttp.responseText;
				console.log('op cacheada');
			}
		}

		//hides/shows the menu
		function hideUnhideMenu() {
			divMenu = parent.document.getElementById("divMenu");
			divContenido = parent.document.getElementById("divContenido");
			if (divMenu != null) {
				if (divMenu.style.width == ANCHO_CHICO + 'px') {
					console.log('agrandamos');
					divMenu.style.width = ANCHO_GRANDE + 'px';
					divContenido.style.left = ANCHO_GRANDE + 'px';
					divContenido.style.width = (parent.window.innerWidth - ANCHO_GRANDE) + 'px';
				} else {
					console.log('achicamos', parent.window.innerWidth, ANCHO_CHICO);
					divMenu.style.width = ANCHO_CHICO + 'px';
					divContenido.style.left = ANCHO_CHICO + 'px';
					divContenido.style.width = (parent.window.innerWidth - ANCHO_CHICO) + 'px';
				}
			} else
				alert('\'divMenu\' no encontrado.');
		}

		/**
		 * Invoca funcion del server para determina usuarios activos
		 */
		function checkUsuariosActivos() {
			var params = [];
			sc3InvokeServerFn('sc3UsuariosActivos', params, checkUsuariosActivosCB);
			//cada 50 segundos * 50, aprox 40'
			//visita todas las operaciones para que estén disponibles offline
			if (aOperacionesOffline[gAccesosServer - 1] !== undefined) {
				operacion = aOperacionesOffline[gAccesosServer - 1];
				document.getElementById("iFrameSW").src = operacion;
			}
			gAccesosServer = (gAccesosServer + 1) % 60;
		}

		function checkUsuariosActivosCB(aResult) {
			ausr = aResult;
			divU = document.getElementById("usuariosActivos");

			usuarios = "";
			i = 0;
			cantU = 0;
			while ((i < ausr.length) && (i < 10)) {
				if ((ausr[i].login != 'undefined') && (isNaN(ausr[i].login))) {
					usuarios = usuarios + ausr[i].login + ' | ';
					cantU++;
				}
				i++;
			}

			divU.innerHTML = cantU;
			document.getElementById("usuarios").title = usuarios;
		}
	</script>

	<style>
		a {
			display: block;
			font-size: 13px;
		}

		a:hover {
			background-color: #eeeeee;
		}

		.fa.iconoMenu {
			color: white;
		}

		.fa.iconoOtraSolapa {
			color: #64668D;
		}


		.dlg_menu {
			border: 0px;
			width: 230px;
			margin: 0px;
			padding: 0px;
		}

		.td_toolbarIzq {
			display: block;
			width: 100%;
			padding: 2px;
		}

		.td_toolbarIzq:HOVER {
			background-color: #cdcdcd;
			text-decoration: none;
		}

		.azul {
			background-color: #607d8b !important
		}

		.rojo {
			background-color: #ff6b6b;
		}

		.naranja2 {
			background-color: #cc8e35;
		}

		.gris {
			background-color: #BDC581;
		}

		.menu-item-izq {
			width: 85%;
			align: left;
			display: inline-block;
		}

		.menu-item-der {
			align: right;
			display: inline-block;
		}

		.menu-item {
			height: 30px;
		}


		.toolbar-izq {
			display: flex;
			justify-content: space-between;
			padding: 1px;
			background-color: #dfe6e9;
			width: 230px;
		}

		.info_status {
			display: block;
			text-align: left;
			margin-left: 9px;
			margin-top: 2px;
			float: right !important;
			width: 65px;
			font-size: 10px;
			color: #607d8b;
		}

		#usuariosActivos {
			font-size: 10px;
		}

		.hover-white:hover {
			background-color: white;
		}
	</style>

</head>

<body onload="checkUsuariosActivos();startMenuDesplegable('buscarop');document.getElementById('buscarop').focus();" class="body-menu">


	<!-- IFRAME OCULTO PARA INICIAR EL SERVICE WORKER-->
	<iframe src="" id="iFrameSW" style="display: none" frameborder="0"></iframe>


	<div class="menu-fijo">

		<div class="tabla-logo">

			<div style="width: 70%;display: inline-block;">
				<a href="hole.php?fstack=1" class='hover-white' target="contenido" title="Cerrar todas las ventanas y acceder al escritorio">
					<img src="app/logo.png" alt="<?php echo ($SITIO); ?>" title="<?php echo ($SITIO); ?>" style="max-width: 100%; max-height: 66px; margin-left: 5px; border-radius: 2px;">
				</a>
			</div>

			<div>
				<a href="sc-loginerror.php" target="_parent" title="Salir del sistema" class="boton_menu">
					<i class="fa fa-sign-out fa-2x"></i>
				</a>
			</div>
		</div>

		<div class="toolbar-izq">

			<div class="autocomplete-container" style="width: 100%;">

				<div class="autocomplete" style="width: 100%;padding: 2px;">
					<input id="buscarop" type="text" name="buscarop" placeholder="buscar..." autocomplete="off" onfocus="sc3SelectAll('buscarop');" style="width: 100%;" title="F4 para llegar aquí">
				</div>

			</div>

		</div>

	</div>

	<div class="toolbar-izq" style="margin-top: 116px;">

		<a href="javascript:hideUnhideMenu();" class="boton_menu">
			<i class="fa fa-step-backward fa-lg" title="Mostrar/Ocultar Men&uacute;"></i>
		</a>

		<a href="insideb.php" target="_top" title="Menú superior" class="boton_menu">
			<i class="fa fa-bars fa-lg"></i>
		</a>

		<a href="sc-cambioclave.php" target="contenido" title="Cambie la clave periodicamente !" class="boton_menu">
			<i class="fa fa-lock fa-lg"></i>
		</a>
	</div>

	<div class="dlg_menu">

		<?php
		$acc = new HtmlAccordeon("Mis Favoritos", "fa-star");
		$acc->setHeaderClass("sc3-accordion gris");

		//recupera los querys que tiene acceso el usuario
		$desk = getEscritorio();
		$sec = new SecurityManager();
		$rsTablas = $sec->getRsFavoritos();

		$desk = getEscritorio();

		while (!$rsTablas->EOF()) {
			$tipo = $rsTablas->getValue("tipo");
			if (sonIguales($tipo, "Q")) {
				$queryName = $rsTablas->getValue("queryname");
				$queryDesc = $rsTablas->getValue("querydescription");

				$url = new HtmlUrl("sc-selitems.php");
				$url->add("query", $queryName);
				$url->add("fstack", "1");
				$url->add("todesktop", "1");
				$item = $queryDesc;

				//nombre que tendrá la pila, sin el filtro
				$stackname = "sel_" . lcfirst(escapeJsNombreVar($item));

				$icon = $rsTablas->getValue("icon");
				if ($icon == "")
					$icon = "images/table.png";

				//agrega los querys de favoritos al historial	
				$query_info = [];
				$query_info["queryname"] = $queryName;
				$query_info["querydescription"] = $queryDesc;
				$query_info["icon"] = $icon;
				$desk->addQuery($query_info, "", "");

				$filters = $rsTablas->getValue("valor2");
				$afilters = explode("--", $filters);
				if (count($afilters) > 1) {
					$filtername = $afilters[1];
					$filter =  $afilters[0];
					if (!esVacio($filtername)) {
						$url->add("filtername", $filtername);
						$url->add("filter", $filter);

						$item .= " (" . $filtername . "...)";
					}
				}

				$target = "contenido";
			} else {
				$url = new HtmlUrl($rsTablas->getValue("queryname"));
				//opid,favicon,stackname,pwa
				$url->add("opid", $rsTablas->getValueInt("id"));
			}

			$item = $rsTablas->getValue("querydescription");
			$icon = $rsTablas->getValue("icon");
			$favicon = favIconBuild($icon, true);
			$url->add("favicon", $favicon);
			$target = $rsTablas->getValue("target");
			if (esVacio($target)) {
				$target = "contenido";
				$url->add("fstack", "1");
			} else {
				//abre en ventana aparte, en otra pila
				$url->add("stackname", "op_" . $target);
			}

			$accesoOffline = $rsTablas->getValueInt("acceso_offline");
			if ($accesoOffline == 1)
				$url->add("pwacache", 1);

			//va al menu (restringe ancho 25)
			$str = span(href(img($icon, $item) . " " .  substr($item, 0, 22), $url->toUrl(), $target, "", "td_toolbarIzq"), "menu-item-izq");

			//los cuadrados de abrir en otra pestaña llevan tackname
			if (esVacio($target) || sonIguales($target, "contenido")) {
				$url->add("stackname", $stackname);
			}

			$str .= span("<a href=\"" . $url->toUrl() . "\" target=\"sel_" . escapeJsNombreVar($item) . "\">
						<i class=\"fa fa-window-restore fa-lg iconoOtraSolapa\"></i>
					</a>", "menu-item-der");

			$acc->addDiv($str, "menu-item");

			$rsTablas->Next();
		}

		saveEscritorio($desk);

		$acc->addDiv("<a href=\"sc-adminfavoritos.php\" title=\"Administrar datos y operaciones que aparecen aqui\" ><img src=\"images/addon.gif\"/>...</a>");
		echo ($acc->toHtml(true));

		$sec = new SecurityManager();
		$rsmenu = $sec->getMenuSc3();

		$i = 0;
		while (!$rsmenu->EOF()) {
			$idmenu = $rsmenu->getValue("idItemMenu");
			$item = $rsmenu->getValue("Item");
			$icon = $rsmenu->getValue("icon");
			$color = $rsmenu->getValue("color");

			$acc = new HtmlAccordeon($item, $icon);
			$acc->setHeaderClass("sc3-accordion");
			$acc->setHeaderStyle("background-color: $color");
			$acc->setScrolleable(true);

			buildMenuExpanding($idmenu, $acc, $item);

			echo ($acc->toHtml(false, $i + 1));

			$rsmenu->Next();
			$i++;
		}
		?>

	</div>

	<script>
		//usado en el menu rapido desplegable
		let aOperaciones = [];
		<?php

/* 		
		version php5.6
		foreach ($aOperaciones as $i => $ops) {
			echo ("\r\n aOperaciones.push(['" . $ops[0] . "', '" . $ops[1] . "', '" . $ops[2] . "', '" . $ops[3] . "', '" . $ops[4] . "']);");
		}
*/
		foreach ($aOperaciones as $i => [$nombre, $icon, $url, $target, $ayuda]) {
			echo ("\r\n aOperaciones.push(['$nombre', '$icon', '$url', '$target', '$ayuda']);");
		}

		?>
	</script>

	<?php
	//Arma fav icons de todo, tablas y operaciones para que la navegación usual no tenga que crearlos 
	$rsTablas = getRs("select distinct icon from sc_querys");
	while (!$rsTablas->EOF()) {
		$icon = $rsTablas->getValue("icon");
		if ($icon == "")
			$icon = "images/table.png";

		//ARMA FAVICON para futuros usos
		favIconBuild($icon);

		$rsTablas->Next();
	}

	$rsTablas->execQuery("update sc_querys set icon='images/table.png' where icon = ''");

	$rsTablas = getRs("select distinct icon 
						from sc_operaciones");
	while (!$rsTablas->EOF()) {
		$icon = $rsTablas->getValue("icon");

		//ARMA FAVICON para futuros usos
		favIconBuild($icon);

		$rsTablas->Next();
	}

	$rsTablas->close();

	// Operaciones marcadas como de acceso offline --------------------------------------------
	$aOpsOffline = [];
	if ($pwa == 1) {
		$desk = getEscritorio();
		$sec = new SecurityManager();
		$rsOps = $sec->getRsOperacionesOffline();
		$i = 0;
		while (!$rsOps->EOF()) {

			$url = new HtmlUrl($rsOps->getValue("url"));
			$url->add("opid", $rsOps->getValueInt("id"));

			$icon = $rsOps->getValue("icon");
			$favicon = favIconBuild($icon, true);
			$url->add("favicon", $favicon);

			$item = $rsOps->getValue("querydescription");
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
			$aOpsOffline[] = $url->toUrl();
			$rsOps->Next();
		}
		$rsOps->close();
	}

	?>

	<div title="Versi&oacute;n y usuarios" class="info-version">
		<i class="fa fa-tag fa-lg fa-fw"></i>
		<?php echo (getVersion()); ?>
		- <?php echo (getSession("login")) ?>
		<i id="usuarios" class="fa fa-user-o fa-lg"></i> <span class="" id="usuariosActivos"></span>
	</div>

	<script type="text/javascript">
		aOperacionesOffline = <?php echo (json_encode($aOpsOffline)); ?>;

		//instala timer que recupera los usuarios activos
		window.setInterval(checkUsuariosActivos, 50000);
	</script>

</body>

</html>