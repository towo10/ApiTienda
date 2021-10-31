<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class subir_imagen extends conexion{

    private $tienda = "";
    private $categoria = "";
    private $subcategoria = "";
    private $producto = "";
    private $imagen = "";
    private $id = "";
    private $usuario = "";
    private $archivo ="";
    
    public function insertImagen($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        //============================================================================INI-TOKEN
        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else {
            $tokenarray = $this->getToken($datos['token']);
            if(!$tokenarray){
                return $_respuestas->error_401("El token que ha enviado es inválido o ha caducado");
            }
        }
        //============================================================================FIN-TOKEN

        if(!isset($datos['imagen']) || !isset($datos['idcat']) ||
        !isset($datos['idsubcat']) || !isset($datos['idpro']) ||
        !isset($datos['idtienda']) || !isset($datos['iduser'])){
            echo json_encode($_respuestas->error_400());
        }else{
            $this->tienda = $datos['idtienda'];
            $this->categoria = $datos['idcat'];
            $this->subcategoria = $datos['idsubcat'];
            $this->producto = $datos['idpro'];
            $this->usuario = $datos['iduser'];
            $this->imagen = $datos['imagen'];
            $this->id = $this->getIDImagen($this->categoria,
                                            $this->subcategoria,
                                            $this->producto);

            /*Copiamos a los Archivos*/
            $Path = "Imagenes/Productos";
            $path_tienda = "/".$this->tienda;
            $path_categoria = "/".$this->categoria;
            $path_subcategoria = "/".$this->subcategoria;

            $this->archivo = "/api_".$this->producto."_"
                            .$this->id."_".date('d.m.Y.H.i.s').".jpeg";
            /*Creamos la Carpeta si no Existe*/
            if (!file_exists($Path.$path_tienda.
                                $path_categoria.
                                $path_subcategoria)) {
                mkdir($Path.$path_tienda.
                        $path_categoria.
                        $path_subcategoria, 0777, true);
            }
            /*Insertamos el Archivo*/
            $data = base64_decode($this->imagen);
            $filepath = $Path.$path_tienda.
                        $path_categoria.
                        $path_subcategoria.
                        $this->archivo;
                        
            file_put_contents($filepath, $data);
            chmod ($filepath, 0644);


            
            /*Insertamos en la tabla*/
            $query = "insert into imagenes_temp(catid,subcatid,prodid,id,dsc_dir,estadoid,userid,fecha)
            values(:catid,:subcatid,:prodid,:id,:dsc_dir,1,:userid,now())";
            $params = [
                'catid' => (int)$this->categoria,
                'subcatid' => (int)$this->subcategoria,
                'prodid' => (int)$this->producto,
                'id' => (int)$this->id,
                'dsc_dir' => $this->archivo,
                'userid' => (int)$this->usuario
            ];
            $resulset = parent::noResultQuery($query,$params);
            if ($resulset){
                $result = $_respuestas->response;
                $result['status'] = "ok";
                $result['result'] = array(
                    "mensaje" => "Se insertó"
                );
                return $result;
            }else{
                return $_respuestas->error_500('Ocurrió un problema enviando datos al servidor.');
            }
        }

    }
    //Obtiene el nuevo código para insertar en la Tabla Temporal de Imagenes
    private function getIDImagen($categoria,$subcategoria,$producto){
        $query = "  select 	ifnull(max(id),0)+1 idimagen
                    from	imagenes_temp
                    where	catid = :categoria
                            and subcatid = :subcategoria
                            and prodid = :producto";
        $params = [
            'categoria' => $categoria,
            'subcategoria' => $subcategoria,
            'producto' => $producto
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql[0]['idimagen'];

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
}

?>