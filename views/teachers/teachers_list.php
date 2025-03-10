<?php
// Verificar si la sesión no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();
}

// Inclusión del archivo de configuración
require_once __DIR__ . "/../../config.php"; // Utilizando una ruta absoluta

// Verificar si se envió el formulario para agregar un nuevo profesor
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $documento = $_POST['documento'];
    $sede = $_POST['sede-select'];
    $saldo = $_POST['saldo'];
    $programa = $_POST['programa'];

    // Insertar el nuevo profesor en la base de datos
    try {
        $sql_insert = "INSERT INTO profesores (profesor_nombre, profesor_apellido, profesor_email, profesor_documento, profesor_sede, profesor_saldo, profesor_programa) VALUES (:nombre, :apellido, :email, :documento, :sede, :saldo, :programa)";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt_insert->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmt_insert->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_insert->bindParam(':documento', $documento, PDO::PARAM_STR);
        $stmt_insert->bindParam(':sede', $sede, PDO::PARAM_STR);
        $stmt_insert->bindParam(':saldo', $saldo, PDO::PARAM_STR);
        $stmt_insert->bindParam(':programa', $programa, PDO::PARAM_STR);
        $stmt_insert->execute();
        
        // Redireccionar para evitar reenvío del formulario
        header("Location: teachers_list.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al agregar el profesor: " . $e->getMessage();
    }
}

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

try {
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

    // Devolver resultados como JSON
    $response = [
        'total_results' => $total_results,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'results_per_page' => $results_per_page,
        'profesores' => $profesores
    ];
   
} catch (PDOException $e) {
    // Manejo de errores de PDO
    echo "Error al ejecutar la consulta: " . $e->getMessage();
}
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
    <link href="<?php echo $url . '/static resources/css/paginator.css'; ?>" rel="stylesheet"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.js"></script>
    <!-- Librerías para exportar a Excel y PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
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
                                <form id="add-professor-form" action="teachers_list.php" method="POST">
                                    <div class="grid grid-cols-12 gap-4 row-gap-3">
                                        <div class="col-span-12 sm:col-span-6">
                                            <label>Nombre</label>
                                            <input type="text" name="nombre" class="input w-full border mt-2 flex-1" placeholder="Nombre" required>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label>Apellido</label>
                                            <input type="text" name="apellido" class="input w-full border mt-2 flex-1" placeholder="Apellido" required>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label>Email</label>
                                            <input type="email" name="email" class="input w-full border mt-2 flex-1" placeholder="Email" required>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label>Documento</label>
                                            <input type="text" name="documento" class="input w-full border mt-2 flex-1" placeholder="Documento" required>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label>Cede</label>
                                            <select id="sede-select" name="sede-select" class="input w-full border mt-2 flex-1" required>
                                                <option value="">Seleccione una sede</option>
                                                <!-- Opciones de sedes cargadas dinámicamente por JavaScript -->
                                            </select>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label>Saldo</label>
                                            <input type="text" name="saldo" class="input w-full border mt-2 flex-1" placeholder="Saldo" required>
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label>Programa</label>
                                            <select id="programa" name="programa" class="input w-full border mt-2 flex-1" required>
                                                <option value="">Seleccione un programa</option>
                                                <!-- Opciones de programas cargadas dinámicamente por JavaScript -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="button w-20 border text-gray-700 mr-1" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="button w-20 bg-theme-1 text-white">Guardar</button>
                                    </div>
                                </form>
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
                                <a href="#" id="print" class="flex items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md">
                                    <i data-feather="printer" class="w-4 h-4 mr-2"></i> Print
                                </a>
                                <a href="#" id="export-excel" class="flex items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md">
                                    <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Export to Excel
                                </a>
                                <a href="#" id="export-pdf" class="flex items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md">
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
                    <table id="profesorTable" class="table table-report -mt-2">
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
                                    <td><?= htmlspecialchars($profesor['profesor_nombre'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($profesor['profesor_apellido'] ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($profesor['profesor_email'] ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($profesor['profesor_documento'] ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($profesor['profesor_sede'] ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($profesor['profesor_saldo'] ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($profesor['profesor_programa'] ?? '') ?></td>
                                    <!-- Dentro del loop foreach de los profesores -->
                                    <td class="table-report__action w-56">
                                        <div class="flex justify-center items-center">
                                            <a class="flex items-center mr-3" href="javascript:;"> 
                                                <i data-feather="check-square" class="w-4 h-4 mr-1"></i> Edit 
                                            </a>
                                            <a class="flex items-center text-theme-6 delete-profesor" data-profesor-id="<?= htmlspecialchars($profesor['id_profesor'] ?? '') ?>" href="teachers-delete.php?id_profesor=<?= htmlspecialchars($profesor['id_profesor'] ?? '') ?>"> 
                                                <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Delete 
                                            </a>
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

    <!-- IMPORTACIONES DE LIBRERIAS -->
    <!-- BEGIN: JS Assets-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.2/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script> 
    <!-- END: JS Assets-->
    <!-- Incluir la librería SheetJS para exportar a Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <!-- Incluir la librería jsPDF para exportar a PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>    
    <!-- FINAL DE IMPORTACIONES DE LIBRERIAS -->

    <!-- Modal JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var openModalButtons = document.querySelectorAll('[data-toggle="modal"]');
            var closeModalButtons = document.querySelectorAll('[data-dismiss="modal"]');
            var sedeSelect = document.getElementById('sede-select');
            var programaSelect = document.getElementById('programa');

            // Función para cargar las sedes
            function loadSedes() {
                fetch('../sedes/sedes_list.php')
                    .then(response => response.json())
                    .then(data => {
                        // Limpiar opciones anteriores
                        sedeSelect.innerHTML = '<option value="">Seleccione una sede</option>';
                        data.forEach(sede => {
                            var option = document.createElement('option');
                            option.value = sede.id_sede;
                            option.textContent = sede.sede_nombre;
                            sedeSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Función para cargar los programas
            function loadProgramas() {
                fetch('../programs/programs_list.php')
                    .then(response => response.json())
                    .then(data => {
                        // Limpiar opciones anteriores
                        programaSelect.innerHTML = '<option value="">Seleccione un programa</option>';
                        data.forEach(programa => {
                            var option = document.createElement('option');
                            option.value = programa.id_programa;
                            option.textContent = programa.programa_nombre;
                            programaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }

            openModalButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetModalId = button.getAttribute('data-target');
                    var targetModal = document.querySelector(targetModalId);
                    if (targetModal) {
                        targetModal.classList.add('active');
                        loadSedes(); // Llamar a la función para cargar las sedes
                        loadProgramas(); // Llamar a la función para cargar los programas
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
    <!-- Menu Desplegable Para Exportar -->
    <script>
        $(document).ready(function() {
            // Función para imprimir la tabla
            $('#print').on('click', function() {
                var divToPrint = document.getElementById('profesorTable');
                var newWin = window.open('', 'Print-Window');
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">' + divToPrint.outerHTML + '</body></html>');
                newWin.document.close();
                setTimeout(function() {
                    newWin.close();
                }, 10);
            });

            // Función para exportar a Excel
            $('#export-excel').on('click', function() {
                var wb = XLSX.utils.table_to_book(document.getElementById('profesorTable'), {sheet: "Sheet JS"});
                XLSX.writeFile(wb, 'Professors.xlsx');
            });

            // Función para exportar a PDF
            $('#export-pdf').on('click', function() {
                var doc = new jsPDF('p', 'pt', 'a4');
                var res = doc.autoTableHtmlToJson(document.getElementById('profesorTable'));
                doc.autoTable(res.columns, res.data);
                doc.save('Professors.pdf');
            });
         
        });
    </script>
    <!-- Final Menu Desplegable Para Exportar -->    
   
    <!-- Agregar profesor -->
    <script>
        // Función para mostrar el modal de registro de profesor
        function showModal() {
            // Limpiar los campos del formulario cuando se muestra el modal
            $('#profesor_nombre').val('');
            $('#profesor_apellido').val('');
            $('#profesor_email').val('');
            $('#profesor_documento').val('');
            $('#profesor_sede').val('');
            $('#profesor_plan').val('');
            $('#profesor_programa').val('');
            $('#profesor_saldo').val('');
            
            // Cargar la lista de sedes desde la base de datos
            $.ajax({
                url: 'sedes.php', // Ruta al archivo PHP que obtiene las sedes
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Limpiar el dropdown de sedes
                    $('#profesor_sede').empty();
                    // Iterar sobre las sedes obtenidas y agregarlas al dropdown
                    $.each(response, function(index, sede) {
                        $('#profesor_sede').append('<option value="' + sede.id_sede + '">' + sede.sede_nombre + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar las sedes:', error);
                }
            });

            // Mostrar el modal de registro de profesor
            $('#profesorModal').modal('show');
        }

        // Función para guardar un nuevo profesor
        function guardarProfesor() {
            // Obtener los valores de los campos del formulario
            var nombre = $('#profesor_nombre').val();
            var apellido = $('#profesor_apellido').val();
            var email = $('#profesor_email').val();
            var documento = $('#profesor_documento').val();
            var sede = $('#profesor_sede').val();
            var plan = $('#profesor_plan').val();
            var programa = $('#profesor_programa').val();
            var saldo = $('#profesor_saldo').val();

            // Validar que los campos obligatorios no estén vacíos
            if (nombre && apellido && email && documento && sede && plan && programa && saldo) {
                // Objeto con los datos del profesor a guardar
                var profesorData = {
                    nombre: nombre,
                    apellido: apellido,
                    email: email,
                    documento: documento,
                    sede: sede,
                    plan: plan,
                    programa: programa,
                    saldo: saldo
                };

                // Enviar los datos del profesor al servidor usando AJAX
                $.ajax({
                    url: 'guardar_profesor.php', // Ruta al archivo PHP para guardar el profesor
                    type: 'POST',
                    data: profesorData,
                    success: function(response) {
                        console.log('Profesor guardado exitosamente:', response);
                        // Opcional: cerrar el modal después de guardar
                        $('#profesorModal').modal('hide');
                        // Recargar la lista de profesores o hacer alguna otra acción necesaria
                        // Por ejemplo, actualizar la tabla de profesores en la página
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al guardar el profesor:', error);
                        // Manejar errores como desees, por ejemplo, mostrando un mensaje al usuario
                    }
                });
            } else {
                // Mostrar un mensaje de error al usuario si faltan campos obligatorios
                alert('Por favor completa todos los campos obligatorios.');
            }
        }
    </script>
    <!-- Final de Agregar profesor -->

</body>
</html>