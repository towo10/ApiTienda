<?php
require_once 'conexion/conexion.php';

class imagenes extends conexion{

    public function getImagenes($tienda,$categoria,$subcategoria,$producto){
        $query = "select	concat('/Imagenes/Productos/',:tienda,'/',
                            cast(b.categorias_codigo as char),'/',
                            cast(b.subcategoria_codigo as char),'/',b.direccion) url_imagen
                    from 	imagenes b
                    where 	b.categorias_codigo = :categoria
                            and b.subcategoria_codigo = :subcategoria
                            and b.productos_codigo = :producto
                            and b.estado_codigo = 1";
        $params = [
            'tienda' => $tienda,
            'categoria' => $categoria,
            'subcategoria' => $subcategoria,
            'producto' => $producto
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;
    }
}

?>