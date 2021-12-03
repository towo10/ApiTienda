<?php
require_once 'conexion/conexion.php';-
require_once 'respuestas.class.php';

class orden_compra_editar extends conexion{
    private $compra = "";
    private $detalle = "";
    private $cantidad = "";

    public function onModificar($json){
        $_respuesta = new respuestas;
        $datos = json_decode($json,true);
        //============================================================================INI-TOKEN
        if(!isset($datos['token'])){
            return $_respuesta->error_401();
        }else {
            $tokenarray = $this->getToken($datos['token']);
            if(!$tokenarray){
                return $_respuesta->error_401("El token que ha enviado es inválido o ha caducado");
            }
        }
        //============================================================================FIN-TOKEN
        if(isset($datos['tipo'])){
            /** Elegimos que tipo de modificación hacemos*/
            
            switch ($datos['tipo']) {
                case '1':
                    return $this->onActualizarCantidad($datos);
                    break;
            }
        }else{
            return $_respuesta->error_401("Falta el tipo de formulario a cambiar");
        }
    }

    public function onActualizarCantidad($datos){
        $_respuesta = new respuestas;
        if (!isset($datos['compra']) || !isset($datos['detalle']) || !isset($datos['cantidad'])){
            return $_respuesta-> error_400();
        }else{
            $this->compra       = $datos['compra'];
            $this->detalle      = $datos['detalle'];
            $this->cantidad     = $datos['cantidad'];

            //Actualizamos la Orden de Compra como anulado / eliminado
            $query = "  update	detalle_compras
                        set		cantidad = :cantidad
                        where	compras_codigo = :compra
                                and codigo = :detalle";
            $params = [
                'compra'        => $this->compra,
                'detalle'       => $this->detalle,
                'cantidad'      => $this->cantidad
            ];
            $resulset = parent::noResultQuery($query,$params);
            if ($resulset){
                $result = $_respuesta->response;
                $result['status'] = "ok";
                $result['result'] = array(
                    "mensaje" => "Datos guardados correctamente"
                );
                return $result;
            }else{
                return $_respuesta->error500("Ocurrió un problema enviando datos al servicio.");
            }
        }
    }

    private function getToken($token){
        $query = "  select	id,userid,estado
                    from	usuario_token
                    where token = :token
                    and estado = 1
                    and now() between fecha and date_add(fecha, interval 4 hour)";
        $params = [
            'token' => $token
        ];
        $resp = parent::getParamDatos($query,$params);
        if ($resp){
            return $resp;
        }else{
            return 0;
        }
    }

    private function extenderToken($tokenid){
        $query = "update usuario_token
                    set fecha = now()
                    where id = :id";
        $params = ['id' => $tokenid];
        $resp = parent::noResultQuery($query,$params);
        if ($resp) {
            return $resp;
        }else{
            return 0;
        }
    }
}