<?
    require_once __DIR__ . '/api/include/config.php';       //конфигурационный файл
    require_once __DIR__ . '/api/include/log.php';          //функция логирования
    require_once __DIR__ . '/api/include/DbStorage.php';    //методы обмена данными с БД
    
    $storageDB = new DbStorage;
    $arWebhook = $storageDB->getWebhook($domain);
    
    //значения для "/in_hook/settings.php"
    $C_REST_WEB_HOOK_URL = $arWebhook["WEBHOOK"];
    $CLIENT_ID = $arWebhook["CLIENT_ID"];