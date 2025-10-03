<?php 
require_once '../vendor/autoload.php';
require_once '../src/Database/conexao.php';
use Carbon\Carbon;

header('Content-type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
$email = $dados['email'] ?? null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Formato de email inválido.']);
    exit;
}

try {

    $pdo = conectar();

    $sql = "SELECT id FROM clientes WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        $token = bin2hex((random_bytes(32)));
        $expiraEm = Carbon::now()->addMinutes(15)->toDayDateTimeString();

        $sqlUpdate = "UPDATE clientes SET reset_token = :token, reset_token_expira_em = :expira_em WHERE id = :id";
        $stmt = $pdo->prepare($sqlUpdate);
        $stmt->execute([
            ':token' => $token,
            ':expira_em' => $expiraEm,
            ':id' => $cliente['id']
        ]);

        echo json_encode([
            'mensagem' => 'Se o e-mail existir, um link de reset foi enviado.',
            'token_para_teste' => $token // Em produção, NUNCA retornar o token aqui!            
        ]);
        exit;
    }

    echo json_encode(['mensagem' => 'Se o e-mail existir, um link de reset foi enviado.']);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no banco de dados']);
}
