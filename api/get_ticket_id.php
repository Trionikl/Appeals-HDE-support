<?php
	require_once __DIR__ . '/include/config.php';       //конфигурационный файл
	require_once __DIR__ . '/include/log.php';          //функция логирования
	require_once __DIR__ . '/include/DbStorage.php';    //методы обмена данными с БД
	
	//Параметры выборки (необходимо задать оба параметры)
	$dialogID = $_GET['dialogID']; //ID диалога
	$portal = $_GET['portal'];    //Адрес портала
	$arStatus  = $_GET['arStatus']; //Нужные статусы заявки
	
	$storage = new DbStorage;
	$arTickets = $storage->getTickets($dialogID, $portal, $arStatus);
	
	$strTickets =json_encode($arTickets);
	
echo $strTickets;