<?php
	date_default_timezone_set('Asia/Calcutta');

	global $couch;
	/*Configuration Class For Whole Application*/

	require_once 'lib/log4php/Logger.php';
	Logger::configure('common/config.xml');
	$logger = Logger::getLogger("CP-POS|INDEX");;
	

	require_once 'common/app_config.php';
	require_once 'common/couchdb_phpclass.php';
	require_once 'lib/mysql/db.php';

	$appConfig = new App_config();

	$couch = new CouchPHP();

		/*Constant's For App*/
	define(	'APP',		$appConfig->getApp()	);
	define(	'URL',		$appConfig->getUrl()	);
	define(	'CSS',		$appConfig->getCss()	);
	define(	'JS',		$appConfig->getJs()		);
	define(	'MODULE',	$appConfig->getModule()	);
	define(	'MODE',		$appConfig->getMode()	);
	define(	'IMG',		$appConfig->getImg()	);
	define('DIR', dirname(__FILE__));
	
	$logger->trace('Dispatch '.MODULE.".".MODE);
	
	if(file_exists(MODULE."/".MODULE.".php")){
		require_once MODULE."/".MODULE.".php";
	}else{
		$logger->trace('Module Not Found {'.MODULE.'}');
		echo "Unable To Process Request, Call To Undifiend Module";
		die();
	}
	$accessedClass = ucfirst(MODULE);
	$class = new $accessedClass();
	if(method_exists($class, MODE)){
		$method = MODE;
		echo $class->$method();
	}else{
		$logger->trace('Mode Not Found '.MODULE.'{'.MODE.'}');
		echo "Unable To Process Request, Call To Undifiend File";
	}

?>