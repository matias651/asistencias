<?php
require_once __DIR__ . "/../../config.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$sede = $data['sede'];
$dia = $data['dia'];
$hora = $data['hora'];

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT id_profesor, profesor_nombre, profesor_apellido 
        FROM profesores 
        WHERE profesor_sede = :sede 
          AND id_profesor NOT IN (
              SELECT horario_profesor 
              FROM horarios 
              WHERE horario_dia = :dia 
                AND horario_hora = :hora
          )
    ");
    $stmt->bindParam(':sede', $sede, PDO::PARAM_INT);
    $stmt->bindParam(':dia', $dia, PDO::PARAM_STR);
    $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
    $stmt->execute();
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($profesores);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
