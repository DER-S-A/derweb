<?php 
require("funcionesSConsola.php");
checkUsuarioLogueado();

//contiene los campos requeridos
$req = new FormValidator();

//variables del Request
$rquery = Request("query");
$rregistrovalor = Request("registrovalor");
$modoCatalogo = RequestInt("modocatalog");

//una pila con otro nombre indica que está en una solapa con su propia pila
$stackname = Request("stackname");

if (strcmp($rquery,"") == 0)
	echo("<h3>Falta parametro: query</h3> Ej: sc-selitems.php<b>?query=qagenda</b>");

//guarda ultimo registro visto
setSession("sc3-last-$rquery", $rregistrovalor);
	
//Setea las variables del query actual (todas las anteriores)
$query_info = Array();
$tc = getCache();
$query_info = $tc->getQueryInfo($rquery);
saveCache($tc);

$qinfo = new ScQueryInfo($query_info);

$grupos = getGruposArray($qinfo->getFieldsDef());

//Manejo de details
$rmquery = Request("mquery");
$rmid = RequestInt("mid");
$rmfield = Request("mfield");

//ubica el Master Record para ver si tiene campos con el mismo nombre y ya toma el valor
$rowMaster = array();
if (!esVacio($rmquery))
{
	$query_infoMaster = Array();
	$tc = getCache();
	$query_infoMaster = $tc->getQueryInfo($rmquery);
	
	$rsMaster = locateRecord($query_infoMaster, $rmid);
	$rowMaster = $rsMaster->getRow();
}


//echo("query=$rquery, mquery=$rmquery, mid=$rmid, mfield=$rmfield");

function esInsert()
{
	if (RequestInt("insert") == 1)
		return true;
	else
		return false;	
}

//El copy es como un insert pero con un valor de registro
function esCopy()
{
	if ((RequestInt("insert") == 1) && (RequestInt("registrovalor") > 0))
	{
		return true;
	}
	else
		return false;
}


/*
Agrega un campo a la lista de requeridos
*/
function agregarRequerido($xnombrecampo, $xdesc)
{
	global $req;
	$req->add($xnombrecampo, $xdesc);
}

/**
 * Muestra un campo 
 *
 * @param string $xnombrecampo
 * @param string $xtype
 * @param int $xsize
 * @param string $xvalor
 * @param ScQueryInfo $xqinfo
 */
function processField($xnombrecampo, $xtype, $xsize, $xvalor, $xqinfo, $xrsPpal, $xDecimales = 2)
{
	//Manejo de details
	global $rmquery;
	global $rmid;
	global $rmfield;
	global $primerValorStr;
	global $qinfo;
	global $rregistrovalor;
	global $modoCatalogo;
	global $rowMaster;
	
	$mostrar = $xqinfo->getFieldVisible($xnombrecampo);
	$requerido = $xqinfo->isFieldRequired($xnombrecampo);
	$descripcionCampo = $xqinfo->getFieldCaption($xnombrecampo);
	
	$aRes = array();

	//ANALISIS DE SI SE PUEDE MOSTRAR
	if (esInsert() && !esCopy())
	{
		//NO entiendo la idea !
		if (esVacio($xvalor) && ($xqinfo->getFieldDefault($xnombrecampo) != ""))
			$xvalor = $xqinfo->getFieldDefault($xnombrecampo);
		$mostrar = 1;
	}	

	//el user root ve y edita todo
	if (esRoot() && $mostrar == 0)
	{
		$mostrar = 1;
		$descripcionCampo .= " *";
	}
	
	//ANALISIS DE SI SE PUEDE EDITAR
	$editar = $xqinfo->isFieldEditable($xnombrecampo);
	
	if ($mostrar == 0)
	{
		$aRes["valor"] = campoEscondido($xnombrecampo, $xvalor);
	}
	else
	{
		$class = $xqinfo->getFieldClass($xnombrecampo, array());
		if (esVacio($class) || sonIguales($class, "td_dato"))
			$class = "td_dato_edit";
		
		$ayuda = "";
		$fieldHelp = $xqinfo->getFieldHelp($xnombrecampo);
		if (($editar == 1) && (!esVacio($fieldHelp)))
		{
			$ayuda = " " . imgFa("fa-info-circle", "fa-lg", "verde", $fieldHelp);
		}
		
		$aRes["etiqueta"] = $descripcionCampo . $ayuda;
		
		if ($xqinfo->isKeyField($xnombrecampo))
		{
			if (!esInsert())
			{
				$hid = new HtmlHidden($xnombrecampo, $xvalor);
				$aRes["valor"] = $xvalor . " " . $hid->toHtml(); 
			}
			else
			{
				$hid = new HtmlHidden($xnombrecampo, "0");
				$aRes["valor"] = "(autonumerico)" . $hid->toHtml(); 
			}
		}
		else
		if (($editar == 0))
		{
			$fieldsDefs = $xqinfo->getFieldsDef();
			$fieldsRefs = $xqinfo->getFieldsRef();
			
			if (esCampoFecha($xtype))
			{
				$cfecha = new HtmlDate($xnombrecampo, $xvalor);
				$cfecha->setReadOnly(true);
				$aRes["valor"] = $cfecha->toHtml();
			}	
			else
			{	
			if (strcmp($xvalor, "") == 0)
				$aRes["valor"] = espacio();
			else	
			{
				if (esCampoStr($xtype))
				{
					if ($xqinfo->getFieldsDef()[$xnombrecampo]["password_field"] == 1)
					{
						$h = new HtmlHidden($xnombrecampo, $xvalor);
						$aRes["valor"] = $h->toHtml() . "********";
					}
					else				
						//Analiza si el campo es destinado para fotos o archivos
						if ($xqinfo->isFileField($xnombrecampo))
						{
							$h = new HtmlHidden($xnombrecampo, $xvalor);
							$aRes["valor"] = $h->toHtml() . img(getImagesPath() . $xvalor, $xvalor);
						} 	
						else
						{
							$h = new HtmlHidden($xnombrecampo, $xvalor);
							$aRes["valor"] = $h->toHtml() . $xvalor;
						}
				}
				else
				if (esCampoMemo($xtype))
				{
					$h = new HtmlHidden($xnombrecampo, $xvalor);
					$str = str_replace("\n", "<br>",$xvalor);
					$aRes["valor"] = $h->toHtml() . $str;
				}
				else		
				if (esCampoBoleano($xtype))
				{
					$bol = new HtmlBoolean2($xnombrecampo, $xvalor);
					$bol->setReadOnly(true);
					$aRes["valor"] = $bol->toHtml();
				}
				else		
				if (esCampoInt($xtype)) 
				{		
					//tipo integer (intentara armar combo con info de FK)
					if (!isset($fieldsRefs[$xnombrecampo]) || ($fieldsRefs[$xnombrecampo] == ""))
					{
						$inp = new HtmlInputText($xnombrecampo, $xvalor);
						$inp->setReadOnly(true);
						$aRes["valor"] = $inp->toHtml();
					}
					else
					{
						$sel = new HtmlSelector($xnombrecampo, $fieldsRefs[$xnombrecampo]["queryname"], $xvalor);
						$sel->setIgnoreFixedValue();
						$sel->setReadOnly();
						$aRes["valor"] = $sel->toHtml();
					}
					
				}
				else
				{
					$int = new HtmlInputText($xnombrecampo, $xvalor);
					$int->setReadOnly(true);
					$aRes["valor"] = $int->toHtml();		
				}
			}		
			}		
		//FIN DE LA VISUALIZACIoN de campos
		}
		else
		{	
			//-------comienza el analisis de tipo de cmapo, asumiendo que se pueden EDITAR					
			$fieldsDefs = $xqinfo->getFieldsDef();
			$fieldsRefs = $xqinfo->getFieldsRef();
			
			if (esCampoStr($xtype) || esCampoMemo($xtype))
			{
				if ($editar == 0)
				{
					$str = str_replace("<br>", "\n" , $xvalor);
					$aRes["valor"] = $str;
				}
				else
					{			
					//Analiza si el campo es destinado a seleccion de color
					if($xqinfo->isColorField($xnombrecampo))
					{
						$color = new HtmlColor($xnombrecampo, $xvalor);
						$aRes["valor"] = $color->toHtml();
					}
					else
					//Analiza si el campo es destinado para archivos
					if ($xqinfo->isFileField($xnombrecampo)) 					
					{
						$file = new HtmlInputFile($xnombrecampo, $xvalor);
						$file->setIframe($xqinfo->getQueryName());
						$aRes["valor"] = $file->toHtml();				
					}	
					else
					//Analiza si es un campo encriptado
					if ($xqinfo->isEncriptedField($xnombrecampo))
					{
						$aRes["valor"] = href("Valor protegido " . img("images/lock_edit.png", "Campo encriptado"), "javascript:openWindow('sc-showencripted.php?mquery=" . $xqinfo->getQueryName() . "&mid=" . $rregistrovalor . "&modoedit=1&campo=$xnombrecampo&valor=$xvalor')");
					}
					else						
					{
						$placeHolder = $descripcionCampo;
						if (!esVacio($fieldHelp))
							$placeHolder = $fieldHelp;

						//va para text area
						if ($xsize > 100)
						{
							if ($xsize <= 255)
								$rows = "2";
							else 
								$rows = "5";
							
							if (isset($fieldsDefs[$xnombrecampo]["rich_text"]) && ($fieldsDefs[$xnombrecampo]["rich_text"] == 1))
							{
								$r = new HtmlRichText($xnombrecampo, $xvalor);
								$aRes["valor"] = $r->toHtml(); 
							}
							else
							{
								$ancho = 70;							
								$classArea = "";
								if ($requerido)
									$classArea = "requerido";
									
								$valorEdit = htmlspecialchars($xvalor, ENT_COMPAT, "UTF-8");
																	
								//la codificacion dio vacia
								if (!esVacio($xvalor) && esVacio($valorEdit))
									$valorEdit = utf8_encode($xvalor);

								$aRes["valor"] = "<textarea rows=\"$rows\" cols=\"$ancho\" name=\"$xnombrecampo\" id=\"$xnombrecampo\" placeholder=\"$placeHolder\" class=\"$classArea\">" . $valorEdit . "</textarea>";
							}
						}
						else
						{
							//verifica si es un CBU
							if (strContiene($xnombrecampo, "cbu"))
							{
								$cbu = new HtmlCBU($xnombrecampo, $xvalor);
								$aRes["valor"] = $cbu->toHtml();
							}
							else
							if (strContiene($xnombrecampo, "email"))
							{
								$ema = new HtmlInputTextEmail($xnombrecampo, $xvalor);
								$aRes["valor"] = $ema->toHtml();
							}
							else
							{
								//verifica si es un CUIT / CUIL
								if (strContiene($xnombrecampo, "cuit") || strContiene($xnombrecampo, "cuil"))
								{
									$cbu = new HtmlCUIT($xnombrecampo, $xvalor);
									$cbu->setOnKeyUp("sc3firejs('" . $xqinfo->getQueryName() . "', '" . $xnombrecampo . "', 'onkeyup', event)");
									$aRes["valor"] = $cbu->toHtml();
								}
								else
								{
									$input = "<input type=\"";
									if (isset($fieldsDefs[$xnombrecampo]["password_field"]) && $fieldsDefs[$xnombrecampo]["password_field"]  == 1) 
										$input .= "password\" ";
									else
										$input .= "text\" ";

									$class = "texto";
									$size = $xsize;	
									if ($xsize > 50)
										$size = "50";

									$valorEdit = htmlspecialchars($xvalor, ENT_COMPAT, "UTF-8");

									//la codificacion dio vacia
									if (!esVacio($xvalor) && esVacio($valorEdit))
										$valorEdit = utf8_encode($xvalor);
									
									//textos cortos tambien valida: ej: codigo de articulos!
									$input .= " onblur=\"sc3firejs('" . $xqinfo->getQueryName() . "', '" . $xnombrecampo . "', 'onblur', event)\"";
									if ($requerido)
										$class .= " requerido";
									$input .= " class=\"$class\" ";	

									$input .= " size=\"" . $size . "\" placeholder=\"$placeHolder\" maxlength=\"" . $xsize . "\" name=\"" . $xnombrecampo . "\" id=\"" . $xnombrecampo . "\" value=\"" . $valorEdit . "\">\n";
									$aRes["valor"] = $input; 
								}
							}
						}
					}	
				}
			}	
			else
			if (esCampoBoleano($xtype))
			{
				$bol = new HtmlBoolean2($xnombrecampo, $xvalor);
				if ($requerido)
					$bol->setRequerido();
				
				$aRes["valor"] = $bol->toHtml();
			}
			else		
			if (esCampoInt($xtype))
			{ 
				//tipo integer (intentara armar combo con info de FK)
				if (!isset($fieldsRefs[$xnombrecampo]) || $fieldsRefs[$xnombrecampo] == "")
				{
					$inp = new HtmlInputText($xnombrecampo, $xvalor);
					$inp->setTypeInt();
					$inp->setOnKeyUp("sc3firejs('" . $xqinfo->getQueryName() . "', '" . $xnombrecampo . "', 'onkeyup', event)");
					if ($requerido)
						$inp->setRequerido();
						
					$aRes["valor"] = $inp->toHtml();			
				}
				else
				{				
					$selectorQuery = $fieldsRefs[$xnombrecampo]["queryname"];

					//analiza si existen 2 referencias de este campo a diferentes consultas, ej: idpersona en las direcciones
					//en la metadata se guarda con id de campo y nombre de query de destino
					if (isset($fieldsRefs[$xnombrecampo . "_" . $rmquery]))
					{
						$selectorQuery = $fieldsRefs[$xnombrecampo . "_" . $rmquery]["queryname"];
					}
					
					$sel = new HtmlSelector($xnombrecampo, $selectorQuery, $xvalor);
					if (sonIguales($xnombrecampo, $rmfield))
					{
						$sel->checkMaster();
						$sel->setIgnoreFixedValue();
					}
					else
					{
						//si es un INSERT, busca si el Registro Master tiene el mismo campo y le copia el valor
						if (esInsert())
						{
							if (isset($rowMaster[$xnombrecampo]))
								$sel->setValue($rowMaster[$xnombrecampo]);
						}
						
						if ($requerido)
						{
							$sel->setRequerido();
							agregarRequerido($xnombrecampo, $xqinfo->getFieldCaption($xnombrecampo));
							$requerido = false;
						}	
					}	
					
					//si hay valor o está editando, ignora el campo fijo
					if (!esVacio($xvalor) || !esInsert())
						$sel->setIgnoreFixedValue();
					
					if ($modoCatalogo)
						$sel->showLupa(false);
						$aRes["valor"] = $sel->toHtml();
				}
			}
			else
			if (esCampoFecha($xtype))
			{
				if ($requerido && esInsert() && !esCopy())
				{
					if (sonIguales($xvalor, ""))
						$xvalor = "INICIO_DIA";
					
					//valores por defecto
					if (sonIguales($xvalor, "FIN_DIA"))
					{
						$hoy = getdate();
						$xvalor = $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"] . " 23:59:00";
					}
					if (sonIguales($xvalor, "INICIO_DIA"))
					{
						$hoy = getdate();
						$xvalor = $hoy["year"] . "-" . $hoy["mon"] . "-" . $hoy["mday"] . " 00:00:00";
					}
					
					if (sonIguales($xvalor, "INICIO_MES"))
					{
						$hoy = getdate();
						$xvalor = $hoy["year"] . "-" . $hoy["mon"] . "-01 00:00:00";
					}
					
					if (sonIguales($xvalor, ""))
						$xvalor = "null";
				}
				
				//no requerido en insert, va NULO
				if (!$requerido && esInsert())
					$xvalor = "null";
						
				if (!esInsert())
				{
					if (sonIguales($xvalor, ""))
						$xvalor = "null";
				}
					
				$cfecha = new HtmlDate($xnombrecampo, $xvalor);
				if ($requerido)
				{
					$cfecha->setRequerido();
					agregarRequerido($xnombrecampo . "_a", $xqinfo->getFieldCaption($xnombrecampo));					
				}
					
				//activa el fireJs en caso de editar la fecha
				$cfecha->setOnKeyUpFireJs($xqinfo->getQueryName());
				$aRes["valor"] = $cfecha->toHtml();
				$requerido = 0;
			}
			else
			{
				if (esCampoFloat($xtype))
				{
					if ((isset($fieldsDefs[$xnombrecampo]["is_google_point"])) && sonIguales($fieldsDefs[$xnombrecampo]["is_google_point"], "1"))
						$aRes["valor"] = googlePointField($xnombrecampo,  $xvalor, $fieldsDefs[$xnombrecampo], $rregistrovalor, $qinfo->getQueryName(), $xrsPpal->getValue("longitud"));
					else				
					{
						$input = new HtmlInputText($xnombrecampo, $xvalor);
						$input->setOnKeyUp("sc3firejs('" . $xqinfo->getQueryName() . "', '" . $xnombrecampo . "', 'onkeyup', event)");
						
						//por los float que queden "#$%&/
						if ($xDecimales > 4)
							$xDecimales = 4;
						
						$input->setTypeFloat($xDecimales);
						if ($requerido)
							$input->setRequerido();
						$aRes["valor"] = $input->toHtml();
					}
				}
				else	
					//datos no contemplados
					if (!esInsert()) 
						$aRes["valor"] = "<input type=text size=" . $xsize . "  maxlength=" . $xsize . " name=" . $xnombrecampo . " id=" . $xnombrecampo . " value='" . $xvalor . "'> T:" . $xtype . "\n";
					else
						$aRes["valor"] = "<input type=text size=" . $xsize . "  maxlength=" . $xsize . " name=" . $xnombrecampo . " id=" . $xnombrecampo . " value=''> Tipo:" . $xtype . "\n";
				}
			}
		
		if ($requerido && ($editar == 1))
		{
			agregarRequerido($xnombrecampo, $xqinfo->getFieldCaption($xnombrecampo));
		}
			
	}
	return $aRes;
}


?>
<!doctype html>
<html lang="es">
<head>

<script type="text/javascript">

	function openWindowGmaps(theURL, winName) 
	{
		var desktop = window.open(theURL, winName, 'width=550,height=400,left=200,top=100');
		desktop.creator = this;
	}
		
</script>

<title>Editando:  <?php echo($qinfo->getQueryDescription()); ?></title>

<?php 
//arma favicon y retorna path
$favicon = favIconBuild($qinfo->getQueryIcon(), true);
?>

<?php include("include-head.php"); ?>

<style>

body
{
	margin-bottom: 50px;
}

</style>

</head>
<body onload="firstFocus();sc3firejs('<?php echo($qinfo->getQueryName()); ?>', '', 'onLoad', null)">

<form action="sc-upitem.php" method="post" name="form1" id="form1">

<div class="">

	<header class="w3-container headerTitulo">
		<?php
		$icon = $qinfo->getQueryIcon();
		if ($icon == "")
			$icon = "images/table.png";
		echo(img($icon, "") . " " . $qinfo->getQueryDescription());
		?>
			
		<div class="header-iconos">
			<a id="" class="boton-fa-sup" onclick="javascript:submitForm()" title="[F8] - Guardar cambios">
				<i class="fa fa-check fa-lg"> </i> Aceptar
			</a>
			<a id="linkcerrar" class="boton-fa-sup" onclick="javascript:cancelForm()" title="ESC - Cerrar sin guardar cambios">
				<i class="fa fa-times fa-lg"> </i> 
			</a>
		</div>
	</header>

</div>

	<?php 
	$div = new HtmlMessageDiv("divmsg");
	echo($div->toHtml());
	?>

<?php
	$str = "select * from " . $qinfo->getQueryTable();	

	if (!esInsert() || esCopy())
		$str.= " where " . $qinfo->getKeyField() . "=" . $rregistrovalor;
		
	$str.= " limit 1";	
	$rsPpal = new BDObject();
	$rsPpal->execQuery($str);
	$tab = new HtmlTabs2();
	$tab->setConvertible();

	$aCamposHidden = [];

	//recorre los grupos
	foreach ($grupos as $grupoActual)
	{
		$aCamposGupo = array();
		$indexCampo = 0;
		while ($indexCampo < $rsPpal->cantF())
		{
			$fieldName = $rsPpal->getFieldName($indexCampo);
			$fieldGroup = $qinfo->getFieldGrupo($fieldName);
			
			if (sonIguales($grupoActual, $fieldGroup)
					|| (sonIguales("", $fieldGroup) && sonIguales("Datos", $grupoActual)))
			{
				$valor = "";
				if (!esInsert() || esCopy())
					$valor = $rsPpal->getValue($indexCampo);
					
				//Siempre gana REQUEST
				if (!esVacio(Request($fieldName)))
					$valor = Request($fieldName);

				//acá sucede la magia
				$aDatos = processField($fieldName, $rsPpal->getFieldType($indexCampo), $rsPpal->getFieldSize($indexCampo), $valor, $qinfo, $rsPpal, $rsPpal->getFieldDecimals($indexCampo));

				//si viene etiqueta vacía es que el campo no se muestra
				if (isset($aDatos["etiqueta"]) && !esVacio($aDatos["etiqueta"]))
				{
					$campo = new HtmlEtiquetaValor($aDatos["etiqueta"], $aDatos["valor"]);
					$campo->setAncho100();
					$aCamposGupo[] = $campo->toHtml(); 
				}
				else
					//no se muestran pero genera campos escondidos con el valor actual
					$aCamposHidden[] = $aDatos["valor"];
			}
			
			$indexCampo++;
		}
		$tab->agregarSolapa($grupoActual, "", implode("",$aCamposGupo));
		
	}
	echo($tab->toHtml());

	//campos no visibles
	echo(implode("\r\n\t", $aCamposHidden));
	?>
	
	<input type="hidden" name="query" value="<?php echo($rquery); ?>" />
	<input type="hidden" name="goon" id="goon" value="0" />
	<input type="hidden" name="anterior" id="anterior" value="0" />
	<input type="hidden" name="registrovalor" value="<?php echo($rregistrovalor);  ?>" />
	<input type="hidden" name="modocatalog" value="<?php echo($modoCatalogo);  ?>" />
	<input type="hidden" name="stackname" value="<?php echo($stackname);  ?>" />

	<input type="hidden" name="mquery" value="<?php echo($rmquery); ?>" />
	<input type="hidden" name="mid" value="<?php echo($rmid);  ?>" />
	<input type="hidden" name="mfield" value="<?php echo($rmfield);  ?>" />

	<?php 
	if (esInsert())
		echo("<input type=\"hidden\" name=\"insert\" id=\"insert\" value=\"1\">");
	?>
		
<div class="div-botones-inferior">
	<button type="button" onclick="javascript:submitForm()" class="btn btn-success" title="Tambi&eacute;n F8" name="bsubmit" id="bsubmit" >
		<i class='fa fa-check fa-fw fa-lg'></i> Aceptar
	</button>
	
	<?php 
	if (esInsert())
	{
	?>
			<button type="button" onclick="javascript:submitForm2()" class="btn btn-success" title="Aceptar y crear uno igual [F9]" name="bsubmit2" id="bsubmit2" >
				<i class='fa fa-check-circle-o fa-fw fa-lg'></i> Aceptar y copiar
			</button>
	<?php
	}
	?>

	<button type="button" onclick="javascript:cancelForm()" class="btn btn-warning" name="bcancelar">
			<i class='fa fa-undo fa-lg'></i> Cancelar  
	</button>
</div>  	
		
<script language="JavaScript" type="text/javascript">

	<?php
	$req->setQueryName($qinfo->getQueryName());
	echo($req->toScript());
	?>
	
	function submitForm() 
	{
		updateRTEs();
		if (validar())
		{
			pleaseWait2();
			setTimeout('document.getElementById(\'form1\').submit();', 50);
		}	
	}
	
	//carga y sigue cargando
	function submitForm2() 
	{
		//make sure hidden and iframe values are in sync for all rtes before submitting form
		updateRTEs();
		if (validar())
		{
			pleaseWait2();
			document.getElementById('goon').value = '1';
			setTimeout('document.getElementById(\'form1\').submit();', 50);
		}	
	}
	

	//carga y sigue cargando
	function cancelForm() 
	{
		<?php
		$url = new HtmlUrl("hole.php");
		if ($qinfo->isDebil())
			$url->setUrl("sc-showgrid.php");
		if ($modoCatalogo == 1)
		{
			$url->setUrl("sc-opencatalog.php");
		}
		?>
		document.getElementById('form1').action = '<?php echo($url->toUrl()); ?>';
		document.getElementById('form1').submit();
	}
	
	</script>

</form>

</div>


<?php include("footer.php"); ?>

</body>
</html>