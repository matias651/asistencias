
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modal Dinámico</title>
    <!-- Incluye Bootstrap CSS para estilos rápidos -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Incluye jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

<!-- Botón para abrir el modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#sedeModal">
  Abrir Modal
</button>

<!-- Modal -->
<div class="modal fade" id="sedeModal" tabindex="-1" role="dialog" aria-labelledby="sedeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sedeModalLabel">Seleccionar Sede y Profesor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="sede">Sede</label>
                        <select class="form-control" id="sede" name="sede">
                            <!-- Opciones llenadas dinámicamente con PHP -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="profesor">Profesor</label>
                        <select class="form-control" id="profesor" name="profesor">
                            <!-- Opciones llenadas dinámicamente con AJAX -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios para Bootstrap y jQuery -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    // Llenar opciones de sede cuando se carga el modal
    $('#sedeModal').on('show.bs.modal', function (e) {
        $.ajax({
            url: 'get_sedes.php',
            type: 'GET',
            success: function(data) {
                $('#sede').html(data);
            }
        });
    });

    // Llenar opciones de profesor cuando se selecciona una sede
    $('#sede').change(function() {
        var sedeId = $(this).val();
        $.ajax({
            url: 'get_profesores.php',
            type: 'GET',
            data: { sede_id: sedeId },
            success: function(data) {
                $('#profesor').html(data);
            }
        });
    });
});
</script>

</body>
</html>
