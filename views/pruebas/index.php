<?php
// Archivo: index.php
require '../../config.php';

// Obtener horas disponibles según el día y la sede seleccionados
if (isset($_POST['dia']) && isset($_POST['sede'])) {
    $dia = $_POST['dia'];
    $sede_id = $_POST['sede'];

    $sql = "SELECT h.id_hora, h.hora
            FROM horas h
            WHERE h.id_hora NOT IN (
                SELECT horario_hora
                FROM horarios
                WHERE horario_dia = :dia
                  AND horario_sede = :sede
            )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['dia' => $dia, 'sede' => $sede_id]);
    $horas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($horas);
    exit();
}

// Obtener sedes
$sedes = [];
$sql = "SELECT id_sede, sede_nombre FROM sedes"; // Asegúrate de tener la tabla de sedes y los campos correctos
$stmt = $pdo->prepare($sql);
$stmt->execute();
$sedes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener profesores disponibles (en la misma página usando AJAX)
if (isset($_POST['dia']) && isset($_POST['hora']) && isset($_POST['sede'])) {
    $dia = $_POST['dia'];
    $hora_id = $_POST['hora'];
    $sede_id = $_POST['sede'];

    $sql = "SELECT p.id_profesor, p.profesor_nombre, p.profesor_apellido
            FROM profesores p
            WHERE p.profesor_sede = :sede
              AND p.id_profesor NOT IN (
                  SELECT h.horario_profesor
                  FROM horarios h
                  WHERE h.horario_dia = :dia 
                    AND h.horario_hora = :hora
              )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['sede' => $sede_id, 'dia' => $dia, 'hora' => $hora_id]);
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

     var_dump($profesores);
    echo json_encode($profesores);
    exit();
}


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modal de Horarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#horariosModal">
            Seleccionar Día, Hora y Sede
        </button>

        <div class="modal fade" id="horariosModal" tabindex="-1" aria-labelledby="horariosModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="horariosModalLabel">Seleccionar Día, Hora, Sede y Profesor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="horariosForm">
                            <div class="form-group">
                                <label for="dia">Día</label>
                                <select class="form-control" id="dia" name="dia">
                                    <option value="Lunes">Lunes</option>
                                    <option value="Martes">Martes</option>
                                    <option value="Miércoles">Miércoles</option>
                                    <option value="Jueves">Jueves</option>
                                    <option value="Viernes">Viernes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="hora">Hora</label>
                                <select class="form-control" id="hora" name="hora">
                                    <!-- Opciones de horas se llenarán dinámicamente -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sede">Sede</label>
                                <select class="form-control" id="sede" name="sede">
                                    <?php foreach ($sedes as $sede): ?>
                                        <option value="<?= $sede['id_sede'] ?>"><?= $sede['sede_nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="profesor">Profesor Disponible</label>
                                <select class="form-control" id="profesor" name="profesor">
                                    <!-- Opciones de profesores se llenarán dinámicamente -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_hora">ID Hora Seleccionada</label>
                                <input class="form-control" id="id_hora" name="id_hora" readonly>
                            </div>
                            <div class="form-group">
                                <label for="diaS">Día Seleccionado</label>
                                <input class="form-control" id="diaS" name="diaS" readonly>
                            </div>
                            <div class="form-group">
                                <label for="id_profesor">ID Profesor Seleccionado</label>
                                <input class="form-control" id="id_profesor" name="id_profesor" readonly>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="guardarBtn">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function(){
    // Obtener horas disponibles cuando se selecciona día y sede
    $('#dia, #sede').change(function(){
        var dia = $('#dia').val();
        var sede = $('#sede').val();
        if(dia && sede){
            console.log('Dia seleccionado:', dia);
            console.log('Sede seleccionada (ID):', sede);
            $.ajax({
                url: 'index.php',
                type: 'POST',
                data: { dia: dia, sede: sede },
                success: function(response){
                    var horas = JSON.parse(response);
                    console.log('Horas disponibles:', horas); // Verifica la respuesta del servidor
                    
                    var options = '';
                    if(horas.length > 0){
                        horas.forEach(function(hora){
                            options += '<option value="' + hora.id_hora + '">' + hora.hora + '</option>';
                        });
                    } else {
                        options = '<option value="">No hay horas disponibles</option>';
                    }
                    $('#hora').html(options);
                },
                error: function(xhr, status, error){
                    console.log('Error en la solicitud AJAX:', error); // Manejo de errores si es necesario
                }
            });
        }
    });

    // Actualizar la lista de profesores cuando se elige una hora
    $('#hora').change(function(){
        var dia = $('#dia').val();
        var hora = $('#hora').val();
        var sede = $('#sede').val();
        if(dia && hora && sede){
            console.log('Dia seleccionado:', dia);
            console.log('Hora seleccionada (ID):', hora);
            console.log('Sede seleccionada (ID):', sede);
            $.ajax({
                url: 'index.php',
                type: 'POST',
                data: { dia: dia, hora: hora, sede: sede },
                success: function(response){
                    console.log('Respuesta del servidor:', response); // Verifica la respuesta del servidor
                    var profesores = JSON.parse(response);
                    console.log('Profesores disponibles:', profesores); // Verifica el parseo de la respuesta
                    
                    var options = '';
                    if(profesores.length > 0){
                        profesores.forEach(function(profesor){
                            options += '<option value="' + profesor.id_profesor + '">' + profesor.profesor_nombre + ' ' + profesor.profesor_apellido + '</option>';
                        });
                    } else {
                        options = '<option value="">No hay profesores disponibles</option>';
                    }
                    $('#profesor').html(options);
                },
                error: function(xhr, status, error){
                    console.log('Error en la solicitud AJAX:', error); // Manejo de errores si es necesario
                }
            });
            $('#id_hora').val(hora); // Mostrar el id de la hora seleccionada
            $('#diaS').val(dia); // Mostrar el día seleccionado
        }
    });
});

    </script>
</body>
</html>
