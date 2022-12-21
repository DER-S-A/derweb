<?php
/**
 * Este script contiene la configuración de las URLS y parámetros básicos de SAP.
 */

// URLs para consumir APIs de SAP.
// ! Login Viejo -> define ("URL_LOGIN_ETL", "https://181.119.112.208:5444/dev/api/ETL/GetToken");
define ("URL_LOGIN_ETL", "https://b.onesolutions.com.ar/OSSYS/Security/Login");

// ! Enviar Pedido Viejo ->define ("URL_ENVIAR_PEDIDO", "https://181.119.112.208:5444/dev/api/ETL/GetAndProcessNews"); 
define ("URL_ENVIAR_PEDIDO","https://b.onesolutions.com.ar/etl/der/ExecuteOperation?OperationCode=SQ2SAP");

// Establece la configuración de logueo al ETL.
// ! LOGIN VIEJO 
 /*define ("BODY_LOGIN_ETL", array(
    "userName" => "ETL", 
    "password" => "1234"));*/
define ("BODY_LOGIN_ETL",array(
    "userName" => "bindapp@der",
    "password" => "!bapp@DeR#"));

// Parametros de conexión para enviar en el body de las APIs del ETL
define ("CONNECTOR_CODE", "DercliWeb");
define ("FUNCIONALITY_CODE", "SalesQuotation");
?>