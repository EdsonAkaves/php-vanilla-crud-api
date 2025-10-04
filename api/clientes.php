<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

try {
    $pdo = conectar();

    $stmt = $pdo->query("SELECT id, nome, email FROM clientes");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($clientes);

} catch (\PDOException $e) {

    http_response_code(500); 
    echo json_encode(['erro' => 'Falha ao conectar ao banco de dados']);
}