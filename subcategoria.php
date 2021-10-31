<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/subcategoria.class.php';

$_respuestas = new respuestas;
$_subcategoria = new subcategoria;

header("Content-Type: application/json");
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['tienda']) && isset($_GET['categoria'])){
        //echo $_GET['tienda'];
        $resultado = $_subcategoria->getSubCategoria($_GET['tienda'],$_GET['categoria']);
        echo json_encode($resultado);
    }else{
        echo json_encode($_respuestas->error_400());
    }
}else{
    echo json_encode($_respuestas->error_405());
}

?>