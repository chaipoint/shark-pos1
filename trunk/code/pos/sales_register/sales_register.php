<?php
	
	class Sales_Register extends App_config{
		private $cDB;
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
		}
		function index(){
			$error = false;
			$activeTask = $this->cDB->getActiveTask();
			if(array_key_exists('cMessage', $activeTask)){
				$error = true;
			}else{
				$date = $this->getCDate();
				if(array_key_exists('sales_reg_search', $_GET)){
					$date = date('Y-m-d',strtotime($_GET['sales_reg_search']));
				}
				
				$resultBillList = $this->cDB->getDesign('billing')->getList('sales_register','handle_updated_bills')->setParam(array("include_docs"=>"true","descending"=>"true","endkey" => '["'.$date.'"]',"startkey" => '["'.$date.'",{},{},{}]'))->execute();//endkey=["2014-09-04"]&startkey=["2014-09-04",{},{},{}]
				$resultExpenseList = $this->cDB->getDesign('petty_expense')->getView('get_expense')->setParam(array("include_docs"=>"true","startkey"=>'"'.$date.'"',"endkey"=>'"'.$date.'"'))->execute();
				$staffList = $this->cDB->getDesign('staff')->getView('staff_username')->setParam(array("include_docs"=>"true"))->execute();
				$rows = $staffList['rows'];
				$staffList = array();
				foreach($rows as $key => $value){
					$staffList[$value['doc']['mysql_id']] = $value['doc']['name'] ;
				}
				ksort($staffList);
				$pettyExpence = 0;
				if(count($resultExpenseList['rows'])>0){
					$rows = $resultExpenseList['rows'];
					foreach($rows as $pKey => $pValue){
						$pettyExpence += $pValue['doc']['expense_amount'];
					}
				}
				$resultBillList['p_ex'] = $pettyExpence;

				$configHead = $this->getConfig($this->cDB, 'head');
				/*$this->getDBConnection($this->cDB);
				$getHeadQuery = 'SELECT id, name FROM cp_reference_master WHERE active ="Y" AND mode = "head"';
				$result = $this->db->func_query($getHeadQuery);/**/
				if(!empty($result)){
		            $headArray = array();
		            foreach ($result as $key => $value) {
						$headArray[$value['id']] = $value['name'];
					}
				} 
			}
			$this->commonView('header_html',array('error'=>$error));
			$this->commonView('navbar');
			if(!$error){
				if(array_key_exists('error', $resultBillList) || array_key_exists('error', $resultExpenseList) ){
					echo 'opps some problem please contact admin';
				}else{
					$resultBillList['head_data'] = $configHead['data']['head'];
					$resultBillList['staff_list'] = $staffList;
					$resultBillList['expense_data'] = $resultExpenseList;
					$resultBillList['at'] = $activeTask;
					$this->view($resultBillList);//array("bill_data"=>$resultBillList['data'],"cash_in_hand"=>$resultBillList['cash_inhand'],"cash_in_delivery"=> $resultBillList['cash_indelivery']));
				}
			}
			$this->commonView('footer_inner');
			$this->commonView('footer_html');

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