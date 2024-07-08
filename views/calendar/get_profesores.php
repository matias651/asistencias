<?php
require_once "../../config.php"; // Asegúrate de que config.php contiene la configuración correcta de PDO

$sede = isset($_POST['sede']) ? $_POST['sede'] : 0;

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener los profesores de la sede seleccionada y que no tengan conflictos de horario
    $sql = "
        SELECT p.id_profesor, CONCAT(p.profesor_nombre, ' ', p.profesor_apellido) AS nombre_completo
        FROM profesores p
        WHERE p.profesor_sede = :sede
        AND p.id_profesor NOT IN (
            SELECT h.horario_profesor
            FROM horarios h
            WHERE h.horario_sede = :sede
            AND h.horario_hora = :hora
        )
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':sede', $sede);
    $stmt->execute();

    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['profesores' => $profesores]);

} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conn = null;
?>
