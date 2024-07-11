<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../../config.php';

    $dia = $_POST['dia'];
    $hora_id = $_POST['hora'];

    // Consulta SQL
    $sql = "SELECT p.id_profesor, p.profesor_nombre, p.profesor_apellido
            FROM profesores p
            WHERE p.id_profesor NOT IN (
                SELECT h.horario_profesor
                FROM horarios h
                WHERE h.horario_dia = :dia
                AND h.horario_hora = :hora_id
            )";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':dia', $dia, PDO::PARAM_STR);
    $stmt->bindParam(':hora_id', $hora_id, PDO::PARAM_INT);
    $stmt->execute();
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($profesores) > 0) {
        echo "<ul>";
        foreach ($profesores as $profesor) {
            echo "<li>" . htmlspecialchars($profesor['profesor_nombre']) . " " . htmlspecialchars($profesor['profesor_apellido']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "No hay profesores disponibles.";
    }
}
?>
