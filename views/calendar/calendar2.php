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
    $sql_sedes = "SELECT id_sede, sede_nombre FROM sedes ORDER BY sede_nombre ASC";
    $stmt_sedes = $conn->prepare($sql_sedes);
    $stmt_sedes->execute();
    $sedes = $stmt_sedes->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener todas las asignaturas ordenadas por nombre
    $sql_asignaturas = "SELECT id_asignatura, asignatura_nombre FROM asignaturas ORDER BY asignatura_nombre ASC";
    $stmt_asignaturas = $conn->prepare($sql_asignaturas);
    $stmt_asignaturas->execute();
    $asignaturas = $stmt_asignaturas->fetchAll(PDO::FETCH_ASSOC);

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
    <!-- Bootstrap CSS -->
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
            margin: 20px auto;
        }
    </style>
</head>
<body>

<div class="form-group">
                                    <label for="addSede">Sede</label>
                                    <select class="form-control" id="addSede" name="sede" required>
                                        <?php foreach ($sedes as $sede) { ?>
                                            <option value="<?php echo $sede['id_sede']; ?>"><?php echo $sede['sede_nombre']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

<div id="calendar"></div>

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
                initialView: 'timeGridWeek',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek'
                },
                hiddenDays: [0], // Ocultar domingos (0)
                slotMinTime: "08:00:00", // Horario mínimo de inicio
                slotMaxTime: "22:00:01", // Horario máximo de fin
                dateClick: function(info) {
                    Swal.fire({
                        title: 'Agregar Evento',
                        html: `
                            <form id="addEventForm">
                                
                                <div class="form-group">
                                    <label for="addDay">Día</label>
                                    <select class="form-control" id="addDay" name="day" required>
                                        <option value="Monday">Lunes</option>
                                        <option value="Tuesday">Martes</option>
                                        <option value="Wednesday">Miércoles</option>
                                        <option value="Thursday">Jueves</option>
                                        <option value="Friday">Viernes</option>
                                        <option value="Saturday">Sábado</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="addStart">Hora de Inicio</label>
                                    <select class="form-control" id="addStart" name="start" required>
                                        ${generateTimeOptions()}
                                    </select>
                                </div>
                             
                                <div class="form-group">
                                    <label for="addSede">Sede</label>
                                    <select class="form-control" id="addSede" name="sede" required>
                                        <?php foreach ($sedes as $sede) { ?>
                                            <option value="<?php echo $sede['id_sede']; ?>"><?php echo $sede['sede_nombre']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="addAsignatura">Asignatura</label>
                                    <select class="form-control" id="addAsignatura" name="asignatura" required>
                                        <?php foreach ($asignaturas as $asignatura) { ?>
                                            <option value="<?php echo $asignatura['id_asignatura']; ?>"><?php echo $asignatura['asignatura_nombre']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="addProfesor">Profesor</label>
                                    <select class="form-control" id="addProfesor" name="profesor" required>
                                        <!-- Aquí se llenarán los profesores con JavaScript -->
                                    </select>
                                </div>
                            </form>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        preConfirm: () => {
                            const title = document.getElementById('addTitle').value;
                            const day = document.getElementById('addDay').value;
                            const start = document.getElementById('addStart').value;
                            const status = document.getElementById('addStatus').value;
                            const sede = document.getElementById('addSede').value;
                            const asignatura = document.getElementById('addAsignatura').value;
                            const profesor = document.getElementById('addProfesor').value;

                            if (!title || !day || !start || !status || !sede || !asignatura || !profesor) {
                                Swal.showValidationMessage(`Por favor completa todos los campos requeridos`);
                                return false;
                            }

                            return {
                                title: title,
                                day: day,
                                start: start,
                                end: (parseInt(start) + 1) + ':00', // Una hora después de la hora de inicio
                                status: status,
                                sede: sede,
                                asignatura: asignatura,
                                profesor: profesor
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const data = result.value;
                            $.ajax({
                                url: 'save_event.php',
                                method: 'POST',
                                data: data,
                                success: function(response) {
                                    response = JSON.parse(response);
                                    if (response.success) {
                                        events.push({
                                            title: data.title,
                                            start: data.start,
                                            end: data.end,
                                            status: data.status,
                                            sede: data.sede,
                                            asignatura: data.asignatura,
                                            profesor: data.profesor
                                        });
                                        calendar.addEvent({
                                            title: data.title,
                                            start: data.start,
                                            end: data.end,
                                            status: data.status,
                                            sede: data.sede,
                                            asignatura: data.asignatura,
                                            profesor: data.profesor
                                        });
                                    } else {
                                        Swal.fire('Error', response.message, 'error');
                                    }
                                },
                                error: function() {
                                    Swal.fire('Error', 'Hubo un problema al guardar el evento', 'error');
                                }
                            });
                        }
                    });
                },
                events: events
            });
            calendar.render();
        }

        function generateTimeOptions() {
            let options = '';
            for (let hour = 8; hour <= 21; hour++) {
                let hourString = hour < 10 ? '0' + hour : hour;
                options += `<option value="${hourString}:00">${hourString}:00</option>`;
            }
            return options;
        }

        // Cargar profesores según la sede seleccionada
        function loadProfesores(sede) {
            $.ajax({
                url: 'get_profesores.php',
                method: 'POST',
                data: { sede: sede },
                success: function(response) {
                    response = JSON.parse(response);
                    var profesorSelect = document.getElementById('addProfesor');
                    profesorSelect.innerHTML = '';
                    response.profesores.forEach(function(profesor) {
                        profesorSelect.innerHTML += `<option value="${profesor.id_profesor}">${profesor.nombre_completo}</option>`;
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Hubo un problema al cargar los profesores', 'error');
                }
            });
        }

        // Escuchar cambios en la sede para cargar profesores
        $(document).on('change', '#addSede', function() {
            var sede = $(this).val();
            loadProfesores(sede);
        });

        var events = <?php echo $events_json; ?>;
        renderCalendar(events);
    });
</script>

</body>
</html>
