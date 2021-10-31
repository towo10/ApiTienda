<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEBASOFT API</title>
    <link rel="stylesheet" href="css/estilos.css" type="text/css">
</head>
<body>
    
<div class="divheader">
        <a class="button" id="buttonid" href="descargar-aplicacion.php?archivo=app-debug.apk">Descargar Tienda</a>
        </div>
    <div  class="container">
        
        
        <div class="divbody">
        <h1>Servicio Api REST para la Gestion de Orden de Compra - SEBASOFT</h1>
            <h3>Login</h3>
            <code>
            POST  /auth
            <br>
                {
                <br>
                &emsp;&emsp;"user" :"",  -> REQUERIDO
                <br>
                &emsp;&emsp;"password": "" -> REQUERIDO
                <br>
                }
            
            </code>
        </div>      
        <div class="divbody">   
            <h3>Productos</h3>
            <code>
            <p>GET  /productos.php?tienda={idtienda}</p>
                <p class=desc>Lista los productos que se desean ingresar como nuevos en el sistema.</p>
                <p>GET  /productos.php?page={página}&tienda={idtienda}</p>
                <p class=desc>Lista los productos del Sistema por el número de página.</p>
                <p>GET  /productos.php?tienda={idtienda}&producto={idproducto}</p>
                <p class=desc>Retorna el producto que se desea insertar en el sistema.</p>
                <p>GET  /productos.php?tienda={idtienda}&buscar={busqueda}</p>
                <p class=desc>Lista todo los productos desde una descripción.</p>           
            </code>
            <code>
            POST  /productos
            <br> 
            {
                <br> 
                &emsp;&emsp;"idtienda" : "",        -> REQUERIDO
                <br> 
                &emsp;&emsp;"idcategoria" : "",     -> REQUERIDO
                <br> 
                &emsp;&emsp;"idsubcategoria": "",    -> REQUERIDO
                <br> 
                &emsp;&emsp;"producto" : "",         -> REQUERIDO
                <br>  
                &emsp;&emsp;"observacion" : "",        
                <br>        
                &emsp;&emsp;"modelo" : "",       
                <br>       
                &emsp;&emsp;"idUnidadMedida" : "",  -> REQUERIDO
                <br> 
                &emsp;&emsp;"idusuario" : "",       -> REQUERIDO
                <br>         
                &emsp;&emsp;"token" : ""            -> REQUERIDO        
                <br>  
            }
            </code>
            <code>
            PUT  /productos
            <br> 
            {
                <br> 
                &emsp;&emsp;"idtienda" : "",        -> REQUERIDO
                <br> 
                &emsp;&emsp;"idproducto" : "",        -> REQUERIDO
                <br> 
                &emsp;&emsp;"idcategoria" : "",     -> REQUERIDO
                <br> 
                &emsp;&emsp;"idsubcategoria": "",    -> REQUERIDO
                <br> 
                &emsp;&emsp;"producto" : "",         -> REQUERIDO
                <br>  
                &emsp;&emsp;"observacion" : "",        
                <br>        
                &emsp;&emsp;"modelo" : "",       
                <br>       
                &emsp;&emsp;"idUnidadMedida" : "",  -> REQUERIDO
                <br> 
                &emsp;&emsp;"idusuario" : "",       -> REQUERIDO
                <br>         
                &emsp;&emsp;"token" : ""            -> REQUERIDO        
                <br> 
            }
            </code>
            <code>
            DELETE  /productos
            <br> 
            {   
                <br> 
                &emsp;&emsp;"idtienda" : "",        -> REQUERIDO
                <br> 
                &emsp;&emsp;"idproducto" : "",        -> REQUERIDO
                <br> 
                &emsp;&emsp;"idusuario" : "",       -> REQUERIDO
                <br>    
                &emsp;&emsp;"token" : "",                -> REQUERIDO        
                <br>
            }
            </code>
        </div>
    </div>
</body>
</html>