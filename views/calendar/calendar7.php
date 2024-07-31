<?php
// Archivo principal (index.php)

// Verificar si la sesión no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();
}

// Inclusión del archivo de configuración
require_once __DIR__ . "/../../config.php";

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener las sedes con sus nombres
    $stmt_sedes = $pdo->prepare("SELECT id_sede, sede_nombre FROM sedes");
    $stmt_sedes->execute();
    $sedes = $stmt_sedes->fetchAll(PDO::FETCH_ASSOC);

    // Obtener la sede seleccionada (por defecto la primera si no hay selección)
    $selected_sede_id = isset($_POST['sede']) ? $_POST['sede'] : (isset($sedes[0]['id_sede']) ? $sedes[0]['id_sede'] : null);

    // Obtener el nombre de la sede seleccionada
    $selected_sede_nombre = null;
    foreach ($sedes as $sede) {
        if ($sede['id_sede'] == $selected_sede_id) {
            $selected_sede_nombre = $sede['sede_nombre'];
            break;
        }
    }

    // Obtener los nombres de las horas desde la tabla horas
    $stmt_horas = $pdo->prepare("SELECT id_hora, hora FROM horas");
    $stmt_horas->execute();
    $horas_result = $stmt_horas->fetchAll(PDO::FETCH_ASSOC);
    $horas = [];
    foreach ($horas_result as $hora) {
        $horas[$hora['id_hora']] = $hora['hora'];
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Instituciones</title>
    <!-- Estilos CSS -->
    <link href="<?php echo $url . '/static resources/css/modal.css'; ?>" rel="stylesheet">
    <link href="<?php echo $url . '/static resources/css/app.css'; ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">
    <!-- Fin Estilos CSS -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.js"></script>
    <!-- Fin Scripts -->
</head>
<body class="app">
    <div class="flex">
        <!-- BEGIN: Side Menu -->
        <?php include $base_path . '/includes/sidebar.php'; ?>
        <!-- END: Side Menu -->
        <!-- BEGIN: Content -->
        <div class="content">
            <!-- BEGIN: Top Bar -->
            <div class="top-bar">
                <div class="-intro-x breadcrumb mr-auto hidden sm:flex">
                    <a href="" class="">Application</a>
                    <i data-feather="chevron-right" class="breadcrumb__icon"></i>
                    <a href="" class="breadcrumb--active">Dashboard</a>
                </div>
                <?php include $base_path . '/includes/account.php'; ?>
            </div>
            <!-- END: Top Bar -->

            <!-- Botón para abrir el modal -->
            <button id="openModal" class="button bg-theme-1 text-white mt-5 ml-5">Agregar Nuevo Horario</button>
            
            <!-- Selector de sede y día -->
            <form id="filterForm" method="POST" action="">
                <div class="intro-y col-span-12 flex flex-wrap sm:flex-no-wrap items-center mt-2">
                    <label for="sede" class="mr-2">Seleccionar Sede:</label>
                    <select name="sede" id="sede" class="input w-full border mt-2 flex-1" onchange="this.form.submit()">
                        <?php foreach ($sedes as $sede): ?>
                            <option value="<?php echo $sede['id_sede']; ?>" <?php echo $selected_sede_id == $sede['id_sede'] ? 'selected' : ''; ?>>
                                <?php echo $sede['sede_nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="dia" class="ml-4 mr-2">Seleccionar Día:</label>
                    <select name="dia" id="dia" class="input w-full border mt-2 flex-1">
                        <?php $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']; ?>
                        <?php foreach ($dias as $dia): ?>
                            <option value="<?php echo $dia; ?>"><?php echo $dia; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <div class="container mt-5"></div>

            <!-- Agenda semanal -->
            <h2 class="intro-y text-lg font-medium mt-10">Agenda Semanal</h2>
            <div class="grid grid-cols-12 gap-6 mt-5">
                <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                    <table class="table table-report -mt-2">
                        <thead>
                            <tr>
                                <th class="text-center w-24">Hora</th>
                                <?php foreach ($dias as $dia): ?>
                                    <th class="text-center w-32"><?php echo $dia; ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($horas as $hora_id => $hora_nombre): ?>
                                <tr>
                                    <td class="text-center w-24"><?php echo htmlspecialchars($hora_nombre); ?></td>
                                    <?php foreach ($dias as $dia): ?>
                                        <td class="text-center w-32">
                                            <!-- Contenido de la celda dinámico aquí -->
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END: Content -->
    </div>
    
    <!-- Modal -->
    <div id="modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
            <div class="modal-content py-4 text-left px-6">
                <div class="flex justify-between items-center pb-3">
                    <p class="text-2xl font-bold">Horarios para <?php echo htmlspecialchars($selected_sede_nombre); ?> - <span id="selectedDay"></span></p>
                    <div class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                            <path d="M16.47 1.65a.996.996 0 1 0-1.41 1.41L9.77 7.3 4.12 1.65a.996.996 0 1 0-1.41 1.41l5.65 5.65-5.65 5.66a.996.996 0 1 0 1.41 1.41l5.65-5.65 5.65 5.65a.996.996 0 1 0 1.41-1.41z"/>
                        </svg>
                    </div>
                </div>

                <p class="text-xl"><?php echo htmlspecialchars($selected_sede_nombre); ?></p>
                
                <!-- Lista desplegable para seleccionar día -->
                <label for="modalDia" class="block text-gray-700 text-sm font-bold mb-2">Seleccionar Día:</label>
                <select name="modalDia" id="modalDia" class="input w-full border mt-2 flex-1">
                    <?php foreach ($dias as $dia): ?>
                        <option value="<?php echo $dia; ?>"><?php echo $dia; ?></option>
                    <?php endforeach; ?>
                </select>

                <!-- Lista desplegable para mostrar horarios -->
                <label for="modalHorarios" class="block text-gray-700 text-sm font-bold mb-2 mt-4">Seleccionar Horario:</label>
                <select name="modalHorarios" id="modalHorarios" class="input w-full border mt-2 flex-1">
                    <!-- Opciones de horarios se cargarán dinámicamente -->
                </select>

                <!-- Lista desplegable para mostrar profesores -->
                <label for="modalProfesores" class="block text-gray-700 text-sm font-bold mb-2 mt-4">Seleccionar Profesor:</label>
                <select name="modalProfesores" id="modalProfesores" class="input w-full border mt-2 flex-1">
                    <!-- Opciones de profesores se cargarán dinámicamente -->
                </select>

                <div class="flex justify-end pt-2">
                    <button class="modal-close px-4 bg-gray-500 p-3 rounded-lg text-white hover:bg-gray-400">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para abrir el modal
        $('#openModal').on('click', function() {
            $('#modal').removeClass('hidden');
            loadHorariosAndProfesores();
        });

        // Función para cerrar el modal
        $('.modal-overlay, .modal-close').on('click', function() {
            $('#modal').addClass('hidden');
        });

        // Función para cargar los horarios y profesores desde get_horarios.php y get_profesores.php
        function loadHorariosAndProfesores() {
            var sede_id = $('#sede').val();
            var dia = $('#modalDia').val() || $('#modalDia option:first').val();
            
            // Actualizar el texto del día seleccionado
            $('#selectedDay').text(dia);

            $.ajax({
                type: 'POST',
                url: 'get_horarios.php',
                data: { sede_id: sede_id, dia: dia },
                success: function(response) {
                    $('#modalHorarios').html(response);
                    var selectedHorario = $('#modalHorarios').val() || $('#modalHorarios option:first').val();
                    loadProfesores(selectedHorario);
                }
            });
        }

        // Función para cargar los profesores disponibles al cargar los horarios
        function loadProfesores(selectedHorario) {
            var sede_id = $('#sede').val();
            var dia = $('#modalDia').val();

            $.ajax({
                type: 'GET',
                url: 'get_profesores.php',
                data: { sede_id: sede_id, dia: dia, hora: selectedHorario },
                success: function(response) {
                    $('#modalProfesores').html(response);
                }
            });
        }

        // Función para cargar los horarios al cambiar el día en el modal
        $('#modalDia').on('change', function() {
            var selectedDay = $(this).val();
            $('#selectedDay').text(selectedDay);
            loadHorariosAndProfesores();
        });

        // Función para cargar los profesores disponibles al cambiar el horario en el modal
        $('#modalHorarios').on('change', function() {
            var selectedHorario = $(this).val();
            loadProfesores(selectedHorario);
        });

        // Función para mostrar los horarios seleccionados
        $('#modalHorarios').on('change', function() {
            var selectedHorario = $(this).val();
            $('#selectedHorario').text(selectedHorario);

            var sede_id = $('#sede').val();
            var dia = $('#modalDia').val();

            $.ajax({
                type: 'POST',
                url: 'get_horarios.php',
                data: { sede_id: sede_id, dia: dia, hora: selectedHorario },
                success: function(response) {
                    $('#modalContent').html(response);
                }
            });
        });
    </script>
</body>
</html>
