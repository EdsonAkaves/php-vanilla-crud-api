<?php 

function conectar(): PDO 
{
    $config = require __DIR__ . '/../../config/database.php';

    $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset=utf8";

    try {
        $pdo = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        return $pdo;

    } catch (PDOException $e) {
        die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
    }
}