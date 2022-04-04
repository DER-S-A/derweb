<?php


/**
 * Si es una linea que solo tiene un comentario, la esquiva
 *
 * @param string $xline
 * @return boolean
 */
function esComentario($xline)
{
	return startsWith($xline, "/*") && endsWith($xline, "*/");
}

function esDebug($xline)
{
	return startsWith($xline, "debug(");
}

//funcion callback para ofuscar una variable que comienza con $ o detras del this->
function ofuscarVariable($xfound)
{
	$exceptions = array(
		'$password', '$database',   '$username', '$server', '$favicon',
		'$_SERVER', '$HTTP_SERVER_VARS', '$_ENV', '$_COOKIE',
		'$_GET', '$_POST', '$_FILES', '$_REQUEST',
		'$SC3_EVITAR_PRECARGA',

		'$_SESSION', '$GLOBALS', '$this', '$php_errormsg', '$condicion',
		'$record', '$class', '$SITIO', '$BD_SERVER', '$BD_USER',
		'$BD_PASSWORD', '$BD_DATABASE', '$FTP_SERVER', '$FTP_USER', '$FTP_PASSWORD',
		'$UPLOAD_PATH', '$UPLOAD_PATH_SHORT', '$IMAGE_SIZE',
		'$VERSION', '$RELEASE', '$HTTP_GET_VARS', '$HTTP_POST_VARS', '$FORCE_PHPIDS',
		'$total_time', '$finish_time', '$start_time',
		'$STO_KEY_VENTA', '$STO_KEY_AJU', '$STO_KEY_COMPRA', '$STO_KEY_NCND', '$STO_KEY_TRANSF', '$STO_KEY_ANULAR',
		'$STO_KEY_CAMBIO', '$STO_KEY_TIC', '$STO_KEY_IMPRIMIR', '$EMPTY_SELECTOR',
		'$INCLUDE_LIGHT',
		'$TABLA', '$TABLA_2', '$TABLA_3_1',	'$TABLA_3_2', '$TABLA_4_1',
		'$ANCHO_FECHA', '$ANCHO_COMPROBANTE',
		'$DESARROLLADOR_NOMBRE', '$DESARROLLADOR_WEB_SITE', '$DESARROLLADOR_LOGO'
	);

	$quita = 14;

	if (in_array($xfound[0], $exceptions))
		return $xfound[0];

	if (startsWith($xfound[0], "this->")) {
		$variables = explode("->", $xfound[0]);
		$variable = $variables[1];
		$last = substr($variable, -1);
		$variable = trim(substr($variable, 0, -1));
		return "this->v" . substr(md5($variable), $quita, -$quita) . $last;
	}

	//elimina el $ inicial
	$variable = substr($xfound[0], 1);
	return "\$v" . substr(md5($variable), $quita, -$quita);
}

//ofusca una linea de codigo
function ofuscarCodigo($xline, $xofuscar = true)
{
	if (!$xofuscar)
		return $xline;

	$line = preg_replace_callback('(\\$[a-zA-Z_][a-zA-Z0-9_]*)', "ofuscarVariable", $xline);
	//todos los this-> que sigan con =, )[]+-;
	$line = preg_replace_callback('(this->[a-zA-Z_][a-zA-Z0-9_]*[\\s|;|=|.|,|\\]|\\[|\\)|+|-])', "ofuscarVariable", $line);

	return $line;
}

/**
 * Ofusca un arreglo de líneas en base 64
 * @param array $xlineas
 * @return string
 */
function ofuscarCodigo64($xlineas)
{
	$lines = preg_replace_callback('(\\$[a-zA-Z_][a-zA-Z0-9_]*)', "ofuscarVariable", $xlineas);
	$lines = preg_replace_callback('(this->[a-zA-Z_][a-zA-Z0-9_]*[\\s|;|=|.|,|\\]|\\[|\\)|+|-])', "ofuscarVariable", $lines);

	$lines = str_replace("<?php", "", $lines);
	$lines = str_replace("<?", "", $lines);
	$lines = str_replace("?>", "", $lines);

	$lines = base64_encode($lines);
	return "<?php\r\n eval(base64_decode(\"" . $lines . "\")); \r\n?>";
}


function ofuscarNada($xi, $xline)
{
	return "";

	if ($xi < 15)
		return "";
	if (strContiene($xline, "<?"))
		return "";

	if ($xi % 4 == 0)
		return "/*" . substr(md5($xi), 5, 3) . "*/";
	return "";
}

function ofuscar($xfilename, $xofuscarVars = true, $xusarDecode64 = false, $xcarpeta = "")
{
	$SEPARADOR_HTML = "<!-- NO-PHP -->";
	$enc = "";
	if ($xusarDecode64)
		$enc = " (base64)";
	echo ("<br>ofuscando <b>$xfilename</b> $enc...");
	$lines = file($xfilename);

	$resOfuscar = "";
	//deja aislados los bloques PHP para ofuscarlos sin mezclar con HTML
	$lines2 = array();
	foreach ($lines as $line_num => $line) {
		$line = trim($line);

		$phpStart = "<?php";
		if (strContiene($line, $phpStart)) {
			$l2 = explode($phpStart, $line);
			$lines2[] = $l2[0];
			$lines2[] = $phpStart;
			$lines2[] = $l2[1];
		} else {
			$phpStart = "<?";
			if (strContiene($line, $phpStart)) {
				$l2 = explode($phpStart, $line);
				$lines2[] = $l2[0];
				$lines2[] = $phpStart;
				$lines2[] = $l2[1];
			} else {
				$phpStart = "?>";
				if (strContiene($line, $phpStart)) {
					$l2 = explode($phpStart, $line);
					$lines2[] = $l2[0];
					$lines2[] = $phpStart;
					$lines2[] = $l2[1];
				} else
					$lines2[] = $line;
			}
		}
	}
	$lines = $lines2;

	$res = "";
	$enComentario = false;
	$enPhp = false;

	foreach ($lines as $line_num => $line) {
		$line = trim($line);

		if (startsWith($line, "/*"))
			$enComentario = true;
		if (startsWith($line, "<?"))
			$enPhp = true;

		if (!$enPhp)
			$res .= $SEPARADOR_HTML . $line;

		if ($enPhp && !$enComentario && !esDebug($line)) {
			$line2 = explode("//", $line);
			if (count($line2) == 1) {
				$res .= ofuscarNada($line_num, $line) . ofuscarCodigo($line, $xofuscarVars) . " ";
				$resOfuscar .= ofuscarCodigo($line, $xofuscarVars) . " ";
			} else
				//si la partio en dos, usa la parte de la izq   
				if (count($line2) == 2) {
					$left = str_replace("\t", "", $line2[0]);
					$left = str_replace(" ", "", $left);
					if (!sonIguales($left, "")) {
						$res .= ofuscarCodigo($line, $xofuscarVars);
						$resOfuscar .= ofuscarCodigo($line, $xofuscarVars) . " ";
						echo ("<li>$line_num: posible comentario incorrecto en $xfilename:$line_num...($line)</li>");
					} else {
						$res .= substr($line2[0], 0, strlen($line2[0]) - 2) . " ";
						$resOfuscar .= substr($line2[0], 0, strlen($line2[0]) - 2) . " ";
					}
				}
		}
		if (endsWith($line, "*/"))
			$enComentario = false;
		if (endsWith($line, "?>"))
			$enPhp = false;
	}

	//la " {" no lo reduce porque hay un string buscado asi
	$sacar = array("\t", "\r\n", " {", " }", ") ", "   ", "  ", "  ", "; ", "} ", "{ ", ", ", "= ", " =", " . ", $SEPARADOR_HTML, "\r\n\r\n", "<? php");
	$poner = array(" ", " ", " {", "}", ")", " ", " ", " ", ";", "} ", "{", ", ", "=", "=", ".", "\r\n",          "\r\n",     "<?php");

	$res = str_replace($sacar, $poner, $res);
	$res = trim($res);

	if ($xusarDecode64) {
		$resOfuscar = str_replace($sacar, $poner, $resOfuscar);
		$res = ofuscarCodigo64($resOfuscar);
	}

	if (esVacio($xcarpeta))
		$filename = "./tmp/$xcarpeta" . $xfilename;
	else
		$filename = "$xcarpeta/$xfilename";

	$handle = fopen($filename, "w+");
	fwrite($handle, $res);
}


function ofuscarJs($xsrc, $xout)
{
	$enc = "";
	echo ("<br>ofuscando <b>$xsrc</b>...");
	$lines = file($xsrc);

	$res = "";
	$enComentario = false;
	$enPhp = false;

	foreach ($lines as $line_num => $line) {
		$line = trim($line);

		if (startsWith($line, "/*"))
			$enComentario = true;

		if (!$enComentario) {
			$line2 = explode("//", $line);
			if (count($line2) == 1) {
				$res .= $line . " ";
			} else
				//si la partio en dos, usa la parte de la izq
				if (count($line2) == 2) {
					$left = str_replace("\t", "", $line2[0]);
					$left = str_replace(" ", "", $left);
					if (!sonIguales($left, "")) {
						$res .= $line;
						echo ("<li>$line_num: posible comentario incorrecto en $xsrc :$line_num...($line)</li>");
					} else {
						$res .= substr($line2[0], 0, strlen($line2[0]) - 2) . " ";
					}
				}
		}
		if (endsWith($line, "*/"))
			$enComentario = false;
	}

	$sacar = array("\t", "\r\n", "  ");
	$poner = array(" ", " ", " ");

	$res = str_replace($sacar, $poner, $res);
	$res = trim($res);

	$filename =  $xout;
	$handle = fopen($filename, "w+");
	fwrite($handle, $res);
}
