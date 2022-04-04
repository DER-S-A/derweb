<?php

$data = [
    'phone' => '5492284372145',
    'body' => 'Aqui tienes tu factura - WhatsAp -- Enviado desde SC3 - Sistemas',
];
$json = json_encode($data); // Encode data to JSON

 //https://app.chat-api.com/instance/125038
//https://eu76.chat-api.com/instance125038/ and token isz6dhc2f5vpwcjk
$url = 'https://eu76.chat-api.com/instance125038/message?token=isz6dhc2f5vpwcjk';
// Make a POST request
$options = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $json
    ]
]);
// Send a request
$result = file_get_contents($url, false, $options);
echo($result);
?>