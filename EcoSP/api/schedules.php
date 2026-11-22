<?php

require_once 'categories.php';

header('Content-Type: application/json; charset=utf-8');

$data_atual = date('Y-m-d');
$valor_aleatorio = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    $dados = json_decode(file_get_contents('php://input'), true);

    if(!is_array($dados)){

        http_response_code(400);
        echo json_encode(['erro' => 'JSON inválido']);
        exit;

    }else{

        $citizen_name = $dados['citizen_name'] ?? '';
        $citizen_email = $dados['citizen_email'] ?? '';
        $ecopoint_id = $dados['ecopoint_id'] ?? '';
        $category_id = $dados['category_id'] ?? '';
        $weight_kg = $dados['weight_kg'] ?? '';
        $scheduled_date = $dados['scheduled_date'] ?? '';
        
        if($citizen_name == "" || $citizen_email == "" || $ecopoint_id == "" || $category_id == "" || $weight_kg == "" || $scheduled_date == ""){

            http_response_code(422);
            echo json_encode(['erro' => 'Preencha todos os campos.']);
            exit;

        }elseif (!filter_var($citizen_email, FILTER_VALIDATE_EMAIL)){

            http_response_code(422);
            echo json_encode(['erro' => 'E-mail inválido.']);
            exit;

        }elseif (!is_numeric($weight_kg) || $weight_kg <= 0){

            http_response_code(422);
            echo json_encode(['erro' => 'O peso deve ser maior que zero.']);
            exit;

        }elseif ($scheduled_date < $data_atual ){

            http_response_code(422);
            echo json_encode(['erro' => 'A data não pode ser menor que a data atual']);
            exit;
            
        }else{

            $stmt = $pdo->prepare(
            "SELECT categories.points_per_kg
            FROM ecopoint_categories
            INNER JOIN categories
            ON ecopoint_categories.category_id = categories.id
            WHERE ecopoint_categories.ecopoint_id = ?
            AND ecopoint_categories.category_id = ?");

            $stmt->execute([$ecopoint_id,$category_id]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado === false){

                http_response_code(422);
                echo json_encode([
                    'erro' => 'Você não pode descartar esse material nesse ecoponto.'
                ]);
                exit;
            
            }else{

                $points_per_kg = $resultado['points_per_kg'];
                $ecopoints_earned = round($weight_kg * $points_per_kg);

                do{
                    $caractere = "";
                    for($i = 0; $i <= 4; $i++):
                        $valor = random_int(0, strlen($valor_aleatorio) -1);
                        $caractere .= $valor_aleatorio[$valor];
                    endfor;

                    $protocolo = "ECO-" . date('Y') . "-" . $caractere; 

                    $stmt = $pdo->prepare("SELECT protocol FROM schedules WHERE protocol = ?");
                    $stmt->execute([$protocolo]);
                    $resultado_protocolo = $stmt->fetch(PDO::FETCH_ASSOC);
                }while($resultado_protocolo !== false);

                $stmt = $pdo->prepare("INSERT INTO schedules (protocol,citizen_name,citizen_email,ecopoint_id,category_id,weight_kg,ecopoints_earned,scheduled_date,status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->execute([$protocolo,$citizen_name,$citizen_email,$ecopoint_id,$category_id,$weight_kg,$ecopoints_earned,$scheduled_date, 'Pendente']);

                http_response_code(201);

                echo json_encode([
                    'mensagem' => 'Agendamento realizado com sucesso',
                    'agendamento' => [
                        'id' => $pdo->lastInsertId(),
                        'protocol' => $protocolo,
                        'citizen_name' => $citizen_name,
                        'citizen_email' => $citizen_email,
                        'ecopoint_id' => $ecopoint_id,
                        'category_id' => $category_id,
                        'weight_kg' => $weight_kg,
                        'ecopoints_earned' => $ecopoints_earned,
                        'scheduled_date' => $scheduled_date,
                        'status' => 'Pendente'
                    ]

                ]);

                exit;
            }
        
        }

    }
    
} else{

    http_response_code(405);
    echo json_encode([
        'erro' => 'Método HTTP não permitido'
    ]);
    exit;
}




