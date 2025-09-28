<?php 

require_once '../vendor/autoload.php';
require_once '../src/Database/conexao.php';

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    http_response_code(400);
    json_encode(['erro' => 'Id do cliente é inválido ou não foi fornecido.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
if (!$dados === null) {
    http_response_code(400);
    echo json_encode(['erro' => 'Corpo da requisição inválido ou vazio.']);
}

$camposParaAtualizar = [];
$parametros = [];

if (!empty(trim($dados['nome'] ?? ''))) {
    $camposParaAtualizar[] = "nome = :nome";
    $parametros[':nome'] = trim($dados['nome']);
}

if (!empty($dados['email'] ?? '') && filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
    $camposParaAtualizar[] = "email = :email";
    $parametros[':email'] = $dados['email'];
}

if (empty($camposParaAtualizar)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados não enviados.']);
    exit;
}


try {
    $pdo = conectar();

    $sql = "UPDATE clientes SET " . implode(', ', $camposParaAtualizar) .  " WHERE  id = :id";
    $parametros[':id'] = $id;

    $stmt = $pdo->prepare($sql);

    $stmt->execute($parametros);

    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(['Sucesso' => 'Cliente alterado com sucesso.']);   
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Cliente não encontrado ou nenhum dado foi alterado.']);
    }

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no banco de dados']);
}