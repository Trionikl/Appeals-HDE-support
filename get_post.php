<?
    //ЭТО ЗАПУСКАЕТСЯ ПО ВНЕШНЕМУ ЗАПРОСУ
    $data = json_decode(file_get_contents('php://input'), true);
    $domain = $data[0][URLPORTAL];
    
    require_once(__DIR__ . '/clients_list.php');
    require_once(__DIR__ . '/in_hook/crest.php');
    
    //получить id бота
    $query_str = http_build_query(array(
    'portal' => $data[0]['URLPORTAL'],
    'record' => 'N' //прочитать, если написать  Y - будет запись
    ));
    
    //получить id бота
    $ch = curl_init('https://*********************/api/record_bot_id.php?' . $query_str);
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
    $tiket_answer_id = ltrim($data[0]['ID'], 'Заявка-'); //ИД заявки
    
    //определить откуда пришёл ответ
    $query_str = http_build_query(array(
    'portal' => $data[0]['URLPORTAL'],
    'record' => 'N' //прочитать, если написать  Y - будет запись
    ));
    
    //получить все ответы по заявке
    $ch = curl_init('https://**********************/api/v2/tickets/' . $tiket_answer_id . '/posts/');
    $headers = array(
    AUTHORIZATION_HDE,
    "Cache-Control: no-cache",
    'Content-Type: application/json',
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    
    curl_close($ch);
    $aResultAnswer = json_decode($response);
    
    //проверить кто владелец заявки
    $client_id = $aResultAnswer->data[count($aResultAnswer->data) - 1]->user_id;
    
    //ответ для чат бота
    if ($client_id != $aResultAnswer->data[0]->user_id) {
        $result = CRest::call(
        'imbot.message.add',
        [
        'DIALOG_ID' => $data[0]['DIALOGID'], // Идентификатор диалога; это либо USER_ID пользователя, либо chatXX - где XX идентификатор чата, передается в событии ONIMBOTMESSAGEADD и ONIMJOINCHAT
        'MESSAGE' => strip_tags($data[0]['LAST_MESSAGE']), // Тест сообщения
        'ATTACH' => '', // Вложение, необязательное поле
        'KEYBOARD' => '', // Клавиатура, необязательное поле
        'MENU' => '', // Контекстное меню, необязательное поле
        'SYSTEM' => 'N', // Отображать сообщения в виде системного сообщения, необязательное поле, по умолчанию 'N'
        'URL_PREVIEW' => 'Y', // Преобразовывать ссылки в rich-ссылки, необязательное поле, по умолчанию 'Y'
        'CLIENT_ID' => CLIENT_ID,
        ], _REQUEST["auth"]);
        
    }    