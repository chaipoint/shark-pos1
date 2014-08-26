<?php	
	class Billing extends App_config{
		private $cDB;
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
			$this->log =  Logger::getLogger("CP-POS|BILLING");
		}
		function index(){

			//Block to get Configs and need to have a generic methode for that
			$configList = $this->cDB->getDesign('config')->getView('config_list')->setParam(array('include_docs'=>'true',"startkey"=>'["bill_config"]',"endkey"=>'["bill_config",{}]'))->execute();
			$deliveryChannel = array_key_exists(0, $configList['rows']) ? $configList['rows'][0]['doc']['category_data']['delivery_channel'] : array();
/*			foreach($configList['rows'][0]['doc']['category_data']['delivery_channel'] as $key => $value){
				$deliveryChannel[$value] = $key;
			}/**/


			$resultJSON = $this->cDB->getDesign('store')->getView('store_mysql_id')->setParam(array('include_docs'=>'true',"key"=>'"'.$_SESSION['user']['store']['id'].'"'))->execute();
//			print_r();
			$result = $resultJSON['rows'][0]['doc'];
			if(!array_key_exists('mysql_id', $_SESSION['user']['store'])){
				unset($resultJSON['rows'][0]['doc']['menu_items']);
				foreach($resultJSON['rows'][0]['doc'] as $key => $data){
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

			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->view(array('catList'=>$catList,'productList'=>$productList,'firstCat'=>$firstCat, 'delivery_channel'=>$deliveryChannel,'bill'=>$billData));
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		}

		public function getBillData($bill_id){
				$return = array('error'=>false, 'message'=>'', 'data' => array());
  				$billDetails = $this->cDB->getDocs($bill_id);
  				if(array_key_exists('error', $billDetails)){
  					$return['message'] = ($billDetails['error'] == 'not_found' ? 'Bill Not Found' : 'OOPS! Some Error Please Contact Admin.');
  					$return['error'] = true;
  				}else{
 						$return['data'] = $billDetails;
  				}
  				return $return;
		}


		function save(){
			global $couch;
			$return = array('error'=>false,'message'=>'','data'=>array());
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(array_key_exists('request_type', $_POST) && $_POST['request_type'] == 'save_bill'){
					$this->log->trace("DATA \r\n".json_encode($_POST));

					$_POST['cd_doc_type'] = 'store_bill';
					$_POST['bill_time'] = $this->getCDTime();

					$_POST['store_id'] = $_SESSION['user']['store']['mysql_id'];
					$_POST['store_name'] = $_SESSION['user']['store']['name'];
					$_POST['staff_id'] = $_SESSION['user'][$this->userIdField];
					$_POST['staff_name'] = $_SESSION['user']['name'];
					$_POST['location_id'] = $_SESSION['user']['store']['location']['id'];
					$_POST['location_name'] = $_SESSION['user']['store']['location']['name'];
				//	$_POST['bill_status'] = 'open';
					$_POST['reprint'] = 0;
					$_POST['is_updated'] = 'N';
				//	$_POST['channel'] = 'store';
					$_POST['card_no'] = 'XXXXX';
					$_POST['coupon_code'] = 'XXXXX';
					$_POST['counter'] = '1';
					$_POST['shift'] = '1';

					if( $_POST['order_no'] > 0 ){
						$orderNO = $this->cDB->getDesign('billing')->getView('bill_by_order')->setParam(array('key'=> '"'.$_POST['order_no'].'"' ))->execute();
						if(array_key_exists(0, $orderNO['rows'])){
								$return['error'] = true;
								$return['message'] = "Order Already Billed";
								$re = json_encode($return);
								$this->log->trace("RESPONSE \r\n".$re);
								return $re;							
						}
						//http://127.0.0.1:5984/testing/_design/billing/_view/bill_by_order?key=%2211834%22
					}


					$currentBillNo = $couch->getDesign('billing')->getUpdate('getbillno','generateBill')->setParam(array('month'=>$this->getCMonth()))->execute();
					if(is_numeric($currentBillNo)){
						$_POST['bill_no'] = $currentBillNo;
						unset($_POST['request_type']);					

						$result = $couch->saveDocument()->execute($_POST);
						if(array_key_exists('ok', $result)){
							$return['data']['bill_no'] = $currentBillNo;
						}
					}else{
						$return['error'] = true;
						$return['message'] = 'OOPS! Some Error Contact Admin.';
					}
				}elseif(array_key_exists('request_type', $_POST) && $_POST['request_type'] == 'update_bill'){
					$billDataReturned = $this->getBillData($_POST['doc']);
					if($billDataReturned['error']){
						$return['error'] = true;
						$return['message'] = $billDataReturned['message'];
					}else{
						$billDataReturned['data']['parent']['id'] = $billDataReturned['data']['_id'];
						$billDataReturned['data']['parent']['rev'] = $billDataReturned['data']['_rev'];
						unset($billDataReturned['data']['_id']);
						unset($billDataReturned['data']['_rev']);
						$billDataReturned['data']['bill_status'] = 'Cancelled';
						$billDataReturned['data']['cancel_reason'] = $_POST['cancel_reason'];
						$billSaveResult = $couch->saveDocument()->execute($billDataReturned['data']);
						if(!array_key_exists('ok', $billSaveResult)){
							$return['error'] = true;
							$return['message'] = 'OOPS! Some Error Contact Admin.';
						}
					}
				}else{
					$return['error'] = true;
					$return['message'] = 'Request Type not Found';
				}
			}else{
				$return['error'] = true;
				$return['message'] = 'Request Method Not Allowed';
			}
			$re = json_encode($return);
			$this->log->trace("RESPONSE \r\n".$re);
			return $re;
		}


		public function getSaleBills(){
			$return = array('error'=>false,'message'=>'','data'=>array());
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$bills = $this->cDB->getDesign('billing')->getView('sales_summary')->setParam(array("group"=>"true","startkey"=>'["'.$this->getcDate().'"]',"endkey"=>'["'.$this->getcDate().'",{}]'))->execute();
//				$return['data']  = array_reverse($bills['rows']);
				//print_r($bills['rows']);
				$bill_array = array();
                $payment_type = array('cash'=>0,'ppc'=>0,'c_card'=>0);
				foreach ($bills['rows'] as $key => $value) {
					$bill_array[$value['key'][1]][$value['key'][2]]=$value['value'];
					
					$payment_type[$value['key'][2]] += $value['value'];
				}
				$return['data']['summary'] = $bill_array;
				$return['data']['payment_type'] = $payment_type;
			}else{
				$return['error'] = true;
				$return['message'] = 'Request Method Not Allowed';
			}
			return json_encode($return);
		}

        public function getBalanceInq(){
			$dir =  dirname(__FILE__).'/../lib/svc/ppc_api.php';
            require_once $dir;
		
			$return = array('error'=>false,'message'=>'','data'=>array());
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$balance_check = array();
			$balance_deduction = array();	
			$balance_check = balanceINQ($_POST);
			//print_r($balance_check);
			  if(is_array($balance_check) && $balance_check['error']==true) {
                
                $return['error'] = true;
				$return['message'] = $balance_check['msg'];
				return json_encode($return);
			  
			  }else {

			    $return['error'] = false;
				$return['message'] = 'Your Balance';
				$return['data']['balance'] = $balance_check['balance'];
				return json_encode($return); 

			  } 
			}

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