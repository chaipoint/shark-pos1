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
			$cash = $_GET['cash'];
			$return = array('error'=>false, 'message'=>'', 'data'=>array());	
			$data['cd_doc_type'] = 'store_shift';
			$data['date'] = $this->getCDate();
			$data['store'] = $_SESSION['user']['store']['id'];
			$data['start_staff'] = $_SESSION['user']['mysql_id'];
			$data['end_staff'] = '';
			$data['time']['start'] = $this->getCDTime();
			$data['time']['end'] = '';
			$data['time']['start_cash'] = $cash;
			$data['time']['end_cash'] = $cash;
			$data['time']['start_staff'] = $_SESSION['user']['mysql_id'];
			$data['time']['end_staff'] = '';
			$data['shift'] = array();

			$result = $this->cDB->getDesign('store')->getView('store_shift')->setParam(array('key'=>'"'.$this->getCDate().'"', 'include_docs'=>'true'))->execute();
			$total_rows = count($result['rows']);
			if(array_key_exists('rows', $result) && $total_rows == 1){
				if($_GET['mode'] == 'day_start'){
					$return['error'] = true;
					$return['message'] = "Can't Start Store Day Again." ;
				}else{
					$data = array('type'=>'shift_start','cash'=>$cash, 'time'=>$this->getCDTime(),'start_staff' =>$_SESSION['user']['mysql_id']);
					$return['message'] = 'Store Shift Started';

					if(count($result['rows']) == 1){
						$totalShifts = count($result['rows'][0]['doc']['shift']);
						if($totalShifts != 0 && empty($result['rows'][0]['doc']['shift'][$totalShifts-1]['end'])){
							$data = array('type'=>'shift_end','cash'=>$cash, 'time'=>$this->getCDTime(),  'shift_no' => $totalShifts-1, 'end_staff' =>$_SESSION['user']['mysql_id']);
								$return['message'] = 'Store Shift Ended';
						}else{
							if($_GET['mode'] == 'day_end'){
								$data = array('type'=>'day_end','cash'=>$cash, 'time'=>$this->getCDTime(), 'end_staff' =>$_SESSION['user']['mysql_id']);
								$return['message'] = 'Store Day Ended';
							}
						}
					}	
					$result = $this->cDB->getDesign('store')->getUpdate('store_shift',$result['rows'][0]['id'])->setParam($data)->execute();
				}			
			}else{
				$result = $this->cDB->saveDocument()->execute($data);
				if(array_key_exists('id', $result)){
					$return['data']['id'] = $result['id'];
					$return['message'] = 'Store Day Started';
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