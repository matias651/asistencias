<?php
$dsn = 'mysql:host=localhost;dbname="dbasist";charset=utf8';
$username = 'root';
$password = '';


$sede = $_POST['sede'];
$dia = $_POST['dia'];
$hora = $_POST['hora'];
$profesor = $_POST['profesor'];

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO horarios (horario_sede, horario_dia, horario_hora, horario_profesor, fechaYhora_creacion, fechaYhora_actualizacion) VALUES (:sede, :dia, :hora, :profesor, NOW(), NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sede', $sede);
    $stmt->bindParam(':dia', $dia);
    $stmt->bindParam(':hora', $hora);
    $stmt->bindParam(':profesor', $profesor);

    if ($stmt->execute()) {
        echo "Horario asignado exitosamente";
    } else {
        echo "Error: no se pudo asignar el horario";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
