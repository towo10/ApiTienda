<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class orden_compra extends conexion{
    

    public function getOrdenCompra($tienda,$usuario){
  
        $query = "select 
                    a.codigo,b.proveedor,date_format(a.fecha,'%d/%m/%Y') fecha,a.descripcion
                from
                    compras a left join
                    vproveedor b on b.codigo = ifnull(a.proveedor_codigo,0)
                where
                    a.tienda_codigo = :tienda
                    and a.usuarios_codigo = :usuario
                    and a.estado_codigo = 1
                order by a.fecha desc";
        $params = [
            'tienda' => $tienda,
            'usuario' => $usuario
        ];
        $sql = parent::getParamDatos($query,$params);

        $cab = array();
        foreach ($sql as $columna => $valor) {
            $det = array();
            foreach($valor as $c => $v){
                $det += [$c => $v];
                if($c == "codigo"){
                    $compra = $v;
                }
            }            
            $det += ["marcas" => $this->getDetalleOrdenCompra_producto($compra)];
            $cab += [$columna => $det];
        }

        return $cab;
    }

    public function getDetalleOrdenCompra_producto($compra){
        $query = "select
                    a.categorias_codigo
                    ,a.subcategoria_codigo
                    ,a.productos_codigo
                    ,ifnull(a.marca_codigo,0) marca_codigo
                    ,a.moneda_codigo
                    ,a.categoria
                    ,a.subcategoria
                    ,a.producto
                    ,ifnull(a.marca,'Sin Marca') marca
                    ,a.moneda
                    ,sum(a.cantidad * a.precio) subtotal
                from
                    vorde_compra_detalle a
                where
                    a.compras_codigo = :compra
                group by a.categorias_codigo
                    ,a.subcategoria_codigo
                    ,a.productos_codigo
                    ,a.marca_codigo
                    ,a.moneda_codigo
                    ,a.categoria
                    ,a.subcategoria
                    ,a.producto
                    ,a.moneda";
        $params = [
            'compra' => $compra
        ];
        $sql = parent::getParamDatos($query,$params);

        $cab = array();
        foreach ($sql as $columna => $valor) {
            $det = array();
            foreach($valor as $c => $v){
                $det += [$c => $v];
                if($c == "categorias_codigo"){
                    $cat = $v;
                }
                if($c == "subcategoria_codigo"){
                    $subcat = $v;
                }
                if($c == "productos_codigo"){
                    $prod = $v;
                }
                if($c == "moneda_codigo"){
                    $moneda = $v;
                }
                if($c == "marca_codigo"){
                    $marca = $v;
                }
            }            
            $det += ["colores" => $this->getDetalleOrdenCompra_marca($compra,$cat,$subcat,$prod,$moneda,$marca)];
            $cab += [$columna => $det];
        }

        return $cab;
    }

    public function getDetalleOrdenCompra_marca($compra,$cat,$subcat,$prod,$moneda,$marca){
        $query = "select
                    a.color_codigo
                    ,a.color
                    ,sum(a.cantidad * a.precio) subtotal
                from
                    vorde_compra_detalle a
                where
                    a.compras_codigo = :compra
                    and a.categorias_codigo = :cat
                    and a.subcategoria_codigo = :subcat
                    and a.productos_codigo = :prod
                    and a.moneda_codigo = :moneda
                    and ifnull(a.marca_codigo,0) = :marca
                group by a.color_codigo
                    ,a.color";
        $params = [
            'compra' => $compra,
            'cat' => $cat,
            'subcat' => $subcat,
            'prod' => $prod,
            'moneda' => $moneda,
            'marca' => $marca
        ];
        $sql = parent::getParamDatos($query,$params);

        $cab = array();
        foreach ($sql as $columna => $valor) {
            $det = array();
            foreach($valor as $c => $v){
                $det += [$c => $v];
                if($c == "color_codigo"){
                    $color = $v;
                }
            }            
            $det += ["talles" => $this->getDetalleOrdenCompra_color($compra,$cat,$subcat,$prod,$moneda,$marca,$color)];
            $cab += [$columna => $det];
        }

        return $cab;
    }

    public function getDetalleOrdenCompra_color($compra,$cat,$subcat,$prod,$moneda,$marca,$color){
        $query = "select
                    a.talla
                    ,a.cantidad
                    ,a.precio
                    ,a.cantidad * a.precio subtotal
                from
                    vorde_compra_detalle a
                where
                    a.compras_codigo = :compra
                    and a.categorias_codigo = :cat
                    and a.subcategoria_codigo = :subcat
                    and a.productos_codigo = :prod
                    and a.moneda_codigo = :moneda
                    and ifnull(a.marca_codigo,0) = :marca
                    and a.color_codigo = :color
                ";
        $params = [
            'compra' => $compra,
            'cat' => $cat,
            'subcat' => $subcat,
            'prod' => $prod,
            'moneda' => $moneda,
            'marca' => $marca,
            'color' => $color
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;
    }

    public function getDetalleOrdenCompra($compra){
        $query = "select
                    a.codigo
                    ,a.categorias_codigo
                    ,a.subcategoria_codigo
                    ,a.productos_codigo
                    ,a.color_codigo
                    ,a.tallas_codigo
                    ,a.marca_codigo
                    ,a.moneda_codigo
                    ,a.unidad_medida_codigo
                    ,b.nombre categoria
                    ,c.nombre subcategoria
                    ,d.nombre producto
                    ,e.nombre color
                    ,f.abreviatura talla
                    ,g.nombre Marca
                    ,a.cantidad
                    ,a.precio
                    ,h.nombre moneda
                    ,i.descripcion unidad_medida
                    ,a.flg_producto_temp
                    ,pt.dsc_prod
                from
                    detalle_compras a left join
                    categorias b on b.codigo = a.categorias_codigo left join
                    subcategoria c on c.categorias_codigo = a.categorias_codigo
                                        and c.codigo = a.subcategoria_codigo left join
                    productos d on d.categorias_codigo = a.categorias_codigo
                                    and d.subcategoria_codigo = a.subcategoria_codigo
                                    and d.codigo = a.productos_codigo left join
                    color e on e.codigo = a.color_codigo left join
                    tallas f on f.codigo = a.tallas_codigo
                                and f.categorias_codigo = a.categorias_codigo
                                and f.subcategoria_codigo = a.subcategoria_codigo left join
                    Marcas g on g.codigo = a.marca_codigo
                                and g.categorias_codigo = a.categorias_codigo
                                and g.subcategoria_codigo = a.subcategoria_codigo
                                and g.producto_codigo = a.productos_codigo left join
                    monedas h on h.codigo = a.moneda_codigo left join
                    unidad_medida i on i.codigo = a.unidad_medida_codigo left join
                    producto_temp pt on pt.catid = a.categorias_codigo
                                        and pt.subcatid = a.subcategoria_codigo
                                        and pt.id = a.productos_codigo
                where
                    a.compras_codigo = :compra";
        $params = [
            'compra' => $compra
        ];
        $sql = parent::getParamDatos($query,$params);

        return $sql;
    }

}

?>