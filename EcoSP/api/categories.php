<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../conexao.php";

// Verifica se o método utilizado é GET
if($_SERVER["REQUEST_METHOD"] !== "GET"){
    http_response_code(405);

    echo json_encode([
        "erro" => "Método não permitido";
    ]);

    exit;
}

try {
    // Consulta todas as categorias

    $sql = "SELECT id, name, slug, points_per_kg, icon_name FROM categories";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
}