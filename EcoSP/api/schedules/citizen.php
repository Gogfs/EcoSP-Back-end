<?php

require_once '../../conexao.php';

header('Content-Type: application/json; charset=utf-8');

// permitir somente requisições GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'erro' => 'Método HTTP não permitido.'
    ]);
    exit;
}

// verificar se o e-mail é válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode([
        'erro' => 'E-mail inválido.'
    ]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        MAX(citizen_name) AS citizen_name,
        SUM(ecopoints_earned) AS total_ecopoints,
        SUM(weight_kg) AS total_weight_recycled_kg,
        COUNT(*) AS schedules_count FROM schedules WHERE citizen_email = ?
        
");

$stmt->execute([$email]);
$totais = $stmt->fetch(PDO::FETCH_ASSOC);

//verificacao para identificar se o cidadao possui historico
if($totais['schedules_count'] == 0){
    http_response_code(404);
    echo json_encode([
        'mensagem' => 'Nenhum agendamento encontrado para este cidadão.'
    ]);
    exit;
}

//buscando historico completo do cidadao
$stmt = $pdo->prepare("
    SELECT
        schedules.protocol,
        ecopoints.name AS ecopoint_name,
        categories.name AS category_name,
        schedules.weight_kg,
        schedules.ecopoints_earned,
        schedules.scheduled_date,
        schedules.status,
        schedules.photo_path 
        FROM schedules 
        INNER JOIN ecopoints 
            ON schedules.ecopoint_id = ecopoints.id
        INNER JOIN categories
            ON schedules.category_id = categories.id
        WHERE schedules.citizen_email = ?
        ORDER BY schedules.scheduled_date DESC

")

$stmt->execute([$email]);

$historico = $stmt->fetchAll(PDO::FETCH_ASSOC);

//montando a resposta
http_response_code(200);

echo json_encode([
    'citizen_email' => $email,
    'citizen_name' => $totais['citizen_name'],
    'total_ecopoints' => (int) $totais['total_ecopoints'],
    'total_weight_recycled_kg' => (float) $totais['total_weight_recycled_kg'],
    'schedules_count' => (int) $totais['schedules_count'],
    'history' => array_map(function ($item){
        $item['weight_kg'] = (float) $item['weight_kg'];
        $item['ecopoints_earned'] = (int) $item['ecopoints_earned'];

        return $item;
        }, $historico)
], JSON_UNESCAPED_UNICODE);

?>