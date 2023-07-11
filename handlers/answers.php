<?php
    //запрос к внешнему API
    $query_str = http_build_query(array(
    'dialogID' => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
    'portal' => $_REQUEST['auth']['domain'],
    'arStatus' => array("Открыто", "В процессе"),
    ));
    
    //получить id заявки
    $ch = curl_init('https://*********/api/get_ticket_id.php?' . $query_str);
    $headers = array(
    "Cache-Control: no-cache"
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    
    curl_close($ch);
    $aResult = json_decode($response);
    
    $ticket_id = $aResult[0]->TICKETID;
    $ticket_status = $aResult[0]->STATUS;
    
    //если есть не закрытый вопрос то записать его для чат-бота
    if ($ticket_id) {
        //Статус вопроса
        $questionStatusRu =  $ticket_status;
        
        //получение вопросов в заявке
        $ch = curl_init('https://**************/api/v2/tickets/' . $ticket_id . '/posts/');
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
        //если присутствуют несколько не закрытых заявок то вывести вопрос по самой первой заявке
        if ($aResult->data[0]->ticket_id) {
            //если пользователь не клиент, то записать ответ техподдержки
            $first_question_sn = count($aResult->data);
            if ($aResult->data[$first_question_sn - 1]->user_id != $aResult->data[0]->user_id) {
                $last_question_text = $aResult->data[0]->text; //последний ответ от службы техподдержки
                
                //поиск последнего ответа пользователя
                $id_client = $aResult->data[$first_question_sn - 1]->user_id;
                foreach ($aResult->data as $key => $value) {
                    $user_answer_el = array_search($id_client, $value);
                    if ($user_answer_el) {
                        break;
                    }
                }
                $currentQuestion = strip_tags($aResult->data[$key]->text);
            }
            else
            {  //вопрос клиента
                $first_question_sn = count($aResult->data);
                $first_question_text = $aResult->data[$first_question_sn-1]->text; // Заявка
                $currentQuestion = strip_tags($first_question_text);
            }
            
        }
    }                    