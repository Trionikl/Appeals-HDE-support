<?php
	/*Записать id бота БИТРИКС24, получить значение бота Битрикс24 */
	require_once __DIR__ . '/include/config.php';       //конфигурационный файл
	require_once __DIR__ . '/include/log.php';          //функция логирования
	require_once __DIR__ . '/include/DbStorage.php';    //методы обмена данными с БД
	
	//Параметры выборки (необходимо задать оба параметры)
	$idBot = $_GET['idBot'];
	$portal = $_GET['portal'];
	$record = $_GET['record'];	
	
	$storage = new DbStorage;
	
	if($record == 'Y') { //записать в таблицу
		$arAnswer = $storage->setIdBot($portal, $idBot);
	}
	else if($record == 'N') { //прочитать из таблицы
		$arAnswer = $storage->getIdBot($portal);
	}
	
	$strAnswer =json_encode($arAnswer);
echo $strAnswer;