<?php
// Incluir la conexión a la base de datos u otras configuraciones necesarias
require_once 'config.php';

// Verificar si se está recibiendo una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el ID del profesor a eliminar, si está presente
    $professor_id = isset($_POST['professor_id']) ? $_POST['professor_id'] : null;

    try {
        if ($professor_id) {
            // Intentar eliminar al profesor
            $sql = "DELETE FROM profesores WHERE id_profesor = :professor_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Eliminación exitosa
                $response = ['success' => true, 'message' => 'Profesor eliminado correctamente'];
                echo json_encode($response);
            } else {
                // Error al eliminar
                http_response_code(500); // Código de error interno del servidor
                $response = ['success' => false, 'message' => 'Error al eliminar el profesor'];
                echo json_encode($response);
            }
        } else {
            // Datos insuficientes
            http_response_code(400); // Código de error de solicitud incorrecta
            $response = ['success' => false, 'message' => 'ID de profesor no proporcionado'];
            echo json_encode($response);
        }
    } catch (PDOException $e) {
        // Error de la base de datos
        http_response_code(500); // Código de error interno del servidor
        $response = ['success' => false, 'message' => 'Error de base de datos al eliminar el profesor'];
        echo json_encode($response);
    }
} else {
    // Método de solicitud no permitido
    http_response_code(405); // Código de error de método no permitido
    $response = ['success' => false, 'message' => 'Método no permitido'];
    echo json_encode($response);
}
?>
