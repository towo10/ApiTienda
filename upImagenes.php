<?php

require_once 'clases/respuestas.class.php';
$_respuestas = new respuestas;


header("Content-Type: application/json");
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $datos = json_decode(file_get_contents('php://input'),true);
    if(!isset($datos['imagenes']) && !isset($datos['nom'])){
      echo json_encode($_respuestas->error_400());
    }else{
      $base64 = $datos['imagenes'];
      $nom_archivo = $datos['nom'];
      
      //$base_to_php = explode( ',', $base64);
      //$data = base64_decode($base_to_php[1]);
      $data = base64_decode($base64);
      $filepath = "Imagenes/".$nom_archivo.".jpeg";
      file_put_contents($filepath, $data);
      chmod ($filepath, 0644);

      $result = $_respuestas->response;
      $result['status'] = "ok";
      $result['result'] = array(
          "mensaje" => "Datos guardados correctamente"
      );
      echo json_encode($result);
    }
}else{
    echo json_encode($_respuestas->error_405());
}


/*

// open the output file for writing
    $ifp = fopen( $output_file, 'wb' ); 

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    fclose( $ifp ); 

    return $output_file; 

*/

/*
$num_articulo = $_POST['num_articulo'];
$Carperta = "Imagenes/Product/".$_POST['Carpeta'];
$dirmake = mkdir($Carperta, 0777);
$Archivo = basename($_FILES['fotoUp']['name']);
$ruta = $Carperta."/" .$Archivo;
if(move_uploaded_file($_FILES['fotoUp']['tmp_name'], $ruta))
chmod ("uploads/".basename( $_FILES['fotoUp']['name']), 0644);

$rutafisica = str_replace("/","\\",getcwd()."/".$ruta);

include 'serv.php';

$log1="UPDATE man_articulo
      set dsc_imagen=( ?)
      WHERE num_articulo=( ?)";

$params1 = array($rutafisica,$num_articulo);
  if( sqlsrv_query( $conect, $log1, $params1)){
    echo 'SE ACTUALIZÓ LA BASE DE DATOS CORRECTAMENTE.';
  }else{
    echo "ERROR EN LA ACTUALIZACIÓN.";
    die( print_r( sqlsrv_errors(), true));
  }
*/
 ?>
