<?
    $name = $_REQUEST['data']['USER']['FIRST_NAME'];
    $lastname = $_REQUEST['data']['USER']['LAST_NAME'];
    $email = $email_user;
    $group_id = 1;
    $department = array(7); //Битрикс24
    $password = gen_password(8); //пароль
    
    //создать пользователя
    $arData = array(
    "name" => $name,
    "lastname" => $lastname,
    "email" => $email,
    "group_id" => $group_id,// Если не задан ID владельца заявки, то для создания заявки будет использоваться э-почта пользователя, если пользователь не существует, то он будет автоматически создан
    "department" => $department,
    "password" => $password,
    );
    
    $data_json = json_encode($arData);
    
    $ch = curl_init('https://***********/api/v2/users/');
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
    $id_user_hde = $aResult->data->id;
    
    //============= ФУНКЦИИ == ...
    //генерация пароля
    function gen_password($length = 6)
    {
        $chars = 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP';
        $size = strlen($chars) - 1;
        $password = '';
        while ($length--) {
            $password .= $chars[random_int(0, $size)];
        }
        return $password;
    }        