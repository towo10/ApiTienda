<?php
require_once 'clases/auth.class.php';
require_once 'clases/respuestas.class.php';

$_auth = new auth;
$_respuestas = new respuestas;

header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //recibimos los datos
    $postBody = file_get_contents("php://input");

    //enviamos los datos al servidor
    $datosArray = $_auth->login($postBody);

    //devolvemos una respuesta
    if(isset($datosArray['result']['error_id'])){
        $code = $datosArray['result']['error_id'];
        http_response_code($code);
        
    }else {
        http_response_code(200);
        
    }
    echo json_encode($datosArray);
}else {
    echo json_encode($_respuestas->error_405());
}

?>