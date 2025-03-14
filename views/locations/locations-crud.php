<?php
// Verificar si la sesión no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();  
}
// Inclusión del archivo de configuración
require_once dirname(__DIR__) . "../../config.php"; // Uso de una ruta absoluta
?>

<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN: Head -->
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion Instituciones</title>
        <!-- Tu CSS personalizado -->
        <link href="<?php echo $url . '/static resources/css/app.css'; ?>" rel="stylesheet">
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
                    <div class="-intro-x breadcrumb mr-auto hidden sm:flex">
                        <a href="" class="">Application</a>
                        <i data-feather="chevron-right" class="breadcrumb__icon"></i>
                        <a href="" class="breadcrumb--active">Dashboard</a>
                    </div>
                    <!-- END: Breadcrumb -->                  
                    <!-- BEGIN: Account Menu -->
                    <?php include $base_path . '/includes/account.php'; ?>   
                    <!-- END: Account Menu -->
                </div>
                <!-- END: Top Bar -->
                <h2 class="intro-y text-lg font-medium mt-10">
                    LISTADO DE SEDES
                </h2>
                <div class="grid grid-cols-12 gap-6 mt-5">
                    <div class="intro-y col-span-12 flex flex-wrap sm:flex-no-wrap items-center mt-2">
                        <button class="button text-white bg-theme-1 shadow-md mr-2">Agregar Sedes</button>
                        <!-- BEGIN: Agregar Sedes Modal -->
                        <div class="modal" id="agregar-Sedes-modal">
                            <div class="modal__content">
                                <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
                                    <h2 class="font-medium text-base mr-auto">Agregar Sedes</h2>
                                    <button data-dismiss="modal" class="button bg-gray-200 dark:bg-dark-1 text-gray-600 dark:text-gray-300">Cerrar</button>
                                </div>
                                <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
                                    <!-- Aquí van los campos para agregar el Sedes -->
                                    <div class="col-span-12 sm:col-span-6">
                                        <label>Nombre</label>
                                        <input type="text" class="input w-full border mt-2 flex-1">
                                    </div>                                    
                                </div>
                                <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5">
                                    <button type="button" class="button w-20 bg-theme-1 text-white">Guardar</button>
                                </div>
                            </div>
                        </div>
                        <!-- END: Agregar Sedes Modal -->
                        <div class="dropdown relative">
                            <button class="dropdown-toggle button px-2 box text-gray-700">
                                <span class="w-5 h-5 flex items-center justify-center">
                                    <i class="w-4 h-4" data-feather="plus"></i>
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
                        <div class="hidden md:block mx-auto text-gray-600">Se muestran de a 10 resultados</div>
                        <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                            <div class="w-56 relative text-gray-700">
                                <input type="text" class="input w-56 box pr-10 placeholder-theme-13" placeholder="Buscar...">
                                <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-feather="search"></i> 
                            </div>
                        </div>
                    </div>
                    <!-- BEGIN: Data List -->
                    <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-no-wrap">NOMBRE</th>
                                    <th class="whitespace-no-wrap">UBICACION</th>
                                    <th class="text-center whitespace-no-wrap">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="intro-x">
                                    <td>
                                        <div class="flex items-center">                                          
                                            <div class="ml-4">Sede 1</div>
                                        </div>
                                    </td>
                                    <td class="flex items-center">Calle Falsa 123</td>
                                    <td class="table-report__action w-56">
                                        <div class="flex justify-center items-center">
                                            <a class="flex items-center mr-3" href="javascript:;">
                                                <i data-feather="check-square" class="w-4 h-4 mr-1"></i> Edit
                                            </a>
                                            <a class="flex items-center text-theme-6" href="javascript:;" data-toggle="modal" data-target="#delete-confirmation-modal">
                                                <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="intro-x">
                                    <td>
                                        <div class="flex items-center">                                          
                                            <div class="ml-4">Sede 2</div>
                                        </div>
                                    </td>
                                    <td class="flex items-center">Calle Falsa 123</td>
                                    <td class="table-report__action w-56">
                                        <div class="flex justify-center items-center">
                                            <a class="flex items-center mr-3" href="javascript:;">
                                                <i data-feather="check-square" class="w-4 h-4 mr-1"></i> Edit
                                            </a>
                                            <a class="flex items-center text-theme-6" href="javascript:;" data-toggle="modal" data-target="#delete-confirmation-modal">
                                                <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>                    
                            </tbody>
                        </table>
                    </div>
                    <!-- END: Data List -->

                    <!-- BEGIN: Pagination -->
                    <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-no-wrap items-center">
                        <ul class="pagination">
                            <li>
                                <a class="pagination__link" href="">
                                    <i class="w-4 h-4" data-feather="chevrons-left"></i>
                                </a>
                            </li>
                            <li>
                                <a class="pagination__link" href="">
                                    <i class="w-4 h-4" data-feather="chevron-left"></i>
                                </a>
                            </li>
                            <li>
                                <a class="pagination__link" href="">...</a>
                            </li>
                            <li>
                                <a class="pagination__link" href="">1</a>
                            </li>
                            <li>
                                <a class="pagination__link pagination__link--active" href="">2</a>
                            </li>
                            <li>
                                <a class="pagination__link" href="">3</a>
                            </li>
                            <li>
                                <a class="pagination__link" href="">...</a>
                            </li>
                            <li>
                                <a class="pagination__link" href="">
                                    <i class="w-4 h-4" data-feather="chevron-right"></i>
                                </a>
                            </li>
                            <li>
                                <a class="pagination__link" href="">
                                    <i class="w-4 h-4" data-feather="chevrons-right"></i>
                                </a>
                            </li>
                        </ul>
                        <select class="w-20 input box mt-3 sm:mt-0">
                            <option>10</option>
                            <option>25</option>
                            <option>35</option>
                            <option>50</option>
                        </select>
                    </div>
                    <!-- END: Pagination -->
                </div>
            </div>
            <!-- END: Content -->
        </div>
    </body>
</html>
