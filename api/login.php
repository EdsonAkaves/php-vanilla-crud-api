<?php 

require_once '../vendor/autoload.php';
require_once '../src/Database/conexao.php';
use Carbon\Carbon;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['Erro' => 'Método não permitido.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

$email = $dados['email'] ?? null;
$senha = $dados['senha'] ?? null;



if (empty($email) || empty($senha)) {
    http_response_code(400);
    echo json_encode(['erro' => "Email e senha são obrigatórios"]);
    exit;
}


try {

    $pdo = conectar();

    $sql = "SELECT id, nome, senha FROM clientes WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente || !password_verify($senha, $cliente['senha'])){
        http_response_code(401);
        echo json_encode(['erro' => 'Credenciais inválidas']);
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $expiraEm = Carbon::now()->addHours(1)->toDateTimeString();


    $sqlInsert = "INSERT INTO auth_tokens (id_cliente, token, data_expiracao ) VALUES (:id_cliente, :token, :data_expiracao)";
    $stmtInsert = $pdo->prepare($sqlInsert);

    $sucesso = $stmtInsert->execute([
        ':id_cliente' => $cliente['id'],
        ':token' => $token,
        ':data_expiracao' => $expiraEm
    ]);

    if($sucesso) {
        http_response_code(200);
        echo json_encode([
            'token' => $token,
            'expira_em' => $expiraEm
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível gerar o token de acesso.']);
    }

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['Erro' => 'Erro no banco de dados.']);

}