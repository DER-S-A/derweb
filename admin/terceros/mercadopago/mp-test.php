<!DOCTYPE html>
<html>
<head>

    <meta charset="ISO-8859-1"> 
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Fin de la inscripci&oacute;n</title>
	
</head>

<body onload="">

<?php
/*
Mercado Pago:
https://www.mercadopago.com/mla/herramientas/aplicaciones

SHORT_NAME: mp-app-195802802
CLIENT_ID: 1185039526412640
CLIENT_SECRET: mOQt0RBKZBUVPBrAjYSwYIdb0w4C3Vhf
*/

// Include Mercadopago library
require_once "mercadopago/lib/mercadopago.php";

//https://www.mercadopago.com.ar/developers/es/solutions/payments/basic-checkout/receive-payments/

//en produccion:
$mp = new MP("1185039526412640", "mOQt0RBKZBUVPBrAjYSwYIdb0w4C3Vhf");
//$mp = new MP ("TEST-1185039526412640-112614-1be7a91fda49b299a6a59824a5b60054-195802802");

$lastinsertok = 100;
$apellido = "casamayor";
$precio = 12.15;
// Available currencies at: https://api.mercadopago.com/currencies
$preference_data = array(
						"items" => array(
							array(
								"title" => "Pago  $lastinsertok| $apellido",
								"quantity" => 1,
								"currency_id" => "ARS", 
								"unit_price" => $precio
							)
						)
);

//print_r($preference_data);

try 
{
	$preference = $mp->create_preference($preference_data);
}
catch (MercadoPagoException $e)
{
	echo($e->getMessage());
}

?>

<a href="<?php echo $preference['response']['init_point']; ?>">
	<img src="pagar-mp.png">
</a>
 
</body>
</html>

