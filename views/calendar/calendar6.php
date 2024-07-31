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

    // Obtener las sedes
    $stmt_sedes = $pdo->prepare("SELECT id_sede, sede_nombre FROM sedes");
    $stmt_sedes->execute();
    $sedes = $stmt_sedes->fetchAll(PDO::FETCH_ASSOC);

    // Obtener la sede seleccionada (por defecto la primera si no hay selección)
    $selected_sede = isset($_POST['sede']) ? $_POST['sede'] : (isset($sedes[0]['id_sede']) ? $sedes[0]['id_sede'] : null);

    // Obtener los nombres de las horas desde la tabla horas
    $stmt_horas = $pdo->prepare("SELECT id_hora, hora FROM horas");
    $stmt_horas->execute();
    $horas_result = $stmt_horas->fetchAll(PDO::FETCH_ASSOC);
    $horas = [];
    foreach ($horas_result as $hora) {
        $horas[$hora['id_hora']] = $hora['hora'];
    }

    // Obtener los horarios, asignaturas y profesores según la sede seleccionada
    $horarios = [];
    if ($selected_sede) {
        // Consulta SQL para obtener los horarios con nombres de hora
        $stmt_horarios = $pdo->prepare("
            SELECT h.horario_hora, h.horario_dia, a.asignatura_nombre, p.profesor_nombre, p.profesor_apellido, dh.hora AS nombre_hora
            FROM horarios h
            JOIN profesores p ON h.horario_profesor = p.id_profesor
            JOIN asignaturas a ON h.horario_asignatura = a.id_asignatura
            JOIN horas dh ON h.horario_hora = dh.id_hora
            WHERE h.horario_sede = :sede
        ");
        $stmt_horarios->bindParam(':sede', $selected_sede, PDO::PARAM_INT);
        $stmt_horarios->execute();
        $horarios_result = $stmt_horarios->fetchAll(PDO::FETCH_ASSOC);

        // Organizar los horarios por hora y día
        foreach ($horarios_result as $horario) {
            $horarios[$horario['nombre_hora']][$horario['horario_dia']] = [
                'asignatura' => $horario['asignatura_nombre'],
                'profesor' => "{$horario['profesor_nombre']} {$horario['profesor_apellido']}"
            ];
        }
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

            <button id="openModal" class="button bg-theme-1 text-white mt-5 ml-5">Agregar Nuevo Horario</button>
            
            <!-- Selector de sede -->
            <form id="filterForm" method="POST" action="">
                <div class="intro-y col-span-12 flex flex-wrap sm:flex-no-wrap items-center mt-2">
                    <label for="sede" class="mr-2">Seleccionar Sede:</label>
                    <select name="sede" id="sede" class="input w-full border mt-2 flex-1" onchange="this.form.submit()">
                        <?php foreach ($sedes as $sede): ?>
                            <option value="<?php echo $sede['id_sede']; ?>" <?php echo $selected_sede == $sede['id_sede'] ? 'selected' : ''; ?>>
                                <?php echo $sede['sede_nombre']; ?>
                            </option>
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
                                <?php $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']; ?>
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
                                            <?php if (isset($horarios[$hora_nombre][$dia])): ?>
                                                Asignatura: <?php echo htmlspecialchars($horarios[$hora_nombre][$dia]['asignatura']); ?><br>
                                                Profesor: <?php echo htmlspecialchars($horarios[$hora_nombre][$dia]['profesor']); ?>
                                            <?php endif; ?>
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
    <div id="modal" class="modal hidden">
        <div class="modal__content">
            <div class="p-5" id="modal-content">
                <h2 class="text-lg font-medium">Agregar Nuevo Horario</h2>
                <form id="addScheduleForm" method="POST" action="">
                    <div class="mt-4">
                        <label for="modal-sede" class="form-label">Sede:</label>
                        <input type="text" id="modal-sede" name="modal-sede" class="input w-full border mt-2" required>
                    </div>
                    <div class="mt-4">
                        <label for="modal-dia" class="form-label">Día:</label>
                        <select name="modal-dia" id="modal-dia" class="input w-full border mt-2">
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sábado">Sábado</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="button bg-theme-1 text-white">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Fin Modal -->
    
    <!-- Scripts -->
    <script>
        // Obtener el modal
        var modal = document.getElementById("modal");
        var btn = document.getElementById("openModal");

        // Cuando el usuario haga clic en el botón, abre el modal
        btn.onclick = function() {
            var selectedSede = document.getElementById("sede").value;
            document.getElementById("modal-sede").value = selectedSede;
            modal.classList.remove("hidden");
        }

        // Cuando el usuario haga clic en el botón de cerrar, cierra el modal
        document.addEventListener('DOMContentLoaded', function() {
            var closeBtn = document.querySelector(".modal-close");
            closeBtn.onclick = function() {
                modal.classList.add("hidden");
            }
        });

        // Cuando el usuario haga clic fuera del modal, también cierra el modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.classList.add("hidden");
            }
        }
    </script>
    <!-- Fin Scripts -->
</body>
</html>
