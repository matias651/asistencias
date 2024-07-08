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
    <style>
        .half-page {
            display: flex;
            flex-direction: column;
            width: 50%;
            padding: 20px;
        }
        .container {
            display: flex;
        }
    </style>
</head> 
<!-- END: Head -->
<body class="app">        
    <div class="container">
        <div class="half-page">
            <!-- Lista desplegable arriba de la tabla -->
            <div class="mb-4">
                <label for="sede">Seleccionar Sede:</label>
                <select id="sede" class="input w-full border mt-2 flex-1">
                    <option value="sede1">Sede 1</option>
                    <option value="sede2">Sede 2</option>
                </select>
            </div>
            <!-- BEGIN: Data List -->
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
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
            <!-- END: Data List -->
            <!-- BEGIN: Pagination -->
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-no-wrap items-center">
                <ul class="pagination">
                    <li>
                        <a class="pagination__link" href=""> <i class="w-4 h-4" data-feather="chevrons-left"></i> </a>
                    </li>
                    <li>
                        <a class="pagination__link" href=""> <i class="w-4 h-4" data-feather="chevron-left"></i> </a>
                    </li>
                    <li> <a class="pagination__link" href="">...</a> </li>
                    <li> <a class="pagination__link" href="">1</a> </li>
                    <li> <a class="pagination__link pagination__link--active" href="">2</a> </li>
                    <li> <a class="pagination__link" href="">3</a> </li>
                    <li> <a class="pagination__link" href="">...</a> </li>
                    <li>
                        <a class="pagination__link" href=""> <i class="w-4 h-4" data-feather="chevron-right"></i> </a>
                    </li>
                    <li>
                        <a class="pagination__link" href=""> <i class="w-4 h-4" data-feather="chevrons-right"></i> </a>
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
        <div class="half-page">
            <!-- Aquí puedes añadir el contenido para la otra mitad de la página -->
            <h2>Otra Mitad de la Página</h2>
            <!-- Contenido adicional -->
        </div>
    </div>
    <!-- BEGIN: Delete Confirmation Modal -->
    <div class="modal" id="delete-confirmation-modal">
        <div class="modal__content">
            <div class="p-5 text-center">
                <i data-feather="x-circle" class="w-16 h-16 text-theme-6 mx-auto mt-3"></i> 
                <div class="text-3xl mt-5">Are you sure?</div>
                <div class="text-gray-600 mt-2">Do you really want to delete these records? This process cannot be undone.</div>
            </div>
            <div class="px-5 pb-8 text-center">
                <button type="button" data-dismiss="modal" class="button w-24 border text-gray-700 mr-1">Cancel</button>
                <button type="button" class="button w-24 bg-theme-6 text-white">Delete</button>
            </div>
        </div>
    </div>
    <!-- END: Delete Confirmation Modal -->
    <!-- BEGIN: JS Assets-->
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=["your-google-map-api"]&libraries=places"></script>
    <script src="dist/js/app.js"></script>
    <!-- END: JS Assets-->    
</body>
</html>
