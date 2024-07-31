<?php
// get_horarios.php

// Incluir el archivo de configuración y establecer la conexión PDO ($pdo)
require_once __DIR__ . "/../../config.php";

// Verificar si se recibieron los datos necesarios por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sede_id']) && isset($_POST['dia'])) {
    $sede_id = $_POST['sede_id'];
    $dia = $_POST['dia'];

    try {
        // Consulta para obtener las horas disponibles para el día y sede especificados
        $stmt = $pdo->prepare("
            SELECT h.id_hora, h.hora
            FROM horas h
            LEFT JOIN horarios ho ON h.id_hora = ho.horario_hora AND ho.horario_dia = :dia AND ho.horario_sede = :sede_id
            WHERE ho.horario_hora IS NULL
        ");
        $stmt->execute(['dia' => $dia, 'sede_id' => $sede_id]);
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Generar opciones para cada hora disponible
        foreach ($horarios as $horario) {
            echo '<option value="' . $horario['id_hora'] . '">' . $horario['hora'] . '</option>';
        }
    } catch (PDOException $e) {
        echo 'Error al obtener horarios disponibles: ' . $e->getMessage();
    }
} else {
    echo 'No se recibieron datos válidos para la consulta de horarios.';
}
?>
