<?php
	global $couch;
	/*Configuration Class For Whole Application*/
	require_once 'common/app.config.php';
	require_once 'common/couchdb.phpclass.php';

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

	if(file_exists(MODULE."/".MODULE.".php")){
		require_once MODULE."/".MODULE.".php";
	}else{
		echo "Unable To Process Request, Call To Undifiend Module";
		die();
	}

	$accessedClass = ucfirst(MODULE);
	$class = new $accessedClass();
	if(method_exists($class, MODE)){
		$method = MODE;
		echo $class->$method();
	}else{
		echo "Unable To Process Request, Call To Undifiend File";
	}

?>