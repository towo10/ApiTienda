<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/productos.class.php';

$_respuesta = new respuestas;
$_producto = new productos;


header("Content-Type: application/json");
switch($_SERVER['REQUEST_METHOD']){
    /*================================================================================================================================= */
    case 'POST': 
        $postBody = file_get_contents('php://input');
        $resp = $_producto->insertProducto($postBody);
        echo json_encode($resp);

        break;
    /*================================================================================================================================= */
    case 'GET':
        
        if (isset($_GET['tienda']) && isset($_GET['usuario']) && isset($_GET['producto'])){
            $resultado = $_producto->getProducto($_GET['tienda'],
                                                $_GET['usuario'],
                                                $_GET['producto']);
            echo json_encode($resultado);

        }elseif (isset($_GET['page']) && isset($_GET['tienda'])){
            $resultado = $_producto->getPageProductos($_GET['page'],$_GET['tienda']);
            echo json_encode($resultado);

        }elseif (isset($_GET['tienda']) && isset($_GET['buscar'])){
            $resultado = $_producto->getbuscarProducto($_GET['tienda'],$_GET['buscar']);
            echo json_encode($resultado);

        }elseif (isset($_GET['tienda']) && isset($_GET['usuario'])){
            $resultado = $_producto->getProductos($_GET['tienda'],$_GET['usuario']);
            echo json_encode($resultado);
        }

        break;

    /*================================================================================================================================= */
    case 'PUT':

        $postBody = file_get_contents('php://input');
        $resp = $_producto->updateProducto($postBody);
        echo json_encode($resp);
        break;
    /*================================================================================================================================= */
    case 'DELETE':
        $postBody = file_get_contents('php://input');
        $resp = $_producto->deleteProducto($postBody);
        echo json_encode($resp);
        
        break;
}

?>