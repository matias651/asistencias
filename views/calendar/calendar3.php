<?php
// Verificar si la sesión no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cache_limiter', 'public');
    session_cache_limiter(false);
    session_start();
}
// Inclusión del archivo de configuración
require_once dirname(__DIR__) . "../../config.php"; // Corregido: Ruta absoluta al archivo de configuración

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener las sedes
    $stmt = $pdo->prepare("SELECT id_sede, sede_nombre FROM sedes");
    $stmt->execute();
    $sedes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $selected_sede = isset($_POST['sede']) ? $_POST['sede'] : null;
    $horarios = []; // Inicializar $horarios como un array vacío

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
    }

    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Instituciones</title>
    <link href="<?php echo $url . '/static resources/css/app.css'; ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">
   
    <!-- Bootstrap CSS (para los modales) -->
   
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" rel="stylesheet">

    <meta charset="utf-8">
        <link href="dist/images/logo.svg" rel="shortcut icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Midone admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">
        <meta name="keywords" content="admin template, Midone admin template, dashboard template, flat admin template, responsive admin template, web app">
        <meta name="author" content="LEFT4CODE">
        <title>Modal - Midone - Tailwind HTML Admin Template</title>
        <!-- BEGIN: CSS Assets-->
        <link rel="stylesheet" href="dist/css/app.css" />



    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            height: 100%;
        }
        #calendar {
            max-width: 100%;
        }
        .left-panel {
            padding: 20px;
            background-color: #f8f9fa;
            height: 100vh;
            overflow-y: auto;
        }
        .right-panel {
            padding: 20px;
            position: relative;
        }
        .calendar-container {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            max-width: 400px;
        }

        .modal {
      display: none;
    }

    .modal.active {
      display: flex;
    }

    .modal-dialog {
      background-color: #ffffff;
      border: 1px solid #e2e8f0;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      max-width: 80%; /* Ancho máximo del modal */
      width: 400px; /* Ancho específico del modal */
      border-radius: 0.5rem;
      overflow: hidden;
    }

    .modal-header {
      background-color: #f0f4f8;
      padding: 1rem;
      border-bottom: 1px solid #e2e8f0;
    }

    .modal-body {
      padding: 1rem;
    }

    .modal-footer {
      padding: 1rem;
      background-color: #f0f4f8;
      border-top: 1px solid #e2e8f0;
      text-align: right;
    }
    </style>
</head>
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

            <button class="button text-white bg-theme-1 shadow-md ml-2" onclick="openModal('modal-agregar')">Abrir Modal</button>

            
               <div class="container mt-5">
  <h1>Ejemplo de Modal con Bootstrap</h1>
  
  <!-- Botón para abrir el modal -->
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
    Abrir Modal
  </button>

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Título del Modal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Contenido del modal aquí...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </div>
    </div>
  </div>

</div>

<a href="javascript:;" data-toggle="modal" data-target="#basic-modal-preview" class="button inline-block bg-theme-1 text-white">Show Modal</a>

                        <div class="intro-y box">
                            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                                <h2 class="font-medium text-base mr-auto">
                                    Blank Modal
                                </h2>
                                <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                                    <div class="mr-3">Show code</div>
                                    <input data-target="#blank-modal" class="show-code input input--switch border" type="checkbox">
                                </div>
                            </div>
                            <div class="p-5" id="blank-modal">
                                <div class="preview">
                                    <div class="text-center"> <a href="javascript:;" data-toggle="modal" data-target="#basic-modal-preview" class="button inline-block bg-theme-1 text-white">Show Modal</a> </div>
                                    <div class="modal" id="basic-modal-preview">
                                        <div class="modal__content p-10 text-center"> This is totally awesome blank modal! </div>
                                    </div>
                                </div>
                                <div class="source-code hidden">
                                    <button data-target="#copy-blank-modal" class="copy-code button button--sm border flex items-center text-gray-700"> <i data-feather="file" class="w-4 h-4 mr-2"></i> Copy code </button>
                                    <div class="overflow-y-auto h-64 mt-3">
                                        <pre class="source-preview" id="copy-blank-modal"> <code class="text-xs p-0 rounded-md html pl-5 pt-8 pb-4 -mb-10 -mt-10"> HTMLOpenTagdiv class=&quot;text-center&quot;HTMLCloseTag HTMLOpenTaga href=&quot;javascript:;&quot; data-toggle=&quot;modal&quot; data-target=&quot;#basic-modal-preview&quot; class=&quot;button inline-block bg-theme-1 text-white&quot;HTMLCloseTagShow ModalHTMLOpenTag/aHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;modal&quot; id=&quot;basic-modal-preview&quot;HTMLCloseTag HTMLOpenTagdiv class=&quot;modal__content p-10 text-center&quot;HTMLCloseTag This is totally awesome blank modal! HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag </code> </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
        


<div class="intro-y box mt-5">
                            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                                <h2 class="font-medium text-base mr-auto">
                                    Header & Footer Modal
                                </h2>
                                <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                                    <div class="mr-3">Show code</div>
                                    <input data-target="#header-footer-modal" class="show-code input input--switch border" type="checkbox">
                                </div>
                            </div>
                            <div class="p-5" id="header-footer-modal">
                                <div class="preview">
                                    <div class="text-center"> <a href="javascript:;" data-toggle="modal" data-target="#header-footer-modal-preview" class="button inline-block bg-theme-1 text-white">Show Modal</a> </div>
                                    <div class="modal" id="header-footer-modal-preview">
                                        <div class="modal__content">
                                            <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200">
                                                <h2 class="font-medium text-base mr-auto">
                                                    Broadcast Message
                                                </h2>
                                                <button class="button border items-center text-gray-700 hidden sm:flex"> <i data-feather="file" class="w-4 h-4 mr-2"></i> Download Docs </button>
                                                <div class="dropdown relative sm:hidden">
                                                    <a class="dropdown-toggle w-5 h-5 block" href="javascript:;"> <i data-feather="more-horizontal" class="w-5 h-5 text-gray-700"></i> </a>
                                                    <div class="dropdown-box mt-5 absolute w-40 top-0 right-0 z-20">
                                                        <div class="dropdown-box__content box p-2">
                                                            <a href="javascript:;" class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md"> <i data-feather="file" class="w-4 h-4 mr-2"></i> Download Docs </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label>From</label>
                                                    <input type="text" class="input w-full border mt-2 flex-1" placeholder="example@gmail.com">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label>To</label>
                                                    <input type="text" class="input w-full border mt-2 flex-1" placeholder="example@gmail.com">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label>Subject</label>
                                                    <input type="text" class="input w-full border mt-2 flex-1" placeholder="Important Meeting">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label>Has the Words</label>
                                                    <input type="text" class="input w-full border mt-2 flex-1" placeholder="Job, Work, Documentation">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label>Doesn't Have</label>
                                                    <input type="text" class="input w-full border mt-2 flex-1" placeholder="Job, Work, Documentation">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label>Size</label>
                                                    <select class="input w-full border mt-2 flex-1">
                                                        <option>10</option>
                                                        <option>25</option>
                                                        <option>35</option>
                                                        <option>50</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="px-5 py-3 text-right border-t border-gray-200">
                                                <button type="button" data-dismiss="modal" class="button w-20 border text-gray-700 mr-1">Cancel</button>
                                                <button type="button" class="button w-20 bg-theme-1 text-white">Send</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="source-code hidden">
                                    <button data-target="#copy-header-footer-modal" class="copy-code button button--sm border flex items-center text-gray-700"> <i data-feather="file" class="w-4 h-4 mr-2"></i> Copy code </button>
                                    <div class="overflow-y-auto h-64 mt-3">
                                        <pre class="source-preview" id="copy-header-footer-modal"> <code class="text-xs p-0 rounded-md html pl-5 pt-8 pb-4 -mb-10 -mt-10"> HTMLOpenTagdiv class=&quot;text-center&quot;HTMLCloseTag HTMLOpenTaga href=&quot;javascript:;&quot; data-toggle=&quot;modal&quot; data-target=&quot;#header-footer-modal-preview&quot; class=&quot;button inline-block bg-theme-1 text-white&quot;HTMLCloseTagShow ModalHTMLOpenTag/aHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;modal&quot; id=&quot;header-footer-modal-preview&quot;HTMLCloseTag HTMLOpenTagdiv class=&quot;modal__content&quot;HTMLCloseTag HTMLOpenTagdiv class=&quot;flex items-center px-5 py-5 sm:py-3 border-b border-gray-200&quot;HTMLCloseTag HTMLOpenTagh2 class=&quot;font-medium text-base mr-auto&quot;HTMLCloseTagBroadcast MessageHTMLOpenTag/h2HTMLCloseTag HTMLOpenTagbutton class=&quot;button border items-center text-gray-700 hidden sm:flex&quot;HTMLCloseTag HTMLOpenTagi data-feather=&quot;file&quot; class=&quot;w-4 h-4 mr-2&quot;HTMLCloseTagHTMLOpenTag/iHTMLCloseTag Download Docs HTMLOpenTag/buttonHTMLCloseTag HTMLOpenTagdiv class=&quot;dropdown relative sm:hidden&quot;HTMLCloseTag HTMLOpenTaga class=&quot;dropdown-toggle w-5 h-5 block&quot; href=&quot;javascript:;&quot;HTMLCloseTag HTMLOpenTagi data-feather=&quot;more-horizontal&quot; class=&quot;w-5 h-5 text-gray-700&quot;HTMLCloseTagHTMLOpenTag/iHTMLCloseTag HTMLOpenTag/aHTMLCloseTag HTMLOpenTagdiv class=&quot;dropdown-box mt-5 absolute w-40 top-0 right-0 z-20&quot;HTMLCloseTag HTMLOpenTagdiv class=&quot;dropdown-box__content box p-2&quot;HTMLCloseTag HTMLOpenTaga href=&quot;javascript:;&quot; class=&quot;flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md&quot;HTMLCloseTag HTMLOpenTagi data-feather=&quot;file&quot; class=&quot;w-4 h-4 mr-2&quot;HTMLCloseTagHTMLOpenTag/iHTMLCloseTag Download Docs HTMLOpenTag/aHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;p-5 grid grid-cols-12 gap-4 row-gap-3&quot;HTMLCloseTag HTMLOpenTagdiv class=&quot;col-span-12 sm:col-span-6&quot;HTMLCloseTag HTMLOpenTaglabelHTMLCloseTagFromHTMLOpenTag/labelHTMLCloseTag HTMLOpenTaginput type=&quot;text&quot; class=&quot;input w-full border mt-2 flex-1&quot; placeholder=&quot;example@gmail.com&quot;HTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;col-span-12 sm:col-span-6&quot;HTMLCloseTag HTMLOpenTaglabelHTMLCloseTagToHTMLOpenTag/labelHTMLCloseTag HTMLOpenTaginput type=&quot;text&quot; class=&quot;input w-full border mt-2 flex-1&quot; placeholder=&quot;example@gmail.com&quot;HTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;col-span-12 sm:col-span-6&quot;HTMLCloseTag HTMLOpenTaglabelHTMLCloseTagSubjectHTMLOpenTag/labelHTMLCloseTag HTMLOpenTaginput type=&quot;text&quot; class=&quot;input w-full border mt-2 flex-1&quot; placeholder=&quot;Important Meeting&quot;HTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;col-span-12 sm:col-span-6&quot;HTMLCloseTag HTMLOpenTaglabelHTMLCloseTagHas the WordsHTMLOpenTag/labelHTMLCloseTag HTMLOpenTaginput type=&quot;text&quot; class=&quot;input w-full border mt-2 flex-1&quot; placeholder=&quot;Job, Work, Documentation&quot;HTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;col-span-12 sm:col-span-6&quot;HTMLCloseTag HTMLOpenTaglabelHTMLCloseTagDoesn&#039;t HaveHTMLOpenTag/labelHTMLCloseTag HTMLOpenTaginput type=&quot;text&quot; class=&quot;input w-full border mt-2 flex-1&quot; placeholder=&quot;Job, Work, Documentation&quot;HTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;col-span-12 sm:col-span-6&quot;HTMLCloseTag HTMLOpenTaglabelHTMLCloseTagSizeHTMLOpenTag/labelHTMLCloseTag HTMLOpenTagselect class=&quot;input w-full border mt-2 flex-1&quot;HTMLCloseTag HTMLOpenTagoptionHTMLCloseTag10HTMLOpenTag/optionHTMLCloseTag HTMLOpenTagoptionHTMLCloseTag25HTMLOpenTag/optionHTMLCloseTag HTMLOpenTagoptionHTMLCloseTag35HTMLOpenTag/optionHTMLCloseTag HTMLOpenTagoptionHTMLCloseTag50HTMLOpenTag/optionHTMLCloseTag HTMLOpenTag/selectHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;px-5 py-3 text-right border-t border-gray-200&quot;HTMLCloseTag HTMLOpenTagbutton type=&quot;button&quot; class=&quot;button w-20 border text-gray-700 mr-1&quot;HTMLCloseTagCancelHTMLOpenTag/buttonHTMLCloseTag HTMLOpenTagbutton type=&quot;button&quot; class=&quot;button w-20 bg-theme-1 text-white&quot;HTMLCloseTagSendHTMLOpenTag/buttonHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag </code> </pre>
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
                                <?php 
                                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                                foreach ($dias as $dia): ?>
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

   

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>

    <!-- JavaScript de Bootstrap (jQuery primero, luego Popper.js, y luego Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
             
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=["your-google-map-api"]&libraries=places"></script>
        <script src="dist/js/app.js"></script>

</body>
</html>
