<?php
require_once 'config.php';  // Incluir configuración de la base de datos y otras configuraciones

// Verificar si se ha enviado una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener la tabla y los datos del formulario desde los parámetros POST
    $table = isset($_POST['table']) ? $_POST['table'] : '';
    $data = isset($_POST['data']) ? $_POST['data'] : [];

    // Validar los parámetros
    if (empty($table) || empty($data)) {
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
        // Construir la consulta de inserción dinámicamente
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $stmt = $pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");

        // Vincular los valores a la consulta
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        // Ejecutar la consulta
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Registro agregado con éxito']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la inserción: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido']);
}
