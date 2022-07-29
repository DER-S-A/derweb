<?php
include_once("include-app.php");

$data = array();
$methodName = json_decode($_GET["method_name"]);
$arguments = json_decode($_GET["args"]);
$result = forward_static_call_array($methodName, $arguments);
if (!empty($result)) {
    $data["result_code"] = 1;
    $data["result_string"] = $result;
    echo json_encode($data);
}
else {
    $data["result_code"] = 0;
    $data["result_string"] = "Error en llamado a la función";
    echo json_encode($data);
}
