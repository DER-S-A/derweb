<?php 

/**
 * Retorna el nombre del archivo de logs
 */
function logFile()
{
	$hoy = getdate(time());
	$fecha = $hoy["year"] . $hoy["mon"];
	return "./errores/log-" . $fecha . ".log";	
}

function logFileEmails()
{
	$hoy = getdate(time());
	$fecha = $hoy["year"] . $hoy["mon"];
	return "./errores/emails-" . $fecha . ".log";	
}

function logFileFiscal()
{
	$hoy = getdate(time());
	$fecha = $hoy["year"] . $hoy["mon"];
	return "./errores/log-fiscal-" . $fecha . ".log";
}

function logTime()
{		
	$hoy = getdate(time());
	$fecha =  "\n" . $hoy["mday"]  . "-" . $hoy["mon"] . "-" . $hoy["year"] . " " . $hoy["hours"] . ":" . $hoy["minutes"] . ":" . $hoy["seconds"] . "";
	return $fecha;	
}	

function gotoError($xcode, $xmsg)
{
	header("Location:./sc-error.php?code=" . $xcode . "&msg=" . $xmsg);
	exit; 
}

function logEmailError($xstr)
{
	error_log("\r\n" . logTime() . ": " . $xstr, 3, logFileEmails());
}


function logFiscal($xmsg, $xtime = false)
{
	if ($xtime)
		error_log("\r\n" . logTime(), 3, logFileFiscal());
	error_log("\r\n" . $xmsg, 3, logFileFiscal());
}

function goErrorDb($xsql,  $xcode, $xerror)
{
	$xerror = str_replace("\n", " ", $xerror);
    error_log("\n\n" . logTime() . "[$xsql], codigo=" . $xcode . ": " . $xerror, 3, logFile());
	
	$server = "\n  HTTP_USER_AGENT=" . $_SERVER['HTTP_USER_AGENT'];
	$server .= "\n  QUERY_STRING=" . $_SERVER['QUERY_STRING'];
	$server .= "\n  SCRIPT_FILENAME=" . $_SERVER['SCRIPT_FILENAME'];
	$server .= "\n  REMOTE_ADDR=" . $_SERVER['REMOTE_ADDR'];
	$server .= "\n  SCRIPT_NAME=" . $_SERVER['SCRIPT_NAME'];
	$server .= "\n  SERVER_SOFTWARE=" . $_SERVER['SERVER_SOFTWARE'];
	error_log($server, 3, logFile());

    setSession("sql-last-error", $xerror);
    
	gotoError($xcode, "Error al actualizar datos. $xcode . $xerror");
}

/*
Retorna el nombre del archivo de debug
*/
function debugFile()
{
	$hoy = getdate(time());
	$fecha = $hoy["year"] . $hoy["mon"] . $hoy["mday"];
	$debug = requestOrSession("debug");
	return "./debug/debug-" . $fecha . "-" . $debug . ".log";	
}
		
/*
Escribe en el archivo de debug
*/
function writeDebug($xstr)
{
    error_log(logTime() . ": " . $xstr, 3, debugFile());
}

function isDebug()
{
	$isdebug = requestOrSession("debug");
	if (strcmp($isdebug, "") == 0 || strcmp($isdebug, "0") == 0)
		return false;
	return true;	
}

function debug($xstr)
{
	if (isDebug())
		writeDebug($xstr);
}


function setdebug()
{
	setSession("debug", "1");
}	
?>