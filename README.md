# PersonalDataApi
Api de la aplicación

# Datos de conección PDO-MYSQL
File=/class/connection/config.json
```JSON
{
    "connection":{
        "host":"", --> URL or IP del servidor
        "user":"", -> Usuario de conección
        "password":"", -> Contraseña de coneccion
        "database":"", -> Nombre de la Base de Datos
        "port":"", -> Puerto
        "charset":"utf8mb4"
    }
}
```
# Extensiones para pho.ini
extension=openssl
extension=pdo_mysql
extension=mbstring

# Instalar MBSTRING para la version de php 7.0.33
sudo apt-get install php7.0-mbstring

## Habilitar MBSTRING
sudo phpenmod mbstring

## Reiniciamos el servicio
sudo systemctl restart apache2

