<?php	
	class Billing extends App_config{
		private $configData;
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|BILLING");
			$this->getDBConnection($this->cDB);
			$configResult = $this->getConfig($this->cDB, array('channel', 'bill_status', 'payment_mode', 'ppa_api', 'delivery_channel', 'company_details'));
			$this->configData = (count($configResult['data']) > 0) ? $configResult['data'] : array();
		}

		/* This Function Is Automatically Called When We Come On Billing Module */
		function index(){ 
			if(array_key_exists('referer',$_GET) && $_GET['referer'] == HOME){
			}else{
				if(!array_key_exists('shift', $_SESSION['user'])){
					header("LOCATION:index.php");
				}
			}
			
			//Block sto get Configs and need to have a generic methode for that
			$data = array('error' => false,'catList'=>array(),'productList'=>array(),'firstCat'=>0, 'config_data'=>array(),'bill'=>array(),'lastBillNo'=>'','lastBillTime'=>'');
			$resultStoreMenu = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID)->setParam(array('include_docs'=>'true',"key"=>'"'.$_SESSION['user']['store']['id'].'"'))->execute();
			$this->log->trace('LOGIN STORE DETAIL'."\r\n".json_encode($resultStoreMenu));
			$resultLastBill = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getView(BILLING_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS)->setParam(array("descending"=>"true","endkey" => '["'.$this->getCDate().'"]',"startkey" => '["'.$this->getCDate().'",{},{},{}]',"limit"=>"1"))->execute();
			$this->log->trace('LAST BILL DETAILS'."\r\n".json_encode($resultLastBill));

			$lastBillNo = '';
			$lastBillTime = '';
			if(array_key_exists('rows', $resultLastBill) && count($resultLastBill['rows'])>0){
				$lastBillNo = $resultLastBill['rows'][0]['key'][1] ;
				$lastBillTime = $resultLastBill['rows'][0]['key'][3] ;	
			}
			
			if(array_key_exists('cMessage', $resultStoreMenu)){
				$data['error'] = true;
				header("Location:index.php?error=true");
				die;
			}else{
				$result = $resultStoreMenu['rows'][0]['doc'];
				if(!array_key_exists('mysql_id', $_SESSION['user']['store'])){
					unset($resultStoreMenu['rows'][0]['doc']['menu_items']);
					foreach($resultStoreMenu['rows'][0]['doc'] as $key => $data){
						$_SESSION['user']['store'][$key] = $data;
					}
				}
				
				$catList = array();
				$productList = array();
				foreach($result['menu_items'] as $key => $Items){
					if(!empty($Items['category']['id'])){
						$catList[$Items['category']['id']] = $Items['category']['name']; 
						$productList[$Items['category']['id']][] = $Items;
					}
		 		}
				$catList[100] = 'C@W';
		 		ksort($catList);
		 		$currectCat = array_keys($catList);
	  			$firstCat = $currectCat[0];


				$billData = array();
	  			if(array_key_exists('bill_no', $_GET) && ! empty($_GET['bill_no'])){
	  				$bill = $_GET['bill_no'];
					$billDataReturned = $this->getBillData($bill); 
					if(!$billDataReturned['error']){
	 						$billData = $billDataReturned['data'];
					}
	  			}
	  			$data = array('error'=>false, 'catList'=>$catList, 'productList'=>$productList, 'firstCat'=>$firstCat, 'config_data'=>$this->configData, 'bill'=>$billData, 'lastBillNo'=>$lastBillNo, 'lastBillTime'=>$lastBillTime);

	  		}
	  		/* To Check Print Utility Exists Or Not */
	  		$utilityCheck = file_exists(EXE_PATH);
			$data['printUtility'] = ($utilityCheck ? 'true' : 'false');
			if($utilityCheck){
				$company_data = array();
			    $company_data = $this->configData['company_details'];
				$this->log->trace("COMPANY DETAILS TO BE PRINT \r\n".json_encode($company_data));
				file_put_contents(COMPANY_DETAIL_TXT_PATH, json_encode($company_data,true));
			}
	  		/* To Get Retail Customer*/
	  		require_once DIR.'/customer/customer.php';
	  		$cus = new Customer();
	  		$data['customer'] = $cus->retail_customer();
	  		
			$this->commonView('header_html',array('error'=>$data['error']));
			$this->commonView('navbar');
			if(!$data['error']){
				$this->view($data);
			}
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		}

		/* Function To Get Retail Customer Detail */
		public function getRetailCustomer(){
			$return = array('error'=>false, 'message'=>'', 'data' => array());

			if(array_key_exists('request_type', $_REQUEST) && $_REQUEST['request_type']==GET_RETAIL_CUSTOMER){
				$resultRetailCustomer = $this->cDB->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_RETAIL_CUSTOMER_LIST)->setParam(array('key'=>$_REQUEST["customer_id"],'include_docs'=>'true'))->execute();
				$this->log->trace('RETAIL CUSTOMER DETAILS'."\r\n".json_encode($resultRetailCustomer));
				if(array_key_exists('rows', $resultRetailCustomer)){
					$rows = $resultRetailCustomer['rows'];
					foreach($rows as $key => $value){
						$return['data']['name'] = $value['doc']['name']; 
						$return['data']['phone'] = $value['doc']['phone']; 
						$return['data']['address'] = $value['doc']['address'];
						$return['data']['contact_person'] = $value['doc']['contact_person'];
						$return['data']['email'] = $value['doc']['e_mail'];
					}
				}
			}else{
				$return['error'] = true;
				$return['message'] = REQUEST_TYPE_NOT_ALLOWED;
			}
			return $return;
		}

		public function rePrint(){
				$return = array('error'=>false, 'message'=>'', 'data' => array());

				$bill = $_POST['doc'];
				$billDataReturned = $this->getBillData($bill); 
				if(!$billDataReturned['error']){
	 				$billData = $billDataReturned['data'];
	 				$re = $this->printBill($billData);
	 				return json_encode($re);
	 			}else{
					$return['error'] = true;
					$return['message'] = $billDataReturned['message'];
					return json_encode($return);
				}
		}
		public function in_array_r($item , $array){
				return preg_match('/"'.$item.'"/i' , json_encode($array));
		}
		/* Function To Get Caw Product */
		public function getCawProduct(){
			$return = array('error'=>false, 'message'=>'', 'data' => array());
			$customer_id = $_POST['customer_id'];
			$resultCawProduct = $this->cDB->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_RETAIL_CUSTOMER_LIST)->setParam(array('key'=>$customer_id,'include_docs'=>'true'))->execute();
			$this->log->trace('CAW PRODUCT DETAILS'."\r\n".json_encode($resultCawProduct));
			
			if(array_key_exists('rows', $resultCawProduct) && count($resultCawProduct['rows'])>0){
				$rows = $resultCawProduct['rows'][0]['doc']['schedule'];
				$day_number = date('N', strtotime(date('d-m-Y')));
				$productList = array();
				if(array_key_exists($day_number, $rows)){
					foreach($rows[$day_number] as $key => $value){ 
						if($this->in_array_r($value['name'] , $productList)){
						}else{
							$productList[100][] = $value;
						}
					} 
					$return['data'] = json_encode($productList, true);
				}else{
					$return['error'] = true;
					$return['message'] = 'NO ORDER SCHEDULE FOR CURRENT DAY';
				}
				
			}else{
				$return['error'] = true;
				$return['message'] = 'Customer Not Found';
			}
			return $return;
		}
			
		/* Function To Get Bill Data */
		public function getBillData($bill_id){
				$return = array('error'=>false, 'message'=>'', 'data' => array());

  				$billDetails = $this->cDB->getDocs($bill_id);
  				$this->log->trace('BILL DETAILS'."\r\n".json_encode($billDetails));
  				if(array_key_exists('error', $billDetails)){
  					$return['message'] = ($billDetails['error'] == 'not_found' ? 'Bill Not Found' : ERROR.' '.$billDetails['error']);
  					$return['error'] = true;
  				}else{
 					$return['data'] = $billDetails;
  				}
  				return $return;
		}

		/* Function To Save Bill Data */
		function save(){
			$return = array('error'=>false,'message'=>'','data'=>array());
			
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(array_key_exists('request_type', $_POST) && $_POST['request_type'] == SAVE_BILL){
					$this->log->trace("DATA \r\n".json_encode($_POST));
					$_POST['cd_doc_type'] = BILLING_DOC_TYPE ;
					$_POST['time'] = array('created'=>$this->getCDTime(), 'updated'=>$this->getCDTime());
					$_POST['store_id'] = $_SESSION['user']['store']['mysql_id'];
					$_POST['store_name'] = $_SESSION['user']['store']['name'];
					$_POST['staff_id'] = $_SESSION['user'][$this->userIdField];
					$_POST['staff_name'] = $_SESSION['user']['name'];
					$_POST['location_id'] = $_SESSION['user']['store']['location']['id'];
					$_POST['location_name'] = $_SESSION['user']['store']['location']['name'];
					$_POST['reprint'] = 0;
					$_POST['is_updated'] = 'N';
					$_POST['card_no'] = NA;
					$_POST['coupon_code'] = NA;
					$_POST['counter'] = $_SESSION['user']['counter'];
					$_POST['shift'] = $_SESSION['user']['shift'];

					if( $_POST['order_no'] > 0 ){
						$orderNO = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getView(BILLING_DESIGN_DOCUMENT_VIEW_BILL_BY_ORDER)->setParam(array('key'=> '"'.$_POST['order_no'].'"' ))->execute();
						$this->log->trace("GET ORDER DETAILS \r\n".json_encode($orderNO));
						if(array_key_exists('cMessage', $orderNO)){
								$return['error'] = true;
								$return['message'] = ERROR.' '.$orderNO['error'];
								$re = json_encode($return);
								$this->log->trace("RESPONSE \r\n".$re);
								return $re;							
						}elseif(array_key_exists(0, $orderNO['rows'])){
								$this->log->debug("Retry For of Bill For Order No \r\n".$_POST['order_no']);
								$return['error'] = true;
								$return['message'] = ORDER_ALREADY_BILLED;
								$re = json_encode($return);
								$this->log->trace("RESPONSE \r\n".$re);
								return $re;							
						}
					}

					$currentBillNo = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getUpdate(BILLING_DESIGN_DOCUMENT_UPDATE_GET_BILL_NO,'generateBill')->setParam(array('month'=>$this->getCMonth()))->execute();
					$this->log->trace("GET CURRENT BILL NO \r\n".$currentBillNo);
					if(is_numeric($currentBillNo)){
						$_POST['bill_no'] = $currentBillNo;
						unset($_POST['request_type']);					
						$result = $this->cDB->saveDocument()->execute($_POST);
						$this->log->trace("SAVE BILL RESULT \r\n".json_encode($result));
						if(array_key_exists('ok', $result)){
							if($_POST['utility_check']=='true'){
								$res = $this->printBill($_POST);
								$return['error'] = $res['error'];
			                    $return['message'] = $res['message'];
							}
			                $return['data']['bill_no'] = $currentBillNo;
						}
					}else{
						$return['error'] = true;
						$return['message'] = ERROR;
					}
				}elseif(array_key_exists('request_type', $_POST) && $_POST['request_type'] == UPDATE_BILL){
					$billDataReturned = $this->getBillData($_POST['doc']);
					if($billDataReturned['error']){
						$return['error'] = true;
						$return['message'] = $billDataReturned['message'];
					}else{
						if($billDataReturned['data']['bill_status_id'] != $_POST['bill_status_id']){
							$billDataReturned['data']['parent']['id'] = $billDataReturned['data']['_id'];
							$billDataReturned['data']['parent']['rev'] = $billDataReturned['data']['_rev'];
							unset($billDataReturned['data']['_id']);
							unset($billDataReturned['data']['_rev']);
							$billDataReturned['data']['bill_status'] = $_POST['bill_status_name'];
							$billDataReturned['data']['bill_status_id'] = $_POST['bill_status_id'];
							$billDataReturned['data']['time']['updated'] = $this->getCDTime();
							$billDataReturned['data']['cancel_reason'] = array_key_exists('cancel_reason', $_POST) ? $_POST['cancel_reason'] : '';
							if($billDataReturned['data']['payment_type']==PPC){
								$data = array();
								$data['amount'] = $billDataReturned['data']['card']['redeem_amount'];
								$data['txn_no'] = $billDataReturned['data']['card']['txn_no'];
								$data['approval_code'] = $billDataReturned['data']['card']['approval_code'];
								$data['invoice_number'] = $billDataReturned['data']['card']['invoice_number'];
								$data['card_number'] = $billDataReturned['data']['card']['no'];
								$data['cancel_reason'] = $_POST['cancel_reason'];
								$dir =  dirname(__FILE__).'/../lib/svc/ppc_api.php';
               					require_once $dir;
                				$ppc = new PpcAPI();
                				$cancelResponse = $ppc->cancel($data, CANCEL_REDEEM);
                				if($cancelResponse['data']['success']=='False'){
                					$return['error'] = true;
                					$return['message'] = $cancelResponse['data']['message'];
                					$res= json_encode($return);
                					return $res;
                				}else{
                					$billDataReturned['data']['card_cancel_detail']['no'] = $cancelResponse['data']['card_number'];
                					$billDataReturned['data']['card_cancel_detail']['type'] = PPC;
                					$billDataReturned['data']['card_cancel_detail']['refund_amount'] = $billDataReturned['data']['card']['redeem_amount'];
                					$billDataReturned['data']['card_cancel_detail']['txn_no'] = $cancelResponse['data']['txn_no'];
                					$billDataReturned['data']['card_cancel_detail']['approval_code'] = $cancelResponse['data']['approval_code'];
                					$billDataReturned['data']['card_cancel_detail']['balance'] = $cancelResponse['data']['balance'];
                				}
							}
							$billSaveResult = $this->cDB->saveDocument()->execute($billDataReturned['data']);
							$this->log->trace("SAVE BILL RESULT \r\n".json_encode($billSaveResult));
							$return['message'] = 'Bill <b>'.$_POST['bill_status_name']."</b> Successfully".(strlen($billDataReturned['data']['cancel_reason'])>0 ? '<br/>Change Return is <b>'.$_POST['due_amount'].'</b>' : '');
							if(!array_key_exists('ok', $billSaveResult)){
								$return['error'] = true;
								$return['message'] = ERROR.' '.$billSaveResult['error'];
							}
						}else{
								$return['error'] = true;
								$return['message'] = 'Bill is Already <b>'.$_POST['bill_status_name'].'</b>';							
						}
					}
				}else{
					$return['error'] = true;
					$return['message'] = REQUEST_TYPE_NOT_ALLOWED;
				}
			}else{
				$return['error'] = true;
				$return['message'] = REQUEST_METHOD_NOT_ALLOWED;
			}
			$re = json_encode($return);
			$this->log->trace("RESPONSE \r\n".$re);
			return $re;
		}

		/* Function To Bill Through PPC METHOD  */
        public function ppcBill(){
			$return = array('error'=>false,'message'=>'','data'=>array());
			$dir =  dirname(__FILE__).'/../lib/svc/ppc_api.php';
            require_once $dir;
			$ppc = new PpcAPI();
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$balanceCheck = array();
				$balanceDeduction = array();	
				$balanceDeduction = $ppc->ppcOperation($_POST, PPC_REDEEM);
				$this->log->trace("PPC REDEEMING METHOD RESPONSE \r\n".json_encode($balanceDeduction));
				$res = json_encode($balanceDeduction,true);
				return $res;
			}
		}

		/* Function To Bill Through PPA METHOD  */
		public function ppaBill(){
			$return = array('error'=>false,'message'=>'','data'=>array());
			$dir =  dirname(__FILE__).'/../lib/api/ppa_api.php';
            require_once $dir;
            $storeDetails = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID)->setParam(array('include_docs'=>'true',"key"=>'"'.$_SESSION['user']['store']['id'].'"'))->execute();
			$result = $storeDetails['rows'][0]['doc'];
			$config_data = $this->configData['ppa_api'];
			$config_data['username'] = $result['ppa_details']['uid'];
			$config_data['password'] = $result['ppa_details']['pwd'];
			//unset($config_data['username']);
			//unset($config_data['password']);
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
            	$balanceDeduction = array();
            	$invoiceNumber = $this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getUpdate(PPC_DETAIL_DESIGN_DOCUMENT_UPDATE_GET_BILL_NO,'generateppcBill')->setParam(array('date'=>$this->getCDate()))->execute();;
				$balanceDeduction = ppa_api($config_data, $_POST, PPA_REDEEM, $invoiceNumber);
				$res = json_encode($balanceDeduction,true);
				return $res;
			}
        }
        

        /* Function To Load Or Activate Card */
        function loadCard(){
        	$return = array('error'=>false,'message'=>'','data'=>array());
        	$loadResponse = array();
        	if(array_key_exists('request_type', $_POST) && ($_POST['request_type'] == LOAD_PPA_CARD || $_POST['request_type'] == REWARD_REDEMPTION || $_POST['request_type'] == REWARD_CHECK)){
        		$dir =  dirname(__FILE__).'/../lib/api/ppa_api.php';
            	require_once $dir;
            	$storeDetails = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID)->setParam(array('include_docs'=>'true',"key"=>'"'.$_SESSION['user']['store']['id'].'"'))->execute();
				$result = $storeDetails['rows'][0]['doc'];
            	$config_data = $this->configData['ppa_api'];
            	$config_data['username'] = $result['ppa_details']['uid'];
				$config_data['password'] = $result['ppa_details']['pwd'];
				//unset($config_data['username']);
				//unset($config_data['password']);
            	$card_type = PPA;
            	$invoiceNumber = '';
            	if($_POST['request_type'] != REWARD_CHECK){
            		$invoiceNumber = $this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getUpdate(PPC_DETAIL_DESIGN_DOCUMENT_UPDATE_GET_BILL_NO,'generateppcBill')->setParam(array('date'=>$this->getCDate()))->execute();;
            	}
            	$loadResponse = ppa_api($config_data, $_POST, $_POST['request_type'], $invoiceNumber);
            
            }else if(array_key_exists('request_type', $_POST) && ($_POST['request_type'] == LOAD_PPC_CARD || $_POST['request_type'] == ACTIVATE_PPC_CARD || $_POST['request_type'] == ISSUE_PPC_CARD || $_POST['request_type'] == GET_CUSTOMER_INFO || $_POST['request_type'] == BALANCE_CHECK_PPC_CARD )){
            	$dir =  dirname(__FILE__).'/../lib/svc/ppc_api.php';
                require_once $dir;
                $ppc = new PpcAPI();
                $card_type = PPC;
                if($_POST['request_type'] == GET_CUSTOMER_INFO){
                	$loadResponse = $ppc->reIssue($_POST, $_POST['request_type']);
                }else{
					$loadResponse = $ppc->ppcOperation($_POST, $_POST['request_type']);
				}
			}
				if(!empty($loadResponse['data']) && $loadResponse['data']['success']=='True' && $loadResponse['data']['txn_type']!=BALANCE_CHECK && $loadResponse['data']['txn_type']!=REISSUE_PPC_CARD){
					$saveData = array();
					$saveData['cd_doc_type'] = CARD_SALE_DOC_TYPE;
					$saveData['card_no'] = $loadResponse['data']['card_number'];
					$saveData['card_type'] = $card_type;
					$saveData['txn_no']	= $loadResponse['data']['txn_no'];
					$saveData['approval_code']	= $loadResponse['data']['approval_code'];
					$saveData['amount'] = $_POST['amount'];
					$saveData['balance'] = $loadResponse['data']['balance'];
					$saveData['txn_type'] = $loadResponse['data']['txn_type'];
					$saveData['time'] = date('Y-m-d H:i:s');
					$saveData['status'] = PAID;
					$saveData['store_id'] = $_SESSION['user']['store']['id'];
					$saveData['store_name'] = $_SESSION['user']['store']['name'];
					$saveData['staff_id'] = $_SESSION['user']['mysql_id'];
					$saveData['staff_name'] = $_SESSION['user']['name'];
					$saveData['invoice_number'] = $loadResponse['data']['invoice_number'];
					$saveData['shift'] = $_SESSION['user']['shift'];
					$result = $this->cDB->saveDocument()->execute($saveData);
					$path = file_exists(EXE_PATH);
					if(array_key_exists('ok', $result) && $path){ 
						file_put_contents(CARD_SALE_TXT_PATH, json_encode($saveData,true));
        				exec(EXE_PATH,$output,$return_value);
					}
				}
				$res = json_encode($loadResponse,true);
				return $res;
			}

		/* Function To Cancel Card Load Transaction */
		function cancelLoad(){
			$return = array('error'=>false,'message'=>'','data'=>array());
			if(array_key_exists('request_type', $_POST) && ($_POST['request_type'] == CANCEL_LOAD)){
				$dir =  dirname(__FILE__).'/../lib/svc/ppc_api.php';
                require_once $dir;
                $ppc = new PpcAPI();
                $cancelResponse = $ppc->cancel($_POST, $_POST['request_type']);
                if($cancelResponse['data']['success']=='False'){
					$return['error'] = true;
                	$return['message'] = $cancelResponse['data']['message'];
                }else{
                	$return['error'] = false;
                	$return['message'] = $cancelResponse['data']['message'].' Your Balance Is: '.$cancelResponse['data']['balance'];
                	$this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getUpdate(PPC_DETAIL_DESIGN_DOCUMENT_UPDATE_CHANGE_STATUS, $_POST['doc_id'])->setParam(array('status'=>CANCEL,'txn_no'=>$cancelResponse['data']['txn_no'],'approval_code'=>$cancelResponse['data']['approval_code'],'balance'=>$cancelResponse['data']['balance']))->execute();
				}

			} 
			$res = json_encode($return);
			return $res;
		}

        /* Function To Print Out Bill  */
        function printBill($data){ 
        	$return = array('error'=>false,'message'=>'','data'=>array());
			//$this->log->trace("BILL DETAILS TO BE PRINT \r\n".json_encode($data));
        	$path = EXE_PATH;
			if(!empty($data)) {
				file_put_contents(BILL_DETAIL_TXT_PATH, json_encode($data,true));
        		exec($path,$output,$return_value);
        		//$this->log->trace("EXE RESPONCE \r\n".json_encode($return_value));
        		$return['message'] = ($return_value=='1' ? 'Printer Not Found' : ($return_value=='2' ? 'File Missing' : ($return_value=='3' ? 'JSON Not Readable' : '')));
     		}else{
				$return['message'] = BILL_DATA_MISSING;
			}
			return $return;
        }


        function print_bill(){
			if(array_key_exists('bill', $_GET) && is_numeric($_GET['bill']) && $_GET['bill'] > 0){
				$billData = $this->cDB->getDesign('billing')->getView('bill_no')->setParam(array("include_docs"=>"true",'key'=>$_GET['bill']))->execute();
				if(array_key_exists(0, $billData['rows'])){
					$this->view(array('bill'=>$billData['rows'][0]['doc']));
				}
			}else{
				return 'Provide Valid Bill NO';
			}
		}

	}