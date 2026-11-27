<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "bibliodesk_web_movil";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión con la base de datos: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");
?>