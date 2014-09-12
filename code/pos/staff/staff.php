<?php	
	class Staff extends App_config{
		private $cDB;
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
		}
		public function index(){
			$result = $this->cDB->getDesign('store')->getView('store_shift')->setParam(array('key'=>'"'.$this->getCDate().'"','include_docs'=>'true'))->execute();
			$data = array();
			$data['is_store_open'] = 'false';
			$data['is_shift_running'] = 'false';
			$totalShifts = 0;
			if(count($result['rows']) == 1){
				$totalShifts = count($result['rows'][0]['doc']['shift']);
				if(empty($result['rows'][0]['doc']['time']['end'])){
					$data['is_store_open'] = 'true';
					if($totalShifts != 0 && empty($result['rows'][0]['doc']['shift'][$totalShifts-1]['end'])){
						$data['is_shift_running'] = 'true';
						$_SESSION['user']['shift'] = $totalShifts;
						$_SESSION['user']['counter'] = $result['rows'][0]['doc']['shift'][$totalShifts-1]['counter'];
					}
				}
			}		
			$data['total_shift'] = $totalShifts;
			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->view($data);			
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		} 
		function getStaff(){
			$name = $_REQUEST['token'];
			//$cDB
			/*			$query = "SELECT name label, id FROM staff_master WHERE active='Y' AND name LIKE '$name%' ORDER BY name ASC";
			$result = $db->func_query($query);/**/
			return json_encode($result,true);
		}	
		function save_petty(){
			
			$return = array('error'=>false, 'message'=>'', 'data'=>array());

			$result = $this->cDB->getDesign('store')->getView('store_shift')->setParam(array('key'=>'"'.$this->getCDate().'"', 'include_docs'=>'true'))->execute();
			$total_rows = count($result['rows']);
			if(array_key_exists('rows', $result) && $total_rows == 1){
				if($_GET['mode'] == 'day_start'){
					$return['error'] = true;
					$return['message'] = "Can't Start Store Day Again." ;
				}else{
					$data = array('type'=>'shift_start','counter_no'=>array_key_exists('counter_no', $_GET) ? $_GET['counter_no'] : '', 'time'=>$this->getCDTime(),'staff' =>$_SESSION['user']['mysql_id']);
					$return['message'] = 'Welcome, Shift has been started, Please Start Sales. <a href="index.php?dispatch=billing" class="btn btn-sm btn-primary">Start Billing</a>';

					if(count($result['rows']) == 1){
						$totalShifts = count($result['rows'][0]['doc']['shift']);
						if($totalShifts != 0 && empty($result['rows'][0]['doc']['shift'][$totalShifts-1]['end'])){
							$data = array('type'=>'shift_end','end_petty_cash'=>$_GET['petty_cash'], 'box_cash'=>$_GET['box_cash'], 'time'=>$this->getCDTime(), 'staff' =>$_SESSION['user']['mysql_id']);
							$return['message'] = 'Store Shift Ended';
							if($result['rows'][0]['doc']['shift'][$totalShifts-1]['staff'] != $_SESSION['user']['mysql_id']){
								$return['error'] = true;
								$return['message'] = 'You are not allowed to end Shift.';
							}else{
								unset($_SESSION['user']['shift']);
								unset($_SESSION['user']['counter']);
							}
						}else{
							if($_GET['mode'] == 'day_end'){
								$data = array('type'=>'day_end','cash'=>$_GET['box_cash'], 'time'=>$this->getCDTime());
								$return['message'] = 'Store Day Ended';
							}
						}
					}	
					if(!$return['error']){
						$result = $this->cDB->getDesign('store')->getUpdate('store_shift',$result['rows'][0]['id'])->setParam($data)->execute();
						if($data['type'] == 'shift_start'){
							$_SESSION['user']['counter'] = $_GET['counter_no'];
							$_SESSION['user']['shift'] = $result['shift_no'];
						}						
					}
				}			
			}else{
				$cash = $_GET['cash'];	
				$data['cd_doc_type'] = 'store_shift';
				$data['date'] = $this->getCDate();
				$data['store'] = $_SESSION['user']['store']['id'];
				$data['start_staff'] = $_SESSION['user']['mysql_id'];
				$data['end_staff'] = '';
				$data['time']['start'] = $this->getCDTime();
				$data['time']['end'] = '';
				$data['time']['petty_cash'] = $cash;
				$data['time']['end_cash'] = '';
				$data['time']['staff'] = $_SESSION['user']['mysql_id'];
				$data['shift'] = array();
				$result = $this->cDB->saveDocument()->execute($data);

				if(array_key_exists('id', $result)){
					$return['data']['id'] = $result['id'];
					$return['message'] = 'Welcome, Day has been started, Please Start Shift.';
				}
			}
			if(array_key_exists('error', $result)){
				$return['error'] = true;
				$return['message'] = 'OOPS! Some Error Please Contact Admin';
			}
			$return['data']['mode'] = $_GET['mode'];
			return json_encode($return);
		}

}