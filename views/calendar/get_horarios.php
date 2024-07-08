<?php
require_once dirname(__DIR__) . "../../config.php";

if (isset($_POST['sede_id'])) {
    $sede_id = $_POST['sede_id'];

    // Obtener los horarios de la base de datos
    $query_horarios = "
        SELECT 
            horario_hora, 
            horario_dia 
        FROM 
            horarios 
        WHERE 
            horario_sede = $sede_id
        ORDER BY 
            horario_hora ASC";
    $result_horarios = mysqli_query($conn, $query_horarios);
    $horarios = mysqli_fetch_all($result_horarios, MYSQLI_ASSOC);
    
    // Crear un array para almacenar los horarios por día y hora
    $agenda = [];
    foreach ($horarios as $horario) {
        $agenda[$horario['horario_hora']][$horario['horario_dia']] = true;
    }

    echo '<table class="table-auto w-full">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Hora</th>';
    echo '<th>Lunes</th>';
    echo '<th>Martes</th>';
    echo '<th>Miércoles</th>';
    echo '<th>Jueves</th>';
    echo '<th>Viernes</th>';
    echo '<th>Sábado</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    $start_time = strtotime('08:00');
    $end_time = strtotime('22:00');
    $interval = 60 * 60; // 1 hora

    for ($time = $start_time; $time <= $end_time; $time += $interval) {
        $hour = date('H:i', $time);
        echo '<tr>';
        echo '<td>' . $hour . '</td>';
        for ($day = 1; $day <= 6; $day++) {
            echo '<td>' . (isset($agenda[$hour][$day]) ? 'Ocupado' : 'Libre') . '</td>';
        }
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}
?>
