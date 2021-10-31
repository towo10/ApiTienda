<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/categoria.class.php';

$_respuestas = new respuestas;
$_categoria = new categoria;

header("Content-Type: application/json");
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['tienda'])){
        //echo $_GET['tienda'];
        $resultado = $_categoria->getCategoria($_GET['tienda']);
        echo json_encode($resultado);
    }else{
        echo json_encode($_respuestas->error_400());
    }
}else{
    echo json_encode($_respuestas->error_405());
}

?>