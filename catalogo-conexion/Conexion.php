<?php
$host = "127.0.0.1";     // o localhost, según DBeaver
$usuario = "root";       // el mismo usuario que uses en DBeaver
$contrasena = "Comes.1016";        // si usás clave, ponela acá
$base_datos = "catalogodb";
$puerto = 3306;          // cambiá si DBeaver usa otro puerto

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos, $puerto);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>

