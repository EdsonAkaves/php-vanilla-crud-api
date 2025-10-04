<?php 

require_once __DIR__ . '/../bootstrap.php';
require_once 'auth.php';

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$usuarioAutenticadoId = autenticar();

$idParaAtualizar = $_GET['id'] ?? null;
if (!$idParaAtualizar || !filter_var($idParaAtualizar, FILTER_VALIDATE_INT)) {
    http_response_code(400);
    json_encode(['erro' => 'Id do cliente é inválido ou não foi fornecido.']);
    exit;
}

$idParaAtualizar = (int)$idParaAtualizar;

if ($idParaAtualizar !== $usuarioAutenticadoId){
    http_response_code(403);
        echo json_encode(['erro' => 'Você não tem permissão para atualizar este usuário.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
if ($dados === null) {
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

if (!empty($dados['senha'] ?? '')) {
    $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);
    $camposParaAtualizar[] = "senha = :senha";
    $parametros[':senha'] = $senhaHash;
}

if (empty($camposParaAtualizar)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados não enviados.']);
    exit;
}


try {
    $pdo = conectar();

    $sql = "UPDATE clientes SET " . implode(', ', $camposParaAtualizar) .  " WHERE  idParaAtualizar = :idParaAtualizar";
    $parametros[':idParaAtualizar'] = $idParaAtualizar;

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