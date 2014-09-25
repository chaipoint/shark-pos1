<?php
	class store extends App_config{
		function __construct(){
			parent::__construct();
		}
		function getStore($store = 0, $include_docs = 'false'){
			if(array_key_exists('store', $_GET)){
				$store = $_GET['store'];
			}
			if(array_key_exists('include_docs', $_GET) && $_GET['include_docs'] != 'false'){
				$include_docs = 'true';
			}
			$param['key'] = '"'.$store.'"';
			$param['include_docs'] = $include_docs;
			$storeResult = $this->cDB->getDesign('store')->getView('store_mysql_id')->setParam($param)->execute();
			return array_key_exists('rows', $storeResult) ? $storeResult['rows'] : $storeResult;
		}
	}