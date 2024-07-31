<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
} catch (PDOException $exception) {
    die($exception->getMessage());
}

$sql = 'SELECT id, name FROM cities WHERE country_id = '.$_GET['country_id'].' ORDER BY name';

try {
    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    die($exception->getMessage());
}

header('Content-Type: application/json');
echo json_encode($data);
