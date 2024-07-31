<?php
// Archivo: index.php
require '../../config.php'; // Incluye el archivo de configuración para conectar a la base de datos

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

// Obtener profesores disponibles según el día, hora y sede seleccionados
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
                    AND h.horario_sede = :sede
              )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['sede' => $sede_id, 'dia' => $dia, 'hora' => $hora_id]);
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($profesores);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modal de Horarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Incluye el CSS de Bootstrap -->
</head>
<body>
    <div class="container mt-5">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#horariosModal">Seleccionar Día, Hora y Sede</button>

        <div class="modal fade" id="horariosModal" tabindex="-1" aria-labelledby="horariosModalLabel" aria-hidden="true"> <!-- Modal -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="horariosModalLabel">Seleccionar Día, Hora, Sede y Profesor</h5> <!-- Título del modal -->
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <!-- Botón para cerrar el modal -->
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="horariosForm">
                            <div class="form-group">
                                <label for="dia">Día</label>
                                <select class="form-control" id="dia" name="dia"> <!-- Dropdown para seleccionar el día -->
                                    <option value="Lunes">Lunes</option>
                                    <option value="Martes">Martes</option>
                                    <option value="Miércoles">Miércoles</option>
                                    <option value="Jueves">Jueves</option>
                                    <option value="Viernes">Viernes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="hora">Hora</label>
                                <select class="form-control" id="hora" name="hora"> <!-- Dropdown para seleccionar la hora (se llenará dinámicamente) -->
                                    <!-- Opciones de horas se llenarán dinámicamente -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sede">Sede</label>
                                <select class="form-control" id="sede" name="sede"> <!-- Dropdown para seleccionar la sede -->
                                    <?php foreach ($sedes as $sede): ?>
                                        <option value="<?= $sede['id_sede'] ?>"><?= $sede['sede_nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="profesor">Profesor Disponible</label>
                                <select class="form-control" id="profesor" name="profesor"> <!-- Dropdown para seleccionar el profesor (se llenará dinámicamente) -->
                                    <!-- Opciones de profesores se llenarán dinámicamente -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_hora">ID Hora Seleccionada</label>
                                <input class="form-control" id="id_hora" name="id_hora" readonly> <!-- Campo para mostrar el ID de la hora seleccionada -->
                            </div>
                            <div class="form-group">
                                <label for="diaS">Día Seleccionado</label>
                                <input class="form-control" id="diaS" name="diaS" readonly> <!-- Campo para mostrar el día seleccionado -->
                            </div>
                            <div class="form-group">
                                <label for="id_profesor">ID Profesor Seleccionado</label>
                                <input class="form-control" id="id_profesor" name="id_profesor" readonly> <!-- Campo para mostrar el ID del profesor seleccionado -->
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button> <!-- Botón para cerrar el modal -->
                        <button type="button" class="btn btn-primary" id="guardarBtn">Guardar</button> <!-- Botón para guardar (aún no tiene funcionalidad) -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- Incluye jQuery -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script> <!-- Incluye el JS de Bootstrap -->
    <script>
    $(document).ready(function(){
        // Obtener horas disponibles cuando se selecciona día y sede
        $('#dia, #sede').change(function(){
            var dia = $('#dia').val(); // Obtiene el valor del día seleccionado
            var sede = $('#sede').val(); // Obtiene el valor de la sede seleccionada
            if(dia && sede){
                $.ajax({
                    type: 'POST',
                    url: 'index.php',
                    data: { dia: dia, sede: sede },
                    dataType: 'json',
                    success: function(response){
                        var options = '';
                        if(response.length > 0){
                            response.forEach(function(hora){
                                options += '<option value="' + hora.id_hora + '">' + hora.hora + '</option>'; // Añade las opciones de horas al dropdown
                            });
                        } else {
                            options = '<option value="">No hay horas disponibles</option>'; // Mensaje si no hay horas disponibles
                        }
                        $('#hora').html(options); // Actualiza el dropdown de horas con las nuevas opciones
                    }
                });
            }
        });

        // Actualizar la lista de profesores cuando se elige una hora
        $('#hora').change(function(){
            var dia = $('#dia').val(); // Obtiene el valor del día seleccionado
            var hora = $('#hora').val(); // Obtiene el valor de la hora seleccionada
            var sede = $('#sede').val(); // Obtiene el valor de la sede seleccionada
            if(dia && hora && sede){
                $.ajax({
                    type: 'POST',
                    url: 'index.php',
                    data: { dia: dia, hora: hora, sede: sede },
                    dataType: 'json',
                    success: function(response){
                        var options = '';
                        if(response.length > 0){
                            response.forEach(function(profesor){
                                options += '<option value="' + profesor.id_profesor + '">' + profesor.profesor_nombre + ' ' + profesor.profesor_apellido + '</option>'; // Añade las opciones de profesores al dropdown
                            });
                        } else {
                            options = '<option value="">No hay profesores disponibles</option>'; // Mensaje si no hay profesores disponibles
                        }
                        $('#profesor').html(options); // Actualiza el dropdown de profesores con las nuevas opciones
                    }
                });
            }
        });

        // Mostrar el ID de la hora seleccionada y el día seleccionado al guardar (ejemplo)
        $('#guardarBtn').click(function(){
            var idHora = $('#hora').val(); // Obtiene el ID de la hora seleccionada
            var diaSeleccionado = $('#dia').val(); // Obtiene el día seleccionado
            var idProfesor = $('#profesor').val(); // Obtiene el ID del profesor seleccionado

            $('#id_hora').val(idHora); // Muestra el ID de la hora seleccionada en el campo correspondiente
            $('#diaS').val(diaSeleccionado); // Muestra el día seleccionado en el campo correspondiente
            $('#id_profesor').val(idProfesor); // Muestra el ID del profesor seleccionado en el campo correspondiente

            // Aquí puedes hacer más acciones, como enviar los datos al servidor para guardarlos en la base de datos
            // Por ejemplo, puedes hacer otra solicitud AJAX para guardar los datos en el servidor

            // Ejemplo de envío de datos al servidor (no implementado en este código)
            /*
            $.ajax({
                type: 'POST',
                url: 'guardar_datos.php',
                data: { id_hora: idHora, dia: diaSeleccionado, id_profesor: idProfesor },
                success: function(response){
                    // Manejar la respuesta del servidor, por ejemplo, mostrar un mensaje de éxito
                    alert('Datos guardados correctamente');
                }
            });
            */
        });
    });
    </script>
</body>
</html>
