<?php
require_once 'conexion/conexion.php';

class categoria extends conexion{

    private $id = 0;
    private $categoria = "";

    public function getCategoria($tienda){
        $query = "select codigo id,nombre categoria from categorias
                    where estado_codigo in (1,3)
                    and tienda_codigo in (:tienda,0)";
        $params = [
            'tienda' => $tienda
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;
    }
    public function getIdCategoria($tienda,$categoria){
        $query = "select codigo id from categorias
                    where tienda_codigo in (0,:tienda)
                    and estado_codigo in (1,3)
                    and lower(nombre) = lower(:categoria)";
        $params = [
            'tienda' => $tienda,
            'categoria' => $categoria
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;
    }

}



?>