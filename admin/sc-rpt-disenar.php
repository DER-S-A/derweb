<?php 
include("funcionesSConsola.php"); 
include("sc-rpt.php");
checkUsuarioLogueado();

$error = "";
$mid = RequestInt("mid");
$mquery = Request("mquery");
$idrep = $mid;

$rsDef = locateRecordWhere("sc_rpt_reportes", "id = $idrep", true);
$nombre = $rsDef->getValue("nombre");

//registra poder llamar a la funcion logRecuperarCodigoEnvio desde javascript
$ajaxH = sc3GetAjaxHelper();
$ajaxH->registerFunction("rptGuardarCampo", "sc-rpt.php");
sc3SaveAjaxHelper($ajaxH);

$pag = RequestInt("pagina");
if ($pag == 0)
	$pag = 1;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>sc3 - reporte <?php echo($nombre); ?></title>

<?php include("include-head.php"); ?>

<script type="text/javascript" src="scripts/fabric.min.js"></script>

</head>
<body onload="firstFocus()">

<form method="post" name="form1" id="form1">
  <?php
  $req = new FormValidator();
  ?>
  <table width="98%" border="0" align="center" cellpadding="1" cellspacing="2" class="dlg">
    <tr>
      <td colspan="2" align="center" class="td_titulo">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
          <tr>
            <td align="center"><?php echo(getOpTitle(Request("opid"))); ?></td>
            <td width="50" align="center">
		      	<?php 
		      	$cbo = new HtmlCombo("pagina", $pag);
		      	$cbo->add(1, "Pag 1");
		      	$cbo->add(2, "Pag 2");
		      	$cbo->add(3, "Pag 3");
		      	$cbo->add(4, "Pag 4");
		
		      	$cbo->onchangeSubmit();
		      	echo($cbo->toHtml());
		      	?>
			</td>
          </tr>
        </table>
      </td>
    </tr>
    
    <tr>
      <td colspan="2" align="left" class="td_dato">
      
	      <canvas id="canvas1" width="1200" height="800">      

	      </canvas>
	      
      </td>
    </tr>

    <tr>
      <td align="right" class="td_etiqueta">&nbsp;</td>
      <td class="td_dato">
      <?php 
      $bok = new HtmlBotonOkCancel();
      echo($bok->toHtml());
      ?>
      </td>
    </tr>
  </table>
  
<script language="JavaScript" type="text/javascript">


var idreporte = <?php echo($idrep); ?>;      
var pagina = <?php echo($pag); ?>;      

/// the main function
function getMousePos(canvas, e) 
{
    /// getBoundingClientRect is supported in most browsers and gives you
    /// the absolute geometry of an element
    var rect = canvas.getBoundingClientRect();

    /// as mouse event coords are relative to document you need to
    /// subtract the element's left and top position:
    return {x: e.clientX - rect.left, y: e.clientY - rect.top};
}


function canvasToXy(x, y)
{
	var ret = new Array();
	ret["x"] = x;
	ret["y"] = 800 - y;

	return ret;
}

function rptGuardarCampo(tipo, nombre, x, y)
{
	if (tipo == 'text')
	{
		console.log('por guardar ', nombre, tipo, x, y);

		xy = canvasToXy(x, y);
		x = xy["x"];
		y = xy["y"];
		pagTexto = pagina;
		if (y < 20)
		{
			pagTexto++;
			y = 400;
		}
		
		var params = [
	          			{"idreporte":idreporte,
	          			 "tipo":tipo,
	                	 "nombre":nombre,
	                	 "pagina":pagTexto,
	                	 "x": x,
	                	 "y": y}
		 				];

		sc3InvokeCallback('rptGuardarCampo', params, rptGuardarCampoCB);		
	}

	if (tipo == "group")
	{
		console.log('Grupo ', tipo, x, y);

	}
}

function rptGuardarCampoCB(aResult)
{
	if (xmlhttp2.readyState == 4 && xmlhttp2.status == 200)
	{ 
		strRta = xmlhttp2.responseText;
		aResult = JSON.parse(strRta);
		result = aResult['RESULT'];
		
		console.log('guardarCampoCB()', result);
	}
}

//turorial: http://fabricjs.com/fabric-intro-part-2
var canvas = new fabric.Canvas('canvas1');
canvas.backgroundColor = 'grey';

var rect = new fabric.Rect({
	  left: 10,
	  top: 10,
	  fill: 'white',
	  width: 1100,
	  height: 780
	});
rect.set('selectable', false); // make object unselec
canvas.add(rect);

var rect = new fabric.Rect({
	  left: 300,
	  top: 780,
	  fill: 'green',
	  width: 300,
	  height: 20
	});
rect.set('selectable', false); // make object unselec
canvas.add(rect);

canvas.on('mouse:up', function(options) 
			{
				if (options.target) 
				{
					var pos = getMousePos(document.getElementById('canvas1'), options.e), /// provide this canvas and event
			        x = pos.x,
			        y = pos.y;
					nombre = options.target.text;

					//busca el obj movido, no usa xy del mouse porque puede no agarrarlo del extremo
					i = 0;
					while (i < canvas.getObjects().length)
					{
						obj = canvas.getObjects()[i];
						if (obj.text == nombre) 
						{
							console.log("por guardar: " + obj.text + " (" + obj.type + "): " + obj.left + "," + obj.top);  
							rptGuardarCampo(obj.type, nombre, obj.left, obj.top);
						}
						i++;
					}
				}
			});

<?php 


function xyToCanvas($x, $y)
{
	$ret = array();
	$ret["x"] = $x;
	$ret["y"] = (800) - $y;
	
	return $ret;
}



$fontSize = $rsDef->getValueInt("tam_fuente") + 2;

$rsFields = locateRecordWhere("sc_rpt_reportes_campos", "idreporte = $idrep and pagina = $pag", true, "pagina");
$i = 1;
while (!$rsFields->EOF())
{
	$texto = $rsFields->getValue("texto");
	$x = $rsFields->getValueFloat("pos_x");
	$y = $rsFields->getValue("pos_y");
	$textFontSize = $fontSize + $rsFields->getValue("diff_fuente");

	$fontWeight = "";
	if ($rsFields->getValueInt("negrita") == 1)
		$fontWeight = ", fontWeight: 'bold'";
	
	$xy = xyToCanvas($x, $y);
	$x = $xy["x"];
	$y = $xy["y"];
	
	echo("var text$i = new fabric.Text('$texto', { left: $x, top: $y, fontSize: $textFontSize $fontWeight});
	canvas.add(text$i);
");		
		
	$i++;
	$rsFields->Next();
}

$x = 100;
$i = 1;
while ($x <= 1100)
{
	echo("var line$i = new fabric.Line([$x, 0, $x, 750], {
    stroke: 'grey',
    strokeWidth: 1,
    hasControls: false,
    hasRotatingPoint: false,
    padding: 1,
    opacity: 0.2,
    scaleX: 1,
    scaleY: 1,
    selectable: false
});
canvas.add(line$i);

");	
	
	$i++;
	$x = $x + 30;
}


$y = 100;
while ($y <= 900)
{
	//for (var i = Math.ceil(gridHeight / gridSizePX); i--;) {
   //lines.push(new fabric.Line([0, gridSizePX * i, gridWidth, gridSizePX * i], lineOption));
	
	echo("var line$i = new fabric.Line([0, $y, 1100, $y], {
			stroke: 'grey',
			strokeWidth: 1,
			hasControls: false,
			hasRotatingPoint: false,
			padding: 1,
			opacity: 0.2,
			scaleX: 1,
			scaleY: 1,
			selectable: false
});
			canvas.add(line$i);

");

	$i++;
	$y = $y + 30;
}

?>


      
</script>
</form>
<?php include("footer.php"); ?>
</body>
</html>
