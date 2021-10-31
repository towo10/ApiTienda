<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/imagenes.class.php';

$_respuestas = new respuestas;
$_imagenes = new imagenes;

header("Content-Type: application/json");
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['tienda']) && isset($_GET['subcategoria']) && 
        isset($_GET['producto']) && isset($_GET['categoria'])){
        $resultado = $_imagenes->getImagenes($_GET['tienda'],$_GET['categoria'],$_GET['subcategoria'],$_GET['producto']);
        echo json_encode($resultado);
    }else{
        echo json_encode($_respuestas->error_400());
    }
}else{
    echo json_encode($_respuestas->error_405());
}

?>