<?php
/**
 * Endpoint que permite obtener los registros de la tabla rubros.
 */

 include ("autoload.php");

// Obtengo la URL y la convierto en array para poder obtener
// el módulo y el método que debo llamar de la clase RubrosController.
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$uri = explode('/', $uri);

// Verifico si el módulo y la clase se encuentra implementada. En caso de que no
// se encuentre devuelvo el error 404 not found.
if (isset($uri[5]) && $uri[5] != 'rubros.php' || !isset($uri[6])) {
    header("HTTP/1.1 404 No encontrado");
    exit();
}

// Invoco a la clase que controla la API Rest.
$objRubrosController = new RubrosController();
$strMethodName = $uri[6];
$objRubrosController->{$strMethodName}();

?>