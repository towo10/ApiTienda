<?php

require_once 'clases/respuestas.class.php';
require_once 'clases/subir_imagen.class.php';

$_respuestas = new respuestas;
$_subir_imagen = new subir_imagen;

header("Content-Type: application/json");
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $postBody = file_get_contents('php://input');
    $resp = $_subir_imagen->insertImagen($postBody);
    echo json_encode($resp);
}else{
    echo json_encode($_respuestas->error_405());
}


?>