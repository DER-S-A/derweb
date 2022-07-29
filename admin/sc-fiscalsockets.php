<pre>
<?

function isBitOn($xvalor, $xbit)
{
	//$bit = hexdec($xbit);
	if (($xvalor & $xbit) == $xbit)
		return true;
	return false;
}

function translateState($xstate)
{
	$estados = array();
	
	$estados[0] = "";
	$estados[1] = "";
	$estados[2] = "";

	$estados[3] = "3 Error / falla de impresora";
	$estados[4] = "4 Impresora fuera de línea";
	$estados[5] = "5 Poco papel para la cinta de auditoria";
	$estados[6] = "6 Poco papel para comprobantes o Tiques";
	$estados[7] = "7 Buffer de impresora lleno";
	$estados[8] = "8 Buffer de impresora vacío";
	$estados[9] = "9 Toma de hojas sueltas frontal preparada";
	$estados[10] = "10 Hoja suelta frontal preparadas";
	$estados[11] = "11 Toma de hojas para validación preparada";
	$estados[12] = "12 Papel para validación presente ";
	$estados[13] = "13 Gaveta de dinero 1 o 2 abierto (solo impresoras de Ticket)";
	$estados[14] = "14";
	$estados[15] = "15 Impresora sin papel";

	$res = array();
	$bits = array();
	
	$i = 1;
	$bit = 1;
	while ($i <= 15)
	{
		if (isBitOn($xstate, $bit))
		{
			$res[] = $estados[$i];
			$bits[] = 1;
		}
		else
			$bits[] = 0;
				
		$i++;
		$bit = $bit * 2;
	}
	return $xstate . ": " . implode("", $bits) . ": " .  implode(", ", $res);
}

$sp = chr(28);

$conexion = fsockopen ('192.168.0.2', 1600);

if ($conexion) 
{

	$parametro = chr(42);
	$comando = $parametro;
	fwrite($conexion,$comando);
	$respuesta = fgets ($conexion, 50);
	echo "Comando: ".$comando."<br>";
	echo "Respuesta: ".$respuesta."<br>";
	
	$arta = explode($sp, $respuesta);
	var_dump($arta);
	echo "Respuesta 0: ". translateState(hexdec($arta[0])) ."<br>";
	
	$parametro = chr(98);
	$comando = $parametro.$sp."Santiago del Castillo".$sp."30271639868".$sp."C".$sp."C".$sp."Juncal 259 Olivos";

	fwrite($conexion,$comando);
	$respuesta = fgets ($conexion, 10);
	
	echo "Comando: ".$comando."<br>";
	echo "Respuesta: ".$respuesta."<br>";
	$arta = explode($sp, $respuesta);
	var_dump($arta);
	echo "Respuesta: ". translateState(hexdec($respuesta)) ."<br>";
	
	//Imprimir item
	$parametro = chr(66);
	$comando = $parametro.$sp."Fotocopias BN".$sp."1.00".$sp."1.00".$sp."21.00".$sp."M".$sp."0.00".$sp."0".$sp."T";
	fwrite($conexion,$comando);
	$respuesta = fgets ($conexion, 10);
	echo "Comando: ".$comando."<br>";
	echo "Respuesta: ".$respuesta."<br>";
	$arta = explode($sp, $respuesta);
	var_dump($arta);
	echo "Respuesta: ". translateState(hexdec($respuesta)) ."<br>";
		
	//open fiscal
	$parametro = chr(64);
	$comando = $parametro.$sp."T".$sp."T";
	fwrite($conexion,$comando);
	$respuesta = fgets ($conexion, 10);
	echo "Comando: ".$comando."<br>";
	echo "Respuesta: ".$respuesta."<br>";
	echo "Respuesta: ". translateState(hexdec($respuesta)) ."<br>";
		
	// subtotal
	$parametro = chr(67);
	$comando = $parametro.$sp."P".$sp."x".$sp."0";
	fwrite($conexion,$comando);
	$respuesta = fgets ($conexion, 10);
	echo "Comando: ".$comando."<br>";
	echo "Respuesta: ".$respuesta."<br>";
	$arta = explode($sp, $respuesta);
	var_dump($arta);
	
	echo "Respuesta: ". translateState(hexdec($respuesta)) ."<br>";
		
	// Total Render
	$parametro = chr(68);
	$comando = $parametro.$sp."Total Venta:".$sp."1.00".$sp."T"; //.$sp."0".$sp."0";
	fwrite($conexion,$comando);
	$respuesta = fgets ($conexion, 10);
	echo "Comando: ".$comando."<br>";
	echo "Respuesta: ".$respuesta."<br>";
	$arta = explode($sp, $respuesta);
	var_dump($arta);
	echo "Respuesta: ". translateState(hexdec($respuesta)) ."<br>";
		
	//close fiscal receipt
	$parametro = chr(69);
	$comando = $parametro;
	fwrite($conexion, $comando);
	$respuesta = fgets ($conexion, 15);
	echo "Comando: ".html_entity_decode($comando)."<br>";
	echo "Respuesta: ".$respuesta."<br>";
	$arta = explode($sp, $respuesta);
	var_dump($arta);
	echo "Respuesta: ". translateState(hexdec($respuesta)) ."<br>";
		
	fclose ($conexion);

}

?>
</pre>