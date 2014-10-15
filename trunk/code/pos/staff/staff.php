<?php	
	class Staff extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|STAFF");
			$this->getDBConnection($this->cDB);
		}
		
		function getStaff(){
			$staffList = $this->cDB->getDesign(STAFF_DESIGN_DOCUMENT)->getView(STAFF_DESIGN_DOCUMENT_VIEW_STAFF_USERNAME)->setParam(array("include_docs"=>"true"))->execute();
			return $staffList;
		}

		/* Function To Get Delivery Boy */
		function getDeliveryBoy(){ 
			$token = $_REQUEST['token'];
			$getStaff = "SELECT name label, id FROM staff_master 
						 WHERE active = 'Y' 
						 AND location_id = '".$_SESSION['user']['location']['id']."' 
						 AND name LIKE '%$token%'";
			$result = $this->db->func_query($getStaff);
			$this->log->trace('GET DELIVERY BOY'."\r\n".$getStaff);
			echo json_encode($result);
		}

		/* Function To Manage Store Start Day, Day End, Shift Start, Shift End And Petty Expense */ 
		function save_petty(){
			$return = array('error'=>false, 'message'=>'', 'data'=>array());

			$result = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_SHIFT)->setParam(array('key'=>'"'.$this->getCDate().'"', 'include_docs'=>'true'))->execute();
			$this->log->trace('STORE SHIFT DATA'."\r\n".json_encode($result));
			$total_rows = count($result['rows']);
			if(array_key_exists('rows', $result) && $total_rows == 1){
				if($_POST['mode'] == 'day_start'){
					$return['error'] = true;
					$return['message'] = START_DAY_ERROR ;
				}else{
					$data = array('type'=>'shift_start','counter_no'=>array_key_exists('counter_no', $_POST) ? $_POST['counter_no'] : '', 'time'=>$this->getCDTime(),'login' =>$_SESSION['user']['mysql_id'],'name' =>$_SESSION['user']['name']);
					$return['message'] = START_DAY_SUCCESS;

					if(count($result['rows']) == 1){
						$totalShifts = count($result['rows'][0]['doc']['shift']);
						if($totalShifts != 0 && empty($result['rows'][0]['doc']['shift'][$totalShifts-1]['end_time'])){
							$data = array('type'=>'shift_end','end_petty_cash'=>$_POST['petty_cash_end'], 'box_cash'=>$_POST['box_cash'], 'time'=>$this->getCDTime(), 'login' =>$_SESSION['user']['mysql_id'], 'name' =>$_SESSION['user']['name']);
							$return['message'] = STORE_SHIFT_ENDED;
							if($result['rows'][0]['doc']['shift'][$totalShifts-1]['start_login_id'] != $_SESSION['user']['mysql_id']){
								$return['error'] = true;
								$return['message'] = 'You are not allowed to end Shift. As it is started by '.$result['rows'][0]['doc']['shift'][$totalShifts-1]['start_staff_name'];
							}else{
								unset($_SESSION['user']['shift']);
								unset($_SESSION['user']['counter']);
							}
						}else{
							if($_POST['mode'] == 'day_end'){
								$data = array('type'=>'day_end','cash'=>$_POST['box_cash_end'], 'time'=>$this->getCDTime(), 'login' =>$_SESSION['user']['mysql_id'], 'name' =>$_SESSION['user']['name']);
									$return['message'] = STORE_DAY_ENDED;
							}
						}
					}	
					if(!$return['error']){
						$inward = 0;
						$pettyExpence = 0;
						if($_POST['mode'] == 'day_end' || $_POST['mode'] == 'shift_end'){
							$resultInward = $this->cDB->getDesign(PETTY_EXPENSE_DESIGN_DOCUMENT)->getView(PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_INWARD)->setParam(array('key'=>'"'.$this->getCDate().'"','include_docs'=>'true'))->execute();
							if( count($resultInward['rows']) >0 ){
								foreach($resultInward['rows'] as $iKey => $iValue){
									if($iValue['doc']['shift_no'] == $result['rows'][0]['doc']['shift'][$totalShifts-1]['shift_no']){
										$inward = $iValue['doc']['inward_amount'];									
									}
								}
							}

							require_once DIR.'/sales_register/sales_register.php';
							$sr = new sales_register();
							$resultExpenseList = $sr->getExpenseData($this->getCDate());
							if(count($resultExpenseList['rows'])>0){
								$rows = $resultExpenseList['rows'];
								foreach($rows as $pKey => $pValue){
									if($pValue['doc']['shift_no'] == $result['rows'][0]['doc']['shift'][$totalShifts-1]['shift_no']){
										$pettyExpence += $pValue['doc']['expense_amount'];
									}
								}
							}		
						}


						$opening_petty_cash_at_start  =  $result['rows'][0]['doc']['day']['start_cash'];
						$data['opening_petty_cash'] = $opening_petty_cash_at_start;
						$data['petty_expense'] = $pettyExpence ;
						$data['closing_petty_cash'] = ($data['opening_petty_cash'] + $inward - $pettyExpence);
						$data['inward_petty_cash'] = $inward;
						
						$result = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getUpdate(STORE_DESIGN_DOCUMENT_UPDATE_STORE_SHIFT,$result['rows'][0]['id'])->setParam($data)->execute();
						if($data['type'] == 'shift_start'){
							$_SESSION['user']['counter'] = $_POST['counter_no'];
							$_SESSION['user']['shift'] = $result['shift_no'];
						}						
					}
				}			
			}else{
				$data['cd_doc_type'] = STORE_SHIFT_DOC_TYPE;
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
				$data['day']['petty_cash_balance'] = array('opening_petty_cash'=>$_POST['petty_cash'],'petty_expense'=> 0,'closing_petty_cash'=>0,'inward_petty_cash'=>0);
				$data['day']['cash_reconciliation'] = array();
				$data['shift'] = array();
				$result = $this->cDB->saveDocument()->execute($data);
				$this->log->trace('SAVE STORE DATA'."\r\n".json_encode($data));

				if(array_key_exists('id', $result)){
					$return['data']['id'] = $result['id'];
					$return['message'] = START_DAY_SUCCESS;
				}
			}
			if(array_key_exists('error', $result)){
				$return['error'] = true;
				$return['message'] = ERROR;
			}
			$return['data']['mode'] = $_POST['mode'];
			$return['data']['message'] = $return['message'];
			return json_encode($return);
		}

}