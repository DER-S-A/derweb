<?php
include_once("funcionesSConsola.php");
include_once("app-cja2.php");

$aRespuesta = ["msg" => [], "error" => []];

$idcomprobante = RequestStr("cif");
$cuitReceptor = RequestStr("cuit");

if ($idcomprobante == 0) {
	$aRespuesta["error"][] = "Error, no se ha enviado el CIF.";
	echo (json_encode($aRespuesta));
	exit;
}


$cuitEmisor = getParameter("ws-cuitcliente", "20241300553");
$serProd =  getParameter("ws-concepto", "3");

//busca si existe el CIF para este CUIT_DNI de destinatario
$rsComp = locateRecordWhere("cja2_comprobantes", "id = $idcomprobante and cuit_dni = '$cuitReceptor'");

if ($rsComp->EOF()) {
	$rsComp->close();
	$aRespuesta["error"][] = "Error, comprobante con CIF no encontrado para el CUIT receptor $cuitReceptor.";
	echo (json_encode($aRespuesta));
	exit;
}

$idempresa = $rsComp->getValueInt("idempresa");
$rsComp->close();

//busca la empresa, si tiene el comprobante
if ($idempresa != 0) {
	$rsEmpresa = locateRecordId("cja2_empresas", $idempresa);
	$cuitEmisor = $rsEmpresa->getValue("cuit");
	$rsEmpresa->close();
}

//recupera comprobante (con datos relacionados)
$rsComp = compRsComprobanteImprimir($idcomprobante);
if ($rsComp->EOF() && $idcomprobante != 0) {
	$aRespuesta["error"][] = "Error, comprobante con CIF no encontrado.";
	echo (json_encode($aRespuesta));
	exit;
}


//analiza si el comprobante no tiene cliente pero viene en el parámetro
$idcliente = $rsComp->getValueInt("idcliente");
$idcuenta = $rsComp->getValueInt("idcuenta");
$idclienteR = RequestInt("idcliente");
$idcuentaR = RequestInt("idcuenta");
if ($idcliente == 0 && $idclienteR != 0) {
	compActualizarClienteCta($idcomprobante, $idclienteR, 0);
	$rsComp = compRsComprobanteParaCAE($idcomprobante);
} else {
	if ($idcuenta == 0 && $idcuentaR != 0) {
		compActualizarClienteCta($idcomprobante, 0, $idcuentaR);
		$rsComp = compRsComprobanteParaCAE($idcomprobante);
	}
}
// FIN: setearle el cliente/cta cuando no lo tenía


//-----------FACTURAS EN DOLARES--------------------------------
$MonId = "PES";
$MonCotiz = 1.00;
$idmoneda = $rsComp->getValueInt("idmoneda");
if ($idmoneda == 2) {
	$MonId = "DOL";

	//busca campo cotizacion en el comprobante y de útima en la moneda
	$MonCotiz = $rsComp->getValueFloat("cotizacion", 3);
	if ($MonCotiz == 0) {
		$rsMone = locateRecordId("bp_monedas", 2);
		$MonCotiz = $rsMone->getValueFloat("cotizacion");
	}
}

$tipo = $rsComp->getValue("tipo");
$talon = $rsComp->getValue("talon");
$linea = $rsComp->getValue("punto_venta");
$total = $rsComp->getValueFloat("total");
$nroFc = $rsComp->getValueInt("numero");
$nroCompleto = $rsComp->getValue("numero_completo");

$docTipo = "";
$docNro = $rsComp->getValue("cliente_cuit");
if (strlen($docNro) == 11) {
	$docTipo = "80";
} else {
	$docTipo = "99";
	$docNro = 0;
}

$cuil = $rsComp->getValue("cliente_cuil");
if (!esVacio($cuil)) {
	$docTipo = "86";
	$docNro = $cuil;
}


//si ya viene en el comprobante gana. Casos de escuelas se le pone el del padre o madre
$cuitDniComprob = $rsComp->getValue("cuit_dni");
if (!esVacio($cuitDniComprob) && $cuitDniComprob != 0) {
	$docNro = $cuitDniComprob;
	//es un CUIT
	if (strlen($docNro) == 11) {
		$docTipo = "80";
	}
	//DNI
	if (strlen($docNro) <= 8) {
		$docTipo = "96";
	}
}

$rsTipoComp = locateRecordWhere("gen_tipos_comprobante", "tipo = '$tipo' and talon = '$talon'");
$cBteTipo = $rsTipoComp->getValue("codigo_afip_fe");
$iLinea = (int) $linea;

//estructura para la CABECERA, punto de venta, cantidad de registros y tipo de comprobante
$aCabecera = [];
$aCabecera['CuitEmisor'] = $cuitEmisor;
$aCabecera['DocTipo'] = $docTipo;
$aCabecera['DocNro'] = $docNro;
$aCabecera['CbteTipo'] = $cBteTipo;
$aCabecera['PtoVta'] = $iLinea;
$aCabecera['CbteNro'] = $nroFc;
$aCabecera['NroCompleto'] = $nroCompleto;

/*
	1 Productos
	2 Servicios
	3 Productos y Servicios
	*/
$aCabecera['Concepto'] = $serProd;
$aCabecera['CbteFch'] = $rsComp->getValueFechaAAAAMMDD("fecha");
$aCabecera['Cae'] = $rsComp->getValue("cae");
$aCabecera['FchVtoCae'] = $rsComp->getValueFechaAAAAMMDD("fecha_vto_cae");
$aCabecera['FchVtoPago'] = $rsComp->getValueFechaAAAAMMDD("fecha_vencimiento");

$aCabecera['MonId'] = $MonId;
$aCabecera['MonCotiz'] = $MonCotiz;

//importes
$aCabecera['ImpTotal'] = $total;
$aCabecera['ImpNeto'] = $rsComp->getValueFloat("total") - $rsComp->getValueFloat("iva") - $rsComp->getValueFloat("impuestos");
$aCabecera['ImpIVA'] = $rsComp->getValueFloat("iva");
$aCabecera['ImpTrib'] = $rsComp->getValueFloat("impuestos");
$aCabecera['ImpTotConc'] = 0.00;
$aCabecera['ImpOpEx'] = 0.00;

// ARTICULOS -------------------------------------------------------------------------------------
$rsArt = compRsArticulosImprimir($idcomprobante);
$aArts = [];
while (!$rsArt->EOF()) {

	$aRow = [];

	$aRow['CodigoBarras'] = $rsArt->getValue("codigo");
	$aRow['Codigo'] = $rsArt->getValue("codigo");
	$aRow['Articulo'] = $rsArt->getValue("articulo");
	$aRow['Cantidad'] = $rsArt->getValueFloat("cantidad");
	$aRow['Subtotal'] = $rsArt->getValueFloat("subtotal");

	$aArts[] = $aRow;
	$rsArt->Next();
}
$rsArt->close();


//IVA ----------------------------------------------------------------------------------------------------
$rsIva = compRsIvaParaCAE($idcomprobante);

$aIvas = [];

$totBaseImponible = 0.00;
$totIva = 0.00;
while (!$rsIva->EOF()) {
	$alicIva = [];
	$totBaseImponible = round($totBaseImponible + $rsIva->getValueFloat("base_imponible"), 2);
	$totIva = round($totIva + $rsIva->getValueFloat("iva"), 2);

	$alicIva['BaseImp'] = $rsIva->getValueFloat("base_imponible");
	$alicIva['Importe'] = $rsIva->getValueFloat("iva");
	$alicIva['Id'] = $rsIva->getValueInt("codigo_afip");
	$alicIva['Alicuota'] = $rsIva->getValueFloat("alicuota");

	$aIvas[] = $alicIva;
	$rsIva->Next();
}
$rsIva->close();


// FIN IVA --------------------------------------------------------------------------------------------------


// Impuestos y tributos (munic, prov, nacionales) ------------

$aTributos = [];
$rsImp = compRsImpuestosParaCAE($idcomprobante);
while (!$rsImp->EOF()) {
	$tributo = [];
	$tributo['BaseImp'] = $rsImp->getValueFloat("base_imponible");
	$tributo['Importe'] = $rsImp->getValueFloat("importe");
	$tributo['Desc'] = $rsImp->getValue("impuesto");
	$tributo['Id'] = $rsImp->getValueInt("tipo_tributo_afip");

	$aTributos[] = $tributo;
	$rsImp->Next();
}

$rsImp->close();

// FIN tributos e impuestos ------------------------------------------------

// COMPROBANTES ASOCIADOS --------------------------------------------------
// Ver service_afip_prod.wsdl
$rsAsoc = getRs("select fc.tipo, fc.talon, fc.punto_venta, fc.numero, fc.fecha
						from cja2_comprobantes_asociados aso
							inner join cja2_comprobantes fc on (aso.idcomprobante_asoc = fc.id)
						where aso.idcomprobante = $idcomprobante
						order by fc.tipo");

$aCompAsociados = [];
while (!$rsAsoc->EOF()) {
	$tipoAsoc = $rsAsoc->getValue("tipo");
	$talonAsoc = $rsAsoc->getValue("talon");
	$rsTipoCompAsoc = compRsTipoComprobante($tipoAsoc, $talonAsoc);
	$codAfipAsoc = $rsTipoCompAsoc->getValue("codigo_afip_fe");

	$asoc = [];
	$asoc["Tipo"] = $codAfipAsoc;
	$asoc["PtoVta"] = $rsAsoc->getValueInt("punto_venta");
	$asoc["Nro"] = $rsAsoc->getValue("numero");
	$asoc["Cuit"] = $cuitEmisor;
	$asoc["CbteFch"] = $rsAsoc->getValueFechaAAAAMMDD("fecha");

	$aCompAsociados[] = $asoc;

	$rsTipoCompAsoc->close();
	$rsAsoc->Next();
}
$rsAsoc->close();

// Fin Asociados -------------------------------------------------


//  manual_desarrollador_COMPG_v2_15.pdf pag: 43 ------- OPCIONALES FCE----------------
// https://www.sistemasagiles.com.ar/trac/wiki/ProyectoWSFEv1 en Tipos de datos opcionales
$aOpcionales = array();
$cbu = compAnexoValor($idcomprobante, "FCE cbu");
$fechaPago = compAnexoValor($idcomprobante, "FCE fecha pago");
$fceModo = compAnexoValor($idcomprobante, "FCE modo");
if (!esVacio($cbu) && ($cBteTipo >= 200)) {
	//gracias Oscar !
	$aOpcionales[] = array('Id' => "2101", 'Valor' => $cbu);
}


// NDE
if ($cBteTipo == 202) {
	$aOpcionales[] = array('Id' => "22", 'Valor' => "N");
}

/*  Msg: Si informa comprobante MiPyMEs (FCE) del tipo Factura, 
			27 es el cod y ADC (actual) o SCA como dato
			es obligatorio informar opcional por RG con ID 27 y su valor correspondiente. 
			Valores esperados 
			SCA = 'TRANSFERENCIA AL SISTEMA DE CIRCULACION ABIERTA' o 
			ADC = 'AGENTE DE DEPOSITO COLECTIVO'
		*/
if ($cBteTipo == 201) {
	if (esVacio($fceModo))
		$fceModo = "ADC";
	$aOpcionales[] = array('Id' => "27", 'Valor' => $fceModo);
}


// FIN OPCIONALES -------------------------------------------------------------------    

//FCE lo requiere    
if (!esVacio($fechaPago) && ($cBteTipo >= 200)) {
	//lo guarda en formato 2019-08-30
	$aCabecera['FchVtoPago'] = str_replace("-", "", $fechaPago);
}

//Lote de COMPROBANTES
$aComprobante = [];
$aComprobante['Cabecera'] = $aCabecera;
$aComprobante['Articulos'] = $aArts;
$aComprobante['Iva'] = $aIvas;
$aComprobante['Tributos'] = $aTributos;
$aComprobante['Asociados'] = $aCompAsociados;
$aComprobante['Opcionales'] = $aOpcionales;

echo (json_encode($aComprobante));
