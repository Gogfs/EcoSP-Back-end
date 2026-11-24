<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../conexao.php";

if($_SERVER["REQUEST_METHOD"] !== "GET"){

    http_response_code(405);

    echo json_encode(["erro" => "Metodo nao permitido"]);
    exit;
}
    try{
        //consulta de tudo
        $sql = "SELECT id, name, address, neighborhood, latitude, longitude, operating_hours, contact_phone FROM ecopoints WHERE 1=1";
        
        $params = [];
        
        //filtracao por bairro
        if(!empty($_GET['neighborhood'])) {
            $sql .= " AND neighborhood LIKE ?";

            $params[] = "%" . $_GET['neighborhood'] . "%";
        }

        //por categoria
        if(isset($_GET['category_id'])) {
            $category_id = filter_var($_GET['category_id'], FILTER_VALIDATE_INT);

            if($category_id === false || $category_id <= 0){
                http_response_code(400);
                echo json_encode([ "erro" => "Categoria inválida"]);

                exit;
            }
        
            $sql .=" AND EXISTS( SELECT 1 FROM ecopoint_categories
            WHERE ecopoint_categories.ecopoint_id = ecopoints.id
            AND ecopoint_categories.category_id = ?)";

            $params[] = $category_id;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $ecopoints = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($ecopoints);

    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([ "Erro ao consultar os ecopontos."]);
    }


?>