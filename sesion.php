<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/sesion.class.php';

$_respuesta = new respuestas;
$_sesion = new sesion;

header("Content-Type: application/json");
switch($_SERVER['REQUEST_METHOD']){
    /*================================================================================================================================= */
    case 'GET':
        if (isset($_GET['tienda'])  && isset($_GET['usuario'])){
            $resultado = $_sesion->getUsuario($_GET['tienda'],
                                                $_GET['usuario']);
            echo json_encode($resultado);
        }else
            echo json_encode($_respuesta->error_400());

        break;
    default:
        echo json_encode($_respuesta->error_405());
        break;
}

?>



