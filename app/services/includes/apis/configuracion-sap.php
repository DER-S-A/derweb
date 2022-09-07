<?php
/**
 * Este script contiene la configuración de las URLS y parámetros básicos de SAP.
 */

// URLs para consumir APIs de SAP.
define ("URL_LOGIN_ETL", "https://181.119.112.208:5444/dev/api/ETL/GetToken");
define ("URL_ENVIAR_PEDIDO", "https://181.119.112.208:5444/dev/api/ETL/GetAndProcessNews"); 

// Establece la configuración de logueo al ETL.
define ("BODY_LOGIN_ETL", array(
    "userName" => "ETL", 
    "password" => "1234"));

// Parametros de conexión para enviar en el body de las APIs del ETL
define ("CONNECTOR_CODE", "DercliWeb");
define ("FUNCIONALITY_CODE", "SalesQuotation");
?>