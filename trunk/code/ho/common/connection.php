<?php

	$dir = dirname(dirname(__FILE__));
	include_once $dir.'/common/logger.php';
	include_once $dir.'/common/classes/db.php';
	include_once $dir.'/config.php';
	
	date_default_timezone_set('Asia/Calcutta');
	$currentDateTime = Date("d-M-Y h:i a");
	$currentDateTime24 = Date("Y-m-d H:i:s");
	$currentTime = Date("h:i a");
	$currentDate = Date("Y-m-d");
	$currentTime24 = Date("H:i:s");
	$currentTime12 = Date("h:i a");
	
	global $config, $currentDateTime24, $currentDateTime, $currentTime, $currentDate, $currentTime12, $currentTime24;
	
	$sql_host = $config['cpos_host'];
	$sql_user = $config['cpos_username'];
	$sql_password = $config['cpos_password'];
    $sql_db = $config['cpos_db'];
	$db = new Database();
?>
