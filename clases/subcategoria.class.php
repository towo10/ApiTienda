<?php
require_once 'conexion/conexion.php';
//require_once 'respuestas.class.php';
//$_respuesta = new respuestas;

class subcategoria extends conexion{

    private $id = 0;
    private $subcategoria = "";

    public function getSubCategoria($tienda,$categoria){
        //echo 'entra';
        $query = "select
                    b.codigo id,b.nombre subcategoria
                from
                    categorias a,
                    subcategoria b
                where
                    a.codigo = b.categorias_codigo
                    and a.tienda_codigo in (0,:tienda)
                    and a.estado_codigo in (1,3)
                    and b.estado_codigo in (1,3)
                    and lower(a.nombre) = lower(:categoria)";
        $params = [
            'tienda' => $tienda,
            'categoria' => $categoria
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;
    }

    public function getidSubCategoria($tienda,$idcategoria,$subcategoria){
        $query = "select
                    b.codigo id
                from
                    categorias a,
                    subcategoria b
                where
                    a.codigo = b.categorias_codigo
                    and a.tienda_codigo in (0,:tienda)
                    and a.estado_codigo in (1,3)
                    and b.estado_codigo in (1,3)
                    and a.codigo = :idcategoria
                    and lower(b.nombre) = lower(:subcategoria)";
        $params = [
            'tienda' => $tienda,
            'idcategoria' => $idcategoria,
            'subcategoria' => $subcategoria
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;
    }

}
?>