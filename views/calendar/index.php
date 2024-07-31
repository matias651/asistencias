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

        $.ajax({
            url: 'get_horas.php',
            type: 'GET',
            success: function(data) {
                $('#hora').html(data);
            }
        });
    });

    // Llenar opciones de profesor cuando se selecciona una sede, día y hora
    $('#sede, #dia, #hora').change(function() {
        var sedeId = $('#sede').val();
        var dia = $('#dia').val();
        var hora = $('#hora').val();
        $.ajax({
            url: 'get_profesores.php',
            type: 'GET',
            data: { sede_id: sedeId, dia: dia, hora: hora },
            success: function(data) {
                $('#profesor').html(data);
            }
        });
    });
});
</script>

</body>
</html>
