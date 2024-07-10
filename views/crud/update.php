<?php
require_once 'config.php';  // Incluir configuración de la base de datos y otras configuraciones

// Verificar si se ha enviado una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener la tabla, el ID del registro y los datos del formulario desde los parámetros POST
    $table = isset($_POST['table']) ? $_POST['table'] : '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $data = isset($_POST['data']) ? $_POST['data'] : [];

    // Validar los parámetros
    if (empty($table) || $id <= 0 || empty($data)) {
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
        // Construir la consulta de actualización dinámicamente
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = :$key";
        }
        $setClause = implode(", ", $setClauses);
        $stmt = $pdo->prepare("UPDATE $table SET $setClause WHERE id = :id");

        // Vincular los valores a la consulta
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        // Ejecutar la consulta
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Registro actualizado con éxito']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Registro no encontrado o no actualizado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la actualización: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido']);
}
