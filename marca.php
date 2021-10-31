<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/marca.class.php';

$_respuestas = new respuestas;
$_marca = new marca;

header("Content-Type: application/json");
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['tienda']) && isset($_GET['categoria']) && 
    isset($_GET['subcategoria']) && isset($_GET['producto']) && isset($_GET['marca'])){
        $resultado = $_marca->getMarcaInfo($_GET['tienda'],$_GET['categoria'],$_GET['subcategoria'],$_GET['producto'],$_GET['marca']);
        
        echo json_encode($resultado);
    }elseif(isset($_GET['tienda']) && isset($_GET['categoria']) && 
    isset($_GET['subcategoria']) && isset($_GET['producto'])){
        $resultado = $_marca->getMarca($_GET['tienda'],$_GET['categoria'],$_GET['subcategoria'],$_GET['producto']);
        echo json_encode($resultado);
    }else{
        echo json_encode($_respuestas->error_400());
    }
}else{
    echo json_encode($_respuestas->error_405());
}

?>