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
                    NUEVO PAGO
                </h2>
                <div class="grid grid-cols-12 gap-6 mt-5">
                    <div class="intro-y col-span-12 flex flex-wrap sm:flex-no-wrap items-center mt-2">
                        
                      
                    </div>
                    <!-- BEGIN: Formulario -->
                    <div class="intro-y col-span-12 lg:col-span-8">
                        <div class="intro-y box p-5">
                            <h2 class="text-lg font-medium">Formulario</h2>
                            <form>
                                <div class="mt-3">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input id="fecha" type="date" class="input w-full border mt-2">
                                </div>
                                
                                <div class="mt-3">
                                    <label for="profesor" class="form-label">Profesor</label>
                                    <input id="profesor" type="text" class="input w-full border mt-2">
                                </div>
                                <div class="mt-3">
                                    <label for="valor-por-hora" class="form-label">Valor por Hora</label>
                                    <input id="valor-por-hora" type="text" class="input w-full border mt-2">
                                </div>
                                <div class="mt-3">
                                    <label for="horas" class="form-label">Horas</label>
                                    <input id="horas" type="number" class="input w-full border mt-2">
                                </div>
                                <div class="mt-3">
                                    <label for="monto" class="form-label">Monto</label>
                                    <input id="monto" type="text" class="input w-full border mt-2">
                                </div>
                                <div class="mt-3">
                                    <label for="periodo" class="form-label">Periodo</label>
                                    <input id="periodo" type="text" class="input w-full border mt-2">
                                </div>
                                <div class="mt-5 text-right">
                                    <button type="submit" class="button bg-theme-1 text-white">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- END: Formulario -->
                </div>
            </div>
            <!-- END: Content -->
        </div>
        <!-- BEGIN: JS Assets-->
        <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=["your-google-map-api"]&libraries=places"></script>
        <script src="dist/js/app.js"></script>
        <!-- END: JS Assets-->    
    </body>
</html>
