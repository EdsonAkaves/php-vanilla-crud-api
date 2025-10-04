<?php 
require_once __DIR__ . '/../bootstrap.php';
require_once 'auth.php';


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método inválido.']);
    exit;
}

$usuarioAutenticadoId = autenticar();

$idParaDeletar = $_GET['id'] ?? null;
if (!$idParaDeletar || !filter_var($idParaDeletar, FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id com formato inválido.']);
    exit;
}

$idParaDeletar = (int)$idParaDeletar;


if ($idParaDeletar !== $usuarioAutenticadoId) {
    http_response_code(403);
    echo json_encode(['erro' => 'Você não tem permissão para deletar este usuário.']);
    exit;
}


try {

    $pdo = conectar();

    $sql1 = "DELETE FROM pedidos WHERE id_cliente = :id_cliente";
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->execute([
        ':id_cliente' => $idParaDeletar
    ]);

    $sql2 = "DELETE FROM clientes WHERE id = :id";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute([
        ':id' => $idParaDeletar
    ]);

    if ($stmt2->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['erro' => 'Nenhum cliente encontrado com esse ID.']);
        exit;
    }


    if ($stmt2->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(['Sucesso' => 'Cliente deletado.']);
        exit;
    }



} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no banco de dados']);
    exit;
}