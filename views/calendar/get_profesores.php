<?php
// Archivo get_profesores.php

// Verificar si la sesión no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();
}

// Inclusión del archivo de configuración
require_once __DIR__ . "/../../config.php";

$sede_id = $_GET['sede_id'];
$dia = $_GET['dia'];
$hora = $_GET['hora'];

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los profesores de la sede especificada que no están ocupados en el día y hora seleccionados
    $stmt = $pdo->prepare('
        SELECT id_profesor, profesor_nombre 
        FROM profesores 
        WHERE profesor_sede = :sede_id 
        AND id_profesor NOT IN (
            SELECT horario_profesor 
            FROM horarios 
            WHERE horario_dia = :dia AND horario_hora = :hora
        )
    ');

    $stmt->execute(['sede_id' => $sede_id, 'dia' => $dia, 'hora' => $hora]);
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($profesores as $profesor) {
        echo '<option value="' . $profesor['id_profesor'] . '">' . $profesor['profesor_nombre'] . '</option>';
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
