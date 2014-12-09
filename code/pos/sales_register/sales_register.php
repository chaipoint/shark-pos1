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
				$date1 = $date2 = $this->getCDate();
				if(array_key_exists('sales_reg_search', $_GET)){
					$date1 = date('Y-m-d',strtotime($_GET['sales_reg_search']));
					$date2 = date('Y-m-d',strtotime($_GET['sales_reg_search1']));
				}	
				$resultBillList = $this->getBills($date1, $date2);
				$resultExpenseList = $this->getExpenseData($date1, $date2);
				$resultCardLoadSale = $this->getCardLoadSale($date1);
				$resultLastPpcBill = $this->getLastPpcBill($this->getCDate()); 
				
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
					$resultBillList['card_load_data'] = $resultCardLoadSale;
					$resultBillList['last_ppc_bill'] = $resultLastPpcBill;
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
		public function getTodaysSale(){
			global $PAYMENT_MODE;
			$return = array('error'=>false,'message'=>'','data'=>array());
			$date1 = date('Y-m-d', strtotime($_POST['date1']));
			$date2 = date('Y-m-d', strtotime($_POST['date2']));
			
			$bills = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getList(BILLING_DESIGN_DOCUMENT_LIST_TODAYS_SALE, BILLING_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS)->setParam(array("descending"=>"true","include_docs"=>"true","endkey"=>'["'.$date1.'"]',"startkey"=>'["'.$date2.'", {},{},{}]'))->execute();
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

		/* Function To Get Card Load Sale */
		function getCardLoadSale($date){
				$resultSale = $this->cDB->getDesign(CARD_SALE_DESIGN_DOCUMENT)->getView(CARD_SALE_DESIGN_DOCUMENT_VIEW_GET_SALE)->setParam(array("include_docs"=>"true","startkey"=>'"'.$date.'"',"endkey"=>'"'.$date.'"'))->execute();
				return $resultSale;
		}
		
		/* Function To Get Last PPC Bill */
		function getLastPpcBill($date){
				$resultLastBill = $this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getView(PPC_DETAIL_DESIGN_DOCUMENT_VIEW_LAST_BILL)->setParam(array("include_docs"=>"true","startkey"=>'"'.$date.'"',"endkey"=>'"'.$date.'"',"descending"=>"true","limit"=>"1"))->execute();
				$lastBill = '';
				if(count($resultLastBill['rows'])>0){
					$lastBill = $resultLastBill['rows'][0]['doc']['invoice_number'];
				}
				return $lastBill;
		}

		/* Function To Get Petty Expense */
		function getExpenseData($date1, $date2){
				$resultExpenseList = $this->cDB->getDesign(PETTY_EXPENSE_DESIGN_DOCUMENT)->getView(PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_EXPENSE)->setParam(array("include_docs"=>"true","startkey"=>'"'.$date1.'"',"endkey"=>'"'.$date2.'"'))->execute();
				return $resultExpenseList;
		}

		
		/* Function To Get Todays Bill For Login Store */
		function getBills($date1, $date2){
				$resultBillList = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getList(BILLING_DESIGN_DOCUMENT_LIST_SALES_REGISTER, BILLING_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS)->setParam(array("include_docs"=>"true","descending"=>"true","endkey" => '["'.$date1.'"]',"startkey" => '["'.$date2.'",{},{},{}]'))->execute();
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