<?php
include("funcionesSConsola.php");

checkUsuarioLogueado();

$error = "";
$mid = RequestInt("mid");
$mquery = Request("mquery");
if (enviado()) {
	//TODO: completar a gusto
	goOn();
}


?>
<!DOCTYPE html>
<html>

<head>
	<title>Test - por SC3</title>

	<?php include("include-head.php"); ?>

</head>

<body onload="firstFocus();alcargar();">


	<form method="post" name="form1" id="form1">

		<?php
		$req = new FormValidator();
		?>

		<table class="dlg" style="background-color: #80b192">
			<tr>
				<td colspan="2" align="center" class="td_titulo">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td align="center"><?php echo (getOpTitle(Request("opid"))); ?></td>
							<td width="50" align="center"> <?php echo (linkImprimir()); ?> </td>
							<td width="50" align="center"><?php echo (linkCerrar(0)); ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<?php
		if ($error != "") {
			echo ($error);
		}

		$tab = new htmlTabs2();
		$aPestaña1 = array();

		$i1 = new HtmlInputText("idi1", "hola");
		$i1->valueFromRequest();
		$req->add("idi1", "Text 1");

		$i2 = new HtmlInputText("idi2", "");
		$i2->setTypeInt();
		$i2->setValue(14);

		$i3 = new HtmlInputText("idi3", "valor en read only");
		$i3->setReadOnly(true);
		$aPestaña1[0]["etiqueta"] = "HtmlInputText";
		$aPestaña1[0]["valor"] = $i2->toHtml() . $i3->toHtml();
		$campo = new HtmlEtiquetaValor($aPestaña1[0]["etiqueta"], $aPestaña1[0]["valor"]);
		$tab->agregarSolapa("HtmlInputText", "", $campo->toHtml());

		$txtArea0 = new HtmlInputTextarea("textarea0", "");

		$txtArea1 = new HtmlInputTextarea("textarea1", "");
		$txtArea1->setVoz(true);

		$txtArea2 = new HtmlInputTextarea("textarea2", "");
		$txtArea2->setVoz(true);
		$tab->agregarSolapa("HtmlInputTextarea", "", $txtArea0->toHtml() . $txtArea1->toHtml() . $txtArea2->toHtml());

		$c = new HtmlCombo("idc", "3");

		$c1 = new HtmlCombo("idc1", "3");
		$c1->add("1", "uno");
		$c1->add("2", "dos");
		$c1->add("3", "tres");
		$c1->add("4", "cuatro");
		$c1->add("5", "cinco");


		$c2 = new HtmlCombo("idc2", "3");
		$c2->add("1", "uno");
		$c2->add("2", "dos");
		$c2->add("3", "tres");
		$c2->add("4", "cuatro");
		$c2->add("5", "cinco");
		$c2->valueFromRequest();

		$c4 = new HtmlCombo("idc4", "2");
		$c4->add("1", "uno");
		$c4->add("2", "dos");
		$c4->add("3", "tres");
		$c4->setReadOnly(true);

		$campo2 = new HtmlEtiquetaValor("HtmlCombo", $c->toHtml() . $c1->toHtml() . $c2->toHtml() . $c4->toHtml());
		$tab->agregarSolapa("HtmlCombo", "", $campo2->toHtml());

		$acc = new HtmlAccordeon("Datos", "fa-truck");
		$acc->addEtiquetaValor("Nombre", "Marcos");
		$acc->addP("este es el rock de santa fe");
		$tab->agregarSolapa("HtmlAccordeon", "", $acc->toHtml());

		// boolean ------------------------------------------------------------
		$b1 = new HtmlBoolean("idb1", 1);
		$b2 = new HtmlBoolean2("idb2", 1);
		$b2->setValue(0);
		$b3 = new HtmlBoolean("idb3", 1);
		$b3->setReadOnly(true);
		$aValores = array();
		$par1 = new HtmlEtiquetaValor("HtmlBoolean", $b1->toHtml());
		$aValores[] = $par1->toHtml();
		$par2 = new HtmlEtiquetaValor("HtmlBoolean2", $b2->toHtml());
		$aValores[] = $par2->toHtml();

		$par3 = new HtmlEtiquetaValor("HtmlBoolean (readonly)", $b3->toHtml());
		$aValores[] = $par3->toHtml();
		$tab->agregarSolapa("HtmlBoolean", "", implode("", $aValores));

		// HtmlInputFile --------------------------------------------------------
		$aValores = array();

		$f1 = new HtmlInputFile("idf1", "");
		$campo5 = new HtmlEtiquetaValor("HtmlInputFile", $f1->toHtml());
		$aValores[] = $campo5->toHtml();

		$f2 = new HtmlInputFile("idf2", "");
		$f2->setValue("images/info.gif");
		$campo5 = new HtmlEtiquetaValor("HtmlInputFile", $f2->toHtml());
		$aValores[] = $campo5->toHtml();

		$f3 = new HtmlInputFile("idf3", "");
		$f3->setValue("images/info.gif");
		$f3->setReadOnly(true);
		$campo5 = new HtmlEtiquetaValor("HtmlInputFile", $f3->toHtml());
		$aValores[] = $campo5->toHtml();

		$f1 = new HtmlInputFile2("file22", "");
		$campo5 = new HtmlEtiquetaValor("HtmlInputFile 2", $f1->toHtml());
		$aValores[] = $campo5->toHtml();

		$f2 = new HtmlInputFile2("file24", "20201/f1584445478_100_1728.jpg");
		$campo5 = new HtmlEtiquetaValor("HtmlInputFile 2", $f2->toHtml());
		$aValores[] = $campo5->toHtml();

		$tab->agregarSolapa("HtmlInputFile2", "fa-upload", implode("", $aValores));

		$s1 = new HtmlSelector("ids1", "sc_querysall", "");
		$req->add("ids1", "selector 1");
		$s2 = new HtmlSelector("ids2", "sc_querysall", "");
		$s2->setValue("37");
		$s3 = new HtmlSelector("ids3", "sc_querysall", "");
		$s3->setReadOnly(true);
		$campo7 = new HtmlEtiquetaValor("HtmlSelector", $s1->toHtml() . $s2->toHtml() . $s3->toHtml());
		$tab->agregarSolapa("HtmlSelector", "", $campo7->toHtml());

		$d1 = new HtmlDateRange("fecha", "");
		$d2 = new HtmlDateRange("fecha2", "ultimo_anio");
		$d3 = new HtmlDateRange("fecha3", "mes_pasado");
		$campo8 = new HtmlEtiquetaValor("HtmlDateRange", $d1->toHtml() . "<br>" .  $d2->toHtml() . "<br>" .  $d3->toHtml());
		$tab->agregarSolapa("HtmlDateRange", "fa-calendar", $campo8->toHtml());

		$r = new HtmlRichText("id6", "");
		$r->setValue("<small>some small text</small><b>bold value</b>");
		$campo9 = new HtmlEtiquetaValor("HtmlRichText", $r->toHtml());
		$tab->agregarSolapa("HtmlRichText", "fa-edit", $campo9->toHtml());

		$area = new HtmlExpandingArea("zonag", "Salimos");
		$cbarcode = new HtmlBarcode("bar1", "1234567890");

		$cbarcode->setValue("111100010003000112000000");
		$cont = "HtmlBarcode: " . $cbarcode->toHtml() . "<br>";
		$color1 = new HtmlColor("color1", "#336699");
		$cont .= "HtmlColor: " . $color1->toHtml() . "<br>";

		$d1 = new HtmlDate2("d1", "");
		$d2 = new HtmlDate("d2", "2010-3-10");
		$d2->setRequerido();
		$d2->setOnlyDate();
		$d3 = new HtmlDate("d3", "2010-3-10");
		$d3->setReadOnly();
		$cont .= "HtmlDate y /2: " . $d1->toHtml() .	"<br>" . $d2->toHtml() . "<br>" . $d3->toHtml() . "<br>";
		$tab->agregarSolapa("HtmlExpandigArea", "", $area->start() . $cont . $area->end());

		$bOkCancel = new HtmlBotonOkCancel();
		$campo10 = new HtmlEtiquetaValor("", $bOkCancel->toHtml());
		$tab->agregarSolapa("Boton Ok/cancelear", "", $campo10->toHtml());


		$cbu = new HtmlCBU("in-cbu22", "");
		$contenido = $cbu->toHtml();

		$acc = new HtmlAccordeon("nueva");
		$acc->addEtiquetaValor("Plata", "$ 5.00 ");
		$acc->addEtiquetaValor("Dolares", 'U$S 55.98');
		$contenido2 = $acc->toHtml();
		$tab1 = new HtmlTabs2();
		$tab1->setHeight("600px");

		$tab1->agregarSolapa("Usuarios del sistema", "fa-address-book-o", $contenido);
		$tab1->agregarSolapa("Filtros de usuario", "fa-address-book-o", $contenido2);
		$tab1->agregarSolapa("Lugares", "", "<p>Hola 3</p>", false);

		$tab2 = new HtmlTabs2();
		$tab2->setHeight("600px");
		$tab2->agregarSolapa("Lectores", "", $contenido, "");
		$tab2->agregarSolapa("getter", "", $contenido2, "");
		$tab2->agregarSolapa("setters", "", "<p>Hola 3</p>", "");
		$tab->agregarSolapa("HtmlTabs2", "", $tab1->toHtml() . $tab2->toHtml());

		$gr = new HtmlGraphic2("gerencial1", "Evolucion de ventas");
		$gr->setLabels(array('enero', 'febrero', 'marzo', 'abril'));
		$gr->addDataSet("Facturado", array(105, 122, 124, 133));
		$gr->addDataSet("No Facturado", array(10, 132, 104, 13));
		$gr->addDataSet("Gasto", array(10, 102, 124, 120));
		$gr->addDataSet("IVA", array(1, 10, 12, 12));
		$tab->agregarSolapa("HtmlGraphic2", "", $gr->toHtml());

		$rs = getRs("select ifnull(m.Item, 'sin menu') as menu, op.id, op.nombre, op.url, op.icon, op.ayuda
					from sc_operaciones op
						left join sc_menuconsola m on (op.idmenu = m.idItemMenu)
					order by menu, op.nombre
					limit 100");

		$grid = new HtmlGrid($rs);
		$grid->setTitle("Clientes por localidad");
		$grid->setAgrupadores(0);
		$grid->setTotalizar(array("saldo"));

		$tab->agregarSolapa("HtmlGrid", "", $grid->toHtml());

		$pdf = new HtmlPdf("Clientes");
		$pdf->addGrid($grid);

		$pdfa5 = new HtmlPdf("Clientes A5");
		$dataEncab = [];	
		$dataEncab[] = [htmlBold("Hola"),  "chau"];
		$dataEncab[] = [htmlBold("Buen día"),  "buenas noches"];
		
		$pdfa5->addTable($dataEncab, "Saludos");

		$tab->agregarSolapa("HtmlPdf", "fa-file-pdf-o", $pdf->toLink() . $pdfa5->toLink());
		echo ($tab->toHtml());

		$tab = new htmlTabs2();
		$tab->setConvertible();

		$color1 = new HtmlColor("color2", "#336699");
		$color2 = new HtmlColor("color3", "#cccccc");
		$tab->agregarSolapa("HtmlColor", "", $color1->toHtml() . "<br>" . $color2->toHtml());

		$cbu = new HtmlCBU("in-cbu", "");
		$cbu1 = new HtmlCBU("in-cbu1", "0140430603690408632702");
		$cbu2 = new HtmlCBU("in-cbu2", "0140430603690408642702");
		$campo4 = new HtmlEtiquetaValor("HtmlCBU", $cbu->toHtml() . $cbu1->toHtml() . $cbu2->toHtml());
		$tab->agregarSolapa("HtmlCBU", "", $campo4->toHtml());

		$cuit = new HtmlCUIT("cuit1", "");
		$tab->agregarSolapa("HtmlCUIT", "", $cuit->toHtml());

		$fc = new HtmlFactura("fc1", "");
		$fc2 = new HtmlFactura("fc2", "");
		$tab->agregarSolapa("HtmlFactura", "fa-file-text-o", $fc->toHtml() . "<br>" . $fc2->toHtml());

		$txtemail = new HtmlInputTextEmail("toemail", "");
		$txtemail->setAutosuggest(true);
		$tab->agregarSolapa("HtmlInputTextEmail", "fa-envelope", $txtemail->toHtml());

		echo ($tab->toHtml());
		?>
		</div>

		<div id="ezequiel">
			<p>HtmlBoolean2</p>
			<script type="text/javascript">
				var bool = new HtmlBoolean2("tgl-prueba", "", "ezequiel");
				bool.toHtml();

				var bool2 = new HtmlBoolean2("tgl-1", "1", "ezequiel");
				bool2.toHtml();
			</script>
		</div>

		<script language="JavaScript" type="text/javascript">
			<?php
			echo ($req->toScript());
			?>

			function submitForm() {
				if (validar())
					document.getElementById('form1').submit();
			}
		</script>

	</form>

	<script language="JavaScript" type="text/javascript">
		/*An array containing all the country names in the world:*/
		var countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Anguilla", "Antigua & Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia & Herzegovina", "Botswana", "Brazil", "British Virgin Islands", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central Arfrican Republic", "Chad", "Chile", "China", "Colombia", "Congo", "Cook Islands", "Costa Rica", "Cote D Ivoire", "Croatia", "Cuba", "Curacao", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands", "Faroe Islands", "Fiji", "Finland", "France", "French Polynesia", "French West Indies", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauro", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", "Saint Pierre & Miquelon", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "St Kitts & Nevis", "St Lucia", "St Vincent", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor L'Este", "Togo", "Tonga", "Trinidad & Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks & Caicos", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Virgin Islands (US)", "Yemen", "Zambia", "Zimbabwe"];

		function alcargar() {

		}
	</script>


	<?php include("footer.php"); ?>

</body>

</html>