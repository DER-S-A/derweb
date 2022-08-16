<?php
/**
 * Casos de pruebas para el manejo de API SAP.
 */

include "autoload.php";

getTokenETL();

echo "Resultado enviar pedido caso de prueba 1:\n";
enviarPedidoETL_CasoPrueba1();


/**
 * getTokenETL_TestMethod
 * Caso de prueba 6: Gettoken usando el método de la clase APISap.
 * @return void
 */
function getTokenETL() {
    $objAPI = new APISap("xxx", "nada");
    $objAPI->getTokenETL();
}

/**
 * enviarPedidoETL
 * Caso de prueba 2 - Enviar Pedido a ETL
 * @return void
 */
function enviarPedidoETL_CasoPrueba1() {
    $objAPI = new APISap(URL_ENVIAR_PEDIDO, "POST");

    // Parámetros de entrada.
    // Los siguientes datos es un array de pedido según el ejemplo que se recibió
    // de la documentación de OneSolution.
    $aBody = [];
    $aBody["connectorCode"] = "DercliWeb";
    $aBody["functionalityCode"] = "SalesQuotation";

    // Datos de cabecera del pedido.
    $aPedido = [];
    $aPedido["DocDate"] = "2022-08-16";
    $aPedido["DocDueDate"] = "2022-08-16";
    $aPedido["CardCode"] = "C20000";
    $aPedido["NumAtCard"] = "RL02";

    // Item 1.
    $aItems[0]["ItemCode"] = "I00003";
    $aItems[0]["Quantity"] = 11;
    $aItems[0]["Price"] = 300.0;

    // Item 2.
    $aItems[1]["ItemCode"] = "A00001";
    $aItems[1]["Quantity"] = 5;
    $aItems[1]["Price"] = 40.0;

    $aPedido["DocumentLines"] = $aItems;

    $aBody["data"] = json_encode($aPedido);

    $objAPI->setData($aBody);
    $objAPI->send();

    echo "\n\nInformación del trace: \n";
    var_dump($objAPI->getInfo());
}

?>