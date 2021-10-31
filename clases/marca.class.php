<?php
require_once 'conexion/conexion.php';

class marca extends conexion{

    public function getMarca($tienda,$categoria,$subcategoria,$producto){
        $query = "select	ifnull(a.marca_codigo,0) marca_codigo,ifnull(b.nombre,'Sin Marca') marca
                            ,f_string_stock_marca(a.categorias_codigo,a.subcategoria_codigo,a.productos_codigo,a.marca_codigo) stock
                            ,f_compra_promedio_marca_top10(a.categorias_codigo,a.subcategoria_codigo,a.productos_codigo,a.marca_codigo) promedio
                            ,f_ultimo_precio_compra_marca(a.categorias_codigo,a.subcategoria_codigo,a.productos_codigo,a.marca_codigo) ult_precio
                            ,ifnull(c.precio_venta,'Aún no definido') precio_venta
                from	vstock_marca a left join
                        Marcas b on b.tienda_codigo = a.tienda_codigo
                                    and b.categorias_codigo = a.categorias_codigo
                                    and b.subcategoria_codigo = a.subcategoria_codigo
                                    and b.producto_codigo = a.productos_codigo
                                    and b.codigo = a.marca_codigo left join
                        productos c on c.tienda_codigo = a.tienda_codigo
                                    and c.categorias_codigo = a.categorias_codigo
                                    and c.subcategoria_codigo = a.subcategoria_codigo
                                    and c.codigo = a.productos_codigo
        where	a.tienda_codigo = :tienda
                and a.categorias_codigo = :categoria
                and a.subcategoria_codigo = :subcategoria
                and a.productos_codigo = :producto";
        $params = [
            'tienda' => $tienda,
            'categoria' => $categoria,
            'subcategoria' => $subcategoria,
            'producto' => $producto
        ];
        $sql = parent::getParamDatos($query,$params);

        $cab = array();
        foreach ($sql as $cmarca => $vmarca) {
            $det = array();
            $detalle = array();
            foreach($vmarca as $c => $v){
                $det += [$c => $v];
                if($c == "marca_codigo"){
                    $marca_codigo = $v;
                }
            }            
            $det += ["Contenido" => $this->getMarcaInfo($tienda,$categoria,$subcategoria,$producto,$marca_codigo)];
            $cab += [$cmarca => $det];
        }
        
        return $cab;
    }

    public function getMarcaInfo($tienda,$categoria,$subcategoria,$producto,$marca){
        $query = "select	distinct b.codigo,b.nombre color
                            ,f_string_stock_marca_color(a.tienda_codigo,a.categorias_codigo,a.subcategoria_codigo,a.productos_codigo,a.marca_codigo,a.color_codigo) stock
                            ,f_string_stock_marca_talle(a.tienda_codigo,a.categorias_codigo,a.subcategoria_codigo,a.productos_codigo,a.marca_codigo,a.color_codigo) talle
                    from	Stock a left join
                            color b on a.color_codigo = b.codigo
                    where	a.tienda_codigo = :tienda
                            and a.categorias_codigo = :categoria
                            and a.subcategoria_codigo = :subcategoria
                            and a.productos_codigo = :producto
                            and ifnull(a.marca_codigo,0) = ifnull(:marca,0)

                    order by if(b.codigo=1,1,0) desc,b.nombre";
        $params = [
            'tienda' => $tienda,
            'categoria' => $categoria,
            'subcategoria' => $subcategoria,
            'producto' => $producto,
            'marca' => $marca
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;
    }
}

?>