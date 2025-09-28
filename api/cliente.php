<?php 

require_once '../vendor/autoload.php';
require_once '../src/Database/conexao.php';

$id = $_GET['id'] ?? null; 

if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['erro' => 'ID do cliente é inválido ou não foi fornecido.']);
    exit;  
};

try {

$pdo = conectar();

$sql = "SELECT id, nome, email FROM clientes WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');

if ($cliente) {
    echo json_encode($cliente);
} else {
    http_response_code(404);
    echo json_encode(['erro' =>  'Cliente não encontrado.']);
}

} catch (\PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');

    echo json_encode(['erro' => 'Falha no servidor.']);
    
}