<?php
// Incluir el archivo de configuración de la base de datos
require '../../config.php';

try {
    // Consulta SQL para obtener las sedes
    $sql = "SELECT id_sede, sede_nombre FROM sedes";
    $stmt = $pdo->query($sql); // $pdo debe estar definido desde config.php
    $sedes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver las sedes en formato JSON
    header('Content-Type: application/json');
    echo json_encode($sedes);
} catch (PDOException $e) {
    // Manejo de errores de conexión o consulta
    die("Error de conexión: " . $e->getMessage());
}
?>




<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ejemplo de Modal con Lista Desplegable</title>
<style>
/* Estilos para el modal */
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.4);
}

.modal-content {
  background-color: #fefefe;
  margin: 15% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  max-width: 600px;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style>
</head>
<body>

<!-- Botón para abrir el modal -->
<button id="openModalBtn">Abrir Modal</button>

<!-- Modal -->
<div id="myModal" class="modal">
  <!-- Contenido del modal -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Selecciona una sede</h2>
    <select id="sedeSelect">
      <!-- Opciones de sedes se cargarán aquí dinámicamente -->
    </select>
    <button id="selectSedeBtn">Seleccionar</button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('myModal');
  const btn = document.getElementById('openModalBtn');
  const closeBtn = document.getElementsByClassName('close')[0];
  const selectBtn = document.getElementById('selectSedeBtn');
  const sedeSelect = document.getElementById('sedeSelect');

  // Función para abrir el modal
  btn.onclick = function() {
    modal.style.display = 'block';
  }

  // Función para cerrar el modal al hacer clic en la X
  closeBtn.onclick = function() {
    modal.style.display = 'none';
  }

  // Función para cerrar el modal al hacer clic fuera del contenido
  window.onclick = function(event) {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  }

  // Evento para seleccionar una sede y hacer algo con ella
  selectBtn.onclick = function() {
    const selectedSede = sedeSelect.value;
    // Aquí puedes agregar la lógica para manejar la sede seleccionada
    console.log('Sede seleccionada:', selectedSede);
    modal.style.display = 'none'; // Cerrar el modal después de seleccionar
  }

  // Realizar una solicitud para obtener las sedes desde el servidor
  fetch('get_sedes.php')
    .then(response => response.json())
    .then(data => {
      data.forEach(sede => {
        const option = document.createElement('option');
        option.value = sede.id_sede;
        option.textContent = sede.sede_nombre;
        sedeSelect.appendChild(option);
      });
    })
    .catch(error => console.error('Error al obtener sedes:', error));
});
</script>

</body>
</html>
