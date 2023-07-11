<?
    error_reporting(0);
    
    $domain = $_POST["auth"]["domain"];
    $feldel_ement = current($_POST['data']['BOT']);
    $BOT_CODE = $feldel_ement['BOT_CODE'];
    $handlerBackUrl = 'https://*******************/index.php';
    $handlerCommand = 'https://********************/handlers/command.php';
    
    require_once(__DIR__ . '/clients_list.php');
    require_once(__DIR__ . '/in_hook/crest.php');
    
    //====================================
    // получить событие "новое сообщение для бота"
    if ($_REQUEST['event'] == 'ONIMBOTMESSAGEADD') {
        $currentQuestion = "none";
        $ticket_status = ""; // текущий статус вопроса
        
        //проверить задан ли вопрос в HDE
        include(__DIR__ . '/handlers/answers.php');
        
        //задать вопрос если он не задан или написать ответ   
        include(__DIR__ . '/handlers/create_ticket_HDE.php');
        
        if ($currentQuestion != "none") { //если есть открытый вопрос вывести его описание
            if ($ticket_status) {
                $ticket_status = "Текущий статус заявки: " . mb_strtolower($ticket_status);
            }
        } 
        else { // Вопрос принят - выводится после создания вопроса, если в HDE все вопросы в статусе "Выполнено"
            $result = CRest::call(
            'imbot.message.add',
            [
            'BOT_ID' => $_REQUEST["data"]["PARAMS"]["TO_USER_ID"], // Идентификатор чат-бота, от которого идет запрос; можно не указывать, если чат-бот всего один
            'DIALOG_ID' => $_REQUEST["data"]["PARAMS"]["DIALOG_ID"], // Идентификатор диалога; это либо USER_ID пользователя, либо chatXX - где XX идентификатор чата, передается в событии ONIMBOTMESSAGEADD и ONIMJOINCHAT
            'MESSAGE' => "Заявка принята", // Тест сообщения
            'ATTACH' => '', // Вложение, необязательное поле
            'KEYBOARD' => '', // Клавиатура, необязательное поле
            'MENU' => '', // Контекстное меню, необязательное поле
            'SYSTEM' => 'N', // Отображать сообщения в виде системного сообщения, необязательное поле, по умолчанию 'N'
            'URL_PREVIEW' => 'Y', // Преобразовывать ссылки в rich-ссылки, необязательное поле, по умолчанию 'Y'
            'CLIENT_ID' => CLIENT_ID,
            ], _REQUEST["auth"]);
        }
    } 
    
    // получить событие "новая команда для бота"
    if ($_REQUEST['event'] == 'ONIMCOMMANDADD') {
        $command = current($_REQUEST["data"]["COMMAND"]);
        $command_button = $command ['COMMAND_PARAMS']; //Номер тикета
        //Поставить оценку сбор данных (получить почту текущего пользователя)
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
        $ch = curl_init('https://***************/api/v2/users/?' . $query_str);
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
        $id_user_hde = $aResult->data[0]->id; //ид пользователя в hde
        //открыть повторно заявку   
        if ($command ['COMMAND'] == 'openTicket') {
            //Открыть заявку
            $arData = array(
            "status_id" => 'open',
            );
            //object2file($id_ticket, LOG_FILENAME);
            $data_json = json_encode($arData);
            
            $ch = curl_init('https://****************/api/v2/tickets/' . $command_button);
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
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            $response = curl_exec($ch);  //отправка запроса
            curl_close($ch);
            
            //ответ - заявка открыта
            $result = CRest::call(
            'imbot.message.add',
            [
            'BOT_ID' => $_REQUEST["data"]["PARAMS"]["TO_USER_ID"], // Идентификатор чат-бота, от которого идет запрос; можно не указывать, если чат-бот всего один
            'DIALOG_ID' => $_REQUEST["data"]["PARAMS"]["DIALOG_ID"], // Идентификатор диалога; это либо USER_ID пользователя, либо chatXX - где XX идентификатор чата, передается в событии ONIMBOTMESSAGEADD и ONIMJOINCHAT
            'MESSAGE' => "Заявка открыта", // Тест сообщения
            'ATTACH' => '', // Вложение, необязательное поле
            'KEYBOARD' => '', // Клавиатура, необязательное поле
            'MENU' => '', // Контекстное меню, необязательное поле
            'SYSTEM' => 'N', // Отображать сообщения в виде системного сообщения, необязательное поле, по умолчанию 'N'
            'URL_PREVIEW' => 'Y', // Преобразовывать ссылки в rich-ссылки, необязательное поле, по умолчанию 'Y'
            'CLIENT_ID' => CLIENT_ID,
            ], _REQUEST["auth"]);    
        }
        
        //отправить оценку тикета
        if ($command['COMMAND'] == 'score_1') {
            scoreTicket($id_user_hde, $command_button);
        }
        if ($command['COMMAND'] == 'score_2') {
            scoreTicket($id_user_hde, $command_button);
        }
        if ($command['COMMAND'] == 'score_3') {
            scoreTicket($id_user_hde, $command_button);
        }
        if ($command['COMMAND'] == 'score_4') {
            scoreTicket($id_user_hde, $command_button);
        }
        if ($command['COMMAND'] == 'score_5') {
            scoreTicket($id_user_hde, $command_button);
        }
    }
    
    //Событие на получение информации чат-ботом о включении его в чат (или личную переписку)
    if ($_REQUEST['event'] == 'ONIMBOTJOINCHAT') {
        //Регистрация команды для открытия повторно заявки
        $botId = $_REQUEST['data']['PARAMS']['TO_USER_ID'];
        $result = CRest::call(
        'imbot.command.register',
        [
        'BOT_ID' => $botId,
        'COMMAND' => 'openTicket',
        'COMMON' => 'N',
        'HIDDEN' => 'Y',
        'EXTRANET_SUPPORT' => 'N',
        'LANG' => array(
        array('LANGUAGE_ID' => 'ru', 'TITLE' => 'Открыть заявку', 'PARAMS' => 'Открыть заявку, которая ранее была закрыта'),
        ),
        'EVENT_COMMAND_ADD' => $handlerBackUrl,
        'CLIENT_ID' => CLIENT_ID, // Строковый идентификатор чат-бота, используется только в режиме Вебхуков
        ], _REQUEST["auth"]);
        
        //отправить оценку тикета - 5 кнопок
        $result = CRest::call(
        'imbot.command.register',
        [
        'BOT_ID' => $botId,
        'COMMAND' => 'score_1',
        'COMMON' => 'N',
        'HIDDEN' => 'Y',
        'EXTRANET_SUPPORT' => 'N',
        'LANG' => array(
        array('LANGUAGE_ID' => 'ru', 'TITLE' => '1', 'PARAMS' => 'Поставить оценку 1'),
        ),
        'EVENT_COMMAND_ADD' => $handlerBackUrl,
        'CLIENT_ID' => CLIENT_ID, // Строковый идентификатор чат-бота, используется только в режиме Вебхуков
        ], _REQUEST["auth"]);
        
        $result = CRest::call(
        'imbot.command.register',
        [
        'BOT_ID' => $botId,
        'COMMAND' => 'score_2',
        'COMMON' => 'N',
        'HIDDEN' => 'Y',
        'EXTRANET_SUPPORT' => 'N',
        'LANG' => array(
        array('LANGUAGE_ID' => 'ru', 'TITLE' => '2', 'PARAMS' => 'Поставить оценку 2'),
        ),
        'EVENT_COMMAND_ADD' => $handlerBackUrl,
        'CLIENT_ID' => CLIENT_ID, // Строковый идентификатор чат-бота, используется только в режиме Вебхуков
        ], _REQUEST["auth"]);
        
        $result = CRest::call(
        'imbot.command.register',
        [
        'BOT_ID' => $botId,
        'COMMAND' => 'score_3',
        'COMMON' => 'N',
        'HIDDEN' => 'Y',
        'EXTRANET_SUPPORT' => 'N',
        'LANG' => array(
        array('LANGUAGE_ID' => 'ru', 'TITLE' => '3', 'PARAMS' => 'Поставить оценку 3'),
        ),
        'EVENT_COMMAND_ADD' => $handlerBackUrl,
        'CLIENT_ID' => CLIENT_ID, // Строковый идентификатор чат-бота, используется только в режиме Вебхуков
        ], _REQUEST["auth"]);
        
        $result = CRest::call(
        'imbot.command.register',
        [
        'BOT_ID' => $botId,
        'COMMAND' => 'score_4',
        'COMMON' => 'N',
        'HIDDEN' => 'Y',
        'EXTRANET_SUPPORT' => 'N',
        'LANG' => array(
        array('LANGUAGE_ID' => 'ru', 'TITLE' => '4', 'PARAMS' => 'Поставить оценку 4'),
        ),
        'EVENT_COMMAND_ADD' => $handlerBackUrl,
        'CLIENT_ID' => CLIENT_ID, // Строковый идентификатор чат-бота, используется только в режиме Вебхуков
        ], _REQUEST["auth"]);
        
        $result = CRest::call(
        'imbot.command.register',
        [
        'BOT_ID' => $botId,
        'COMMAND' => 'score_5',
        'COMMON' => 'N',
        'HIDDEN' => 'Y',
        'EXTRANET_SUPPORT' => 'N',
        'LANG' => array(
        array('LANGUAGE_ID' => 'ru', 'TITLE' => '5', 'PARAMS' => 'Поставить оценку 5'),
        ),
        'EVENT_COMMAND_ADD' => $handlerBackUrl,
        'CLIENT_ID' => CLIENT_ID, // Строковый идентификатор чат-бота, используется только в режиме Вебхуков
        ], _REQUEST["auth"]);
        
        //Показать приветствие Ромашки
        $result = CRest::call(
        'imbot.message.add',
        [
        'BOT_ID' => $_REQUEST["data"]["PARAMS"]["TO_USER_ID"], // Идентификатор чат-бота, от которого идет запрос; можно не указывать, если чат-бот всего один
        'DIALOG_ID' => $_REQUEST["data"]["PARAMS"]["DIALOG_ID"], // Идентификатор диалога; это либо USER_ID пользователя, либо chatXX - где XX идентификатор чата, передается в событии ONIMBOTMESSAGEADD и ONIMJOINCHAT
        'MESSAGE' => "Я Ромашка, бот технической поддержки, все вопросы можно писать мне, я отвечу", // Тест сообщения
        'KEYBOARD' => '', // Клавиатура, необязательное поле
        'MENU' => '', // Контекстное меню, необязательное поле
        'SYSTEM' => 'N', // Отображать сообщения в виде системного сообщения, необязательное поле, по умолчанию 'N'
        'URL_PREVIEW' => 'Y', // Преобразовывать ссылки в rich-ссылки, необязательное поле, по умолчанию 'Y'
        'CLIENT_ID' => CLIENT_ID,
        ], _REQUEST["auth"]);
    }
    
    // ==== ФУНКЦИИ ===
    //   Записать оценку в HDE
    function scoreTicket($id_user_hde, $command_button)
    {
        $user_id = $id_user_hde; // id пользователя в hde
        $ar_id_ticket_score = unserialize($command_button);
        $id_ticket = $ar_id_ticket_score['id_ticket'];
        $text = 'Оценка: ' . $ar_id_ticket_score['score'];     
        $arData = array(
        "text" => $text,
        "user_id" => $user_id,
        );
        $data_json = json_encode($arData);
        $ch = curl_init('https://************************/api/v2/tickets/' . $id_ticket . '/comments/');
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
        //сообщение об оценке
        $result = CRest::call(
        'imbot.message.add',
        [
        'BOT_ID' => $_REQUEST["data"]["PARAMS"]["TO_USER_ID"], // Идентификатор чат-бота, от которого идет запрос; можно не указывать, если чат-бот всего один
        'DIALOG_ID' => $_REQUEST["data"]["PARAMS"]["DIALOG_ID"], // Идентификатор диалога; это либо USER_ID пользователя, либо chatXX - где XX идентификатор чата, передается в событии ONIMBOTMESSAGEADD и ONIMJOINCHAT
        'MESSAGE' => "Оценка отправлена", // Тест сообщения
        'KEYBOARD' => '', // Клавиатура, необязательное поле
        'MENU' => '', // Контекстное меню, необязательное поле
        'SYSTEM' => 'N', // Отображать сообщения в виде системного сообщения, необязательное поле, по умолчанию 'N'
        'URL_PREVIEW' => 'Y', // Преобразовывать ссылки в rich-ссылки, необязательное поле, по умолчанию 'Y'
        'CLIENT_ID' => CLIENT_ID,
        ], _REQUEST["auth"]);
    }            