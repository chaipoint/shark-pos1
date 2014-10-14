<?php
class Customer extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|RETAIL-CUSTOMERS");
		}
		function retail_customer(){
			$return = array();	
			$options = array();
			if(array_key_exists('token', $_REQUEST)){
				$options['startkey'] = '"'.$_REQUEST['token'].'a"';
				$options['endkey'] = '"'.$_REQUEST['token'].'z"';
			}
			$customer_list = $this->cDB->getDesign(CUSTOMERS_DESIGN_DOCUMENT)->getView(CUSTOMERS_DESIGN_DOCUMENT_VIEW_RETAIL_CUSTOMER_LIST)->setParam($options)->execute();
			$this->log->trace('RETAIL CUSTOMER DETAILS'."\r\n".json_encode($customer_list));
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