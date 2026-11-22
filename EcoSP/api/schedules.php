<?php

require_once 'categories.php'
$citizen_name = "";
$citizen_email = "";
$ecopoint = "";
$category_id = "";
$weight_kg = "";
$scheduled_date = "";
$mensagem = "";
$data_atual = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    $citizen_name = $_POST['citizen_name'];
    $citizen_email = $_POST['citizen_email'];
    $ecopoint = $_POST['ecopoint'];
    $category_id = $_POST['category_id'];
    $weight_kg = $_POST['weight_kg'];
    $scheduled_date = $_POST['scheduled_date'];

    /*if (strpos($citizen_email, '.') !== 0 && strpos($citizen_email,'@') - 1 !== strpos($citizen_email,'.')){
        if (strpos($citizen_email,'@') !== 0 && strpos($citizen_email,'@') !== false && strpos($citizen_email,'@') < strlen($citizen_email) - 1 && substr_count($citizen_email, '@') == 1){
        $domain = substr($citizen_email, strpos($citizen_email, '@') + 1);
        echo $domain;

            if(strrpos($domain, '.') !== false && strrpos($domain, '.') !== 0 && strrpos($domain,'.') < strlen($domain) - 1 && str_contains($domain, '..') !== true){
                echo "O domínio contém ponto e não está nem no final e nem no começo";
                $extensao = substr($domain, strrpos($domain, '.') + 1);
                if(strlen($extensao) < 2){
                    echo " Não Há pelo menos dois caracteres após o último ponto";
                }else{
                    echo "Há pelo menos dois caracteres após o último ponto";
                }
            }else{
                echo "O dominio não contèm ponto";
            }
        }else{
        echo "O email não possui um domínio válido";
        }
    }*/
    
    if($citizen_name == "" || $citizen_email == "" || $ecopoint == "" || $category_id == ""; || $weight_kg == "" || $scheduled_date == ""){
        $mensagem = "Preencha todos os campos";
    }elseif (!filter_var($citizen_email, FILTER_VALIDATE_EMAIL)){
        $mensagem = "Coloque um email válido";
    }elseif ($weight_kg <= 0){
        $mensagem = "O peso deve ser maior que zero!";
    }elseif ($scheduled_date < $data_atual ){
        $mensagem = "A data não pode ser menor que a data atual";
    }elseif (){
        $ecopoints_earned = round(weight_kg * points_per_kg);
        
    }




}

