<?php	
	class Staff extends App_config{
		private $cDB;
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
		}
		
		function getStaff(){
			$staffList = $this->cDB->getDesign('staff')->getView('staff_username')->setParam(array("include_docs"=>"true"))->execute();
			return $staffList;
		}	
		function save_petty(){
			
			$return = array('error'=>false, 'message'=>'', 'data'=>array());

			$result = $this->cDB->getDesign('store')->getView('store_shift')->setParam(array('key'=>'"'.$this->getCDate().'"', 'include_docs'=>'true'))->execute();
			$total_rows = count($result['rows']);
			if(array_key_exists('rows', $result) && $total_rows == 1){
				if($_POST['mode'] == 'day_start'){
					$return['error'] = true;
					$return['message'] = "Can't Start Store Day Again." ;
				}else{
					$data = array('type'=>'shift_start','counter_no'=>array_key_exists('counter_no', $_POST) ? $_POST['counter_no'] : '', 'time'=>$this->getCDTime(),'login' =>$_SESSION['user']['mysql_id'],'name' =>$_SESSION['user']['name']);
					$return['message'] = 'Welcome, Shift has been started, Please Start Sales. <a href="index.php?dispatch=billing" class="btn btn-sm btn-primary">Start Billing</a>';

					if(count($result['rows']) == 1){
						$totalShifts = count($result['rows'][0]['doc']['shift']);
						if($totalShifts != 0 && empty($result['rows'][0]['doc']['shift'][$totalShifts-1]['end_time'])){
							$data = array('type'=>'shift_end','end_petty_cash'=>$_POST['petty_cash'], 'box_cash'=>$_POST['box_cash'], 'time'=>$this->getCDTime(), 'login' =>$_SESSION['user']['mysql_id'], 'name' =>$_SESSION['user']['name']);
							$return['message'] = 'Store Shift Ended';
							if($result['rows'][0]['doc']['shift'][$totalShifts-1]['start_login_id'] != $_SESSION['user']['mysql_id']){
								$return['error'] = true;
								$return['message'] = 'You are not allowed to end Shift. As it is started by '.$result['rows'][0]['doc']['shift'][$totalShifts-1]['start_staff_name'];
							}else{
								unset($_SESSION['user']['shift']);
								unset($_SESSION['user']['counter']);
							}
						}else{
							if($_POST['mode'] == 'day_end'){
								$data = array('type'=>'day_end','cash'=>$_POST['box_cash'], 'time'=>$this->getCDTime(), 'login' =>$_SESSION['user']['mysql_id'], 'name' =>$_SESSION['user']['name']);
									$return['message'] = 'Store Day Ended';
							}
						}
					}	
					if(!$return['error']){
						if($_POST['mode'] == 'day_end' || $_POST['mode'] == 'shift_end'){
								$inward = 0;
								$resultInward = $this->cDB->getDesign('petty_expense')->getView('get_inward')->setParam(array('key'=>'"'.$this->getCDate().'"'))->execute();
								if( count($resultInward['rows']) >0 ){
									$inward = $resultInward['rows'][0]['value'];
								}
								require_once DIR.'/sales_register/sales_register.php';
								$sr = new sales_register();
								$resultExpenseList = $sr->getExpenseData($this->getCDate());
								$pettyExpence = 0;
								if(count($resultExpenseList['rows'])>0){
									$rows = $resultExpenseList['rows'];
										foreach($rows as $pKey => $pValue){
											$pettyExpence += $pValue['doc']['expense_amount'];
										}
								}
								$data['opening_petty_cash'] = $result['rows'][0]['doc']['day']['start_cash'];
								$data['petty_expense'] = $pettyExpence ;
								$data['closing_petty_cash'] = ($result['rows'][0]['doc']['day']['start_cash'] + $inward - $pettyExpence);
								$data['inward_petty_cash'] = $inward;
						}
						$result = $this->cDB->getDesign('store')->getUpdate('store_shift',$result['rows'][0]['id'])->setParam($data)->execute();
						if($data['type'] == 'shift_start'){
							$_SESSION['user']['counter'] = $_POST['counter_no'];
							$_SESSION['user']['shift'] = $result['shift_no'];
						}						
					}
				}			
			}else{
				$data['cd_doc_type'] = 'store_shift';
				$data['login_id'] = $_POST['login_id'];
				$data['store_id'] = $_SESSION['user']['store']['id'];
				$data['login_staff_name'] = $_POST['login_name'];
				$data['login_time'] = $_POST['login_time'];

				$data['day']['start_time'] = $this->getCDTime();
				$data['day']['start_login_id'] = $_SESSION['user']['mysql_id'];
				$data['day']['start_staff_name'] = $_SESSION['user']['name'];
				$data['day']['start_cash'] = $_POST['petty_cash'];
				$data['day']['end_time'] = '';
				$data['day']['end_login_id'] = '';
				$data['day']['end_staff_name'] = '';
				$data['day']['end_fullcash'] = '';
				$data['day']['petty_cash_balance'] = array('opening_petty_cash'=>0,'petty_expense'=>0,'closing_petty_cash'=>0,'inward_petty_cash'=>0);
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
			$return['data']['mode'] = $_POST['mode'];
			return json_encode($return);
		}

}