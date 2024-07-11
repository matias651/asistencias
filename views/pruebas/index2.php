<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal con Profesores Disponibles</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<!-- Botón para abrir el modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#profesoresModal">
    Ver Profesores Disponibles
</button>

<!-- Modal -->
<div class="modal fade" id="profesoresModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Profesores Disponibles</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario de selección de día y hora -->
                <form id="formSeleccion">
                    <div class="form-group">
                        <label for="dia">Día</label>
                        <select id="dia" class="form-control">
                            <option value="lunes">Lunes</option>
                            <option value="martes">Martes</option>
                            <option value="miércoles">Miércoles</option>
                            <option value="jueves">Jueves</option>
                            <option value="viernes">Viernes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hora">Hora</label>
                        <select id="hora" class="form-control">
                            <!-- Las opciones de hora se rellenarán dinámicamente con PHP -->
                            <?php
                            include 'config.php';

                            $sqlHoras = "SELECT id_hora, hora FROM horas";
                            $stmtHoras = $conexion->prepare($sqlHoras);
                            $stmtHoras->execute();
                            $horas = $stmtHoras->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($horas as $hora) {
                                echo "<option value='" . $hora['id_hora'] . "'>" . $hora['hora'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
                <!-- Contenedor para la lista de profesores disponibles -->
                <div id="listaProfesores">
                    Seleccione un día y una hora para ver los profesores disponibles.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#dia, #hora').change(function() {
        var dia = $('#dia').val();
        var hora = $('#hora').val();

        $.ajax({
            url: 'obtener_profesores.php',
            type: 'POST',
            data: {
                dia: dia,
                hora: hora
            },
            success: function(response) {
                $('#listaProfesores').html(response);
            }
        });
    });
});
</script>
</body>
</html>
