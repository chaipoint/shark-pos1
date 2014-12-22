<?php
	date_default_timezone_set('Asia/Calcutta');

	/*Configuration Class For Whole Application*/
	require_once 'config.php';
	require_once 'constant.php';
	require_once 'lib/log4php/Logger.php';
	Logger::configure('common/config.xml');
	$logger = Logger::getLogger("CP-POS|INDEX");;
	

	require_once 'common/app_config.php';
	//require_once 'lib/mysql/db.php';

	$appConfig = new App_config();


		/*Constant's For App*/
	define(	'APP',		$appConfig->getApp()	);
	define(	'URL',		$appConfig->getUrl()	);
	define(	'CSS',		$appConfig->getCss()	);
	define(	'JS',		$appConfig->getJs()		);
	define(	'MODULE',	$appConfig->getModule()	);
	define(	'MODE',		$appConfig->getMode()	);
	define(	'IMG',		$appConfig->getImg()	);
	define('DIR', dirname(__FILE__));
	
	$logger->debug('Dispatch '.MODULE.".".MODE);
	
	if(file_exists(MODULE."/".MODULE.".php")){
		require_once MODULE."/".MODULE.".php";
	}else{
		$logger->debug('Module Not Found {'.MODULE.'}');
		echo "Unable To Process Request, Call To Undifiend Module";
		die();
	}
	$accessedClass = ucfirst(MODULE);
	$class = new $accessedClass();
	if(method_exists($class, MODE)){
		$method = MODE;
		$res = $class->$method();
		if(is_array($res)){
			echo json_encode($res);
		}else{
			echo $res;
		}
	}else{
		$logger->debug('Mode Not Found '.MODULE.'{'.MODE.'}');
		echo "Unable To Process Request, Call To Undifiend File";
	}

?>