<?php
// Verificar si la sesión no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();  
}

// Inclusión del archivo de configuración
require_once "../../config.php"; // Asegúrate de que config.php contiene la configuración correcta de PDO

try {
    // Establecer conexión PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener la sede seleccionada
    $sede = isset($_POST['sede']) ? $_POST['sede'] : '1'; // El valor predeterminado es la primera sede

    // Consulta para obtener todas las sedes ordenadas por nombre
    $sql_sedes = "SELECT id_sede, sede_nombre FROM sedes ORDER BY sede_nombre  ASC";
    $stmt_sedes = $conn->prepare($sql_sedes);
    $stmt_sedes->execute();
    $sedes = $stmt_sedes->fetchAll(PDO::FETCH_ASSOC);

    // Consulta a la base de datos para obtener los eventos de la sede seleccionada
    $sql = "SELECT id_horario, horario_profesor, horario_asignatura, horario_hora, fechaYhora_actualizacion, fechaYhora_creacion, horario_sede FROM horarios WHERE horario_sede = :sede";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':sede', $sede);
    $stmt->execute();

    $events = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $events[] = array(
            'id' => $row['id_horario'],
            'title' => 'Asignatura: ' . $row['horario_asignatura'] . ' - Profesor: ' . $row['horario_profesor'],
            'start' => $row['fechaYhora_creacion'],
            'end' => $row['fechaYhora_actualizacion'],
            'status' => 'disponible', // Puedes ajustar esto según tu lógica
            'sede' => $row['horario_sede']
        );
    }

    $events_json = json_encode($events);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario con FullCalendar</title>
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <!-- Bootstrap CSS (para los modales) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            height: 100%;
        }
        #calendar {
            max-width: 100%;
        }
        .left-panel {
            padding: 20px;
            background-color: #f8f9fa;
            height: 100vh;
            overflow-y: auto;
        }
        .right-panel {
            padding: 20px;
            position: relative;
        }
        .calendar-container {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>

<main id="main" class="main container-fluid"> 
    <div class="row">
        <div class="col-lg-8 left-panel"> 
            <!-- Contenido adicional de la izquierda -->
            <h2>Contenido de la Izquierda</h2>
            <p>Este es el contenido que estará en la parte izquierda de la pantalla.</p>

            <div class="mb-4">
                <label for="sede">Seleccionar Sede:</label>
                <select id="sede" class="input w-full border mt-2 flex-1">
                    <?php foreach ($sedes as $sede_option): ?>
                        <option value="<?php echo $sede_option['id']; ?>" <?php echo $sede_option['id'] == $sede ? 'selected' : ''; ?>><?php echo $sede_option['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <table class="table table-report -mt-2 w-full">
                <thead>
                    <tr>
                        <th class="whitespace-no-wrap">NOMBRE</th>  
                        <th class="whitespace-no-wrap">PROFESOR</th>  
                        <th class="whitespace-no-wrap">HORARIO</th>                                 
                        <th class="text-center whitespace-no-wrap">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="intro-x">
                        <td class="w-40">
                            <div class="flex items-center">                                          
                                <div class="ml-4">Matematica 1</div>
                            </div>
                        </td> 
                        <td class="w-40">
                            <div class="flex items-center">                                          
                                <div class="ml-4">Profesor A</div>
                            </div>
                        </td>  
                        <td class="w-40">
                            <div class="flex items-center">                                          
                                <div class="ml-4">Lunes 10-12</div>
                            </div>
                        </td>                                                                    
                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                                <a class="flex items-center mr-3" href="javascript:;"> <i data-feather="check-square" class="w-4 h-4 mr-1"></i> Edit </a>
                                <a class="flex items-center text-theme-6" href="javascript:;" data-toggle="modal" data-target="#delete-confirmation-modal"> <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Delete </a>
                            </div>
                        </td>
                    </tr>
                    <tr class="intro-x">
                        <td class="w-40">
                            <div class="flex items-center">                                          
                                <div class="ml-4">Economia 1</div>
                            </div>
                        </td>
                        <td class="w-40">
                            <div class="flex items-center">                                          
                                <div class="ml-4">Profesor B</div>
                            </div>
                        </td>  
                        <td class="w-40">
                            <div class="flex items-center">                                          
                                <div class="ml-4">Martes 14-16</div>
                            </div>
                        </td>                                                                    
                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                                <a class="flex items-center mr-3" href="javascript:;"> <i data-feather="check-square" class="w-4 h-4 mr-1"></i> Edit </a>
                                <a class="flex items-center text-theme-6" href="javascript:;" data-toggle="modal" data-target="#delete-confirmation-modal"> <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Delete </a>
                            </div>
                        </td>
                    </tr>                    
                </tbody>
            </table>

        </div>
        <div class="col-lg-4 right-panel"> 
            <div class="calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</main>

<!-- Modal para editar/eliminar turnos -->
<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEventModalLabel">Editar Turno</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editEventForm">
                    <input type="hidden" id="editEventId" name="id">
                    <div class="form-group">
                        <label for="editTitle">Título</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="editStart">Fecha y Hora de Inicio</label>
                        <input type="datetime-local" class="form-control" id="editStart" name="start" required>
                    </div>
                    <div class="form-group">
                        <label for="editEnd">Fecha y Hora de Finalización</label>
                        <input type="datetime-local" class="form-control" id="editEnd" name="end">
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Estado</label>
                        <select class="form-control" id="editStatus" name="status" required>
                            <option value="disponible">Disponible</option>
                            <option value="reservado">Reservado</option>
                            <option value="realizado">Realizado</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveEventBtn">Guardar</button>
                <button type="button" class="btn btn-danger" id="deleteEventBtn">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/es.js"></script>
<!-- jQuery y Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function renderCalendar(events) {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                dateClick: function(info) {
                    calendar.changeView('timeGridDay', info.dateStr);
                },
                eventClick: function(info) {
                    var event = info.event;
                    $('#editEventModal').modal('show');
                    $('#editEventModalLabel').text('Editar Turno');
                    $('#editTitle').val(event.title);
                    $('#editStart').val(event.start.toISOString().slice(0, 16));
                    $('#editEnd').val(event.end ? event.end.toISOString().slice(0, 16) : '');
                    $('#editStatus').val(event.extendedProps.status);
                    $('#editEventId').val(event.id);
                },
                events: events
            });
            calendar.render();
        }

        var events = <?php echo $events_json; ?>;

        renderCalendar(events);

        $('#editEventModal').on('hidden.bs.modal', function () {
            $('#editEventForm')[0].reset();
            $('#editEventId').val('');
        });

        $('#deleteEventBtn').click(function() {
            if (confirm('¿Estás seguro de que deseas eliminar este turno?')) {
                $('#editAction').val('delete');
                $('#editEventForm').submit();
            }
        });

        $('#saveEventBtn').click(function() {
            $('#editEventForm').submit();
        });

        $('#addEventBtn').click(function() {
            Swal.fire({
                title: 'Agregar Turno',
                html: `
                    <form id="addEventForm">
                        <div class="form-group">
                            <label for="addTitle">Título</label>
                            <input type="text" class="form-control" id="addTitle" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="addStart">Fecha y Hora de Inicio</label>
                            <input type="datetime-local" class="form-control" id="addStart" name="start" required>
                        </div>
                        <div class="form-group">
                            <label for="addEnd">Fecha y Hora de Finalización</label>
                            <input type="datetime-local" class="form-control" id="addEnd" name="end">
                        </div>
                        <div class="form-group">
                            <label for="addStatus">Estado</label>
                            <select class="form-control" id="addStatus" name="status" required>
                                <option value="disponible">Disponible</option>
                                <option value="reservado">Reservado</option>
                                <option value="realizado">Realizado</option>
                            </select>
                        </div>
                        <input type="hidden" id="addAction" name="action" value="save">
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const title = document.getElementById('addTitle').value;
                    const start = document.getElementById('addStart').value;
                    const end = document.getElementById('addEnd').value;
                    const status = document.getElementById('addStatus').value;

                    if (!title || !start || !status) {
                        Swal.showValidationMessage(`Por favor completa todos los campos requeridos`);
                        return false;
                    }

                    return {
                        title: title,
                        start: start,
                        end: end,
                        status: status
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = result.value;
                    $.post('', {
                        action: 'save',
                        title: data.title,
                        start: data.start,
                        end: data.end,
                        status: data.status
                    }, function() {
                        location.reload();
                    });
                }
            });
        });

        $('#sede').change(function() {
            var sede = $(this).val();
            $.post('', { sede: sede }, function(data) {
                var events = JSON.parse(data);
                $('#calendar').html('');
                renderCalendar(events);
            });
        });
    });
</script>

</body>
</html>
