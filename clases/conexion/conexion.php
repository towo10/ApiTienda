<?php
class conexion {
    private $hos;
    private $port;
    private $db;
    private $user;
    private $password;
    private $charset;

    public function __construct(){
        $listaDB = $this->infodb();
        foreach ($listaDB as $key => $value) {
            $this->host     = $value['server'];
            $this->port     = $value['port'];
            $this->db       = $value['database'];
            $this->user     = $value['user'];
            $this->password = $value['password'];
            $this->charset  = $value['charset'];
        }
        try{


            $this->conn = new PDO("mysql:host=".$this->host.
                                        ';dbname='.$this->db.
                                        ';port='.$this->port.
                                        ';charset='.$this->charset,
                                        $this->user,
                                        $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo 'ok conex';
        }catch(PDOException $e){
           die("Error: ".$e->getMessage());
            exit;
        }
    }

    private function infodb(){
        $direccion = dirname(__FILE__);
        $jsondata = file_get_contents($direccion."/"."config");
        return json_decode($jsondata,true);
    }

    private function cast_utf8($array){
        array_walk_recursive($array,function(&$item,$key){
            if(!mb_detect_encoding($item,'utf-8',true)){
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    public function getDatos($query){
        $sql = $this->conn->prepare($query);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        $result = $sql->fetchAll();
        return $this->cast_utf8($result);
    }

    public function getParamDatos($query,$params){
        $sql = $this->conn->prepare($query);
        $sql->execute($params);
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        $result = $sql->fetchAll();
        return $this->cast_utf8($result);
    }

    public function getParamGrupoDatos($query,$params){
        $sql = $this->conn->prepare($query);
        $sql->execute($params);
        $sql->setFetchMode(PDO::FETCH_GROUP);
        $result = $sql->fetchAll();
        return $this->cast_utf8($result);
    }

    public function noResultQuery($query,$params){
        $sql = $this->conn->prepare($query);
        $sql->execute($params);
        $filas = $sql->rowCount();
        if($filas > 0){
            $resultado['codigo']=0;
            $resultado['mensaje']="Se actualizaron $filas registros.";
        }else{
            $resultado['codigo']=-1;
            $resultado['mensaje']="No se actualizó ningun dato";
        }
        
        return $filas;
    }

    protected function encriptar($string){
        return hash('sha256',$string);

    }

}

?>