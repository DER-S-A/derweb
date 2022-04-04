<?php
/*
Libreria controladores Hazar
Fecha: 31-oct-2013
Autor: Marcos C
*/


class Sc3FiscalHasar
{

	function __construct($xres)
	{
		
	}

	function logFiscal($xmsg, $xtime = false)
	{
		logFiscal($xmsg, $xtime);
	}

	function openConnection()
	{
		return true;
	}

	function closeConnection()
	{
		return 1;
	}

	function isConnOpened()
	{
		return false;
	}

	function leerCampoRta($xpos)
	{
		return "";
	}

	function leerCampoRtaInt($xpos)
	{
		return 0;
	}

	function nombreArticulo($xart)
	{
		$sacar = array("á", "A", "é", "í", "ó", "ú", "ñ", "*", "#", "(", ")", "°", ",", ".", "[", "]", "=", "   ", "  ");
		$poner = array("a", "A", "e", "i", "o", "u", "n", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " "  , " ");
		$str = substr(str_replace($sacar, $poner, $xart), 0, 24);
		return $str;
	}
	
	function leerCampoRtaFloat($xpos)
	{
		return 0.00;
	}

	function enviarComando($xcomando, $xleerCant = 10, $xtime = false)
	{
		return 1;
	}

	function leerRta()
	{
		return "";
	}

	function Syncro()
	{
		return 1;
	}

	/*
	 int @OpenFiscalReceipt(BYTE byVar0, BYTE byVar1)
	Abrir comprobante fiscal

	Devuelve 0 si no hubo error o -1 si se produjo un error.

	byVar0
	Tipo de documento {ABabDE}.
	A = Factura A, B = Factura B/C, a = Recibo A
	b = Recibo B/C, D = Nota de Debito A, E = Nota de Debito B o C

	byVar1
	T ó S (valor fijo) {TS}.
	@OpenFiscalReceipt|A|T

	Rta:
	01  Status de la impresora
	02  Status del controlador fiscal
	03  Número del comprobante abierto
	*/
	function OpenFiscalReceipt($xtipo, $xnro = "")
	{
		return 1;
	}

	/*
	 int @SetCustomerData(STRING strVar0, STRING strVar1, BYTE byVar2, BYTE byVar3, STRING strVar4)
	Datos comprador factura
	Devuelve 0 si no hubo error, -1 si se produjo un error.
	strVar0
	Nombre (hasta 50 caracteres)

	strVar1
	CUIT / Nro documento (hasta 11 caracteres)

	byVar2
	Responsabilidad frente al IVA {INEACBMSVWT}.
	I = Responsable inscripto, 	N = Responsable no inscripto (no existente en 330F), E = Exento, A = No responsable
	C = Consumidor final, B = Resp. no inscripto, venta de bienes de uso (no existente en 330F)
	M = Resp. monotributo, S = Monotributista social (solo disponible en 330F), V = Pequeno contribuyente eventual (solo disponible en 330F),
	W = Pequeno contribuyente eventual social (solo disponible en 330F), T = No categorizado

	byVar3
	Tipo de documento {CL1234}.
	C = CUIT, L = CUIL (Solo en modelos H330F y en version 2.01 de los modelos PL8F y H322F)
	0 = Libreta de enrolamiento, 1 = Libreta civica
	2 = Documento Nacional de Identidad, 3 = Pasaporte, 4 = Cedula de identidad, espacio = Sin calificador

	strVar4
	Domicilio comercial (hasta 50 caracteres)
	@SetCustomerData|ABBA Servitank|30702383923|I|C|Paunero 2770|
	*/
	function SetCustomerData($xcliente, $xcuit, $xiva, $xtipodoc, $xdomicilio)
	{
		return 1;
	}

	/*
	 int @PrintLineItem(STRING strVar0, DOUBLE dblVar1, DOUBLE dblVar2, STRING strVar3, BYTE byVar4, STRING strVar5, BYTE byVar6, BYTE byVar7)
	Imprimir item

	Devuelve 0 si no hubo error -1 si se produjo un error.

	strVar0
	Texto descripcion del item (hasta 50 caracteres)

	dblVar1
	Cantidad (nnnn.nnnnnnnnnn) (En SMH/P-321F, SMH/P-322F y SMH/P-330F: nueve numeros (incluyendo punto decimal movil))

	dblVar2
	Precio unitario (nnnnnnn.nnnn)

	strVar3
	Porcentaje IVA (nn.nn)/(**.**) (hasta 5 caracteres)

	byVar4
	Calificador de la operacion {Mm}.
	M = Suma monto, m = Resta monto

	strVar5
	Impuestos internos, +0.nnnnn = impuestos internos fijos
	0.nnnnn = impuestos internos porcentuales, $nnnn.nnnnn = impuestos internos fijos, %nnnn.nnnnn = imp. internos porcentuales

	byVar6
	Parametro display: 0, 1 o 2 {012}. (No tiene efecto en el presente modelo)

	byVar7
	T: precio total; otro caracter: precio base {TBO}.

	@PrintLineItem|Pila Eveready 1.5V|2.0|1.50|21.0|M|0.0|1|B
	*/
	function PrintLineItem($xarticulo, $xcant, $xpreciou, $xiva, $xsuma = true, $ximpuestos = 0, $xdisplay = 1)
	{
		return 1;
	}

	/*
	 int @Subtotal(BYTE byVar0, STRING strVar1, BYTE byVar2)

	Devuelve 0 si no hubo error, -1 si se produjo un error.

	byVar0
	Parametro impresion

	strVar1
	Reservado (llenar con un caracter cualquiera) (hasta 26 caracteres)

	byVar2
	Parametro display: 0, 1 o 2 {012}.

	@Subtotal|P|Subtotal|0|
	*/
	function Subtotal()
	{
		return 1;
	}

	/*
	 int @TotalTender(STRING strVar0, DOUBLE dblVar1, BYTE byVar2, BYTE byVar3)

	Devuelve 0 si no hubo error , -1 si se produjo un error.

	strVar0
	Texto de descripcion (hasta 50 caracteres)

	dblVar1
	Monto pagado (nnnnnnnnn.nn)

	byVar2
	Calificador operacion {CT}.
	C = Cancela el comprobante fiscal, T = Pago

	byVar3
	Parametro display: 0, 1 o 2 {012}.

	@TotalTender|Efectivo|20.00|T|0

	Rta:
	01  Status de la impresora
	02  Status del controlador fiscal
	03  Vuelto o monto faltante
	*/
	function TotalTender($xtexto, $xmonto, $xoperacion = "T", $xdisplay = 0)
	{
		return 1;
	}

	/*
	 @CloseFiscalReceipt

	Rta:
	01  Status de la impresora
	02  Status del controlador fiscal
	03  Número del comprobante fiscal recién emitido
	04  Cantidad de hojas numeradas impresas (Sólo en modelo SMH/P-330F y en la versión 2.01 de los modelos SMH/P-PL-8F y SMH/P-322)
	05  Número de CAI (Sólo en modelo SMH/P-330F y en la versión 2.01 de los modelos SMH/P-PL-8F y SMH/P-322F.)
	*/
	function CloseFiscalReceipt()
	{
		return 1;
	}

	/*

	int @DailyClose(BYTE byVar0)
	Cierre de jornada fiscal

	Devuelve 0 si no hubo error ó -1 si se produjo un error.

	Parámetros
	byVar0
	Z: Cierre de jornada fiscal; X: Informe X {XZ}.
	*/
	function cierreZ()
	{
		return 1;
	}


	/*
	 3 Error / falla de impresora.
	4 Impresora fuera de línea
	5 Poco papel para la cinta de auditoria (solo impresoras de Ticket)
	6 Poco papel para comprobantes o Tiques (solo impresoras de Ticket)
	7 Buffer de impresora lleno.
	8 Buffer de impresora vacío.
	9 Toma de hojas sueltas frontal preparada
	10 Hoja suelta frontal preparada.
	11 Toma de hojas para validación preparada.
	12 Papel para validación presente.
	13 Gaveta de dinero 1 o 2 abierto (solo impresoras de Ticket).
	14 Sin uso.
	15 Impresora sin papel.
	*/
	function translateState($xstate)
	{
		$estados[] = "";
		$estados[] = "";
		$estados[] = "";

		$estados[3] = "3 Error / falla de impresora.";
		$estados[4] = "4 Impresora fuera de línea";
		$estados[5] = "5 Poco papel para la cinta de auditoria";
		$estados[6] = "6 Poco papel para comprobantes o Tiques";
		$estados[7] = "7 Buffer de impresora lleno.";
		$estados[8] = "8 Buffer de impresora vacío.";
		$estados[9] = "9 Toma de hojas sueltas frontal preparada";
		$estados[10] = "10 Hoja suelta frontal preparada.";
		$estados[11] = "11 Toma de hojas para validación preparada.";
		$estados[12] = "12 Papel para validación presente. ";
		$estados[13] = "13 Gaveta de dinero 1 o 2 abierto (solo impresoras de Ticket).";
		$estados[15] = "15 Impresora sin papel.";

		$res = array();
		$res[] = $xstate;

		$i = 3;
		while ($i <= 15)
		{
			if (isBitOn($xstate, $i))
				$res[] = $estados[$i];
			$i++;
		}
		return implode(", ", $res);
	}

}



class Sc3FiscalHasarSerial extends Sc3FiscalHasar
{
	var $mcom = "COM1:";
	var $fcom = false;
	var $resbuffer = "";

	function Sc3FiscalHasar($xcom = "COM1:")
	{
		$this->mcom = $xcom;
		$this->openConnection();
	}

	function openConnection()
	{
		$this->fcom = fopen($this->mcom, "w+");
		if (!$this->fcom)
		{
			$this->logFiscal("Error: Imposible abrir puerto.", true);
			return false;
		}
		return true;
	}

	function closeConnection()
	{
		if ($this->isConnOpened())
			fclose($this->fcom);
		return 1;
	}

	function isConnOpened()
	{
		if (!$this->fcom)
			return false;
		return true;
	}

	function leerCampoRta($xpos)
	{
		$arta = explode("|", $this->resbuffer);
		if (isset($arta[$xpos - 1]))
			return $arta[$xpos - 1];
		return "";
	}

	function leerCampoRtaInt($xpos)
	{
		return (int) $this->leerCampoRta($xpos);
	}

	function leerCampoRtaFloat($xpos)
	{
		return round(floatval($this->leerCampoRta($xpos)), 2);
	}

	function enviarComando($xcomando, $xleerCant = 10, $xtime = false)
	{
		$this->logFiscal($xcomando, $xtime);
		if ($this->isComOpened())
		{
			$res = fwrite($this->fcom, $xcomando);
			if ($res === FALSE)
			{
				$this->logFiscal("Error al enviar comando");
				return 0;
			}
			if ($res != strlen($xcomando))
			{
				$this->logFiscal("Error: se escribieron $res bytes de un total de " . strlen($xcomando));
				return 0;
			}
			$this->resbuffer = fgets($this->fcom, 1);
			$irta = intval($this->resbuffer);
			$this->logFiscal("Rta (handler: " . $this->fcom . "): ($irta)" . bin2hex($this->resbuffer));
		}
		else
		{
			$this->logFiscal("Error: envio de comando con puerto cerrado");
			return 0;
		}
		return 1;
	}

	function leerRta()
	{
		$res = fread($this->fcom, 1);
		$this->logFiscal("Resultado: $res");
		return $res;
	}

	function Syncro()
	{
		return $this->enviarComando("@Sincro");
	}

	/*
	 int @OpenFiscalReceipt(BYTE byVar0, BYTE byVar1)
	Abrir comprobante fiscal

	Devuelve 0 si no hubo error o -1 si se produjo un error.

	byVar0
	Tipo de documento {ABabDE}.
	A = Factura A, B = Factura B/C, a = Recibo A
	b = Recibo B/C, D = Nota de Debito A, E = Nota de Debito B o C

	byVar1
	T ó S (valor fijo) {TS}.
	@OpenFiscalReceipt|A|T

	Rta:
	01  Status de la impresora
	02  Status del controlador fiscal
	03  Número del comprobante abierto
	*/
	function OpenFiscalReceipt($xtipo, $xnro = "")
	{
		$this->Syncro();
		$this->enviarComando("@OpenFiscalReceipt|$xtipo|T", true);
		return $this->leerCampoRtaInt(3);
	}

	/*
	 int @SetCustomerData(STRING strVar0, STRING strVar1, BYTE byVar2, BYTE byVar3, STRING strVar4)
	Datos comprador factura
	Devuelve 0 si no hubo error, -1 si se produjo un error.
	strVar0
	Nombre (hasta 50 caracteres)

	strVar1
	CUIT / Nro documento (hasta 11 caracteres)

	byVar2
	Responsabilidad frente al IVA {INEACBMSVWT}.
	I = Responsable inscripto, 	N = Responsable no inscripto (no existente en 330F), E = Exento, A = No responsable
	C = Consumidor final, B = Resp. no inscripto, venta de bienes de uso (no existente en 330F)
	M = Resp. monotributo, S = Monotributista social (solo disponible en 330F), V = Pequeno contribuyente eventual (solo disponible en 330F),
	W = Pequeno contribuyente eventual social (solo disponible en 330F), T = No categorizado

	byVar3
	Tipo de documento {CL1234}.
	C = CUIT, L = CUIL (Solo en modelos H330F y en version 2.01 de los modelos PL8F y H322F)
	0 = Libreta de enrolamiento, 1 = Libreta civica
	2 = Documento Nacional de Identidad, 3 = Pasaporte, 4 = Cedula de identidad, espacio = Sin calificador

	strVar4
	Domicilio comercial (hasta 50 caracteres)
	@SetCustomerData|ABBA Servitank|30702383923|I|C|Paunero 2770|
	*/
	function SetCustomerData($xcliente, $xcuit, $xiva, $xtipodoc, $xdomicilio)
	{
		$res = array();
		$res[] = "@SetCustomerData";
		$res[] = substr($xcliente, 0, 49);
		$res[] = $xcuit;
		$res[] = $xiva;
		$res[] = $xtipodoc;
		$res[] = substr($xdomicilio, 0, 49);
		return $this->enviarComando(implode($res, "|") . "|");
	}

	/*
	 int @PrintLineItem(STRING strVar0, DOUBLE dblVar1, DOUBLE dblVar2, STRING strVar3, BYTE byVar4, STRING strVar5, BYTE byVar6, BYTE byVar7)
	Imprimir item

	Devuelve 0 si no hubo error -1 si se produjo un error.

	strVar0
	Texto descripcion del item (hasta 50 caracteres)

	dblVar1
	Cantidad (nnnn.nnnnnnnnnn) (En SMH/P-321F, SMH/P-322F y SMH/P-330F: nueve numeros (incluyendo punto decimal movil))

	dblVar2
	Precio unitario (nnnnnnn.nnnn)

	strVar3
	Porcentaje IVA (nn.nn)/(**.**) (hasta 5 caracteres)

	byVar4
	Calificador de la operacion {Mm}.
	M = Suma monto, m = Resta monto

	strVar5
	Impuestos internos, +0.nnnnn = impuestos internos fijos
	0.nnnnn = impuestos internos porcentuales, $nnnn.nnnnn = impuestos internos fijos, %nnnn.nnnnn = imp. internos porcentuales

	byVar6
	Parametro display: 0, 1 o 2 {012}. (No tiene efecto en el presente modelo)

	byVar7
	T: precio total; otro caracter: precio base {TBO}.

	@PrintLineItem|Pila Eveready 1.5V|2.0|1.50|21.0|M|0.0|1|B
	*/
	function PrintLineItem($xarticulo, $xcant, $xpreciou, $xiva, $xsuma = true, $ximpuestos = 0, $xdisplay = 1)
	{
		$res = array();
		$res[] = "@PrintLineItem";
		$res[] = substr($xarticulo, 0, 49);
		$res[] = formatFloat($xcant);
		$res[] = formatFloat($xpreciou);
		$res[] = formatFloat($xiva);
		if ($xsuma)
			$res[] = "M";
		else
			$res[] = "m";
		$res[] = $ximpuestos;
		$res[] = $xdisplay;
		$res[] = "B";

		return $this->enviarComando(implode($res, "|"));
	}

	/*
	 int @Subtotal(BYTE byVar0, STRING strVar1, BYTE byVar2)

	Devuelve 0 si no hubo error, -1 si se produjo un error.

	byVar0
	Parametro impresion

	strVar1
	Reservado (llenar con un caracter cualquiera) (hasta 26 caracteres)

	byVar2
	Parametro display: 0, 1 o 2 {012}.

	@Subtotal|P|Subtotal|0|
	*/
	function Subtotal()
	{
		return $this->enviarComando("@Subtotal|P|Subtotal|0|");
	}

	/*
	 int @TotalTender(STRING strVar0, DOUBLE dblVar1, BYTE byVar2, BYTE byVar3)

	Devuelve 0 si no hubo error , -1 si se produjo un error.

	strVar0
	Texto de descripcion (hasta 50 caracteres)

	dblVar1
	Monto pagado (nnnnnnnnn.nn)

	byVar2
	Calificador operacion {CT}.
	C = Cancela el comprobante fiscal, T = Pago

	byVar3
	Parametro display: 0, 1 o 2 {012}.

	@TotalTender|Efectivo|20.00|T|0

	Rta:
	01  Status de la impresora
	02  Status del controlador fiscal
	03  Vuelto o monto faltante
	*/
	function TotalTender($xtexto, $xmonto, $xoperacion = "T", $xdisplay = 0)
	{
		$xmonto = formatFloat($xmonto);
		return $this->enviarComando("@TotalTender|Efectivo|$xmonto|T|$xdisplay");
	}

	/*
	 @CloseFiscalReceipt

	Rta:
	01  Status de la impresora
	02  Status del controlador fiscal
	03  Número del comprobante fiscal recién emitido
	04  Cantidad de hojas numeradas impresas (Sólo en modelo SMH/P-330F y en la versión 2.01 de los modelos SMH/P-PL-8F y SMH/P-322)
	05  Número de CAI (Sólo en modelo SMH/P-330F y en la versión 2.01 de los modelos SMH/P-PL-8F y SMH/P-322F.)
	*/
	function CloseFiscalReceipt()
	{
		$res = $this->enviarComando("@CloseFiscalReceipt");
		$res = $this->leerCampoRtaInt(3);
			
		$this->closeConnection();
		return $res;
	}

	/*

	int @DailyClose(BYTE byVar0)
	Cierre de jornada fiscal

	Devuelve 0 si no hubo error ó -1 si se produjo un error.

	Parámetros
	byVar0
	Z: Cierre de jornada fiscal; X: Informe X {XZ}.
	*/
	function cierreZ()
	{
		return $this->enviarComando("@DailyClose|Z");
	}

}



/**
 * Se comunica por un socket al puerto 1600
 * En el equipo con la controladora debe estar ejecutandose: wspooler -p3 -k
 * Ver ayuda de comandos en: PUBLTICK.pdf
 * @author marcos
 */
class Sc3FiscalHasarSocket extends Sc3FiscalHasar
{
	var $conexion = false;
	var $port = 1600;
	var $ip = "";
	var $nroComprobante = 0;
	
	//separador de campos
	var $sp = 0;
	
	var $resbuffer = "";
	var $arta = array();

	function Sc3FiscalHasarSocket($xip, $xport = 1600)
	{
		$this->sp = chr(28);
		$this->ip = $xip;
		$this->port = $xport;
		$this->openConnection();
	}

	function getNroComprobante()
	{
		return $this->nroComprobante;
	}
	
	function openConnection()
	{
		$errno = 0;
		$errstr = "";
		$this->conexion = fsockopen($this->ip, $this->port, $errno, $errstr, 10);
		if (!$this->conexion)
		{
			$this->logFiscal("Error: Imposible abrir socket en " . $this->ip . ":" . $this->port . ": $errno - $errstr", true);
			return false;
		}
		return true;
	}

	function closeConnection()
	{
		if ($this->isConnOpened())
			fclose($this->conexion);
		$this->conexion = false;
		return 1;
	}

	function isConnOpened()
	{
		if (!$this->conexion)
			return false;
		return true;
	}

	function leerCampoRta($xpos)
	{
		if (isset($this->arta[$xpos - 1]))
			return $this->arta[$xpos - 1];
		return "";
	}

	function leerCampoRtaInt($xpos)
	{
		return (int) $this->leerCampoRta($xpos);
	}

	function leerCampoRtaFloat($xpos)
	{
		return round(floatval($this->leerCampoRta($xpos)), 2);
	}

	function enviarComando($xcomando, $xleerCant = 10, $xtime = false)
	{
		$this->logFiscal($xcomando, $xtime);
		if ($this->isConnOpened())
		{
			fwrite($this->conexion, $xcomando);
			$this->resbuffer = fgets ($this->conexion, $xleerCant);
			
			if ($this->resbuffer === FALSE)
			{
				$this->logFiscal("Error al enviar comando");
				return 0;
			}
			$this->arta = explode($this->sp, $this->resbuffer);
			$this->logFiscal("Rta: " . implode("|", $this->arta));
		}
		else
		{
			$this->logFiscal("Error: envio de comando con puerto cerrado");
			return 0;
		}
		return 1;
	}

	function Syncro()
	{
		/*	
		$parametro = chr(42);
		*/
		$this->enviarComando(chr(42), 50);
		$this->nroComprobante = intval($this->arta[2]) + 1;		
	}

	/*
	 int @OpenFiscalReceipt(BYTE byVar0, BYTE byVar1)
	Abrir comprobante fiscal

	Devuelve 0 si no hubo error o -1 si se produjo un error.

	byVar0
	Tipo de documento {ABabDE}.
	A = Factura A, B = Factura B/C, a = Recibo A
	b = Recibo B/C, D = Nota de Debito A, E = Nota de Debito B o C

	byVar1
	T ó S (valor fijo) {TS}.
	@OpenFiscalReceipt|A|T

	Rta:
	01  Status de la impresora
	02  Status del controlador fiscal
	03  Número del comprobante abierto
	*/
	function OpenFiscalReceipt($xtipo, $xnro = "")
	{
		$this->Syncro();
		
		/*	
		$parametro = chr(64);
		$comando = $parametro.$sp."T".$sp."T";
		*/
		$sp = $this->sp;
		$parametro = chr(64);
		$comando = $parametro.$sp."$xtipo".$sp."T";
		
		$this->enviarComando($comando);
		//$this->nroComprobante = $this->leerCampoRtaInt(3);
		return $this->getNroComprobante();
	}

	/*
	 int @SetCustomerData(STRING strVar0, STRING strVar1, BYTE byVar2, BYTE byVar3, STRING strVar4)
	Datos comprador factura
	Devuelve 0 si no hubo error, -1 si se produjo un error.
	strVar0
	Nombre (hasta 50 caracteres)

	strVar1
	CUIT / Nro documento (hasta 11 caracteres)

	byVar2
	Responsabilidad frente al IVA {INEACBMSVWT}.
	I = Responsable inscripto, 	N = Responsable no inscripto (no existente en 330F), E = Exento, A = No responsable
	C = Consumidor final, B = Resp. no inscripto, venta de bienes de uso (no existente en 330F)
	M = Resp. monotributo, S = Monotributista social (solo disponible en 330F), V = Pequeno contribuyente eventual (solo disponible en 330F),
	W = Pequeno contribuyente eventual social (solo disponible en 330F), T = No categorizado

	byVar3
	Tipo de documento {CL1234}.
	C = CUIT, L = CUIL (Solo en modelos H330F y en version 2.01 de los modelos PL8F y H322F)
	0 = Libreta de enrolamiento, 1 = Libreta civica
	2 = Documento Nacional de Identidad, 3 = Pasaporte, 4 = Cedula de identidad, espacio = Sin calificador

	strVar4
	Domicilio comercial (hasta 50 caracteres)
	@SetCustomerData|ABBA Servitank|30702383923|I|C|Paunero 2770|
	*/
	function SetCustomerData($xcliente, $xcuit, $xiva, $xtipodoc, $xdomicilio)
	{
		/*
		$parametro = chr(98);
		$comando = $parametro.$sp."Santiago del Castillo".$sp."30271639868".$sp."C".$sp."C".$sp."Juncal 259 Olivos";
		*/
		$res = array();
		$res[] = chr(98);
		$res[] = substr($xcliente, 0, 20);
		$res[] = $xcuit;
		$res[] = $xiva;
		$res[] = $xtipodoc;
		$res[] = substr($xdomicilio, 0, 20);
		return $this->enviarComando(implode($res, $this->sp), 10);
	}

	/*
	 int @PrintLineItem(STRING strVar0, DOUBLE dblVar1, DOUBLE dblVar2, STRING strVar3, BYTE byVar4, STRING strVar5, BYTE byVar6, BYTE byVar7)
	Imprimir item

	Devuelve 0 si no hubo error -1 si se produjo un error.

	strVar0
	Texto descripcion del item (hasta 50 caracteres)

	dblVar1
	Cantidad (nnnn.nnnnnnnnnn) (En SMH/P-321F, SMH/P-322F y SMH/P-330F: nueve numeros (incluyendo punto decimal movil))

	dblVar2
	Precio unitario (nnnnnnn.nnnn)

	strVar3
	Porcentaje IVA (nn.nn)/(**.**) (hasta 5 caracteres)

	byVar4
	Calificador de la operacion {Mm}.
	M = Suma monto, m = Resta monto

	strVar5
	Impuestos internos, +0.nnnnn = impuestos internos fijos
	0.nnnnn = impuestos internos porcentuales, $nnnn.nnnnn = impuestos internos fijos, %nnnn.nnnnn = imp. internos porcentuales

	byVar6
	Parametro display: 0, 1 o 2 {012}. (No tiene efecto en el presente modelo)

	byVar7
	T: precio total; otro caracter: precio base {TBO}.

	@PrintLineItem|Pila Eveready 1.5V|2.0|1.50|21.0|M|0.0|1|B
	*/
	function PrintLineItem($xarticulo, $xcant, $xpreciou, $xiva, $xsuma = true, $ximpuestos = 0, $xdisplay = 1, $xcodigo = "")
	{
		/*	
		$parametro = chr(66);
		$comando = $parametro.$sp."Fotocopias BN".$sp."1.00".$sp."1.00".$sp."21.00".$sp."M".$sp."0.00".$sp."0".$sp."T";
		*/
		$descArt = $this->nombreArticulo($xarticulo);
		if (!esVacio($xcodigo))
			$descArt .= " " . substr($xcodigo, strlen($xcodigo) - 4, 4);
		
		$res = array();
		$res[] = chr(66);
		$res[] = $descArt;
		$res[] = formatFloat($xcant);
		$res[] = formatFloat($xpreciou);
		$res[] = formatFloat($xiva);
		if ($xsuma)
			$res[] = "M";
		else
			$res[] = "m";
		$res[] = $ximpuestos;
		$res[] = $xdisplay;
		$res[] = "B";

		return $this->enviarComando(implode($res, $this->sp));
	}

	/*
	 int @Subtotal(BYTE byVar0, STRING strVar1, BYTE byVar2)

	Devuelve 0 si no hubo error, -1 si se produjo un error.

	byVar0
	Parametro impresion

	strVar1
	Reservado (llenar con un caracter cualquiera) (hasta 26 caracteres)

	byVar2
	Parametro display: 0, 1 o 2 {012}.

	@Subtotal|P|Subtotal|0|
	*/
	function Subtotal()
	{
		$sp = $this->sp;
		$parametro = chr(67);
		$comando = $parametro.$sp."P".$sp."x".$sp."0";
		return $this->enviarComando($comando);
	}

	/*
	 int @TotalTender(STRING strVar0, DOUBLE dblVar1, BYTE byVar2, BYTE byVar3)

	Devuelve 0 si no hubo error , -1 si se produjo un error.

	strVar0
	Texto de descripcion (hasta 50 caracteres)

	dblVar1
	Monto pagado (nnnnnnnnn.nn)

	byVar2
	Calificador operacion {CT}.
	C = Cancela el comprobante fiscal, T = Pago

	byVar3
	Parametro display: 0, 1 o 2 {012}.

	@TotalTender|Efectivo|20.00|T|0

	Rta:
	01  Status de la impresora
	02  Status del controlador fiscal
	03  Vuelto o monto faltante
	*/
	function TotalTender($xtexto, $xmonto, $xoperacion = "T", $xdisplay = 0)
	{
		/*
		$parametro = chr(68);
		$comando = $parametro.$sp."Total Venta:".$sp."1.00".$sp."T"; //.$sp."0".$sp."0";
		*/
		$sp = $this->sp;
		$xmonto = formatFloat($xmonto);
		
		$parametro = chr(68);
		$comando = $parametro.$sp.$xtexto.$sp.$xmonto.$sp.$xoperacion.$sp."0".$sp.""; 
		return $this->enviarComando($comando);
	}

	/*
	 @CloseFiscalReceipt

	Rta:
	01  Status de la impresora
	02  Status del controlador fiscal
	03  Número del comprobante fiscal recién emitido
	04  Cantidad de hojas numeradas impresas (Sólo en modelo SMH/P-330F y en la versión 2.01 de los modelos SMH/P-PL-8F y SMH/P-322)
	05  Número de CAI (Sólo en modelo SMH/P-330F y en la versión 2.01 de los modelos SMH/P-PL-8F y SMH/P-322F.)
	*/
	function CloseFiscalReceipt()
	{
		/*
		$parametro = chr(69);
		$comando = $parametro;
		*/
		$res = $this->enviarComando(chr(69));
			
		$this->closeConnection();
		return $res;
	}

	/*

	int @DailyClose(BYTE byVar0)
	Cierre de jornada fiscal

	Devuelve 0 si no hubo error ó -1 si se produjo un error.

	Parámetros
	byVar0
	Z: Cierre de jornada fiscal; X: Informe X {XZ}.
	*/
	function cierreZ($xtipo = "Z")
	{
		/*
		39H (9 – ASCII 57)
		FS
		Z: Cierre de jornada fiscal; otro caracter: Informe ‘X’
		*/
		$sp = $this->sp;
		$parametro = chr(57);
		$comando = $parametro.$sp.$xtipo;

		$this->openConnection();
		$res = $this->enviarComando($comando, 80, true);
		$this->closeConnection();
		return $res;
	}

}


?>