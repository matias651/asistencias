<?php
// Verificar si la sesión no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();
}

// Inclusión del archivo de configuración
require_once __DIR__ . "/../../config.php"; // Utilizando una ruta absoluta

// Consulta SQL para obtener las sedes
$sql = "SELECT id_sede, sede_nombre FROM sedes";
$stmt = $pdo->query($sql);

$sedes = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($sedes); // Devolver las sedes como JSON
?>
