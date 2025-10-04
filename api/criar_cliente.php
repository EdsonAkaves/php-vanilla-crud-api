<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'),  true);

if ($dados == null) {
    http_response_code(400);
    echo json_encode(['erro' => 'Corpo da requisição inválido ou vazio.']);
    exit;
}

$nome = $dados['nome'] ?? '';
$email = $dados['email'] ?? '';
$senha = $dados['senha'] ?? '';

if (empty(trim($nome)) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($senha)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados inválidos ou faltando.']);
    exit;
}


try {
    $senhaHasheada = password_hash($senha, PASSWORD_DEFAULT);

    $pdo = conectar();

    $sql = "INSERT INTO clientes (nome, email, senha) VALUES (:nome, :email, :senha)";

    $stmt = $pdo->prepare($sql);

    $sucesso = $stmt->execute(
        [
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senhaHasheada
        ]
    );


    if ($sucesso) {
        http_response_code(201);
        echo json_encode(['Sucesso' => 'Cliente criado.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível criar o cliente.']);
    }



} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no banco de dados']);
}