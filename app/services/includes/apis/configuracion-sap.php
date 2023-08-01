<?php
/**
 * Este script contiene la configuración de las URLS y parámetros básicos de SAP.
 */

// URLs para consumir APIs de SAP.
// ! Login Viejo -> define ("URL_LOGIN_ETL", "https://181.119.112.208:5444/dev/api/ETL/GetToken");
define ("URL_LOGIN_ETL", "https://back.bindapp.net/v1/sys/svc/Login");

// ! Enviar Pedido Viejo ->define ("URL_ENVIAR_PEDIDO", "https://181.119.112.208:5444/dev/api/ETL/GetAndProcessNews"); 
define ("URL_ENVIAR_PEDIDO","https://back.bindapp.net/v1/mps/svc/ExecuteProcess/SQ2SAP");


// Establece la configuración de logueo al ETL.
// ! LOGIN VIEJO 
/*define ("BODY_LOGIN_ETL", array(
    "userName" => "ETL", 
    "password" => "1234"));*/
define ("BODY_LOGIN_ETL",array(
    "userName" => "user1@der",
    "password" => "uSeR1$27-35"));

// Parametros de conexión para enviar en el body de las APIs del ETL
define ("CONNECTOR_CODE", "DercliWeb");
define ("FUNCIONALITY_CODE", "SalesQuotation");
?>