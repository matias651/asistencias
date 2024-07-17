<?php
// Iniciar la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();
}

// Inclusión del archivo de configuración
require_once __DIR__ . "/../../config.php";

try {
    // Preparar la consulta SQL utilizando PDO
    $query = "SELECT id_programa, programa_nombre FROM programas";
    $stmt = $pdo->query($query);

    // Obtener los resultados como un array asociativo
    $programas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los resultados como JSON
    header('Content-Type: application/json');
    echo json_encode($programas);
} catch (PDOException $e) {
    // Manejar errores de conexión o consulta
    http_response_code(500);
    echo json_encode(array("message" => "Error al obtener los programas: " . $e->getMessage()));
}
?>
