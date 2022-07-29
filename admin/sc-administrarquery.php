<?php
include("funcionesSConsola.php");
checkUsuarioLogueado();

$error = "";
$rquery = "";
$rquery = Request("query");

if ($rquery == "") {
	$queryID = Request("mid");

	$rsQuerys = locateRecordWhere("sc_querys", " id = $queryID");
	$rquery = $rsQuerys->getvalue("queryname");
}

//busca la definicion y el obj en la cache ----------------------------
$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
if ($tc->existsQueryObj($rquery))
	$qinfo = $tc->getQueryObj($rquery);
else {
	$qinfo = new ScQueryInfo($query_info);
	$tc->saveQueryObj($rquery, $qinfo);
}
saveCache($tc);
//------fin recuperar qinfo ------------------------------------------


$ajaxH = sc3GetAjaxHelper();
$ajaxH->registerFunction("cambiarGrupoCampo", "sc-query-api.php");
$ajaxH->registerFunction("getFieldInfo", "sc-query-api.php");
$ajaxH->registerFunction("generateInfoCampo", "sc-query-api.php");
$ajaxH->registerFunction("actualizarCampo", "sc-query-api.php");
sc3SaveAjaxHelper($ajaxH);

$idquery = $qinfo->getQueryId();
?>
<!doctype html>
<html lang="es">

<head>
	<title>Definir campos - por SC3</title>

	<?php include("include-head.php");
	//guardo el idquery para poder realizar el update y obtenerlo mediante JS.
	echo "<input type=\"hidden\" name=\"idquery\" id=\"idquery\" value=\"" . $idquery . "\">"
	?>

	<script language="javascript">
		var queryname = '<?php echo ($rquery); ?>';
	</script>

	<style>
		.divDrageable {
			padding: 1px;
			cursor: move;
			background-color: #E1E1E1;
			color: #000000;
			margin-top: 4px;
			margin-left: 5px;
			margin-right: 5px;
			width: auto;
		}

		.campoDrageable {
			margin-left: 4px;
			font-weight: bold;
		}

		.contenedorNuevoGrupo {
			display: block;
		}
	</style>

</head>

<body onload="firstFocus()">

	<?php

	$tab = new HtmlTabs2();
	$tab->setId("tabreporte");

	$camposMuestra = "";
	//lista de grupos posibles
	$aGrupos = getGruposArray($qinfo->getFieldsDef());
	$camposGrilla = $qinfo->getQueryFields();

	$sql = $qinfo->buildSelectLeftJoin(true);
	$sql .= " limit 0";
	$rsPpal = getRs($sql);

	$cantGrupos = 0;
	//recorre los grupos de datos
	$txtNuevoGrupo = new HtmlInputText("nuevoGrupo", "");
	$txtNuevoGrupo->setSize(20);
	$botonAgregar = "<a href=\"javascript:agregarNuevoGrupo()\" class=\"boton-secundario\" title=\"Agregar un nuevo grupo\">
						<i class=\"fa fa-plus fa-fw fa-lg\"></i> Agregar grupo
					</a>";

	$agregarCampo = $txtNuevoGrupo->toHtml() . $botonAgregar;

	$divContenedorNuevoGrupo = div($agregarCampo, "contenedorNuevoGrupo", "contenedorNuevoGrupo");
	$camposMuestra .= $divContenedorNuevoGrupo;
	foreach ($aGrupos as $grupoActual) {
		$area1 = new HtmlDivDatos($grupoActual);
		$area1->setExpandible(true);
		$i = 0;

		//recorre todos los campos y solo muestra los del grupo = $grupoActual
		while ($i < $rsPpal->cantF()) {
			$nombreCampo = $rsPpal->getFieldName($i);
			$fieldGroup = $qinfo->getFieldGrupo($nombreCampo);
			$visible = $qinfo->getFieldVisible($nombreCampo);
			$nombreMuestra = $qinfo->getFieldCaption($nombreCampo);
			$requerido = $qinfo->getFieldRequerido($nombreCampo);
			$editable = $qinfo->getFieldEditable($nombreCampo);
			$passwordField = $qinfo->getFieldPasswordField($nombreCampo);
			$fileField = $qinfo->getfieldFileField($nombreCampo);
			$googlePoint = $qinfo->getFieldGooglePoint($nombreCampo);
			$colorField = $qinfo->getfieldColorField($nombreCampo);
			$richText = $qinfo->getFieldRichText($nombreCampo);
			$subgrupo = $qinfo->getFieldSubgrupo($nombreCampo);
			$defaultValue = $qinfo->getFieldDefaultValue($nombreCampo);
			$ocultarVacio = $qinfo->getFieldOCultarVacio($nombreCampo);
			$class = $qinfo->getFieldClass($nombreCampo, []);

			$pos = strpos($nombreCampo, "_fk");
			if ($pos === FALSE) {
				//analiza si el campo esta en el grupo que estoy mostrando
				if (
					sonIguales($grupoActual, $fieldGroup)
					|| (sonIguales("", $fieldGroup) && sonIguales("Datos", $grupoActual))
				) {

					$iRequerido = "";
					if ($requerido == 1) {
						$iRequerido = "<i title=\"Requerido\" style=\"min-width: 15px; cursor: help; float: right; margin-right:10px;;\" class=\"fa fa-check-square fa-lg azul\"></i>";
					}

					$iEditable = "";
					if ($editable == 0) {
						$iEditable = "<i title=\"No editable\" style=\"min-width: 15px; cursor: help; float: right; margin-right: 10px;\" class=\" fa fa-ban  fa-lg amarillo\"></i>";
					}

					$iOcultarVacio = "";
					if ($ocultarVacio == 1) {
						$iOcultarVacio = "<i title=\"Ocultar vacio\" style=\"min-width: 15px; cursor: help; float: right; margin-right: 10px;\" class=\"fa fa-square-o fa-lg amarillo\"></i>";
					}

					$iVisible = "";
					if ($visible == 0) {
						$iVisible = "<i title=\"No visible\" style=\"min-width: 15px; cursor: help; float: right; margin-right: 10px;\" class=\"fa fa-eye-slash fa-lg amarillo\"></i>";
					}

					$iPasswordField = "";
					if ($passwordField == 1) {
						$iPasswordField = "<i title=\"Es password\" style=\"min-width: 15px; cursor: help; float: right; margin-right: 10px;\" class=\"fa fa-lock fa-lg amarillo\"></i>";
					}

					$iFileField = "";
					if ($fileField == 1) {
						$iFileField = "<i title=\"Tipo archivo\" style=\"min-width: 15px; cursor: help; float: right; margin-right: 10px;\" class=\"fa fa-cloud-upload fa-lg verde\"></i>";
					}

					$iGooglePoint = "";
					if ($googlePoint == 1) {
						$iGooglePoint = "<i title=\"Es Google point\" style=\"min-width: 15px; cursor: help; float: right; margin-right: 10px;\" class=\"fa fa-map-marker fa-lg verde\"></i>";
					}

					$iColorField = "";
					if ($colorField == 1) {
						$iColorField = "<i title=\"Tipo color\" style=\"min-width: 15px; cursor: help; float: right; margin-right: 10px;\" class=\"fa fa-paint-brush fa-lg naranja\"></i>";
					}

					$iRichtext = "";
					if ($richText == 1) {
						$iRichtext = "<i title=\"Es rich text\" style=\"min-width: 15px; cursor: help; float: right; margin-right: 10px;\" class=\"fa fa-text-height verde\"></i>";
					}

					$aExtra = [];
					if ($defaultValue != "")
						$aExtra[] = "Default: $defaultValue";

					if ($subgrupo != "")
						$aExtra[] = "Subgrupo: $subgrupo";

					if ($class != "")
						$aExtra[] = "Class: $class";

					$pDefaultValue = "<span style=\"margin-left: 8px;\">" . implode(", ", $aExtra) . "</span>";

					$divCampo = div('<p class="campoDrageable" value="' . $nombreCampo . '">
					' . $iRichtext . $iColorField . $iGooglePoint . $iFileField . $nombreMuestra . $iPasswordField . $iOcultarVacio . $iVisible . $iEditable . $iRequerido . '</p>' . $pDefaultValue, "divDrageable", $nombreCampo, true, "mostrarModalCampo('" . $nombreCampo . "')");
					$area1->add("", $divCampo);
				}
			}
			$i++;
		}

		$camposMuestra .= $area1->toHtmlDraggeable();

		$cantGrupos++;
	}
	$rsPpal->close();

	$tab->agregarSolapa("Campos a mostrar", "fa-table", $camposMuestra);
	echo ($tab->toHtml());
	?>


	<!--
		Div oculto que se rellena en js al hacer doble click sobre un campo
		donde se va a poder editar eequerido, nombre fantasia ETC.

		El input hidden es para guardar el nombre del campo
	-->

	<input type="hidden" name="nombreCampo" id="nombreCampo">
	<div id="modalCampo" class="w3-modal">
		<div class="w3-modal-content">
			<header class="w3-container w3-purple">
				<div style="padding:10px;" id="tituloModalCampo"></div>
				<span onclick="document.getElementById('modalCampo').style.display='none';" class="w3-button w3-light-grey w3-display-topright">&times;</span>
			</header>
			<div class="w3-container" id="rellenarCampo">

				<div style="width: 50%; height: 50%; display: inline-block; float: left; padding: 5px">
					<div style="display: block">
						<p style="display: inline-block">Show name:</p>
						<input type="text" style="float: right;" size="50" placeholder="Show name" maxlength="80" id="show_name" value="">
					</div>
					<div style="display: block">
						<p style="display: inline-block">subgrupo</p>
						<input type="text" style="float: right;" size="50" placeholder="Subgrupo" maxlength="80" id="subgrupo" value="">
					</div>
					<div style="display: block">
						<p style="display: inline-block">Default value:</p>
						<input type="text" style="float: right;" size="50" placeholder="Default value exp" maxlength="80" id="default_value_exp" value="">
					</div>
					<div style="display: block">
						<p style="display: inline-block">Example:</p>
						<input type="text" style="float: right;" size=" 50" placeholder="Example" maxlength="80" id="example" value="">
					</div>
					<div style="display: block">
						<p style="display: inline-block">Class:</p>
						<input type="text" style="float: right;" size="50" placeholder="Class" maxlength="80" id="class" value="">
					</div>
					<div style="display: block">
						<p style="display: inline-block">Field help:</p>
						<input type="text" style="float: right;" size="50" placeholder="Field help" maxlength="80" id="field_help" value="">
					</div>

				</div>
				<div style="height: 50%; padding: 5px; display: inline-block;">
					<div style="display: block">
						<p style="display: inline-block">Requerido:</p>
						<?php
						$boolean = new HtmlBoolean2("is_required", "0");
						echo ($boolean->toHtml());
						?>
					</div>
					<div style="display: block">
						<p style="display: inline-block">Password field:</p>
						<?php
						$passwordField = new HtmlBoolean2("password_field", "0");
						echo ($passwordField->toHtml());
						?>
					</div>
					<div style="display: block">
						<p style="display: inline-block">File field:</p>
						<?php
						$fileField = new HtmlBoolean2("file_field", "0");
						echo ($fileField->toHtml());
						?>
					</div>
					<div style="display: block">
						<p style="display: inline-block">Color field:</p>
						<?php
						$colorField = new HtmlBoolean2("color_field", "0");
						echo ($colorField->toHtml());
						?>
					</div>
					<div style="display: block">
						<p style="display: inline-block">Rich text:</p>
						<?php
						$richText = new HtmlBoolean2("rich_text", "0");
						echo ($richText->toHtml());
						?>
					</div>
					<div style="display: block">
						<p style="display: inline-block">Google Point:</p>
						<?php
						$googlePoint = new HtmlBoolean2("is_google_point", "0");
						echo ($googlePoint->toHtml());
						?>
					</div>
					<div style="display: block">
						<p style="display: inline-block">Editable:</p>
						<?php
						$editable = new HtmlBoolean2("is_editable", "0");
						echo ($editable->toHtml());
						?>
					</div>
					<div style="display: block">
						<p style="display: inline-block">Ocultar vacio:</p>
						<?php
						$ocultarVacio = new HtmlBoolean2("ocultarVacio", "0");
						echo ($ocultarVacio->toHtml());
						?>
					</div>
					<div style="display: block">
						<p style="display: inline-block">Visible:</p>
						<?php
						$visible = new HtmlBoolean2("visible", "0");
						echo ($visible->toHtml());
						?>
					</div>
				</div>
				<div style="display: block; text-align: center;">
					<a href="javascript:confirmarCambiosCampo()" class="boton-secundario" title="Confirmar cambios">
						<i class="fa fa-check-circle-o fa-fw fa-lg"></i> Aceptar
					</a>
				</div>
			</div>
		</div>
	</div>

	<?php include("footer.php"); ?>


</body>
<script>
	function dragStart(event) {
		event.dataTransfer.setData("Text", event.target.id);
	}

	function dragEnter(event) {
		if (event.target.className == "droptarget") {
			event.target.style.border = "3px dotted red";
		}
	}

	function dragLeave(event) {
		if (event.target.className == "droptarget") {
			event.target.style.border = "";
		}
	}

	function allowDrop(event) {
		event.preventDefault();
	}

	function drop(event, nombreDiv) {
		event.preventDefault();
		var data = event.dataTransfer.getData("Text");
		event.target.appendChild(document.getElementById(data));
		var idquery = document.getElementById("idquery").value;
		pleaseWait2();
		actualizarGrupoDelCampo(nombreDiv, data, idquery);
	}

	function actualizarGrupoDelCampo(nombreGrupo, campo, idquery) {
		//falta agregar parametros
		params = [{
			"grupo": nombreGrupo,
			"campo": campo,
			"idquery": idquery
		}];
		sc3InvokeServerFn("cambiarGrupoCampo", params, actualizarGrupoDelCampoCB);
	}

	function actualizarGrupoDelCampoCB(aResult) {
		console.log(aResult);
		location.reload();
	}

	function agregarNuevoGrupo() {
		var nombreGrupo = document.getElementById("nuevoGrupo").value;
		var divPadre = document.getElementById("tabreporte0");


		var div1 = document.createElement("div");
		div1.setAttribute("class", "div-flotante");
		div1.setAttribute("id", nombreGrupo);

		//nuevo div 
		var div2 = document.createElement("div");
		div2.setAttribute("width", "99%");
		div2.setAttribute("class", "expandingtable");
		div1.appendChild(div2);

		//nuevo div con botton flecha
		var div3 = document.createElement("div");
		div3.setAttribute("class", "expandingcell");
		//a con el icono para expandingtableinner
		var aExpandir = document.createElement("a");
		aExpandir.setAttribute("onclick", "sc3expand2('dr" + nombreGrupo + "', 'mr" + nombreGrupo + "', '" + nombreGrupo + "')")
		aExpandir.setAttribute("id", "mr" + nombreGrupo);
		aExpandir.setAttribute("style", "style=\"cursor: pointer\"")
		aExpandir.textContent = nombreGrupo;
		//i con el icono del
		var iIcono = document.createElement("i");
		iIcono.setAttribute("class", "fa fa-angle-double-up fa-lg")
		aExpandir.appendChild(iIcono);
		div3.appendChild(aExpandir);

		div2.appendChild(div3);

		//div2 y 3 se cierra, el nuevo div se agrega a div 1
		var div4 = document.createElement('div');
		div4.setAttribute('id', "dr" + nombreGrupo);

		var div5 = document.createElement('div');
		div5.setAttribute("class", "expandingtableinner");

		//a div 4 se le agrega el div drageables
		var divDrageable = document.createElement('div');
		divDrageable.setAttribute("class", "droptarget");
		divDrageable.setAttribute("ondragenter", "dragEnter(event)");
		divDrageable.setAttribute("ondragleave", "dragLeave(event)");
		divDrageable.setAttribute("ondragover", "allowDrop(event)");
		divDrageable.setAttribute("ondrop", "drop(event,'" + nombreGrupo + "')");
		div5.appendChild(divDrageable);

		div4.appendChild(div5);
		div1.appendChild(div4);

		divPadre.appendChild(div1);
	}

	function mostrarModalCampo(nombreCampo) {
		pleaseWait2();
		document.getElementById('modalCampo').style.display = "block";
		//establezco el nombre del campo para usarlo en el confirmar;
		document.getElementById("nombreCampo").value = nombreCampo;
		document.getElementById("tituloModalCampo").innerHTML = nombreCampo;

		//obtengo el idquery, para ir a buscar la info del campo por javascript.
		//tengo que traer la info del campo, y rellenar el modal.
		var idquery = document.getElementById("idquery").value;
		params = [{
			"campo": nombreCampo,
			"idquery": idquery
		}];

		sc3InvokeServerFn("getFieldInfo", params, rellenarModalCampoCB);
	}

	function rellenarModalCampoCB(aResponse) {
		//una vez teniendo la info del campo, empiezo a agregar al div los check box de requerido, etc
		var fieldInfo = aResponse["data"];

		//si fieldinfo viene nulo, mando a generar el field info
		if (fieldInfo == null) {
			params = [{
				"idquery": aResponse["idquery"],
				"campo": aResponse["campo"]
			}];
			sc3InvokeServerFn("generateInfoCampo", params, rellenarModalCampoCB);
		} else {

			sc3SetBoolean("is_required", fieldInfo["is_required"] != null ? fieldInfo["is_required"] : 0);
			sc3SetBoolean("password_field", fieldInfo["password_field"] != null ? fieldInfo["password_field"] : 0);
			sc3SetBoolean("file_field", fieldInfo["file_field"] != null ? fieldInfo["file_field"] : 0);
			sc3SetBoolean("color_field", fieldInfo["color_field"] != null ? fieldInfo["color_field"] : 0);
			sc3SetBoolean("rich_text", fieldInfo["rich_text"] != null ? fieldInfo["rich_text"] : 0);
			sc3SetBoolean("is_google_point", fieldInfo["is_google_point"] != null ? fieldInfo["is_google_point"] : 0);
			sc3SetBoolean("is_editable", fieldInfo["is_editable"] != null ? fieldInfo["is_editable"] : 0);
			sc3SetBoolean("ocultarVacio", fieldInfo["ocultar_vacio"] != null ? fieldInfo["ocultar_vacio"] : 0);
			sc3SetBoolean("visible", fieldInfo["visible"] != null ? fieldInfo["visible"] : 0);

			//cambio valores de los text field.
			document.getElementById("show_name").value = fieldInfo["show_name"];
			document.getElementById("subgrupo").value = fieldInfo["subgrupo"];
			document.getElementById("default_value_exp").value = fieldInfo["default_value_exp"];
			document.getElementById("example").value = fieldInfo["example"];
			document.getElementById("class").value = fieldInfo["class"];
			document.getElementById("field_help").value = fieldInfo["field_help"];

			pleaseWaitStop();
		}
	}

	function confirmarCambiosCampo() {
		var idquery = document.getElementById("idquery").value;
		var nombreCampo = document.getElementById("nombreCampo").value;


		params = [{
			"idquery": idquery,
			"campo": nombreCampo,
			"is_required": document.getElementById("is_required").value,
			"password_field": document.getElementById("password_field").value,
			"file_field": document.getElementById("file_field").value,
			"color_field": document.getElementById("color_field").value,
			"rich_text": document.getElementById("rich_text").value,
			"ocultarVacio": document.getElementById("ocultarVacio").value,
			"visible": document.getElementById("visible").value,
			"is_google_point": document.getElementById("is_google_point").value,
			"is_editable": document.getElementById("is_editable").value,
			"show_name": document.getElementById("show_name").value,
			"subgrupo": document.getElementById("subgrupo").value,
			"default_value_exp": document.getElementById("default_value_exp").value,
			"example": document.getElementById("example").value,
			"class": document.getElementById("class").value,
			"field_help": document.getElementById("field_help").value,
		}];
		pleaseWait2();
		sc3InvokeServerFn("actualizarCampo", params, actualizarCampoCB);
	}

	function actualizarCampoCB(aResponse) {
		pleaseWaitStop();
		document.getElementById('modalCampo').style.display = "none";
		sc3DisplayMsgEmergente(aResponse["msg"]);
		location.reload();
	}
</script>

</html>