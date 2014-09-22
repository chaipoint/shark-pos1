<?php
	class Login extends App_config{
		private $cDB;
		private $configData;
		public function __construct(){
			parent::__construct();

			$this->log =  Logger::getLogger("CP-POS|LOGIN");
			

			global $couch;
			$this->cDB = $couch;
			
			$this->configData = $this->getInstallationConfig();
			if(array_key_exists('data', $this->configData)){
				if(!$this->configData['data']['store_config']['is_configured']){
					$this->config($this->configData);
					die();
				}else{
					$this->store = $this->configData['data']['store_config']['store_id'];
				}
			}else{
				$this->config($this->configData);
				die();
			}
		}

		public function config($data){
			$this->commonView('header_html');
			$this->commonView('initial_config', array('data'=>$data));
			$this->commonView('footer_html');			
		}
		
		public function index(){
			unset($_SESSION);
			session_destroy();
			$edata = array('error'=>false);	
			if(array_key_exists('error', $_GET) && $_GET['error'] === "true"){
				$result = $this->cDB->version();
				$edata = array('error'=>true);				
				if(!array_key_exists('cMessage', $result)){
					$edata = array('error'=>false);				
				}
			}
			$this->commonView('header_html',$edata);
			$this->view();
			$this->commonView('footer_html');

		}
		public function out(){
			if(!array_key_exists('user', $_SESSION)){
				header("LOCATION:index.php");
			}
			$response = $this->cDB->getDesign('login')->getUpdate('login_history',$_SESSION['user']['login']['id'])->setParam(array('logout_time'=>$this->getCDTime()))->execute();
			if(!empty($response)){
				unset($_SESSION['user']);
				session_destroy();
				header("LOCATION:index.php".(array_key_exists('cMessage', $response) ? '?error=true' : ''));
			}
		}
		public function validate(){
			$returnData = array('error'=>false,'message'=>"",'data'=>array());
			if($_SERVER['REQUEST_METHOD'] === 'POST'){

				$this->log->trace('POST DATA '."\r\n".json_encode($_POST));

				$_POST['password'] = md5($_POST['password']);
				$resultJSON = $this->cDB->getDesign('staff')->getList('getuser','staff_username')->execute($_POST);			

				$result = $resultJSON;
				if(!array_key_exists('cMessage', $result)){
					if(!$result['error']){
						if($_POST['validateFor'] == 'sales_register' || $_POST['validateFor'] == 'cash_reconciliation'){
							require_once DIR.'/sales_register/sales_register.php';
							$sr = new sales_register();
							$staffList = $sr->getStaffList();
							if(array_key_exists($result['data']['mysql_id'], $staffList)){
								switch($staffList[$result['data']['mysql_id']]['title_id']){
									case 4:
									case 6:
										require_once DIR.'/billing/billing.php';
										$bl = new billing();
										$todaysale = json_decode($bl->getTodaysSale(),true);
										$sales_reg = $sr->getBills($this->getCDate());

										$shift_data = $this->cDB->getDesign('store')->getView('store_shift')->setParam(array('key'=>'"'.$this->getCDate().'"','include_docs'=>'true'))->execute();
										$excess = "";
										$tablesShiftData = '<div class="panel panel-success"><div class="panel-heading"><h4 class="panel-title">Shift Data</h4></div><div class="panel-body"><table class="table">
																<thead><tr><th>Event</th><th>Petty Cash</th><th>Petty Cash Inward</th><th> Petty Cash Variance</th><th>Shift End Petty Cash</th><th>Shift End (Cash in the box)</th><th>Expected Closing Cash</th><th>Excess Cash</th><th>Expected Cash in the Box</th><th>Sales Cash Variance</th></tr></thead>
    															<tbody>';
    						 			if(count($shift_data['rows'])){
											$shifts = $shift_data['rows'][0]['doc']['shift'];
							    			$total = count($shifts);
							    			$day = $shift_data['rows'][0]['doc']['day'];
							    			$shift_end_cash = ($day['start_cash']+$day['petty_cash_balance']['inward_petty_cash']-$day['petty_cash_balance']['petty_expense']);
											$tablesShiftData .='<tr><td>DAY START</td>
			    										<td class="text-center">'.$day['start_cash'].'</td>
			    										<td class="text-center">'.$day['petty_cash_balance']['inward_petty_cash'].'</td>
			    										<td class="text-center">'.$day['petty_cash_balance']['petty_expense'].'</td>
			    										<td class="text-center">0</td>
			    										<td class="text-center">0</td>
			    										<td class="text-center">'.$shift_end_cash.'</td>
			    										<td class="text-center">'.(-$shift_end_cash).'</td>
			    										<td class="text-center">'.$day['end_fullcash'].'</td>
							    						<td class="text-center">'.($day['end_fullcash']).'</td>
			    									</tr>';
											foreach($shifts as $key => $values){
												$excess .= '<tr><td>SHIFT '.$values['shift_no'].' EXCESS CASH</td><td>'.($values['petty_cash_balance']['closing_petty_cash'] - $values['end_petty_cash']).'</td></tr>';
												$closing_cash = (( $values['petty_cash_balance']['opening_petty_cash'])
				    								+($day['petty_cash_balance']['inward_petty_cash'] + $values['petty_cash_balance']['inward_petty_cash']) 
													-($day['petty_cash_balance']['petty_expense'] + $values['petty_cash_balance']['petty_expense']) 
				    								);
												$tablesShiftData .='<tr>
						    						<td>SHIFT '.$values['shift_no'].'</td>
						    						<td class="text-center">'.$values['petty_cash_balance']['opening_petty_cash'].'</td>
						    						<td class="text-center">'.$values['petty_cash_balance']['inward_petty_cash'].'</td>
						    						<td class="text-center">'.$values['petty_cash_balance']['petty_expense'].'</td>
						    						<td class="text-center">'.$values['end_petty_cash'].'</td>
						    						<td class="text-center">'.$values['end_cash_inbox'].'</td>
						    						<td class="text-center">'.$closing_cash.'</td>
						    						<td class="text-center">'.($values['end_petty_cash']-$closing_cash).'</td>
						    						<td class="text-center">'.$values['end_cash_inbox'].'</td>
						    						<td class="text-center">'.($values['end_cash_inbox']-$values['end_cash_inbox']).'</td>
						    						</tr>';
											}
    									}
			    						$tablesShiftData .='</tbody></table></div></div>';
			    						$returnData['data']['shift_table'] = $tablesShiftData;
			    						$cash_reconciliation_table = '<div class="panel panel-success"><div class="panel-heading"><h4 class="panel-title">Cash Reconciliation</h4></div><div class="panel-body"><table class="table"><tbody>';
    									foreach($sales_reg['payment_type']['amount'] as $pKey => $pValue){
	    									$cash_reconciliation_table .= '<tr><td>'.$pKey.'</td><td class="text-center">'.$pValue.'</td></tr>';
    									}
    									$cash_reconciliation_table .= $excess;
    									$cash_reconciliation_table .= '</tbody></table></div></div>';
			    						$returnData['data']['cash_reconciliation_table'] = $cash_reconciliation_table;
									break;
									default:
										$returnData['error'] = true;
										$returnData['message'] = 'You are not allowed to access.';
								}
							}else{
								$returnData['error'] = true;
								$returnData['message'] = 'You are not allowed to access.';
							}
						}else{
							$userData["_id"] = $result['data']['_id'];
							$userData["_rev"] = $result['data']['_rev'];
							$userData["code"] = $result['data']['code'];
							$userData["mysql_id"] = $result['data']['mysql_id'];
							$userData["name"] = $result['data']['name'];
							$userData["username"] = $result['data']['username'];
							$userData["email"] = $result['data']['email'];
							$userData["phone"] = $result['data']['phone'];
							$userData["address"] = $result['data']['address'];
							$userData["location"]['id'] = $result['data']['location_id'];
							$userData["location"]['name'] = $result['data']['location_name'];
							$userData["title"]['id'] = $result['data']['title']['id'];
							$userData["title"]['name'] = $result['data']['title']['name'];
							$userData["store"]['id'] = $this->store;

							$resultJSON = $this->cDB->getDesign('store')->getView('store_mysql_id')->setParam(array("key"=>'"'.$this->store.'"'))->execute();
							$userData["store"]['name'] = $resultJSON['rows'][0]['value'];



							$loginHistory['cd_doc_type'] = 'login_history';
							$loginHistory['id'] = $userData["mysql_id"];
							$loginHistory['store'] = $this->store;
							$loginHistory['login_time'] = $this->getCDTime();
							$loginHistory['logout_time'] = '';

							$result = $this->cDB->saveDocument()->execute($loginHistory);
							if(array_key_exists('ok', $result)){
									$userData['login']['id'] = $result['id'];
									$userData['login']['time'] = $this->getCDTime();
							}
							if(array_key_exists('user', $_SESSION)){
								$_POST['login_id'] = $_SESSION['user']['mysql_id'];
								$_POST['login_name'] = $_SESSION['user']['name'];
								$_POST['login_time'] = $_SESSION['user']['login']['time'];
								unset($_SESSION['user']);
							}
							$_SESSION['user'] = $userData;

							$this->log->trace('LOGIN HISTORY '."\r\n".json_encode($loginHistory));
							$this->log->trace('SESSION DATA '."\r\n".json_encode($userData));

							if($_POST['validateFor'] == 'shift'){
								require_once DIR.'/staff/staff.php';
								$staff = new Staff();
								$returnData = json_decode($staff->save_petty(),true);
							}else{
								$returnData['data']['redirect'] = 'index.php?dispatch=home'; 								
							}
						}
						//$returnData['data']['redirect'] = 'index.php?dispatch=billing.index'; 

					}else{
						$returnData['error'] = true;
						if(array_key_exists('message', $result)){
							$returnData['message'] = $result['message'];						
						}else{
							$returnData['message'] = 'OOPS! Some Problem Please Contact Admin';						
						}
					}
				}else{
						$returnData['error'] = true;
						$returnData['message'] = 'OOPS! Some Problem Please Contact Admin.';						
				}
			}else{
				$returnData['error'] = true;
				$returnData['message'] = "Invalid Request";
			}
			$re = json_encode($returnData);
			$this->log->trace('RESPONSE '."\r\n".$re);
			return $re;
		}
	}