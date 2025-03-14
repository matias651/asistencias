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
    $stmt = $pdo->prepare("SELECT id_sede, sede_nombre FROM sedes");
    $stmt->execute();
    $sedes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $selected_sede = isset($_POST['sede']) ? $_POST['sede'] : null;
    $horarios = [];

    if ($selected_sede) {
        // Obtener los horarios basados en la sede seleccionada
        $stmt = $pdo->prepare("
            SELECT h.horario_hora, a.asignatura_nombre, p.profesor_nombre, p.profesor_apellido, h.horario_dia
            FROM horarios h
            JOIN profesores p ON h.horario_profesor = p.id_profesor
            JOIN asignaturas a ON h.horario_asignatura = a.id_asignatura
            WHERE h.horario_sede = :sede
        ");
        $stmt->bindParam(':sede', $selected_sede, PDO::PARAM_INT);
        $stmt->execute();
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener las asignaturas
        $stmt = $pdo->prepare("SELECT id_asignatura, asignatura_nombre FROM asignaturas");
        $stmt->execute();
        $asignaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener los profesores
        $stmt = $pdo->prepare("
            SELECT id_profesor, profesor_nombre, profesor_apellido 
            FROM profesores 
            WHERE profesor_sede = :sede 
              AND id_profesor NOT IN (
                  SELECT horario_profesor 
                  FROM horarios 
                  WHERE horario_dia = :dia 
                    AND horario_hora = :hora
              )
        ");
        $stmt->bindParam(':sede', $selected_sede, PDO::PARAM_INT);
        $stmt->execute();
        $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Instituciones</title>
    <link href="<?php echo $url . '/static resources/css/app.css'; ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="dist/images/logo.svg" rel="shortcut icon">
    <link rel="stylesheet" href="dist/css/app.css" />
    <link href="<?php echo $url . '/static resources/css/modal.css'; ?>" rel="stylesheet">
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

            <!-- Selector de sede -->
            <form method="POST" action="">
                <div class="intro-y col-span-12 flex flex-wrap sm:flex-no-wrap items-center mt-2">
                    <label for="sede" class="mr-2">Seleccionar Sede:</label>
                    <select name="sede" id="sede" class="input w-full border mt-2 flex-1">
                        <?php foreach ($sedes as $sede): ?>
                            <option value="<?php echo $sede['id_sede']; ?>" <?php echo $selected_sede == $sede['id_sede'] ? 'selected' : ''; ?>>
                                <?php echo $sede['sede_nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="button text-white bg-theme-1 shadow-md ml-2">Filtrar</button>
                </div>
            </form>

            <div class="container mt-5"></div>

          
                            <!-- Modal -->
<div id="horariosModal" class="modal">
    <div class="modal-content">
        <span data-dismiss="modal" class="close-button">&times;</span>
        <h2 class="text-lg font-medium mt-4 mb-4 text-center">Horarios Semanales</h2>
        <div class="grid grid-cols-7 gap-4 p-4">
            <?php foreach ($dias as $dia): ?>
                <div class="text-center"><?php echo $dia; ?></div>
            <?php endforeach; ?>
            <!-- Aquí puedes agregar más contenido dinámico según tu necesidad -->
        </div>
    </div>
</div>

            <!-- Agenda semanal -->
            <h2 class="intro-y text-lg font-medium mt-10">Agenda Semanal</h2>
            <div class="grid grid-cols-12 gap-6 mt-5">
                <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                    <table class="table table-report -mt-2">
                        <thead>
                            <tr>
                                <th class="text-center w-24">Hora</th> <!-- Ancho fijo para la columna de hora -->
                                <?php foreach ($dias as $dia): ?>
                                    <th class="text-center w-32"><?php echo $dia; ?></th> <!-- Ancho fijo para las columnas de los días -->
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $start_time = new DateTime('08:00:00');
                            $end_time = new DateTime('22:00:00');
                            $interval = new DateInterval('PT1H');

                            while ($start_time <= $end_time): ?>
                                <tr>
                                    <td class="text-center w-24"><?php echo $start_time->format('H:i'); ?></td> <!-- Ancho fijo para las celdas de hora -->
                                    <?php foreach ($dias as $dia): ?>
                                        <td class="text-center w-32"> <!-- Ancho fijo para las celdas de los días -->
                                            <?php
                                            foreach ($horarios as $horario) {
                                                if ($horario['horario_hora'] == $start_time->format('H:i:s') && $horario['horario_dia'] == $dia) {
                                                    echo "Asignatura: {$horario['asignatura_nombre']}<br>Profesor: {$horario['profesor_nombre']} {$horario['profesor_apellido']}";
                                                }
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php $start_time->add($interval); ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END: Content -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="dist/js/app.js"></script>
    <script>
        document.querySelectorAll('[data-toggle="modal"]').forEach(button => {
            button.addEventListener('click', () => {
                const target = button.getAttribute('data-target');
                document.querySelector(target).classList.add('active');
            });
        });

        document.querySelectorAll('[data-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', () => {
                button.closest('.modal').classList.remove('active');
            });
        });

        // Fetch available professors when the day or time changes
        document.getElementById('dia').addEventListener('change', fetchAvailableProfessors);
        document.getElementById('hora').addEventListener('change', fetchAvailableProfessors);

        function fetchAvailableProfessors() {
            const sede = '<?php echo $selected_sede; ?>';
            const dia = document.getElementById('dia').value;
            const hora = document.getElementById('hora').value;

            fetch('fetch_available_professors.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ sede, dia, hora }),
            })
            .then(response => response.json())
            .then(data => {
                const profesorSelect = document.getElementById('profesor');
                profesorSelect.innerHTML = ''; // Clear the current options
                data.forEach(profesor => {
                    const option = document.createElement('option');
                    option.value = profesor.id_profesor;
                    option.textContent = `${profesor.profesor_nombre} ${profesor.profesor_apellido}`;
                    profesorSelect.appendChild(option);
                });
            });
        }
    </script>
</body>
</html>
