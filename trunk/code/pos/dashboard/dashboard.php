<?php 
	class Dashboard extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|DASHBOARD");
		}
		function index(){
			$data = array('error'=>false, 'message' => '', 'data'=> array());
			$postData = array('action'=>STORE_AUDIT, 'store_id'=>$_SESSION['user']['store']['id']);
			$url = ACTIVITY_TRACKER_API_URL;
			$ch = curl_init();
			curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_CONNECTTIMEOUT=>3, CURLOPT_TIMEOUT=>5, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData));
			$result = curl_exec($ch);
			$curl_errno = curl_errno($ch);
			$curl_error = curl_error($ch);
			curl_close($ch);
			$response = array();
			if($curl_errno > 0){
				$data['error'] = true;
				$data['message'] = INTERNET_ERROR;
			}else{
				$res = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
				$response = json_decode($res, true);
			}
			
			$data['activity_data'] = $response;
			require_once DIR.'/home/home.php';
			$home = new Home();
			$data['reconcilation'] = $home->reconcilation();
			
			require_once DIR.'/sales_register/sales_register.php';
			$sr = new sales_register();
			$data['data'] = $sr->getBills($this->getCDate(), $this->getCDate());
			$data['last_ppc_bill'] = $sr->getLastPpcBill($this->getCDate());
			
			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->view($data);
			//$this->commonView('footer_inner');
			$this->commonView('footer_html');
			
		}
	}