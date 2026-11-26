<?php

require_once '../../conexao.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $dados = json_decode(file_get_contents('php://input'), true);
    echo json_encode(['dados' => $dados]);

    if(!is_array($dados)){

        http_response_code(400);
        echo json_encode(['erro' => 'JSON inválido']);
        exit;

    }else{

        $dir = "uploads/";
        $protocol = $dados['protocol'] ?? '';
        $file = $dir . basename($_FILES["file_image"]["name"]);

        if(!empty($protocol) && !empty($file)){

            $consulta = "SELECT protocol FROM schedules WHERE protocol = ?";

            $stmt = $pdo->prepare($consulta);
            $stmt->execute($protocol);

            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if($resultado !== false && move_uploaded_file($_FILES["file_image"]["tmp_name"], $file)){

                $inserir = "UPDATE schedules SET photo_path = ? WHERE protocol = ?";
                $stmt = $pdo->prepare($inserir);
                $stmt->execute($file,$protocol);

                http_response_code(201);
                echo json_encode([
                    "message:" => "Foto do resíduo vinculada com sucesso ao agendamento!",
                    "protocol:" => $protocol,
                    "photo_path:" => $file  
                ]);
                exit;

            }else{
                http_response_code(404);
                echo json_encode(["erro" => "O protocolo informado não existe no banco de dados"]);
                exit;
            }

        }else{

            http_response_code(422);
            echo json_encode(["erro" => "Preencha todos os campos"]);
            exit;
        }

    }
}

