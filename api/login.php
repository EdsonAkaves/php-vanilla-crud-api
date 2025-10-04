<?php 
require_once __DIR__ . '/../bootstrap.php';
use Carbon\Carbon;
use Firebase\JWT\JWT;

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


        $chaveSecreta = $_ENV['JWT_SECRET_KEY'];

        $payload = [
            'iss' => 'http://localhost/php-vanilla-crud-api',
            'aud' => 'http://localhost/php-vanilla-crud-api',
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addHour()->timestamp,
            'sub' => $cliente['id']
        ];


        $jwt = JWT::encode($payload, $chaveSecreta, 'HS256');

        http_response_code(200);
        echo json_encode(['token' => $jwt]);



} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['Erro' => 'Erro no banco de dados.']);

}