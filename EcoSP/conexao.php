<?php
try {

$pdo = new PDO("mysql:host=localhost;dbname=ecosp_db;charset=utf8mb4","root","");

$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

} catch (PDOException $e) {
    http_response_code(500);

    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode([
        "erro" => "Erro ao conectar com o banco de dados."
    ]);

    exit;
}