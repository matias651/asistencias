<?php
// Verificar si la sesión no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();
}

// Inclusión del archivo de configuración
require_once __DIR__ . "/../../config.php"; // Utilizando una ruta absoluta

// Definir el número de resultados por página
$results_per_page = isset($_GET['results_per_page']) ? (int)$_GET['results_per_page'] : 10;

// Determinar en qué página está el usuario (por defecto es la primera página)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// Determinar el inicio del conjunto de resultados basado en la página actual
$start_from = ($page - 1) * $results_per_page;

// Obtener el término de búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Obtener el total de registros en la tabla de profesores
$sql_count = "SELECT COUNT(id_profesor) AS total FROM profesores WHERE profesor_nombre LIKE :search OR profesor_apellido LIKE :search OR profesor_email LIKE :search OR profesor_documento LIKE :search";
$stmt_count = $pdo->prepare($sql_count);
$search_term = '%' . $search . '%';
$stmt_count->bindParam(':search', $search_term, PDO::PARAM_STR);
$stmt_count->execute();
$row_count = $stmt_count->fetch(PDO::FETCH_ASSOC);
$total_results = $row_count['total'];

// Calcular el número total de páginas
$total_pages = ceil($total_results / $results_per_page);

// Obtener los profesores de la base de datos con limitación y desplazamiento para la paginación
$sql = "SELECT * FROM profesores WHERE profesor_nombre LIKE :search OR profesor_apellido LIKE :search OR profesor_email LIKE :search OR profesor_documento LIKE :search LIMIT :start_from, :results_per_page";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':start_from', $start_from, PDO::PARAM_INT);
$stmt->bindParam(':results_per_page', $results_per_page, PDO::PARAM_INT);
$stmt->bindParam(':search', $search_term, PDO::PARAM_STR);
$stmt->execute();
$profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<!-- BEGIN: Head -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Instituciones</title>
    <!-- Tu CSS personalizado -->
    <link href="<?php echo $url . '/static resources/css/modal.css'; ?>" rel="stylesheet">
    <link href="<?php echo $url . '/static resources/css/app.css'; ?>" rel="stylesheet">    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.js"></script>
</head>
<!-- END: Head -->
<body class="app">
    <div class="flex">
        <!-- BEGIN: Side Menu -->
        <?php include $base_path . '/includes/sidebar.php'; ?>           
        <!-- BEGIN: Content -->
        <div class="content">
            <!-- BEGIN: Top Bar -->
            <div class="top-bar">
                <!-- BEGIN: Breadcrumb -->
                <div class="-intro-x breadcrumb mr-auto hidden sm:flex"> <a href="" class="">Application</a> <i data-feather="chevron-right" class="breadcrumb__icon"></i> <a href="" class="breadcrumb--active">Dashboard</a> </div>
                <!-- END: Breadcrumb -->                  
                <!-- BEGIN: Account Menu -->
                <?php include $base_path . '/includes/account.php'; ?>   
                <!-- END: Account Menu -->
            </div>
            <!-- END: Top Bar -->
            <h2 class="intro-y text-lg font-medium mt-10">
                LISTADO DE PROFESORES
            </h2>
            <div class="grid grid-cols-12 gap-6 mt-5">
                <div class="intro-y col-span-12 flex flex-wrap sm:flex-no-wrap items-center mt-2">
                    <!-- caja boton modal -->
                    <div class="p-5 text-center">
                        <!-- Botón para abrir el modal -->
                        <a href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" class="button inline-block bg-theme-1 text-white">Agregar Profesor</a>
                    </div>
                    <!-- BEGIN: Agregar Profesor Modal -->
                    <div class="modal" id="header-footer-modal-preview">
                        <div class="modal-dialog">
                            <div class="modal-header">
                                <h2 class="font-medium text-base mr-auto text-center">Agregar Profesor</h2>                                  
                            </div>
                            <div class="modal-body">
                                <div class="grid grid-cols-12 gap-4 row-gap-3">
                                    <div class="col-span-12 sm:col-span-6">
                                        <label>Nombre</label>
                                        <input type="text" class="input w-full border mt-2 flex-1" placeholder="Nombre">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label>Apellido</label>
                                        <input type="text" class="input w-full border mt-2 flex-1" placeholder="Apellido">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label>Email</label>
                                        <input type="email" class="input w-full border mt-2 flex-1" placeholder="Email">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label>Documento</label>
                                        <input type="text" class="input w-full border mt-2 flex-1" placeholder="Documento">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label>Cede</label>
                                        <input type="text" class="input w-full border mt-2 flex-1" placeholder="Cede">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label>Saldo</label>
                                        <input type="text" class="input w-full border mt-2 flex-1" placeholder="Saldo">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label>Programa</label>
                                        <input type="text" class="input w-full border mt-2 flex-1" placeholder="Programa">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                            <!-- <button class="button border items-center text-gray-700 hidden sm:flex" data-dismiss="modal"> <i data-feather="x" class="w-4 h-4 mr-2"></i> Close </button> -->
                                <button type="button" class="button w-20 border text-gray-700 mr-1" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="button w-20 bg-theme-1 text-white">Guardar</button>
                            </div>
                        </div>
                    </div>
                    <!-- END: Agregar Profesor Modal -->
                    <div class="dropdown relative">
                        <button class="dropdown-toggle button px-2 box text-gray-700">
                            <!-- Reemplazar el icono por una imagen -->
                            <span class="w-5 h-5 flex items-center justify-center">
                                <img src="<?php echo $url . '/static resources/images/export_img.png'; ?>" alt="Icono" class="w-4 h-4" />
                            </span>
                        </button>
                        <div class="dropdown-box mt-10 absolute w-40 top-0 left-0 z-20">
                            <div class="dropdown-box__content box p-2">
                                <a href="" class="flex items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md">
                                    <i data-feather="printer" class="w-4 h-4 mr-2"></i> Print
                                </a>
                                <a href="" class="flex items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md">
                                    <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Export to Excel
                                </a>
                                <a href="" class="flex items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md">
                                    <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Export to PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block mx-auto text-gray-600" id="results_text">Se muestran de a <?= htmlspecialchars($results_per_page) ?> resultados</div>                
                    <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0 flex items-center">
                        <div class="relative text-gray-700">
                        <form id="search-form" method="GET" action="" class="flex items-center">
                            <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" class="input w-56 box pr-10 placeholder-theme-13" placeholder="Buscar...">
                            <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3" style="border: none; background: none;">
                                <img src="<?php echo $url . '/static resources/images/search_img.png'; ?>" alt="Buscar" style="width: 20px; height: 20px;">
                            </button>
                            <input type="hidden" name="results_per_page" id="results_per_page" value="<?= htmlspecialchars($results_per_page) ?>">
                        </form>
                        </div>
                        <button id="clear-search" class="button w-20 bg-theme-1 text-white ml-3">Limpiar</button>
                    </div>     
                </div>
                <!-- BEGIN: Data List -->
                <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                    <table class="table table-report -mt-2">
                        <thead>
                            <tr>
                                <th class="whitespace-no-wrap">NOMBRE</th>
                                <th class="whitespace-no-wrap">APELLIDO</th>
                                <th class="text-center whitespace-no-wrap">EMAIL</th>
                                <th class="text-center whitespace-no-wrap">DOCUMENTO</th>
                                <th class="text-center whitespace-no-wrap">SEDE</th>
                                <th class="text-center whitespace-no-wrap">SALDO</th>
                                <th class="text-center whitespace-no-wrap">PROGRAMA</th>
                                <th class="text-center whitespace-no-wrap">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($profesores as $profesor): ?>
                            <tr class="intro-x">
                                <td><?= htmlspecialchars($profesor['profesor_nombre']) ?></td>
                                <td><?= htmlspecialchars($profesor['profesor_apellido']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($profesor['profesor_email']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($profesor['profesor_documento']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($profesor['profesor_sede']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($profesor['profesor_saldo']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($profesor['profesor_programa']) ?></td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a class="flex items-center mr-3" href="javascript:;"> <i data-feather="check-square" class="w-4 h-4 mr-1"></i> Edit </a>
                                        <a class="flex items-center text-theme-6" href="javascript:;"> <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Delete </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- END: Data List -->
                <!-- BEGIN: Pagination -->
                <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-no-wrap items-center justify-center">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li><a class="pagination__link" href="?page=<?= $page - 1 ?>">Anterior</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li><a class="pagination__link <?= ($i == $page) ? 'pagination__link--active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a></li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li><a class="pagination__link" href="?page=<?= $page + 1 ?>">Siguiente</a></li>
                        <?php endif; ?>
                    </ul>
                    <select id="results_per_page_selector" class="w-20 input box mt-3 sm:mt-0">
                        <option value="10" <?= $results_per_page == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $results_per_page == 25 ? 'selected' : '' ?>>25</option>
                        <option value="35" <?= $results_per_page == 35 ? 'selected' : '' ?>>35</option>
                        <option value="50" <?= $results_per_page == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                </div>
                <!-- END: Pagination -->
            </div>
        </div>
        <!-- END: Content -->
    </div>

    <!-- BEGIN: JS Assets-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.2/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script> 
    <!-- END: JS Assets-->
    <script src="dist/js/app.js"></script>
    <!-- Modal JS-->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var openModalButtons = document.querySelectorAll('[data-toggle="modal"]');
            var closeModalButtons = document.querySelectorAll('[data-dismiss="modal"]');

            openModalButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetModalId = button.getAttribute('data-target');
                    var targetModal = document.querySelector(targetModalId);
                    if (targetModal) {
                        targetModal.classList.add('active');
                    }
                });
            });

            closeModalButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetModal = button.closest('.modal');
                    if (targetModal) {
                        targetModal.classList.remove('active');
                    }
                });
            });

            window.addEventListener('click', function (event) {
                var activeModal = document.querySelector('.modal.active');
                if (activeModal && event.target === activeModal) {
                    activeModal.classList.remove('active');
                }
            });
        });
    </script>
    <!-- Final Modal JS -->
    <!-- boton Buscador JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Clear search input and reset the form
            document.getElementById('clear-search').addEventListener('click', function () {
                document.getElementById('search').value = '';
                document.getElementById('search-form').submit();
            });

            // Change results per page and submit the form
            document.getElementById('results_per_page_selector').addEventListener('change', function () {
                document.getElementById('results_per_page').value = this.value;
                document.getElementById('search-form').submit();
            });

        });
    </script>
    <!-- Final boton Buscador JS -->
   

</body>
</html>