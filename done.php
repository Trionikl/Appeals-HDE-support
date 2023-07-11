<?php
    //ЭТО ЗАПУСКАЕТСЯ ПО ВНЕШНЕМУ ЗАПРОСУ
    $data = json_decode(file_get_contents('php://input'), true);
    
    //если пользователь я вляется пользователем чат-бота
    if ($data[0]['URLPORTAL']) {
        $done = $data[0]['DONE'];
        $application = ltrim($data[0]['ID'], 'Заявка-'); //заявка
        $dialog_id = $data[0]['DIALOGID'];
        $domain = $data[0]['URLPORTAL'];
        
        require_once(__DIR__ . '/clients_list.php');
        require_once(__DIR__ . '/in_hook/crest.php');
        
        if ($done == 'Y') {         
            for ($i = 1; $i <= 5; $i++) {
                $ar_score_command = array("id_ticket" => $application, "score" => $i);
                $score[$i] = serialize(array("id_ticket" => $application, "score" => $i));
            }
            
            $keyboard = array(
            array("TEXT" => "1", "COMMAND" => "score_1", "COMMAND_PARAMS" => $score[1], "DISPLAY" => "LINE", "BLOCK" => "Y"),
            array("TEXT" => "2", "COMMAND" => "score_2", "COMMAND_PARAMS" => $score[2], "DISPLAY" => "LINE", "BLOCK" => "Y"),
            array("TEXT" => "3", "COMMAND" => "score_3", "COMMAND_PARAMS" => $score[3], "DISPLAY" => "LINE", "BLOCK" => "Y"),
            array("TEXT" => "4", "COMMAND" => "score_4", "COMMAND_PARAMS" => $score[4], "DISPLAY" => "LINE", "BLOCK" => "Y"),
            array("TEXT" => "5", "COMMAND" => "score_5", "COMMAND_PARAMS" => $score[5], "DISPLAY" => "LINE", "BLOCK" => "Y"),
            array("TYPE" => "NEWLINE"), // перенос строки
            array("TEXT" => "Вопрос не решен!", "COMMAND" => "openTicket", "COMMAND_PARAMS" => $application, "TEXT_COLOR" => "#fff", "BG_COLOR" => "#3bc8f5", "DISPLAY" => "BLOCK"),
            );
            
            $result = CRest::call(
            'imbot.message.add',
            [
            'DIALOG_ID' => $dialog_id, // Идентификатор диалога; это либо USER_ID пользователя, либо chatXX - где XX идентификатор чата, передается в событии ONIMBOTMESSAGEADD и ONIMJOINCHAT
            'MESSAGE' => 'Ваша заявка успешно выполнена и закрыта', // Тест сообщения
            'ATTACH' => array(
            array("MESSAGE" => "Оцените качество выполнения заявки"),
            ), // Вложение, необязательное поле
            'KEYBOARD' => $keyboard, // Клавиатура, необязательное поле
            'MENU' => '', // Контекстное меню, необязательное поле
            'SYSTEM' => 'N', // Отображать сообщения в виде системного сообщения, необязательное поле, по умолчанию 'N'
            'URL_PREVIEW' => 'Y', // Преобразовывать ссылки в rich-ссылки, необязательное поле, по умолчанию 'Y'
            'CLIENT_ID' => CLIENT_ID,
            ], _REQUEST["auth"]);
            
        }
    }         