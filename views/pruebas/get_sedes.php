<?php
$dsn = 'mysql:host=localhost;dbname="dbasist";charset=utf8';
$username = 'root';
$password = '';



try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT id_sede, sede_nombre FROM sedes";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $options = "";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $options .= "<option value='" . htmlspecialchars($row['id_sede']) . "'>" . htmlspecialchars($row['sede_nombre']) . "</option>";
    }

    echo $options;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
