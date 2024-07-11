<?php
require '../../config.php';


$sql = "SELECT id_horas, hora FROM horas";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$horas = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($horas as $hora) {
    echo '<option value="' . $hora['id_horas'] . '">' . $hora['hora'] . '</option>';
}
?>
