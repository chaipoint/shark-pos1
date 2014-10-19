<?php
	
	class Sales_Register extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|SALE-REGISTER");
		}
		/* This Function Is Automatically Called When We Come On Sale Register Module */
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
			}
			$this->commonView('header_html',array('error'=>$error));
			$this->commonView('navbar');
			if(!$error){
				if(array_key_exists('error', $resultBillList) || array_key_exists('error', $resultExpenseList) ){
					echo ERROR.' '.(array_key_exists('error', $resultBillList) ? $resultBillList['error'] : $resultExpenseList['error']);
				}else{
					require_once DIR.'/staff/staff.php';
					$st = new Staff();

					$resultBillList['head_data'] = $configHead['data']['head'];
					$resultBillList['staff_list'] = $st->getStaffList();
					$resultBillList['expense_data'] = $resultExpenseList;
					$this->view($resultBillList);

					require_once DIR.'/login/login.php';
					$login = new Login();
					$login->form_login();
				
					require_once DIR.'/utils/utils.php';
					$utils = new Utils();
					$utils->generate_rep_running_flag();
				}
			}
			$this->commonView('footer_inner');
			$this->commonView('footer_html');

		}

		/* Function To Get Todays sale */
		public function getTodaysSale($date = ''){
			global $PAYMENT_MODE;
			$return = array('error'=>false,'message'=>'','data'=>array());

			if(empty($date)){
				$date = $this->getCDate();
			}
			//$paymentMode = $this->getConfig($this->cDB, 'payment_mode');
			//print_r($paymentMode);
			$bills = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getList(BILLING_DESIGN_DOCUMENT_LIST_TODAYS_SALE, BILLING_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS)->setParam(array("descending"=>"true","include_docs"=>"true","endkey"=>'["'.$date.'"]'))->execute();
			$this->log->trace("TODAYS SALE DETAILS \r\n".json_encode($bills));
			if(array_key_exists('error', $bills)){
				$return['error'] = true;
				$return['message'] = ERROR.' '.($bills['error']);
			}else{
	            $payment_type = $PAYMENT_MODE;
				$return['data']['summary'] = $bills;
				$return['data']['payment_type'] = $payment_type;
			}
			return json_encode($return);
		}

		/* Function To Get Petty Expense */
		function getExpenseData($date){
				$resultExpenseList = $this->cDB->getDesign(PETTY_EXPENSE_DESIGN_DOCUMENT)->getView(PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_EXPENSE)->setParam(array("include_docs"=>"true","startkey"=>'"'.$date.'"',"endkey"=>'"'.$date.'"'))->execute();
				return $resultExpenseList;
		}

		
		/* Function To Get Todays Bill For Login Store */
		function getBills($date){
				$resultBillList = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getList(BILLING_DESIGN_DOCUMENT_LIST_SALES_REGISTER, BILLING_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS)->setParam(array("include_docs"=>"true","descending"=>"true","endkey" => '["'.$date.'"]',"startkey" => '["'.$date.'",{},{},{}]'))->execute();
				return 	$resultBillList;
		}

		/* Function To Save Petty Expense AND Petty Inward */
		function save(){
			$return = array('error'=>false,'message'=>'','data'=>array());
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$this->log->trace("DATA \r\n".json_encode($_POST));
				if(array_key_exists('expense_head',$_POST)){
	                $_POST['cd_doc_type'] = PETTY_EXPENSE_DOC_TYPE;
					$_POST['expense_time'] = $this->getCTime();
					$return['message'] = 'Please Start Shift, Befor Saving Expense';					
				}elseif(array_key_exists('inward_amount',$_POST)){
	                $_POST['cd_doc_type'] = PETTY_INWARD_DOC_TYPE;
					$_POST['inward_time'] = $this->getCTime();
					$return['message'] = 'Please Start Shift, Befor doing Petty Cash Inward';
				}
				
				if(array_key_exists('shift', $_SESSION['user']) && array_key_exists('store', $_SESSION['user'])){
					$_POST['shift_no'] = $_SESSION['user']['shift'];
					$_POST['store_id'] = $_SESSION['user']['store']['id'];
					$_POST['store_name'] = $_SESSION['user']['store']['name'];
					$result = $this->cDB->saveDocument()->execute($_POST);
					$this->log->trace("SAVE PETTY EXPENSE RESULT \r\n".json_encode($result));
					if(array_key_exists('error', $result)){
						$return['error'] = true;
						$return['message'] = ERROR.' '.$result['error'];
					}else{
							$return['message'] = SUCCESS;
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