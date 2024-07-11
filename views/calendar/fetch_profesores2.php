<?php
// fetch_profesores.php

// Incluir archivo de configuración y conexión a la base de datos
require_once __DIR__ . "/../../config.php";

// Obtener datos enviados por POST
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['sede'], $data['dia'], $data['asignatura'])) {
    $sede = $data['sede'];
    $dia = $data['dia'];
    $asignatura = $data['asignatura'];

    try {
        // Obtener los profesores disponibles para el día y la asignatura en la sede seleccionada
        $stmt = $pdo->prepare("
            SELECT p.id_profesor, p.profesor_nombre, p.profesor_apellido
            FROM profesores p
            LEFT JOIN horarios h ON p.id_profesor = h.horario_profesor
            WHERE p.profesor_sede = :sede
            AND p.id_profesor NOT IN (
                SELECT h.horario_profesor
                FROM horarios h
                WHERE h.horario_dia = :dia
                AND h.horario_sede = :sede
            )
            AND p.profesor_plan = :asignatura
        ");
        $stmt->bindParam(':sede', $sede, PDO::PARAM_INT);
        $stmt->bindParam(':dia', $dia, PDO::PARAM_STR);
        $stmt->bindParam(':asignatura', $asignatura, PDO::PARAM_INT);
        $stmt->execute();
        $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devolver los resultados como JSON
        header('Content-Type: application/json');
        echo json_encode($profesores);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error fetching data: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}
?>
