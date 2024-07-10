<?php
require_once 'config.php';  // Incluir configuración de la base de datos y otras configuraciones

// Verificar si se ha enviado una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener la tabla y el ID del registro desde los parámetros POST
    $table = isset($_POST['table']) ? $_POST['table'] : '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Validar los parámetros
    if (empty($table) || $id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Parámetros inválidos']);
        exit;
    }

    // Asegurarse de que la tabla es una de las permitidas
    $allowedTables = ['profesores', 'asignaturas', 'horarios', 'sedes'];
    if (!in_array($table, $allowedTables)) {
        echo json_encode(['status' => 'error', 'message' => 'Tabla no permitida']);
        exit;
    }

    try {
        // Preparar y ejecutar la consulta de eliminación
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Registro no encontrado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la eliminación: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido']);
}
?>