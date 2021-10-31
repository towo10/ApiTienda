<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';
require_once 'clases/categoria.class.php';
require_once 'clases/subcategoria.class.php';

$_respuesta = new respuestas;

class productos extends conexion{

    private $idtienda ="";
    private $idcategoria ="";
    private $idsubcategoria ="";
    private $idproducto ="";
    private $producto ="";
    private $modelo ="";
    private $observacion ="";
    private $idusuario ="";
    
    
    //Muestra una Lista de Productos de la tabla original divididos en páginas de 100
    public function getPageProductos($pagina,$tienda){
        $inicio = 0;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }

        $query = "select 	a.codigo,a.categorias_codigo,a.subcategoria_codigo,a.categoria,a.subcategoria,a.nombre,a.observacion,a.unidad_medida,a.estado,a.stock
                    from	vproductos a
                    where	a.tienda_codigo = :tienda
                    limit $inicio,$cantidad";
        $params = [
            'tienda' => $tienda
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;
    }

    //Muestra un listado de la tabla temporal de Productos
    public function getProductos($tienda,$usuario){
        $query = "select	a.id,a.catid,a.subcatid,b.nombre categoria,c.nombre subcategoria,a.dsc_prod,a.dsc_obs,a.dsc_modelo
                    from 	producto_temp a left join
                            categorias b on b.codigo = a.catid left join
                            subcategoria c on c.codigo = a.subcatid
                                                and c.categorias_codigo = a.catid
                where 
                    a.tiendaid = :tienda
                    and a.userid = :usuario
                    and a.estadoid = 1";
        $params = [
            'tienda' => $tienda,
            'usuario' => $usuario
        ];
        $sql = parent::getParamDatos($query,$params);



        $cab = array();
        foreach ($sql as $cmarca => $vmarca) {
            $det = array();
            $detalle = array();
            foreach($vmarca as $c => $v){
                $det += [$c => $v];

                switch ($c) {
                    case "catid":
                        $categoria = $v;
                        break;
                    
                    case 'subcatid':
                        $subcategoria = $v;
                        break;

                    case 'id':
                        $producto = $v;
                        break;
                }
            }            
            $det += ["imagenes" => $this->getProductoImagen($tienda,$categoria,$subcategoria,$producto)];
            $cab += [$cmarca => $det];
        }
        
        return $cab;


    }


    public function getProductoImagen($tienda,$cat,$subcat,$pro){
        $query = "select	a.id,if(length(trim(a.dsc_dir))=0,null,concat('/Imagenes/Productos/',cast(:tienda as char),'/',cast(a.catid as char),'/',cast(a.subcatid as char),a.dsc_dir)) dsc_dir
                    from	imagenes_temp a
                    where	a.catid = :cat
                            and a.subcatid = :subcat
                            and a.prodid = :pro";
        $params = [
            'tienda' => $tienda,
            'cat' => $cat,
            'subcat' => $subcat,
            'pro' => $pro
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;


    }

    //Retorna el Producto de la Tabla Temporal
    public function getProducto($tienda,$usuario,$producto){
        $query = "  select	a.id,a.catid,a.subcatid,b.nombre categoria,c.nombre subcategoria,a.dsc_prod,a.dsc_obs,a.dsc_modelo
                    from 	producto_temp a left join
                                categorias b on b.codigo = a.catid left join
                                subcategoria c on c.codigo = a.subcatid
                                                    and c.categorias_codigo = a.catid
                    where 
                        a.tiendaid = :tienda
                        and a.userid = :usuario
                        and a.id = :producto";
        $params = [
            'tienda' => $tienda,
            'producto' => $producto,
            'usuario' => $usuario
        ];
        $sql = parent::getParamDatos($query,$params);
        $cab = array();
        foreach ($sql as $columna => $valor) {
            $det = array();
            $detalle = array();
            foreach($valor as $c => $v){
                $det += [$c => $v];

                switch ($c) {
                    case "catid":
                        $categoria = $v;
                        break;
                    
                    case 'subcatid':
                        $subcategoria = $v;
                        break;

                    case 'id':
                        $producto = $v;
                        break;
                }
            }            
            $det += ["imagenes" => $this->getProductoImagen($tienda,$categoria,$subcategoria,$producto)];
            $cab += [$columna => $det];
        }
        
        return $cab;
    }

    //Busca de la tabla orginal de Producto, una coincidencia.
    public function getbuscarProducto($tienda,$buscar){
        $query = "CALL sp_buscar_producto(:buscar,:tienda)";
        $params = [
            'tienda' => $tienda,
            'buscar' => $buscar
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;

    }
    
    //Obtiene el nuevo código para insertar en la Tabla Temporal de Productos
    private function getIDProducto($tienda){
        $query = "  select ifnull(max(id),0)+1 idproducto
                    from producto_temp
                    where 
                        tiendaid = :tienda";
        $params = ['tienda' => $tienda];
        $sql = parent::getParamDatos($query,$params);
        return (int)$sql[0]['idproducto'];

    }

    //Inserta en la tabla temporal de Prodcuto, esperando ser aprobado desde el sistema principal
    public function insertProducto($json){
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


        if (!isset($datos['idtienda']) || !isset($datos['categoria']) || !isset($datos['subcategoria']) ||  
        !isset($datos['producto']) || !isset($datos['idusuario'])) {
            return $_respuesta->error_400();
        }else {
            $this->idtienda     = $datos['idtienda'];
            $categoria          = $datos['categoria'];
            $subcategoria       = $datos['subcategoria'];
            $this->producto     = $datos['producto'];
            $this->idusuario    = $datos['idusuario'];

            //Buscamos los id de la categoria y la subcategoria
            $_categoria = new categoria;
            $_subcategoria = new subcategoria;
            $resultado = $_categoria->getIdCategoria($this->idtienda,$categoria);
            if($resultado){
                $this->idcategoria = $resultado[0]['id'];
            }else{
                return $_respuesta->error_200('La categoría no es válida.');
            }

            $resul = $_subcategoria->getidSubCategoria($this->idtienda,$this->idcategoria,$subcategoria);
            if($resul){
                $this->idsubcategoria = $resul[0]['id'];
            }else{
                return $_respuesta->error_200('La subcategoría no es válida o no coincide con la categoria.');
            }
            
            if(isset($datos['modelo'])){
                $this->modelo = $datos['modelo'];
            }
            if(isset($datos['observacion'])){
                $this->observacion = $datos['observacion'];
            }
            $query = "insert into producto_temp(tiendaid,id,catid,subcatid,dsc_prod,dsc_obs,dsc_modelo,estadoid,userid,fecha)
            values(:tienda,:id,:catid,:subcatid,:dsc_pro,:dsc_obs,:dsc_modelo,1,:userid,now())";
            $idpro = $this->getIDProducto($this->idtienda);
            $params = [
                'tienda' => $this->idtienda,
                'id' => $idpro,
                'catid' => $this->idcategoria,
                'subcatid' => $this->idsubcategoria,
                'dsc_pro' => $this->producto,
                'dsc_obs' => $this->observacion,
                'dsc_modelo' => $this->modelo,
                'userid' => $this->idusuario
            ];
            
            $resulset = parent::noResultQuery($query,$params);

            if ($resulset){
                $result = $_respuesta->response;
                $result['status'] = "ok";
                $result['result'] = array(
                    "idcat" => $this->idcategoria,
                    "idsubcat" => $this->idsubcategoria,
                    "idpro" => $idpro,
                );
                return $result;
            }else{
                return $_respuesta->error_500('Ocurrió un problema enviando datos al servidor.');
            }
        }
    }
    
    //Actualiza los datos de la tabla temporal de productos.
    public function updateProducto($json){
        
        $datos = json_decode($json,true);
        //============================================================================INI-TOKEN
        if(!isset($datos['token'])){
            return $_respuesta->error_401();
        }else {
            $tokenarray = $this->getToken($datos['token']);
            if(!$tokenarray){
                return $_respuesta->error_401("El token que ha enviado es inválido o ha caducado:");
            }
        }
        //============================================================================FIN-TOKEN

        if (!isset($datos['idtienda']) || !isset($datos['idusuario']) || !isset($datos['idproducto']) ||
        !isset($datos['idcategoria']) || !isset($datos['idsubcategoria']) ||  
        !isset($datos['producto']) || !isset($datos['modelo']) || !isset($datos['observacion'])) {
            return $_respuesta->error_400();
        }else {
            $this->idtienda         = $datos['idtienda'];
            $this->idusuario        = $datos['idusuario'];
            $this->idproducto       = $datos['idproducto'];

            $this->idcategoria      = $datos['idcategoria'];
            $this->idsubcategoria   = $datos['idsubcategoria'];

            $this->producto         = $datos['producto'];
            $this->observacion      = $datos['observacion'];
            $this->modelo           = $datos['modelo'];
            
            

            $query = "  update	producto_temp
                        set		dsc_prod = :dsc_pro,
                                dsc_obs = :dsc_obs,
                                dsc_modelo = :dsc_modelo,
                                fecha = now()        
                        where	tiendaid = :tienda
                                and id = :id
                                and userid = :userid
                                and estadoid = 1";
            $params = [
                'tienda' => $this->idtienda,
                'id' => $this->idproducto,
                'dsc_pro' => $this->producto,
                'dsc_obs' => $this->observacion,
                'dsc_modelo' => $this->modelo,
                'userid' => $this->idusuario
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
                return $_respuesta->error_500('Ocurrió un problema enviando datos al servidor.');
            }
        }
    }
    //elimina un producto de la tabla temporal Productos
    public function deleteProducto($json){
        $_respuesta = new respuestas;
        $datos = json_decode($json,true);
        //============================================================================INI-TOKEN
        if(!isset($datos['token'])){
            return $_respuesta->error_401('No autorizado wilder '.$datos['idusuario']);
        }else {
            $tokenarray = $this->getToken($datos['token']);
            if(!$tokenarray){
                return $_respuesta->error_401("El token que ha enviado es inválido o ha caducado");
            }
        }
        //============================================================================FIN-TOKEN
        
        if (!isset($datos['idtienda']) || !isset($datos['idcategoria']) || !isset($datos['idsubcategoria']) 
        || !isset($datos['idproducto']) || !isset($datos['idusuario'])) {
            return $_respuesta->error_400();
        }else {
            $this->idtienda         = $datos['idtienda'];
            $this->idusuario        = $datos['idusuario'];
            $this->idproducto       = $datos['idproducto'];
            $this->idcategoria      = $datos['idcategoria'];
            $this->idsubcategoria   = $datos['idsubcategoria'];            
            // Buscamos las imagenes que contiene este producto.
            $imagenes = $this->getProductoImagen($this->idtienda,$this->idcategoria,$this->idsubcategoria,$this->idproducto);

            if ($imagenes){
                //borramos el registro de imagenes asociado al producto
                $query = "delete from	imagenes_temp
                        where	catid = :categoria
                                and subcatid = :subcategoria
                                and prodid = :producto";
                $params = [
                    'categoria' => $this->idcategoria,
                    'subcategoria' => $this->idsubcategoria,
                    'producto' => $this->idproducto
                ];           
                $resulset = parent::noResultQuery($query,$params);
                if ($resulset){
                    //ELIMINAMOS LOS ARCHIVOS QUE TIENEN LA TABLA DE IMAGENES
                    $ListanoEliminada = array();
                    foreach ($imagenes as $columna => $valor) {
                        $noEliminada = array();
                        foreach($valor as $c => $v){
                            if ($c == "dsc_dir"){
                                if (!unlink(".".$v)){
                                    $noEliminada += [$c => $v];
                                }   
                            }
                        }
                        if($noEliminada){
                            $ListanoEliminada += [$columna => $noEliminada];
                        }   
                    }

                    //borramos el registro del producto
                    $resulset = $this->setdeletePro($this->idtienda,$this->idusuario,$this->idproducto);
                    if ($resulset){
                        $result = $_respuesta->response;
                        $result['status'] = "ok";
                        $result['result'] = array(
                            "mensaje" => "Datos eliminados correctamente",
                            "ArchivosBorrarManual" => $ListanoEliminada
                        );
                        return $result;
                    }else{
                        return $_respuesta->error_500('1.-Ocurrió un problema eliminando datos da la tabla de Productos.');
                    }

                }else{
                    return $_respuesta->error_500('1.-Ocurrió un problema eliminando datos da la tabla de Imágenes.');
                }

            }else{
                //BORRAMOS LOS DATOS DE LA TABLA PRODUCTO TEMPORAL
                $resulset = $this->setdeletePro($this->idtienda,$this->idusuario,$this->idproducto);
                if ($resulset){
                    $result = $_respuesta->response;
                    $result['status'] = "ok";
                    $result['result'] = array(
                        "mensaje" => "Datos eliminados correctamente"
                    );
                    return $result;
                }else{
                    return $_respuesta->error_500('2.-Ocurrió un problema eliminando datos da la tabla de Productos.');
                }
            }    
        }
    }

    private function setdeletePro($tienda,$usuario,$producto){
        $query = "  delete from producto_temp   
                    where	tiendaid = :tienda
                            and id =:id
                            and userid = :userid
                            and estadoid = 1";
        $params = [
        'tienda' => $tienda,
        'id' => $producto,
        'userid' => $usuario
        ];
        $resulset = parent::noResultQuery($query,$params);
        return $resulset;
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

?>