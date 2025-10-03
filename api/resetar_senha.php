<?php 

require_once '../vendor/autoload.php';
require_once '../src/Database/conexao.php';
use Carbon\Carbon;

header('Content-type: application-json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
$token = $dados['token'] ?? null;
$novaSenha = $dados['senha'] ?? null;

if (!$token || !$novaSenha || strlen($novaSenha) < 6) {
    http_response_code(400);
    echo json_encode(['erro' => 'Token e uma senha com no mínimo 6 caracteres são necessários.']);
    exit;
}

try {
    $pdo = conectar();
    $agora = Carbon::now()->toDateTimeString();

    $sql = "SELECT id from clientes WHERE reset_token = :token AND reset_token_expira_em > :agora";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':token' => $token,
        ':agora' => $agora
    ]);
    $cliente = $stmt->fetch((PDO::FETCH_ASSOC));

    if (!$cliente) {
        http_response_code(400);
        echo json_encode(['erro' => 'Token de reset inválido ou expirado.']);
        exit;
    }

    $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

    $sqlUpdate = "UPDATE clientes SET senha = :senha, reset_tokenn = NULL, reset_token_expira_em = NULL WHERE id = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([
        ':senha' => $novaSenhaHash,
        ':id' => $cliente['id']
    ]);

    http_response_code(200);
    echo json_encode(['sucesso' => 'Senha redefinida com sucesso.']);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no banco de dados.']);
}

