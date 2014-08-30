<?php
	
	class Sales_Register extends App_config{
		private $cDB;
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
			
		}
		function index(){
		//	http://127.0.0.1:5984/testing/_design/billing/_list/handle_updated_bills/handle_updated_bills?descending=true&include_docs=true
			$resultBillList = $this->cDB->getDesign('billing')->getList('sales_register','handle_updated_bills')->setParam(array("include_docs"=>"true","descending"=>"true", "endkey" => '["'.$this->getCDate().'"]'))->execute();//, "endkey" => '["'.$this->getCDate().'"]'
			$resultExpenseList = $this->cDB->getDesign('petty_expense')->getView('get_expense')->setParam(array("include_docs"=>"true","startkey"=>'"'.$this->getCDate().'"',"endkey"=>'"'.$this->getCDate().'"'))->execute();
			$this->getDBConnection($this->cDB);
			$getHeadQuery = 'SELECT id, name FROM cp_reference_master WHERE active ="Y" AND mode = "head"';
			$result = $this->db->func_query($getHeadQuery);
			if(!empty($result)){
	            $headArray = array();
	            foreach ($result as $key => $value) {
					$headArray[$value['id']] = $value['name'];
				}
			} 
			if(array_key_exists('error', $resultBillList) || array_key_exists('error', $resultExpenseList) ){
				echo 'opps some problem please contact admin';
			}else{
				$resultBillList['head_data'] = $headArray;
				$resultBillList['expense_data'] = $resultExpenseList;
				$this->commonView('header_html');
				$this->commonView('navbar');
				$this->view($resultBillList);//array("bill_data"=>$resultBillList['data'],"cash_in_hand"=>$resultBillList['cash_inhand'],"cash_in_delivery"=> $resultBillList['cash_indelivery']));
				$this->commonView('footer_inner');
				$this->commonView('footer_html');
			}
		}

		function save(){
			global $couch;
			$return = array('error'=>false,'message'=>'','data'=>array());
			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				$this->log->trace("DATA \r\n".json_encode($_POST));
                $_POST['cd_doc_type'] = 'petty_expense';
				$_POST['expense_time'] = $this->getCTime();
				$result = $couch->saveDocument()->execute($_POST);
				if(array_key_exists('error', $result)){
					$return['error'] = true;
					$return['message'] = 'OOPS! Some Error Contact Admin';
				}else{
						$return['error'] = false;
						$return['message'] = 'Save Successfully';
				    }
			}
			$res = json_encode($return);
			$this->log->trace("RESPONSE \r\n".$res);
			return $res;
		}
	}