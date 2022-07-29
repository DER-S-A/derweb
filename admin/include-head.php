<?php
$start_time = microtime_float();
include("sc-cachebuster.php");
?>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- w3 css v4 -->
<link rel="stylesheet" href="<?php echo (sc3CacheButer("css/w3.css")); ?>">
<link rel="stylesheet" href="<?php echo (sc3CacheButer("css/w3-colors-2017.css")); ?>">

<!-- LA hoja de estilos -->
<link rel="stylesheet" href="<?php echo (sc3CacheButer("css/sc-core.css")); ?>">

<script type="text/javascript">
	//Prefijo de cache de los objetos guardados en SessionStorage
	var gCacheName = '<?php echo (getCacheName($SITIO)); ?>-';
</script>

<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc3.js")); ?>" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/ajax.js")); ?>" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-dom.js")); ?>" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-cola.js")); ?>"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html.js")); ?>" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-localstorage.js")); ?>" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-sessionstorage.js")); ?>" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-metadata.js")); ?>" charset="UTF-8"></script>

<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-accordeon.js")); ?>" charset="UTF-8"></script>
<link rel="stylesheet" href="<?php echo (sc3CacheButer("css/sc-accordeon.css")); ?>" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-cbu.js")); ?>" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-cuit.js")); ?>" charset="UTF-8"></script>

<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-tabs.js")); ?>" charset="UTF-8"></script>
<link rel="stylesheet" href="<?php echo (sc3CacheButer("css/sc-html-tabs.css")); ?>" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-input-file.js")); ?>"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-input-text-email.js")); ?>"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-date.js")); ?>"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-color.js")); ?>"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-factura.js")); ?>"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-boolean2.js")); ?>"></script>
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-input-text.js")); ?>"></script>

<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-html-grid.js")); ?>"></script>

<script type='text/javascript' src="<?php echo (sc3CacheButer("scripts/sc-html-buscador.js")); ?>"></script>

<!-- QuillJS Editor HTML -->
<link rel="stylesheet" href="scripts/quill/github.min.css" />
<script src="scripts/quill/highlight.min.js"></script>
<script charset="UTF-8" src="scripts/quill/xml.min.js"></script>

<script type="text/javascript" src="scripts/quill/quill.min.js"></script>
<link rel="stylesheet" href="scripts/quill/quill.snow.css" />
<script src="scripts/quill/quill.htmlEditButton.js"></script>
<link rel="stylesheet" href="scripts/quill/quill.htmlEditButton.css" />

<!-- DropZone -->
<link href="<?php echo (sc3CacheButer("terceros/dropzone/dropzone.min.css")) ?>" rel="stylesheet">
<script src="terceros/dropzone/dropzone.min.js"></script>

<!-- ChartJS -->
<script type="text/javascript" src="scripts/chartjs/Chart.min.js"></script>

<!-- http://fontawesome.io/ -->
<link rel="stylesheet" href="terceros/font-awesome-4.7.0/css/font-awesome.min.css">

<script type="text/javascript" src="scripts/AnchorPosition.js"></script>

<script type="text/javascript" src="scripts/autosuggest/bsn.AutoSuggest_2.1.3.js"></script>
<link rel="stylesheet" href="<?php echo (sc3CacheButer("scripts/autosuggest/autosuggest_inquisitor.css")); ?>" type="text/css" />

<script type="text/javascript" src="scripts/PopupWindow.js"></script>
<script type="text/javascript" src="scripts/date.js"></script>
<script type="text/javascript" src="scripts/CalendarPopup.js"></script>

<script type="text/javascript" src="scripts/jstoolbox-table.js"></script>
<script type="text/javascript" src="scripts/hilitor.js"></script>

<!-- Tabber -->
<script type="text/javascript" src="scripts/tabber.js"></script>
<link rel="stylesheet" href="<?php echo (sc3CacheButer("css/tabber.css")); ?>" type="text/css" media="screen" />

<!-- selector con caché -->

<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/sc-autocomplete.js")); ?>"></script>
<link rel="stylesheet" href="<?php echo (sc3CacheButer("css/sc-autocomplete.css")); ?>" type="text/css">

<!-- Menu desplegable basado en w3schools https://www.w3schools.com/howto/howto_js_dropdown.asp -->
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/w3dropdown.js")); ?>"></script>
<link rel="stylesheet" href="<?php echo (sc3CacheButer("scripts/w3dropdown.css")); ?>" type="text/css" media="screen">
<!-- FIN Menu desplegable basado en w3schools                                                   -->

<!-- Menu contextual (boton derecho) -->
<script type="text/javascript" src="<?php echo (sc3CacheButer("scripts/ctxmenu.js")); ?>"></script>
<link rel="stylesheet" href="<?php echo (sc3CacheButer("css/ctxmenu.css")); ?>" type="text/css">

<?php
if (!isset($favicon))
	$favicon = "ico/logo.ico";

if (!esVacio(Request("favicon")))
	$favicon = Request("favicon");
?>
<link rel="shortcut icon" href="<?php echo ($favicon); ?>" type="image/x-icon" />

<script type="text/javascript">
	<?php
	//carga las tablas que se cargan siempre
	//se evita la precarga  en selitems / viewitem
	if (!isset($SC3_EVITAR_PRECARGA) || $SC3_EVITAR_PRECARGA == 0) {
		$rs = getRs("select distinct table_ 
					from sc_querys 
					where cargar_siempre = 1");
		while (!$rs->EOF()) {
			echo ("sc3LoadTableCache1('" . $rs->getValue('table_') . "');
		");
			$rs->Next();
		}

		$rs->close();
	}
	?>
</script>


<?php include("include-head-app.php"); ?>