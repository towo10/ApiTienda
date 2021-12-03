<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/orden_compra.class.php';

$_respuesta = new respuestas;
$_orden_compra = new orden_compra;


header("Content-Type: application/json");
switch($_SERVER['REQUEST_METHOD']){
    /*================================================================================================================================= */
    case 'POST': 
        $postBody = file_get_contents('php://input');
        $resp = $_orden_compra->sendOrdenCompra($postBody);
        echo json_encode($resp);
        break;
    /*================================================================================================================================= */
    case 'GET':
        if (isset($_GET['tienda']) && isset($_GET['usuario'])){
            $resultado = $_orden_compra->getOrdenCompra($_GET['tienda'],$_GET['usuario']);
            echo json_encode($resultado);

        }else{
            echo json_encode($_respuesta->error_400());
        }

        break;

    /*================================================================================================================================= */
    case 'PUT':
        //$postBody = file_get_contents('php://input');
        //$resp = $_producto->updateProducto($postBody);
        //echo json_encode($resp);
        break;
    /*================================================================================================================================= */
    case 'DELETE':
        //$postBody = file_get_contents('php://input');
        //$resp = $_producto->deleteProducto($postBody);
        //echo json_encode($resp);
        
        break;
}

?>