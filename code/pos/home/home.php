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
			$sr = new sales_register();
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
			$data['shift_data'] = $result;
			$data['staff_list'] = $sr->getStaffList();
			$data['total_shift'] = $totalShifts;
			$configHead = $this->getConfig($this->cDB, 'head');
			$data['head_data'] = $configHead['data']['head'];
			$data['expense_data'] = $sr->getExpenseData($this->getCDate());

			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->view($data);			
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		} 
	}
