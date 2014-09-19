<?php
	class Home extends App_config{
		private $cDB;
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
		}
		public function index(){
			$result = $this->cDB->getDesign('store')->getView('store_shift')->setParam(array('key'=>'"'.$this->getCDate().'"','include_docs'=>'true'))->execute();
			$data = array();
			
			require_once DIR.'/sales_register/sales_register.php';
			require_once DIR.'/billing/billing.php';
			$sr = new sales_register();
			$bl = new billing();

			$data = $sr->getBills($this->getCDate());
			$data['is_store_open'] = 'false';
			$data['is_shift_running'] = 'false';
			$totalShifts = 0;
			if(count($result['rows']) == 1){
				$totalShifts = count($result['rows'][0]['doc']['shift']);
				if(empty($result['rows'][0]['doc']['day']['end_time'])){
					$data['is_store_open'] = 'true';
					if($totalShifts != 0 && empty($result['rows'][0]['doc']['shift'][$totalShifts-1]['end_time'])){
						$data['is_shift_running'] = 'true';
						$_SESSION['user']['shift'] = $result['rows'][0]['doc']['shift'][$totalShifts-1]['shift_no'];
						$_SESSION['user']['counter'] = $result['rows'][0]['doc']['shift'][$totalShifts-1]['counter_no'];
						$data['shift_starter'] = $result['rows'][0]['doc']['shift'][$totalShifts-1]['start_staff_name'];
					}
				}
			}
			$todaysale = json_decode($bl->getTodaysSale(),true);
			$data['payment_sum'] = (!$todaysale['error']) ? $todaysale['data'] : array();
			$data['shift_data'] = $result;
			$data['staff_list'] = $sr->getStaffList();
			$data['total_shift'] = $totalShifts;
			$configHead = $this->getConfig($this->cDB, 'head');
			$data['head_data'] = $configHead['data']['head'];
			$data['expense_data'] = $sr->getExpenseData($this->getCDate());

			//print_r($data['payment_sum']);
			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->view($data);			
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		} 

		function save(){
			global $couch;
			$return = array('error'=>false,'message'=>'','data'=>array());
			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				$this->log->trace("DATA \r\n".json_encode($_POST));
                $_POST['cd_doc_type'] = 'petty_inward';
				$_POST['inward_time'] = $this->getCTime();
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
