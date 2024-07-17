<?php
// Incluye el archivo de configuración y la conexión a la base de datos
require_once __DIR__ . "/../../config.php"; // Utilizando una ruta absoluta

// Verificar si se recibió una solicitud POST para agregar un profesor
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';
    $documento = $_POST['documento'] ?? '';
    $sede = $_POST['sede'] ?? '';
    $saldo = $_POST['saldo'] ?? '';
    $programa = $_POST['programa'] ?? '';

    // Validar y sanitizar los datos (puedes implementar validaciones más robustas aquí)

    // Preparar la consulta SQL para insertar un nuevo profesor
    $sql = "INSERT INTO profesores (profesor_nombre, profesor_apellido, profesor_email, profesor_documento, profesor_sede, profesor_saldo, profesor_programa) 
            VALUES (:nombre, :apellido, :email, :documento, :sede, :saldo, :programa)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':documento', $documento, PDO::PARAM_STR);
    $stmt->bindParam(':sede', $sede, PDO::PARAM_INT);
    $stmt->bindParam(':saldo', $saldo, PDO::PARAM_STR);
    $stmt->bindParam(':programa', $programa, PDO::PARAM_INT);

    // Ejecutar la consulta
    try {
        $stmt->execute();
        // Redirigir a la página de lista de profesores después de agregar correctamente
        header("Location: teachers_list.php");
        exit();
    } catch (PDOException $e) {
        // Manejar errores de PDO (por ejemplo, mostrar un mensaje de error)
        echo "Error al agregar el profesor: " . $e->getMessage();
    }
}
?>
