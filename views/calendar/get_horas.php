<?php
// Archivo get_horas.php

// Verificar si la sesión no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();
}

// Inclusión del archivo de configuración
require_once __DIR__ . "/../../config.php";

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT id_hora, hora FROM horas');
    $horas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($horas as $hora) {
        echo '<option value="' . $hora['id_hora'] . '">' . $hora['hora'] . '</option>';
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>