<?php
require_once __DIR__ . '/../bootstrap.php';
use Carbon\Carbon;

function autenticar(): int
{
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? null;

    if ($authHeader === null) {
        http_response_code(401);
        echo json_encode(['erro' => 'Token de autenticação não fornecido.']);
        exit;
    }

    $partes = explode(' ', $authHeader);
    if (count($partes) !== 2 || $partes[0] !== 'Bearer') {
        http_response_code(401);
        echo json_encode(['erro' => 'Formato do token inválido.']);
        exit;
    }

    $token = $partes[1];

    try {
        $pdo = conectar();

        $agora = Carbon::now()->toDateString();

        $sql = "SELECT id_cliente FROM auth_tokens WHERE token = :token AND data_expiracao > :agora";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':token' => $token,
            ':agora' => $agora
        
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            http_response_code(401);
            echo json_encode(['erro' => 'Token inválido ou expirado.']);
            exit;
        }

        return (int)$resultado['id_cliente'];

    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro no servidor ao validar o token.']);
        exit;
    }
}