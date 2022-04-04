<?php 

require_once('terceros/phpmailer/class.phpmailer.php');

function emaLogErrorToFile($xError)
{
	$handle = fopen("errores/log-emails-" . date("Y-m-d") . ".log", "w+");
	fwrite($handle, "\r\n" . logTime() . ": " . $xError);
	fclose($handle);
}

/**
 * Analiza si es un nro correcto 
 * Saca 15, pone LAC adelante, saca cero inicial, saca 15 inicial
 */
function emaValidarCelular($xcel)
{
	$lac = getParameter("sc3-sms-lac", "2284");
	
	//saca todo del medio
	$xcel = str_replace(array("("), "", trim($xcel));
	$xcel = str_replace(array(",", ".", "-", "_", "  ", "(", ")", "*"), " ", trim($xcel));
	$xcel = str_replace("  ", " ", $xcel);

	//caso: celu de capital con el 01115
	if (startsWith($xcel, "01115"))
	{
		$xcel = str_replace("01115", "11", $xcel);
		if (strlen($xcel) == 10)
			return $xcel;
	}
	
	//caso 1: ideal, mide 10 y tiene el LAC adelante
	if (strlen($xcel) == 10 && startsWith($xcel, $lac))
		return $xcel;
	
	//mide 10 sin espacios;	
	if (strlen($xcel) == 10 && !strContiene($xcel, " "))
		return $xcel;
	
	//caso 2: arranca con 15 y la longitud con el LAC da 10
	if (startsWith($xcel, "15") && ((strlen($xcel) + strlen($lac) - 2)  == 10))
	{
		echo("<br> tiene 15: da 10: $lac $xcel");
		return $lac . substr($xcel, 2, 8);
	}

	//caso X: LAC + nro da 10
	if ((strlen($xcel) + strlen($lac))  == 10)
	{
		echo("<br> da 10 $lac $xcel");
		return $lac . $xcel;
	}
	//caso 3: algún delimitador analiza si es LAC + CEL
	$aCel = explode(" ", $xcel);
	if (sizeof($aCel) == 2)
	{
		$lacCel = trim($aCel[0]);
		$celCel = trim($aCel[1]);
		
		if (startsWith($lacCel, "0"))
			$lacCel = substr($lacCel, 1);

		if (sonIguales($lacCel, "15"))
			$lacCel = $lac;
		
		if (startsWith($celCel, "15"))
			$celCel = substr($celCel, 2);

		if (strlen($lacCel . $celCel) == 10)
			return $lacCel . $celCel;
	}

	return "";
}


/**
 * Retorna el destinatario y su conexión a la BD
 * @param int $xiddestinatario
 * @return BDObject
 */
function emaRsDestinatarios($xiddestinatario)
{
	$sql = "select dest.*, 
				con.servidor as conn_servidor,
				con.usuario as conn_usuario,
				con.clave as conn_clave,
				con.base as conn_base,
				con.tipo as conn_tipo
				
 			from ema_destinatarios dest 
				 left join sc_conexiones_bd con on (dest.idconexion = con.id)
			where dest.id = $xiddestinatario";

	$bd = new BDObject();
	$bd->execQuery($sql);
	return $bd;
}


/**
 * Lista los envios de email o sms agrupados por día
 * @param string $xtipo
 * @param string $xfdesde
 * @param string $xfhasta
 * @param string $xtipoListado
 * @return BDObject
 */
function emaRsEnviosAgrupados($xtipo, $xfdesde, $xfhasta, $xtipoListado)
{
	$auxDate1 = new HtmlDate($xfdesde);
	$auxDate2 = new HtmlDate($xfhasta);
	
	$sql = "select concat(year(fecha), '-', month(fecha)) as mes,
				day(fecha) as dia,
				count(*) as cantidad
			from ema_envios 
			where tipo_envio = '$xtipo' and 
				(fecha >= " . $auxDate1->getRequestToSql() . " and fecha <= " . $auxDate2->getRequestToSql2359() . ")";
	
	$sql .= " group by concat(year(fecha), '-', month(fecha)), day(fecha)";
	
	$bd = new BDObject();
	$bd->execQuery($sql);
	return $bd;
}


/**
 * A un grupo de destinatarios les manda un email dado en la plantilla
 * @param int $xiddestino 
 * @param int $xidtemplate
 */
function emaEnviarEmailing($xiddestino, $xidtemplate, $xcc, $xprueba, $xlog = 1, $xaTildados = array())
{
	//evalua los destinatarios
	$rsdest = emaRsDestinatarios($xiddestino);
	
	$sql = $rsdest->getValue("conjunto");
	$sql = str_replace("delete", "", $sql);
	$sql = str_replace("update", "", $sql);

	$tipo = $rsdest->getValue("conn_tipo");
	
	if (sonIguales($tipo, "MSSQL"))
	{
		require_once "dbobject-sql.php";
		
		$srv = $rsdest->getValue("conn_servidor");
		$usr = $rsdest->getValue("conn_usuario");
		$clave = $rsdest->getValue("conn_clave");
		$base = $rsdest->getValue("conn_base");
		$rs = new BDObjectSQL($srv, $usr, $clave, $base);
		$rs->execQuery($sql);
	}
	else
		$rs = getRs($sql);
		
	return emaEnviarEmailingRs($rs, $xidtemplate, $xcc, $xprueba, $xlog, false, $xaTildados);
}


/**
 * Dado un conjunto de datos (se espera que tenga la columan 'email') y un template,
 * hace el envío a todos
 */
function emaEnviarEmailingRs($xrs, $xidtemplate, $xcc, $xprueba, $xlog = 1, $xantispam = false, $xaTildados = array())
{
	$rstemplate = locateRecordId("ema_templates", $xidtemplate);
	if ($rstemplate->EOF())
		return "";
	
	$textoPlanoComoHtml = getParameterInt("email-texto-plano-html", 0);
	$templatehtml = $rstemplate->getValue("cuerpo_html");
	if ($textoPlanoComoHtml == 1)
	    $templatehtml = $rstemplate->getValue("cuerpo_text");
	$templateName = $rstemplate->getValue("nombre");
	
	return emaEnviarEmailingText($xrs, $templatehtml, $templateName, $xcc, $xprueba, $xlog, $xantispam, 0, false, $xaTildados);
}

/**
 * 
 * @return array array("exito"=>$aexito, "error"=>$aerror, "yaenviados"=>$aYaEnviados);
 */
function emaEnviarEmailingText($xrs, $xtemplateText, $xtemplateName, $xcc, $xprueba, $xlog = 1, $xantispam = false, $xattachs = 0, $xagrupaEmails = false, $xaTildados = array(), $xFromForzado = "")
{
	$MAX = 700;
	if ($xprueba == 1)
		$MAX = 3;

	$aexito = array();
	$aerror = array();
	$aYaEnviados = array();
	
	$emailFrom = getParameter("emailing-from", "enviador@sc3.com.ar");
	$delayAntiSpam = getParameterInt("email-delay-antispam", "1");
	
	//viene el parametro, sale de otra cuenta
	if (!esVacio($xFromForzado))
		$emailFrom = $xFromForzado;

	if ($xrs->EOF() && $xlog == 0) 
		return "destinatarios vacio...";
		
	$i = 0;	
	while (!$xrs->EOF() && ($i < $MAX))
	{
		if ($xantispam)
			$result = "";
					
		if (empty($xaTildados) || in_array($xrs->getValue("email"), $xaTildados))
		{
			//permite que el subject tambien tenga reemplazo de campos: no codifica, eso se hace en el enviar
			$subject = buildBody($xtemplateName, $xrs, "", false);

			// ";" o "," son tomados igual
			$to = str_replace(";", ",", $xrs->getValue("email"));
			
			//Si agrupa emails, avanza y arma body mientras no cambie el email
			$bodyhtml = "";
			if ($xagrupaEmails)
			{
				$toEmail = $xrs->getValue("email");
				$toEmailAnt = $toEmail;
				
				while (sonIguales($toEmailAnt, $toEmail) && !$xrs->EOF())
				{
					$bodyhtml .= buildBody($xtemplateText, $xrs, $xattachs);
					
					$xrs->Next();
					if (!$xrs->EOF())
					{
						$toEmailAnt = $toEmail;
						$toEmail = $xrs->getValue("email");
					}
				}
			}
			else
			{
				$bodyhtml = buildBody($xtemplateText, $xrs, $xattachs);
			}
			
			if (sonIguales($xprueba, "1"))
				$to = $xcc;		

			$cliente = $xrs->getValue("cliente");
			if (esVacio($cliente))
				$cliente = $xrs->getValue("propietario_id");
			
			$servicio = $xrs->getValue("servicio");
			if (esVacio($servicio))
				$servicio = $xrs->getValue("propietario_unidad");
			
			$nombre = $xrs->getValue("nombre");
			if (esVacio($nombre))
				$nombre = $xrs->getValue("propietario_nombre");
			
			//toma el adjunto del RS
			$adjunto1 = $xrs->getValue("adjunto1");
			if (!esVacio($adjunto1))
			{
				$xattachs = array();
				$xattachs[] = $adjunto1;
			}

			//para saber si efectivamente manda este email y luego tiene que esperar
			$envioEmail = false;
			
			//CONTROL DE no enviar dos emails el mismo dia al mismo email (con el mismo adjunto)
			$registro = str_replace("'", " ", $subject) . date(" Y-m-d ") . implode(" ", $xattachs);
			$rsLog = locateRecordWhere("sc_logs", "codigo_operacion = 'EMAIL' and 
													objeto_operado = '$to' and 
													descripcion = '$registro'");

			if ($rsLog->EOF())
			{
				//loguea el email enviado
				logOp("EMAIL", $to, $xrs->getValueInt("id"), $registro);
				
				$envioEmail = true;
				if (enviarEmail($emailFrom, $to, $xcc, $subject, "<html>$bodyhtml</html>", "", 1, 0, $xattachs))
				{
					$aexito[] = $to;
					$envioEmail = true;					
				}
				else
				{
					logOp("EMAIL-ERR", $to, $xrs->getValueInt("id"), "" . $subject . ", To: $to");
					$aerror[] = $to;		
				}
			}
			else
				$aYaEnviados[] = $to;
				
			//si envió email tiene sentido esperar, sinó no
			if ($envioEmail)
			{
				if ($xantispam)
					sleep($delayAntiSpam);
				else 
					//nos dormimos algo si o si (250.000 es un cuarto de segundo)
					usleep(250000);

				//para que no de timeout, siempre espera 20" mas desde ahora
				set_time_limit(20);
			}
		}
		if (!$xagrupaEmails)
			$xrs->Next();
			
		$i++;			
	}

	//analiza si quedaron por enviarse
	if (!$xrs->EOF())
		$aerror[] = "Se enviaron $MAX emails, aun quedan por enviarse";

	return array("exito"=>$aexito, "error"=>$aerror, "yaenviados"=>$aYaEnviados);
}


/**
 * Envia emails usando PHPMailer()
 * xattachs contiene un arreglo con archivos adjuntos a agregar 
 **/
function enviarEmail($xfrom, $xto, $xcc, $xsubject, $xbody, $xattach1 = "", $xaddfirma = false, $xsilent = false, $xattachs = 0)
{
	//saca el texto que le agrega la plantilla
    $xbody = str_replace(array("body{", "body {", "background", "}", "{", "#FFF;"), "", $xbody);
	
	global $SITIO;
	
	// the true param means it will throw exceptions on errors, which we need to catch
	$mail = new PHPMailer(true); 
	
	$smtpHost = getParameter("smtp-host", "");
	$smtpUser = getParameter("smtp-user", "");
	$smtpPass = getParameter("smtp-pass", "");
	$smtpPort = getParameterInt("smtp-port", "");
	if (!sonIguales($smtpHost, ""))
	{
		// telling the class to use SMTP
		$mail->IsSMTP(); 
		// enable SMTP authentication
		$mail->SMTPAuth = true;                  
		$mail->Host = $smtpHost;
		$mail->Username = $smtpUser;
		$mail->Password  = $smtpPass;
		if ($smtpPort != 0)
			$mail->Port = $smtpPort;
	}
	
	try
	{
		if ($xaddfirma)
			$xbody .= getFirmaEmails();

		//primero el reply to porque sinó lo pone al FROM
		$mail->AddReplyTo($xfrom);

		//manda desde misma cuenta
		if (!esVacio($smtpUser))
		{
			$mail->SetFrom($smtpUser, $SITIO);
		}
		else
			$mail->SetFrom($xfrom, $SITIO);

		// ";" o "," son tomados igual
		$xto = str_replace(";", ",", $xto);

		//tiene lista de destinatarios
		if (strContiene($xto, ","))
		{
			$toEmails = explode(",", $xto);
			foreach ($toEmails as $index => $email) 
			{
				$mail->AddAddress(strtolower(trim($email)), trim($email));
			}
		}
		else
			$mail->AddAddress(strtolower(trim($xto)), $xto);

		if (!sonIguales($xcc, ""))
			$mail->AddCC($xcc);
	
		//SI CONTIENE el logo lo manda como adjunto
		if (strContiene($xbody, "@@LOGO@@"))
		{
            $mail->AddEmbeddedImage('app/logo.jpg', 'logoimg');
            $xbody = str_replace("@@LOGO@@", "<img src=\"cid:logoimg\" />", $xbody);
		}

		//acumula todos los adjuntos que vienen por diferents lados
		$aAdjuntos = array();

        $mail->Subject = '=?UTF-8?B?' . base64_encode($xsubject) . '?=';
		$mail->AltBody = $xbody;
		$xbody = str_replace("\n", "<br>", $xbody);
		if (!sonIguales($xattach1, ""))
		{
			$aAdjuntos[] = basename($xattach1);
			$mail->AddAttachment($xattach1);
		}

		//analiza si hay adjuntos con el nombre adjunto1, 
		$i = 1;
		while ($i <= 5)
		{
			GLOBAL $UPLOAD_PATH_SHORT;
			$adjunto = Request("adjunto$i");
			if (!sonIguales($adjunto, ""))
			{
				$aAdjuntos[] = basename($adjunto);
				if (!strContiene($adjunto, "tmp/"))
					$mail->AddAttachment($UPLOAD_PATH_SHORT . "/" . $adjunto);
				else	
					$mail->AddAttachment($adjunto);
			}
			$i++;
		}

		//analiza si hay adjuntos con el nombre file1 (igual, pero no agrega /ufiles)
		$i = 1;
		while ($i <= 5)
		{
			GLOBAL $UPLOAD_PATH_SHORT;
			$adjunto = Request("file$i");
			if (!sonIguales($adjunto, ""))
			{
				$mail->AddAttachment($adjunto);
				$aAdjuntos[] = basename($adjunto);
			}

			$i++;
		}
		
		//No envias dos emails con el mismo titulo y adjunto ni de beduino
		$rsEnvio = getRs("select id 
						from ema_envios
						where destinatario = '$xto' and
							nombre = '$xsubject' and
							adjuntos = '" . implode(" ", $aAdjuntos) . "' and
							year(current_timestamp()) = year(fecha) and 
							month(current_timestamp()) = month(fecha) and
							week(current_timestamp()) = week(fecha) ");

		if (!$rsEnvio->EOF())
		{
			setMensaje("Email '$xsubject' ya enviado a $xto");
			logOp("EMAIL-DUP", $xto, 0, "$xsubject ya enviado");
			return false;
		}

		//analiza si hay un arreglo con archivos adjuntos y los agrega
		//si los adjuntos están como links, se blanquea el arreglo de adjuntos
		$attachsAsLinks = getParameter("email-attachs-links", 0);
		if (($attachsAsLinks == 0) && is_array($xattachs))
		{
			foreach ($xattachs as $attachFile)
			{
				$aAdjuntos[] = $attachFile;
				$mail->AddAttachment($UPLOAD_PATH_SHORT . "/" . $attachFile);
			}
		}
		
		//por si ya le agregaron el registro de envio
		if (!strContiene($xsubject, "SC3-ERROR")  && !strContiene($xbody, "sc-emailrecibido.php"))
		{
			//guarda en tabla ema_envios
			$idenvio = emaLogEnvio("EMAIL", $xto, 0, "", "", $xsubject, $xbody, implode(" ", $aAdjuntos));
			
			if ($idenvio != 0)
			{
				$url = "";
				$server = "";
				
				if (isset($_SERVER["SCRIPT_URI"]))
					$url = dirname($_SERVER["SCRIPT_URI"]);
				elseif (isset($_SERVER['SERVER_NAME']))
					$url = $_SERVER['SERVER_NAME'];
					
				if (strContiene($url, "www") || strContiene($url, ".com"))
				{
					$url .= "/sc-emailrecibido.php?id=$idenvio";
					$xbody .= "<br><br><img src=\"$url\" width=\"16\">";
				}
			}
		}
		
		$mail->IsHTML(true);
		$mail->MsgHTML($xbody);
		$ret = $mail->Send();
		
		if (!$ret)
		{
			logEmailError("Error al enviar email a $xto: " . $mail->ErrorInfo);
		}
		return $ret;
	} 
	catch (phpmailerException $e) 
	{
		if (!$xsilent)
			emaLogErrorToFile($e->errorMessage() . " <br>usando $smtpHost:$smtpPort - $smtpUser<br>" . $mail->ErrorInfo); 

	}
	catch (Exception $e) 
	{
		if (!$xsilent)
			emaLogErrorToFile($e->getMessage()); 
	}
	return false;
}

function getFirmaEmails()
{
	global $SITIO;
	return "\n\n---\nEnviado desde <b>" . $SITIO . "</b>\nUsamos <b><i>Sistemas SC3</i></b> - www.sc3.com.ar";
}


/**
 * Arma el body reemplazando todas los @CAMPO@ por el valor actual del RS
 */
function buildBody($xtemplate, $xrs, $xattachs = "", $usarHtmlEntities = true)
{
	$body = htmlVisible($xtemplate);

	$i = 0;	
	while ($i < $xrs->cantF())
	{
		$field = $xrs->getFieldName($i);
		$valor = str_replace("'", " ", $xrs->getValue($field));
		$tipo = $xrs->getFieldType($i);
		$valorFloat = 0.0;
		if (esCampoFecha($tipo))
		{
			$day = getdate(toTimestamp($valor));
			$valor = Sc3FechaUtils::formatFecha2($day, false);
		}
		elseif (esCampoFloat($tipo))
		{
			$valorFloat = (float) $valor;
			$valor = formatFloat($valor);
		}
		else
		{
		    //acentos !!!
		    if ($usarHtmlEntities)
		      $valor = htmlentities($valor);
		}
		
		$idmoneda = $xrs->getValueInt("idmoneda");
		$moneda = "pesos";
		if ($idmoneda == 2)
			$moneda = "dolares estadounidenes";
		
		if (getParameterInt("sc3-nro-letras-mayus", 0) == 1)
			$body = str_replace("@@" . $field . "@@", strtoupper(sayMoneyWords($moneda, $valorFloat)), $body);
		else
			$body = str_replace("@@" . $field . "@@", sayMoneyWords($moneda, $valorFloat), $body);
        			
		$body = str_replace("@" . $field . "@", $valor, $body);
		$i++;
	}
	
	$fechaHoy = Sc3FechaUtils::formatFechaHoy();
	$body = str_replace("@HOY@", $fechaHoy, $body);

	$fecha = getdate();
	$anio = $fecha["year"];
	$mes = $fecha["mon"];
	$dia = $fecha["mday"];
	
	$mesAnterior = $mes - 1;
	$anioAnterior = $anio;
	if ($mesAnterior == 0)
	{
		$mesAnterior = 12;
		$anioAnterior = $anio - 1;
	}
	$textMesAnterior = Sc3FechaUtils::mesAStr2($mesAnterior) . " de $anioAnterior";
	
	$body = str_replace("@DIA@", $dia, $body);
	$body = str_replace("@MES@", $mes, $body);
	$body = str_replace("@AÑO@", $anio, $body);
	$body = str_replace("@MES_ANTERIOR@", $textMesAnterior, $body);
	
	//hay archivos adjuntos que podrian enviarse como links
	if (is_array($xattachs))
	{
		GLOBAL $UPLOAD_PATH_SHORT;
		
		$baseUrl = getParameter("email-base-url", "http://www.sc3.com.ar/");
		$txtLinks = "";
		foreach ($xattachs as $i => $attach)
		{
			$icon = "images/scfile.png";
			if (endsWith($attach, "pdf"))
				$icon = "images/pdf.gif";

			$txtLinks .= href(img($baseUrl . $icon, "Adjunto $attach"), $baseUrl . $UPLOAD_PATH_SHORT . "/" . $attach, "_blanck") . " ";
		}
		
		$body = str_replace("@link_archivos@", $txtLinks, $body);
	}

	//saca el texto que le agrega la plantilla
	$body = str_replace(array("body", "background", "}", "{", "#FFF", ":#FFF;"), '', $body);
	
	return $body;
}


/**
 * Registra en tabla ema_envios
 * @param string $xtipo
 * @param string $xdestinatario
 * @param int $xidtemplate
 * @param string $xcliente
 * @param string $xservicio
 * @param string $xnombre
 * @return idenvio agregado en ema_envios para su confirmacion de lectura
 */
function emaLogEnvio($xtipo, $xdestinatario, $xidtemplate, $xcliente, $xservicio, $xnombre, $xMsg = "", $xadjuntos = "")
{
	
	$values = array();
	$values["idtemplate"] = $xidtemplate;
	$values["fecha"] = "CURRENT_TIMESTAMP()";	 
	$values["cliente"] = $xcliente;
	$values["nombre"] = substr($xnombre, 0, 90);
	$values["servicio"] = $xservicio;
	$values["tipo_envio"] = $xtipo;
	$values["adjuntos"] = $xadjuntos;
	$values["abierto"] = 0;
	$values["fecha_visualizacion"] = "null";
	$values["destinatario"] = substr($xdestinatario, 0, 60);
	$values["mensaje"] = substr(strip_tags($xMsg), 0, 950);
	
	$sql = insertIntoTable2("ema_envios", $values);

	$bd = new BDObject();
	$idenvio = $bd->execInsert($sql);
	
	return $idenvio;
}


function emaGetEtiquetaValor($xetiqueta, $xvalor, $xincluirSiVacia = true)
{
	if ($xincluirSiVacia || !sonIguales($xvalor, ""))
		return "\n$xetiqueta: $xvalor";
	return "";	
}

function emaGetEtiquetaValorBoolean($xetiqueta, $xvalor, $xincluirSiVacia = true)
{
	if (sonIguales($xvalor, "1"))
		$xvalor = "Si";
		
	if (sonIguales($xvalor, "0"))
		$xvalor = "No";
		
	if ($xincluirSiVacia || !sonIguales($xvalor, ""))
		return "\n$xetiqueta: $xvalor";
	return "";	
}

function emaGetEspacio()
{
	return "\n";
}

function emaGetSubtitulo($xtitulo)
{
	return "\n\n" . ucfirst($xtitulo) . "\n";
}

function emaGetEtiquetaValorHtml($xetiqueta, $xvalor)
{
	return "\n<br><b>$xetiqueta:</b> $xvalor";
}

function emaGetEspacioHtml()
{
	return "\n<br><br>";
}

function emaGetSubtituloHtml($xtitulo)
{
	return "\n<br><br><b>" . ucfirst($xtitulo) . "</b>";
}
?>