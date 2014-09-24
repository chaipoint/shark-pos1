<?php
	
	class Sales_Register extends App_config{
		function __construct(){
			parent::__construct();
		}
		function index(){
			$error = false;
			$activeTask = $this->cDB->getActiveTask();
			if(array_key_exists('cMessage', $activeTask)){
				$error = true;
				header("Location:index.php?error=true");
				die;
			}else{
				$date = $this->getCDate();
				if(array_key_exists('sales_reg_search', $_GET)){
					$date = date('Y-m-d',strtotime($_GET['sales_reg_search']));
				}	
				$resultBillList = $this->getBills($date);
				$resultExpenseList = $this->getExpenseData($date);
				
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
					$resultBillList['staff_list'] = $this->getStaffList();
					$resultBillList['expense_data'] = $resultExpenseList;
					$resultBillList['at'] = $activeTask;
					$this->view($resultBillList);//array("bill_data"=>$resultBillList['data'],"cash_in_hand"=>$resultBillList['cash_inhand'],"cash_in_delivery"=> $resultBillList['cash_indelivery']));
				}
			}
			$this->commonView('footer_inner');
			$this->commonView('footer_html');

		}
		function getExpenseData($date){
				$resultExpenseList = $this->cDB->getDesign('petty_expense')->getView('get_expense')->setParam(array("include_docs"=>"true","startkey"=>'"'.$date.'"',"endkey"=>'"'.$date.'"'))->execute();
				return $resultExpenseList;
		}
		function getStaffList(){
				$staffList = $this->cDB->getDesign('store')->getView('store_mysql_id')->setParam(array("include_docs"=>"true"))->execute();
				$rows = $staffList['rows'][0]['doc']['store_staff'];
				$staffList = array();
				foreach($rows as $key => $value){
					$staffList[$value['mysql_id']]['mysql_id'] = $value['mysql_id'] ;
					$staffList[$value['mysql_id']]['code'] = @$value['code'] ;
					$staffList[$value['mysql_id']]['name'] = $value['name'] ;
					$staffList[$value['mysql_id']]['title_id'] = $value['title_id'] ;
					$staffList[$value['mysql_id']]['title_name'] = @$value['title_name'] ;
				}
				ksort($staffList);
				return $staffList;
		}
		function getBills($date){
				$resultBillList = $this->cDB->getDesign('billing')->getList('sales_register','handle_updated_bills')->setParam(array("include_docs"=>"true","descending"=>"true","endkey" => '["'.$date.'"]',"startkey" => '["'.$date.'",{},{},{}]'))->execute();//endkey=["2014-09-04"]&startkey=["2014-09-04",{},{},{}]
				return 	$resultBillList;
		}
		function save(){
			$return = array('error'=>false,'message'=>'','data'=>array());
			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				$this->log->trace("DATA \r\n".json_encode($_POST));

				if(array_key_exists('expense_head',$_POST)){
	                $_POST['cd_doc_type'] = 'petty_expense';
					$_POST['expense_time'] = $this->getCTime();
					$return['message'] = 'Please Start Shift, Befor Saving Expense';					
				}elseif(array_key_exists('inward_amount',$_POST)){
	                $_POST['cd_doc_type'] = 'petty_inward';
					$_POST['inward_time'] = $this->getCTime();
					$return['message'] = 'Please Start Shift, Befor doing Petty Cash Inward';
				}
				
				if(array_key_exists('shift', $_SESSION['user']) && array_key_exists('store', $_SESSION['user'])){
					$_POST['shift_no'] = $_SESSION['user']['shift'];
					$_POST['store_id'] = $_SESSION['user']['store']['id'];
					$_POST['store_name'] = $_SESSION['user']['store']['name'];
					$result = $this->cDB->saveDocument()->execute($_POST);
					if(array_key_exists('error', $result)){
						$return['error'] = true;
						$return['message'] = 'OOPS! Some Error Contact Admin';
					}else{
							$return['message'] = 'Saved Successfully';
					}
				}else{
					$return['error'] = true;	
				}
			
			}

			$res = json_encode($return);
			$this->log->trace("RESPONSE \r\n".$res);
			return $res;
		}
	}