<?
//define('C_REST_CLIENT_ID','**************');//Application ID
//define('C_REST_CLIENT_SECRET','*****************');//Application key
// or

//наш ПОРТАЛ
define('C_REST_WEB_HOOK_URL', $C_REST_WEB_HOOK_URL); 
define("CLIENT_ID", $CLIENT_ID);

//define('C_REST_CURRENT_ENCODING','windows-1251');
//define('C_REST_IGNORE_SSL',true);//turn off validate ssl by curl
//define('C_REST_LOG_TYPE_DUMP',true); //logs save var_export for viewing convenience
define('C_REST_BLOCK_LOG',true);//turn off default logs
//define('C_REST_LOGS_DIR', __DIR__ .'/logs/'); //directory path to save the log

//авторизация в HDE
define("AUTHORIZATION_HDE", 'Authorization: Basic ****************************************');