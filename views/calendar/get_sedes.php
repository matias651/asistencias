<?php


// Archivo principal (index.php)

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

    $stmt = $pdo->query('SELECT id_sede, sede_nombre FROM sedes');
    $sedes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($sedes as $sede) {
        echo '<option value="' . $sede['id_sede'] . '">' . $sede['sede_nombre'] . '</option>';
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
