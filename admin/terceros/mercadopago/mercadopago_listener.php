<?php
include("../funcionesSConsola.php");
include("../app-comp.php");

require_once "lib/mercadopago.php";

//para instalar
/*
http_response_code(200);
return;
*/

//encontralo en https://www.mercadopago.com/mla/account/credentials
$mpClienteId = getParameter("mercadopago-client_id", "TEST-c1c6b1f4-9695-425f-842d-5bf7c9723e20");
$mpSecret = getParameter("mercadopago-client_secret", "TEST-8698162945950227-060506-ae0c2b04a50b5ff6b1dcdb7f8039f10b-53340008");
if (strContiene($mpClienteId, "TEST"))
	$mp = new MP ($mpSecret);
else
	$mp = new MP($mpClienteId, $mpSecret);

$txt = "\r\n----------------------------------------------------------------\r\n" . date("Y-m-d H:i:s") . "\r\nID: " . $codTransaccion;
$myfile = fopen("logs/logs-" . date("Y-W-d") . ".txt", "a+") or die("no no no!");

//echo ("MP Listener (por SC3)");

$id = RequestInt("id");
$topic = Request("topic");

if ($id == 0) 
{
	$txt .= "\r\nSin parametros";
	fwrite($myfile, $txt);
	fclose($myfile);

	http_response_code(400);
	return;
}

//echo ("<br>Iniciando MP Listener ($id)...");
$merchant_order_info = null;

try 
{
	$payment_info = array();
	switch ($topic) 
	{
		case 'payment':
			$payment_info = $mp->get("/v1/payments/" . $id);
			$merchant_order_info = $mp->get("/merchant_orders/" . $payment_info["response"]["order"]["id"]);
			break;
		case 'merchant_order':
			$merchant_order_info = $mp->get("/merchant_orders/" . $id);
			break;
		default:
			$merchant_order_info = null;
	}
}
catch (Exception $e)
{
	$txt .= "\r\n" . $e;
	$txt .= "\r\n" . implode(',', $payment_info);
	//echo($txt);
	fwrite($myfile, $txt);
	fclose($myfile);

	die();
}

if ($merchant_order_info == null) 
{
	$txt .= "\r\nError obtaining the merchant_order";
	//echo($txt);
	fwrite($myfile, $txt);
	fclose($myfile);

	die();
}

$codTransaccion = $id;

//busca caja en orden: mercado, de banco o abierta
$rsCaja = locateRecordWhere("cja2_cajas", "nombre like '%mercado%'");
$idcaja = $rsCaja->getId();
if ($idcaja == 0)
{
	$rsCaja = locateRecordWhere("cja2_cajas", "banco = 1");
	$idcaja = $rsCaja->getId();
	if ($idcaja == 0)
	{
		$rsCaja = locateRecordWhere("cja2_cajas", "abierta = 1");
		$idcaja = $rsCaja->getId();
	}
}

$rsUser = locateRecordWhere("sc_usuarios", "habilitado = 1", true, "esRoot desc, id");
$idusuario = $rsUser->getId();

if ($merchant_order_info["status"] == 200) 
{
	$payments = $merchant_order_info["response"]["payments"];
	$totPagos = 0;
	foreach ($payments as $payment) 
	{
		$txt .= "\r\nPayment: " . json_encode($payment);
		if ($payment['status'] == 'approved') 
		{
			$idtransaccion = $payment["id"];
			$totPagos += $payment['transaction_amount'];
			$idcuenta = $merchant_order_info["response"]["items"][0]["id"];

			//para evitar pago duplicado
			$rsAnexo = locateRecordWhere("cja2_comprobantes_anexos", "campo = 'Transaccion MP' and valor = '$idtransaccion'");
			if ($rsAnexo->EOF())
			{
				$sec = new ScSecuencia("RC", 0);
					
				$values = compIniciarComprobante($sec, "RC", "X", "0");
				$values["idcuenta"] = $idcuenta;
				$values["idusuario"] = $idusuario;
				$values["fecha"] = "CURRENT_TIMESTAMP()";
				$values["idmoneda"] = 1;
				$values["idmoneda_equiv"] = 1;
				$values["credito"] = $totPagos;
				$values["credito_equiv"] = $totPagos;
				$values["observaciones"] = "Mercado Pago operacion: $idtransaccion";
							
				$sql = insertIntoTable("qcja2comprobantes", $values);

				$valuesMovsCaja = array();
				$valuesMovsCaja["idconcepto"] = getParameter("cja2-idconcepto-pago", 0);
				$valuesMovsCaja["idusuario"] = $idusuario;
				$valuesMovsCaja["idmediopago"] = 1;
				$valuesMovsCaja["idmoneda"] = 1;
				$valuesMovsCaja["idcomprobante"] = "HOLE_IDCOMPROBANTE";
				$valuesMovsCaja["fecha_pase"] = "CURRENT_TIMESTAMP()";
				
				$valuesMovsCaja["idcaja"] = $idcaja;
				$valuesMovsCaja["fecha"] = "CURRENT_TIMESTAMP()";
				$valuesMovsCaja["idmediopago"] = getParameter("cja2-idmediopago-efectivo", "1");
				$valuesMovsCaja["monto"] = $totPagos;

				$sqlMovCaja = insertIntoTable("qcja2movimientos", $valuesMovsCaja);

				$valuesAnexo = array();
				$valuesAnexo["idcomprobante"] = "HOLE_IDCOMPROBANTE"; 
				$valuesAnexo["campo"] = "Transaccion MP";
				$valuesAnexo["valor"] = $idtransaccion;
				$sqlAnexo = insertIntoTable2("cja2_comprobantes_anexos", $valuesAnexo);

				$bd = new BDObject();
			
				$bd->beginT();
						
				$idcomprobante = 0;
				$idcomprobante = $bd->execInsert($sql);
				$aids["HOLE_IDCOMPROBANTE"] = $idcomprobante;
				$aids["HOLE_NRO_COMPROBANTE"] = $values["numero_completo"];
		
				$bd->execQuerysIdsInArray($sqlMovCaja, $aids);
				$bd->execQuerysIdsInArray($sqlAnexo, $aids);

				$txt .= "\r\nRecibo creado ID: $idcomprobante";

				$sec->incrementarYGrabar($bd);

				$bd->commitT();			
			}
		}
	}
	$txt .= "\r\nTotal pagos: $totPagos";

	print_r($merchant_order_info["response"]["payments"]);
	print_r($merchant_order_info["response"]["shipments"]);
}

$myfile = fopen("logs/logs-" . date("Y-W-d") . ".txt", "a+") or die("no no no!");
fwrite($myfile, $txt);
fclose($myfile);

http_response_code(200);
?>