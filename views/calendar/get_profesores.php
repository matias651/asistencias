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


if (isset($_GET['sede_id'])) {
    $sede_id = $_GET['sede_id'];

    try {
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('SELECT id_profesor, profesor_nombre, profesor_apellido FROM profesores WHERE profesor_sede = :sede_id');
        $stmt->execute(['sede_id' => $sede_id]);
        $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($profesores as $profesor) {
            echo '<option value="' . $profesor['id_profesor'] . '">' . $profesor['profesor_nombre'] . ' ' . $profesor['profesor_apellido'] . '</option>';
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>
