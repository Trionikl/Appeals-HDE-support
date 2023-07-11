<?php
/******************* Логирование **************************/
function writeToLog($data, $title = '', $name='') {
	$log = "\n------------------------\n";
	$log .= date("Y.m.d G:i:s") . "\n";
	$log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
	$log .= print_r($data, 1);
	$log .= "\n------------------------\n";
    if (!empty($name)){
        file_put_contents(getcwd() . '/logs/'.date("d-m-Y").'-'.$name.'.log', $log, FILE_APPEND);
    }else{
        file_put_contents(getcwd() . '/logs/'.date("d-m-Y").'.log', $log, FILE_APPEND);
    }
	return true;
}
?>