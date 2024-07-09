<?php
// Archivo: config.php

// Variables de configuración de la base de datos
$host = "localhost";
$dbname = "dbasist";
$username = "root";
$password = "";

// Intentar conectar utilizando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Failed to connect to MySQL: " . $e->getMessage();
    exit(); // Salir del script en caso de error de conexión
}

// Configurar la zona horaria
date_default_timezone_set("America/Argentina/Cordoba");
$fechahora = date('YmdHis');

// Si la aplicación se ejecuta en un servidor remoto
if ($_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['HTTP_HOST'] !== '127.0.0.1') {
    $url = "http://" . $_SERVER['HTTP_HOST'] . "/asistencias";
} else {
    $url = "http://localhost/asistencias";
}

$base_path = __DIR__;

// Ruta base de las imágenes
define('IMAGE_BASE_PATH', $base_path . '/img/profile/');
define('IMAGE_BASE_URL', $url . '/img/profile/');
?>
