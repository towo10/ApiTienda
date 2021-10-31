<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class auth extends conexion{

    public function login($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        if(!isset($datos['user'])||!isset($datos['password'])){
            return $_respuestas->error_400();
        }else{
            $user = $datos['user'];
            $password = parent::encriptar($datos['password']);
            $datos = $this->getUser($user,$password);

            if($datos){
                $codigo = (int)$datos[0]['resp'];

                if($codigo>0){
                    $existe = $this->existToken($codigo);
                    //echo json_encode($existe);
                    //exit;
                    //===========================================================DEVOLVEMOS-TOKEN
                    if ($existe){
                        $token = $this->gettoken($codigo);
                    }else{
                        $token = $this->setToken($codigo);
                    }
                    
                    if ($token){
                        $result = $_respuestas->response;
                        $result['result'] = array(
                            "token" => $token,
                            "idtienda" => $datos[0]['tienda'],
                            "idusuario" => $datos[0]['resp']
                        );
                        return $result;
                    }else {
                        return $_respuestas->error_500("Error interno con el token -1");
                    }
                }
            }else {
                return $_respuestas->error_200("El usuario o contraseña no coinciden, también puede ser que se encuentra anulado o bloqueado por su administrador.");
            }
        }
    }

    private function getUser($user,$password){
        $query = "CALL sp_iniciar_sesion_android(:correo,:password)";
        $params = array(
            'correo' => $user,
            'password'=> $password
        );
        $datos = parent::getParamDatos($query,$params);
        if(isset($datos[0]['resp'])){            
            if((int)$datos[0]['resp']>0){
                return $datos;
            }else{
                return 0;
            }
        }else {
            return 0;
        }

    }
    private function gettoken($codigo){
        $query = "SELECT a.token FROM usuario_token a 
                where a.userid = :usuario and a.estado = 1 
                and a.id = (select max(x.id) from usuario_token x where x.userid = a.userid and x.estado = a.estado)";
        $params = ['usuario'=>$codigo];
        $datos = parent::getParamDatos($query,$params);
        return $datos[0]['token'];
    }

    private function existToken($codigo){
        $existe = 1;
        // Preguntamos si existe el usuario con un token vigente
        $query = "SELECT count(*) total FROM usuario_token 
                where userid = :usuario and estado = 1";
        $params = ['usuario' => $codigo];
        $datos = parent::getParamDatos($query,$params);
        
        if(isset($datos[0]['total'])){ 
            $total = (int)$datos[0]['total'];  
            
            if ($total == 1){
                
                //Si existe usuario vemos si el token esta dentro del rango
                $query = "SELECT count(*) total FROM usuario_token 
                        where userid = :usuario and now() between fecha and date_add(fecha, interval 4 hour)
                        and estado = 1";
                $datos = parent::getParamDatos($query,$params);
                //echo json_encode($datos);
                if(isset($datos[0]['total'])){   
                    $total = (int)$datos[0]['total'];
                    if ($total == 0){
                        $existe = 0;
                    }elseif($total>1){
                         // Anulamos los token desfazados que quedaron vigentes
                        $query = "update usuario_token set estado = 2 where usuario = :usuario and estado = 1";
                        parent::noResultQuery($query,$params);
                        $existe = 0;
                    }   
                }else{
                    $existe = 0;
                }
            }elseif($total>1){
                // Anulamos los token desfazados que quedaron vigentes
                $query = "update usuario_token set estado = 2 where userid = :usuario and estado = 1";
                parent::noResultQuery($query,$params);
                $existe = 0;
            }else{
                $existe = 0;
            }
        }else{
            $existe = 0;
        }
        //Existe un unico token vigente y dentro del rango de tiempo
        return $existe;
    }
    

    private function setToken($codigo){
        $val = true;
        $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
        $query = "insert into usuario_token (userid,token,fecha,estado)
                    value(:user,:token,now(),1)";
        
        $params = array(
            'user' => $codigo,
            'token' => $token
        );
        $verificar = parent::noResultQuery($query,$params);
        if($verificar){
            return $token;
        }else{
            return 0;
        }
    }
}

?>