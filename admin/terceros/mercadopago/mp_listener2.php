<?php
include("../funcionesSConsola.php");
include("../app-comp.php");

require_once "lib/mercadopago.php";

$txt = "\r\n----------------------------------------------------------------\r\n" . date("Y-m-d H:i:s") . "\r\nID: " . $codTransaccion;
$myfile = fopen("logs/logs-" . date("Y-W") . ".txt", "a+") or die("no no no!");

echo ("MP Listener (por SC3)");

$id = RequestInt("id");
$topic = Request("topic");

echo ("<br>Iniciando MP Listener ($id)...");

$codTransaccion = $id;

$txt = "\r\LLegue aca, buscando caja";
echo($txt);

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

$txt = "<br>Ubicada la caja $idcaja";
echo($txt);

    $idcuenta = 18;
    $totPagos = 10;
    $idtransaccion = 1;

    $fechaHoy = date("Y-m-d h:i");

    $txt = "<br>por crear comproante $fechaHoy";
    echo($txt);

    $sec = new ScSecuencia("RC", 0);
        
    $values = compIniciarComprobante($sec, "RC", "X", "0");
    $values["idcuenta"] = $idcuenta;
    $values["fecha"] = comillasSql($fechaHoy);
    $values["idmoneda"] = 1;
    $values["idmoneda_equiv"] = 1;
    $values["credito"] = $totPagos;
    $values["credito_equiv"] = $totPagos;
    $values["observaciones"] = "Mercado Pago operacion: $idtransaccion";
                
    $sql = insertIntoTable("qcja2comprobantes", $values);

    $valuesMovsCaja = array();
    $valuesMovsCaja["idconcepto"] = getParameter("cja2-idconcepto-pago", 0);
    $valuesMovsCaja["idusuario"] = ":IDUSUARIO";
    $valuesMovsCaja["idmediopago"] = 1;
    $valuesMovsCaja["idmoneda"] = 1;
    $valuesMovsCaja["idcomprobante"] = "HOLE_IDCOMPROBANTE";
    $valuesMovsCaja["fecha_pase"] = "CURRENT_TIMESTAMP()";
    
    $valuesMovsCaja["idcaja"] = $idcaja;
    $valuesMovsCaja["fecha"] = comillasSql($fechaHoy);
    $valuesMovsCaja["idmediopago"] = getParameter("cja2-idmediopago-efectivo", "1");
    $valuesMovsCaja["monto"] = $totPagos;

    $sqlMovCaja = insertIntoTable("qcja2movimientos", $valuesMovsCaja);

    $txt = "<br> $sql <br> $sqlMovCaja";
    echo($txt);

    $bd = new BDObject();

    $bd->beginT();
            
    $idcomprobante = 0;
    $idcomprobante = $bd->execInsert($sql);
    $aids["HOLE_IDCOMPROBANTE"] = $idcomprobante;
    $aids["HOLE_NRO_COMPROBANTE"] = $values["numero_completo"];

	$txt = "<br> comprobante $idcomprobante";
    echo($txt);

    $bd->execQuerysIdsInArray($sqlMovCaja, $aids);
  
	$sec->incrementarYGrabar($bd);
    $txt .= "\r\n $sql\r\n $sqlMovCaja";

    $bd->commitT();			

echo("fin");

?>