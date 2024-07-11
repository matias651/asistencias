<?php
// Archivo guardar_horario.php

session_start(); // Asegúrate de iniciar la sesión si no está iniciada ya
require_once dirname(__DIR__) . "/config.php"; // Incluye el archivo de configuración de tu base de datos

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recoge los datos del formulario
    $sede = $_POST['sede'];
    $dia = $_POST['dia'];
    $hora = $_POST['hora'];
    $asignatura = $_POST['asignatura'];
    $profesor = $_POST['profesor'];

    // Inserta el nuevo horario en la tabla horarios
    $stmt = $pdo->prepare("
        INSERT INTO horarios (horario_sede, horario_dia, horario_hora, horario_asignatura, horario_profesor)
        VALUES (:sede, :dia, :hora, :asignatura, :profesor)
    ");
    $stmt->bindParam(':sede', $sede, PDO::PARAM_INT);
    $stmt->bindParam(':dia', $dia, PDO::PARAM_STR);
    $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
    $stmt->bindParam(':asignatura', $asignatura, PDO::PARAM_INT);
    $stmt->bindParam(':profesor', $profesor, PDO::PARAM_INT);
    $stmt->execute();

    // Redirecciona o muestra un mensaje de éxito
    header('Location: calendar4.php'); // Cambia esto por la página donde quieres redirigir después de guardar
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

