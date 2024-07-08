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
        $stmt = $pdo->prepare("SELECT id_profesor, profesor_nombre, profesor_apellido FROM profesores WHERE profesor_sede = :sede");
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
<html lang="en">
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

            <div class="intro-y box mt-5">
                
                <div class="p-5" id="header-footer-modal">
                    <div class="preview">
                        <div class="text-center">
                            <a href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" class="button inline-block bg-theme-1 text-white">Show Modal</a>
                        </div>
                        <div class="modal" id="header-footer-modal-preview">
                            <div class="modal-dialog">
                                <div class="modal-header"></div>
                                <div class="modal-body">
                                    <form id="modal-form" method="POST" action="guardar_horario.php">
                                        <div class="grid grid-cols-12 gap-4 row-gap-3">
                                            <div class="col-span-12">
                                                <label>Sede</label>
                                                <input type="text" class="input w-full border mt-2 flex-1" value="<?php echo $selected_sede ? $sedes[array_search($selected_sede, array_column($sedes, 'id_sede'))]['sede_nombre'] : ''; ?>" disabled>
                                            </div>
                                            <div class="col-span-12">
                                                <label>Día</label>
                                                <select name="dia" class="input w-full border mt-2 flex-1">
                                                    <?php foreach ($dias as $dia): ?>
                                                        <option value="<?php echo $dia; ?>"><?php echo $dia; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-span-12">
                                                <label>Horario</label>
                                                <select name="hora" class="input w-full border mt-2 flex-1">
                                                    <?php
                                                    $start_time = new DateTime('08:00:00');
                                                    $end_time = new DateTime('21:00:00');
                                                    $interval = new DateInterval('PT1H');
                                                    while ($start_time <= $end_time): ?>
                                                        <option value="<?php echo $start_time->format('H:i'); ?>"><?php echo $start_time->format('H:i'); ?></option>
                                                        <?php $start_time->add($interval); ?>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="col-span-12">
                                                <label>Asignatura</label>
                                                <select name="asignatura" class="input w-full border mt-2 flex-1">
                                                    <?php foreach ($asignaturas as $asignatura): ?>
                                                        <option value="<?php echo $asignatura['id_asignatura']; ?>"><?php echo $asignatura['asignatura_nombre']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-span-12">
                                                <label>Profesor</label>
                                                <select name="profesor" class="input w-full border mt-2 flex-1">
                                                    <?php foreach ($profesores as $profesor): ?>
                                                        <option value="<?php echo $profesor['id_profesor']; ?>"><?php echo $profesor['profesor_nombre'] . ' ' . $profesor['profesor_apellido']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="button w-20 border text-gray-700 mr-1" data-dismiss="modal">Cancel</button>
                                    <button type="submit" form="modal-form" class="button w-20 bg-theme-1 text-white">Guardar</button>
                                </div>
                            </div>
                        </div>
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
                                <th class="text-center">Hora</th>
                                <?php foreach ($dias as $dia): ?>
                                    <th class="text-center"><?php echo $dia; ?></th>
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
                                    <td class="text-center"><?php echo $start_time->format('H:i'); ?></td>
                                    <?php foreach ($dias as $dia): ?>
                                        <td class="text-center">
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
    </script>
</body>
</html>
