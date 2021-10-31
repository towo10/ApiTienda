<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class sesion extends conexion{

    public function getUsuario($tienda,$usuario){
        $query = "select concat(a.apellidos,', ',a.nombres) nombre,b.correo,concat('/Imagenes/Usuarios/',ifnull(a.imagen,'sinimagen.png')) imagen
                    from perfiles a,usuarios b
                    where a.usuarios_codigo = b.codigo and a.tiendas_codigo = :tienda and a.usuarios_codigo = :usuario";
        $params = [
            'tienda' => $tienda,
            'usuario' => $usuario
        ];
        $sql = parent::getParamDatos($query,$params);
        return $sql;
    }
}

?>