<?php
	class store extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|STORE");
		}
		/*Function to show all the store after Login-in   */
		function index(){
		
			/*Check session   */
			if(!array_key_exists('user', $_SESSION)){
				header("LOCATION:index.php");
			}
			
			$data = array('error' => false, 'storeList'=>array());
			/*Get all the store */
			$getStore = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID)->setParam(array('include_docs'=>'true'))->execute();
			$this->log->trace('STORE LIST'."\r\n".json_encode($getStore));
			
			/*check data exists or not in $getStore Array */
			if(array_key_exists('rows', $getStore) && count($getStore['rows'])>0){
				foreach($getStore['rows'] as $key => $value){
					$storeList[$key]['name'] = $value['value']['name'] ;
					$storeList[$key]['id'] = $value['key'] ;
					$storeList[$key]['code'] = $value['doc']['code'];
					$storeList[$key]['bill_type'] = @$value['doc']['bill_type'];
					$storeList[$key]['store_message'] = @$value['doc']['store_message'];
					$storeList[$key]['tin_no'] = @$value['doc']['tin_no'];
					$storeList[$key]['stn_no'] = @$value['doc']['stn_no'];
				}
			}
			
			sort($storeList);
			$data = array('error'=>false, 'storeList'=>$storeList);
			$this->commonView('header_html',array('error'=>$data['error']));
			$this->commonView('navbar');
			if(!$data['error']){
				$this->view($data);
			}
			//$this->commonView('footer_inner');
			$this->commonView('footer_html');

		}
		
		/* Function To Get Store Details */
		function getStore($store = 0, $include_docs = 'false'){
			/*Check if store id exists in GET   */
			if(array_key_exists('store', $_GET)){
				$store = $_GET['store'];
			}
			/*Check if includes_docs key exists in GET   */
			if(array_key_exists('include_docs', $_GET) && $_GET['include_docs'] != 'false'){
				$include_docs = 'true';
			}
			$param['key'] = '"'.$store.'"';
			$param['include_docs'] = $include_docs;
			$storeResult = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID)->setParam($param)->execute();
			$this->log->trace('STORE DETAIL'."\r\n".json_encode($storeResult));
			return array_key_exists('rows', $storeResult) ? $storeResult['rows'][0] : $storeResult;
		}
		
		/* Function To Get Store Staff */
		function get_store_staff($store = 0, $include_docs = 'false'){
			$staff = array();
			/*Check if includes_docs key exists in GET   */
			if(array_key_exists('store', $_GET)){
				$store = $_GET['store'];
			}
			
			/*Check if includes_docs key exists in GET   */
			if(array_key_exists('include_docs', $_GET) && $_GET['include_docs'] != 'false'){
				$include_docs = 'true';
			}
			
			$result = $this->getStore($store, $include_docs);
			/*Checking the value and staff key in $result   */
			if(array_key_exists('value', $result) && array_key_exists('staff', $result['value'])){
				foreach($result['value']['staff'] as $key => $value){
					$staff[$value['mysql_id']]['name'] = $value['name']; 
					$staff[$value['mysql_id']]['title'] = $value['title_id']; 
				}
			}
			return $staff;
		}

		/* Function To Manage Store Start Day, Day End, Shift Start, Shift End And Petty Expense */ 
		function storeFunction(){
			$return = array('error'=>false, 'message'=>'', 'data'=>array());
			
			/*Query to get store shift data   */
			$result = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_SHIFT)->setParam(array("startkey" => '["'.$this->getCDate().'","'.$_SESSION['user']['store']['id'].'"]', "endkey" => '["'.$this->getCDate().'","'.$_SESSION['user']['store']['id'].'"]', 'include_docs'=>'true'))->execute();
			$this->log->trace('STORE SHIFT DATA'."\r\n".json_encode($result));
			$total_rows = count($result['rows']);
			
			/*Check rows and total rows    */
			if(array_key_exists('rows', $result) && $total_rows == 1){
				if($_POST['mode'] == 'day_start'){
					$return['error'] = true;
					$return['message'] = START_DAY_ERROR ;
				}else{
					$data = array('type'=>'shift_start','opening_box_cash'=>array_key_exists('opening_box_cash',$_POST) ? $_POST['opening_box_cash'] : '' ,'counter_no'=>array_key_exists('counter_no', $_POST) ? $_POST['counter_no'] : '', 'time'=>$this->getCDTime(),'login' =>$_SESSION['user']['mysql_id'],'name' =>$_SESSION['user']['name'],'qty_10'=>'','qty_20'=>'','qty_50'=>'','qty_100'=>'','qty_500'=>'','qty_1000'=>'','qty_sodex'=>'','qty_restaurent'=>'');
					$return['message'] = START_DAY_SUCCESS;
					
					/*  */
					if(count($result['rows']) == 1){
						$totalShifts = count($result['rows'][0]['doc']['shift']);
						/* Check if shift end or not */
						if($totalShifts != 0 && empty($result['rows'][0]['doc']['shift'][$totalShifts-1]['end_time'])){
							$data = array('type'=>'shift_end','end_petty_cash'=>$_POST['petty_cash_end'], 'box_cash'=>$_POST['box_cash'], 'time'=>$this->getCDTime(), 'login' =>$_SESSION['user']['mysql_id'], 'name' =>$_SESSION['user']['name'],'qty_10'=>$_POST['qty_10'],'qty_20'=>$_POST['qty_20'],'qty_50'=>$_POST['qty_50'],'qty_100'=>$_POST['qty_100'],'qty_500'=>$_POST['qty_500'],'qty_1000'=>$_POST['qty_1000'],'qty_sodex'=>$_POST['qty_sodex'].".".$_POST['amount_sodex'],'qty_restaurent'=>$_POST['qty_restaurent'].".".$_POST['amount_restaurent']);
							
							$return['message'] = STORE_SHIFT_ENDED;
							/* Check that user is same who start the shift  */
							if($result['rows'][0]['doc']['shift'][$totalShifts-1]['start_login_id'] != $_SESSION['user']['mysql_id']){
								$return['error'] = true;
								$return['message'] = 'You are not allowed to end Shift. As it is started by '.$result['rows'][0]['doc']['shift'][$totalShifts-1]['start_staff_name'];
							}else{
								//unset($_SESSION['user']['shift']);
								//unset($_SESSION['user']['counter']);
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
						/* Check mode whether day-end or shift-end and get expense data */
						if($_POST['mode'] == 'day_end' || $_POST['mode'] == 'shift_end'){
							$resultInward = $this->cDB->getDesign(PETTY_EXPENSE_DESIGN_DOCUMENT)->getView(PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_GET_INWARD)->setParam(array("include_docs"=>"true", "descending"=>"true", "startkey" => '["'.$this->getCDate().'", "'.$_SESSION['user']['store']['id'].'" ,"'.$_SESSION['user']['counter'].'"]',"endkey" => '["'.$this->getCDate().'","'.$_SESSION['user']['store']['id'].'" ,"'.$_SESSION['user']['counter'].'"]'))->execute();
							/*Check array size and get inward value  */
							if( count($resultInward['rows']) >0 ){
								foreach($resultInward['rows'] as $iKey => $iValue){
									if($iValue['doc']['shift_no'] == $result['rows'][0]['doc']['shift'][$totalShifts-1]['shift_no']){
										$inward = $iValue['doc']['inward_amount'];									
									}
								}
							}
							require_once DIR.'/home/home.php';
							$hm = new home();
							$hm->reconcilation();
							require_once DIR.'/sales_register/sales_register.php';
							$sr = new sales_register();
							$resultExpenseList = $sr->getExpenseData($this->getCDate(), $this->getCDate());
							/*Check array size and get petty expense value  */
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