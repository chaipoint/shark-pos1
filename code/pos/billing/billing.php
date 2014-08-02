<?php
	session_start();
	//print_r($_SESSION);
	class Billing extends App_config{
		private $cDB;
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
		}
		function index(){
			global $couch;
			$resultJSON = json_decode($couch->getDesign('store')->getView('store_list',$_SESSION['user']['store']['id'])->execute(),true);
			$result = $resultJSON['rows'][0]['value'];
			
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


			$this->commonView('html_header');
			$this->commonView('navbar');
			$this->view(array('catList'=>$catList,'productList'=>$productList,'firstCat'=>$firstCat));
			$this->commonView('inner_footer');
			$this->commonView('html_footer');
		}
		function save(){
			global $couch;
			$return = array('error'=>false,'message'=>'','data'=>array());
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(array_key_exists('request_type', $_POST) && $_POST['request_type'] == 'save_bill'){
					$_POST['cd_doc_type'] = 'store_menu_bill';
					$_POST['billed_by'] = $_SESSION['user'][$this->userIdField];
					$_POST['bill_time'] = $this->getCDTime();
					$currentBillNo = $couch->getDesign('billing')->getUpdate('getbillno','generateBill')->execute(array('month'=>$this->getCMonth()));
					if(is_numeric($currentBillNo)){
						$_POST['bill_no'] = $currentBillNo;
						unset($_POST['request_type']);					

						$result = json_decode($couch->saveDocument()->execute($_POST),true);
						if(array_key_exists('ok', $result)){
							$return['data']['bill_no'] = $currentBillNo;
						}
					}else{
						$return['error'] = true;
						$return['message'] = 'OOPS! Some Error Contact Admin.';
					}
				}else{
					$return['error'] = true;
					$return['message'] = 'Request Type not Found';
				}
			}else{
				$return['error'] = true;
				$return['message'] = 'Request Method Not Allowed';
			}
			return json_encode($return);
		}

		public function getSaleBills(){
			$return = array('error'=>false,'message'=>'','data'=>array());
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$bills = json_decode($this->cDB->getDesign('billing')->getView('bill_by_date',date('Y-m-d'))->execute(),true);
				$return['data']  = array_reverse($bills['rows']);
			}else{
				$return['error'] = true;
				$return['message'] = 'Request Method Not Allowed';
			}
			return json_encode($return);
		}
	}