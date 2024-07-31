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

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los profesores que no están ocupados en el día seleccionado en la sede seleccionada
    $stmt = $pdo->prepare('
        SELECT id_profesor, profesor_nombre 
        FROM profesores 
        WHERE id_profesor NOT IN (
            SELECT horario_profesor 
            FROM horarios 
            WHERE horario_dia = :dia AND horario_sede = :sede_id
        )
    ');

    $stmt->execute(['dia' => $dia, 'sede_id' => $sede_id]);
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($profesores as $profesor) {
        echo '<option value="' . $profesor['id_profesor'] . '">' . $profesor['profesor_nombre'] . '</option>';
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
