<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/productos.class.php';

$_respuesta = new respuestas;
$_producto = new productos;


header("Content-Type: application/json");
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $postBody = file_get_contents('php://input');
    $resp = $_producto->deleteProducto($postBody);
    echo json_encode($resp);
}else{
    echo json_encode($_respuestas->error_405());
}

?>