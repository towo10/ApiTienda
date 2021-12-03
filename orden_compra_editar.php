<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/orden_compra_editar.class.php';

$_respuesta = new respuestas;
$_orden_compra = new orden_compra_editar;


header("Content-Type: application/json");
switch($_SERVER['REQUEST_METHOD']){
    /*================================================================================================================================= */
    case 'POST': 
        $postBody = file_get_contents('php://input');
        $resp = $_orden_compra->onModificar($postBody);
        echo json_encode($resp);
        break;
    /*================================================================================================================================= */
    case 'GET':
        break;
    /*================================================================================================================================= */
    case 'PUT':
        break;
    /*================================================================================================================================= */
    case 'DELETE':        
        break;
}

?>