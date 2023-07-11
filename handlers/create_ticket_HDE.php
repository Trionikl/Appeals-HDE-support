<?php
    //получить почту текущего пользователя
    $result = CRest::call(
    'user.get',
    [
    "ID" => $_REQUEST['data']['USER']['ID']
    ], _REQUEST["auth"]);
    $email_user = $result["result"][0]["EMAIL"];
    
    //Получить контакт клиента, отправившего заявку
    $query_str = http_build_query(array(
    'search' => $email_user
    ));
    
    //получить id пользователя в HDE
    $ch = curl_init('https://**************/api/v2/users/?' . $query_str);
    $headers = array(
    AUTHORIZATION_HDE,
    "Cache-Control: no-cache"
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $aResult = json_decode($response);
    $id_user_hde = $aResult->data[0]->id;
    
    /*проверить Статус вопросов, если есть открытые
        вопросы, то задать в первый открытый вопрос вопрос, 
        если открытых вопросов нет - создать новый вопрос
    */
    //запрос к внешнему API  
    
    if ($currentQuestion == "none") { // тикета со статусом открыт - нет - создать новый тикет
        //если пользователя нет, создать нового
        if (!$id_user_hde) {
            include(__DIR__ . '/create_user.php');
        }
        
        //подготовка данных к отправке вопроса в HDE - если пользователь существует
        $arData = array(
        "title" => $_REQUEST["data"]["PARAMS"]["MESSAGE"],
        "description" => $_REQUEST["data"]["PARAMS"]["MESSAGE"],
        "type_id" => 4, //Тип Битрикс24
        "department_id" => 7,//Департамент
        "user_id" => $id_user_hde, //ID владельца заявки
        "user_email" => $email_user,// Если не задан ID владельца заявки, то для создания заявки будет использоваться э-почта пользователя, если пользователь не существует, то он будет автоматически создан
        "custom_fields" => array(
        "1" => "2",  //Источник заявки
        "2" => $_REQUEST['auth']['domain'], //Адрес портала Битрикс24
        "3" => $_REQUEST["data"]["PARAMS"]["DIALOG_ID"],   //Номер диалога
        ),
        );
        
        //Отправка вопроса в HelpDeskEddy
        $data_json = json_encode($arData);
        
        $ch = curl_init('https://*******************/api/v2/tickets/');
        $headers = array(
        AUTHORIZATION_HDE,
        "Cache-Control: no-cache",
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json)
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        $response = curl_exec($ch);  //отправка запроса
        curl_close($ch);
        
        $aResult = json_decode($response);
        } else {  //тикет со статусом открыт существует, написать вопрос в этот тикет
        $arData = array(
        "text" => $_REQUEST["data"]["PARAMS"]["MESSAGE"], //(обязательный) Пример: Problem with email Текст комментария
        "user_id" => $id_user_hde,  //  (необязательный) Пример: 1 Владелец комментария, в случае если не будет указан - владельцем будет пользователь API.
        );
        
        $data_json = json_encode($arData);
        
        $ch = curl_init('https://***************/api/v2/tickets/' . $ticket_id . '/posts/');
        $headers = array(
        AUTHORIZATION_HDE,
        "Cache-Control: no-cache",
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json)
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        $response = curl_exec($ch);  //отправка запроса
        curl_close($ch);
        
        $aResult = json_decode($response);        
    }            