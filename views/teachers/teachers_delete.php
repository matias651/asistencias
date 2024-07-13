<?php
// Verificar si se ha pasado el ID del profesor a eliminar
if (isset($_GET['id_profesor'])) {
    $id_profesor = (int) $_GET['id_profesor'];

    // Incluir el archivo de configuración para conectarse a la base de datos
    require_once __DIR__ . "/../../config.php"; // Asegúrate de que la ruta sea correcta

    // Preparar la consulta SQL para eliminar el profesor
    $sql = "DELETE FROM profesores WHERE id_profesor = :id_profesor";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_profesor', $id_profesor, PDO::PARAM_INT);

    // Ejecutar la consulta y verificar si se eliminó correctamente
    if ($stmt->execute()) {
        // Redirigir de vuelta a la página de listado de profesores con un mensaje de éxito
        header("Location: teachers_list.php");
    } else {
        // Redirigir de vuelta a la página de listado de profesores con un mensaje de error
        header("Location: teachers_list.php");
    }
} else {
    // Redirigir de vuelta a la página de listado de profesores si no se pasó un ID válido
    header("Location: teachers_list.php");
}
?>
