<?php
class Customer extends App_config{
		function __construct(){
			parent::__construct();
		}
		function retail_customer(){
			$return = array();	
			$options = array();
			if(array_key_exists('token', $_REQUEST)){
				$options['startkey'] = '"'.$_REQUEST['token'].'a"';
				$options['endkey'] = '"'.$_REQUEST['token'].'z"';
			}
			$customer_list = $this->cDB->getDesign('customers')->getView('retail_customer_list')->setParam($options)->execute();
			$this->cDB->getLastUrl();			
			if(array_key_exists('rows', $customer_list)){
				$rows = $customer_list['rows'];
				foreach($rows as $key => $value){
					$return[$key]['id'] = $value['value']; 
					$return[$key]['label'] = $value['key']; 
				}
			}
			return $return;
		}
}