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
			return array_key_exists('rows', $storeResult) ? $storeResult['rows'][0] : $storeResult;
		}
		function get_store_staff($store = 0, $include_docs = 'false'){
			$staff = array();
			if(array_key_exists('store', $_GET)){
				$store = $_GET['store'];
			}
			if(array_key_exists('include_docs', $_GET) && $_GET['include_docs'] != 'false'){
				$include_docs = 'true';
			}		
			$result = $this->getStore($store, $include_docs);
			if(array_key_exists('value', $result) && array_key_exists('staff', $result['value'])){
				foreach($result['value']['staff'] as $key => $value){
					$staff[$value['mysql_id']]['name'] = $value['name']; 
					$staff[$value['mysql_id']]['title'] = $value['title_id']; 
				}
			}
			return $staff;
		}
	}