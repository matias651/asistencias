<?php

if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();
}

// Inclusión del archivo de configuración
require_once __DIR__ . "/../../config.php";


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sede = $_POST['sede'];
$dia = $_POST['dia'];
$hora = $_POST['hora'];
$profesor = $_POST['profesor'];

$sql = "INSERT INTO horarios (horario_sede, horario_dia, horario_hora, horario_profesor, fechaYhora_creacion, fechaYhora_actualizacion) VALUES (?, ?, ?, ?, NOW(), NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param('issi', $sede, $dia, $hora, $profesor);

if ($stmt->execute()) {
    echo "Horario asignado exitosamente";
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
