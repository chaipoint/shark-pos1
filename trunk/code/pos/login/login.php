<?php
	class Login extends App_config{
		private $configData;
		public function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|LOGIN");
			$this->configData = $this->getInstallationConfig();
			if(array_key_exists('data', $this->configData)){
				if(!$this->configData['data']['store_config']['is_configured']){
					$this->log->debug('Initalizing first time for store '.$this->configData['data']['store_config']['store_id']);
					$this->config($this->configData);
					die();
				}else{
					$this->log->debug('Configured Store is '.$this->configData['data']['store_config']['store_id']);
					require_once DIR."/store/store.php";
					$store = new Store();
					$store_result = $store->getStore($this->configData['data']['store_config']['store_id']);
					if(count($store_result) == 0){
						die;
					}elseif(array_key_exists('error', $store_result) && array_key_exists('source', $store_result)){
						die;
					}
					$this->store = $store_result['key'];
					$this->store_name = $store_result['value']['name'];
					$this->store_location = $store_result['value']['location'];
				}
			}else{
				$this->log->debug('Config File Have Some Problem \n\r'.'Error Message :- '.$this->configData['message']);
				$this->config($this->configData);
				die();
			}
		}
		/* Function To Get Configration Data */
		public function config($data){
			$this->commonView('header_html');
			$this->commonView('initial_config', array('data'=>$data));
			$this->commonView('footer_html');			
		}

		
		public function index(){
			$this->log->debug('Login Page is Ready For '.$this->store_name." | ".$this->store);
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
		/* Function For Logout */
		public function out(){
			if(!array_key_exists('user', $_SESSION)){
				header("LOCATION:index.php");
			}
			$response = $this->cDB->getDesign(LOGIN_DESIGN_DOCUMENT)->getUpdate(LOGIN_DESIGN_DOCUMENT_UPDATE_LOGIN_HISTORY,$_SESSION['user']['login']['id'])->setParam(array('logout_time'=>$this->getCDTime()))->execute();
			$this->log->trace('LOGOUT RESPONSE'."\r\n".json_encode($response));
			if(!empty($response)){
				unset($_SESSION['user']);
				session_destroy();
				header("LOCATION:index.php".(array_key_exists('cMessage', $response) ? '?error=true' : ''));
			}
		}
		/* Function To Validate Form(Login, Day Start, Day End, Shift Start, Shift Day, Data Sync, Sale Register) */
		public function validate(){
			global $config;
			$returnData = array('error'=>false,'message'=>"",'data'=>array());
			if($_SERVER['REQUEST_METHOD'] === 'POST'){
				$this->log->trace('POST DATA '."\r\n".json_encode($_POST));
				$_POST['password'] = md5($_POST['password']);
				$resultJSON = $this->cDB->getDesign(STAFF_DESIGN_DOCUMENT)->getList(STAFF_DESIGN_DOCUMENT_LIST_GET_USER, STAFF_DESIGN_DOCUMENT_VIEW_STAFF_USERNAME)->execute($_POST);			

				$result = $resultJSON;
				if(!array_key_exists('for', $result)){ // To Check Which form to be validate
					if(!$result['error']){
						if($_POST['validateFor'] == SALE_REGISTER_PROTECTED_SCREEN || $_POST['validateFor'] == SHIFT_DATA_PROTECTED_SCREEN || $_POST['validateFor'] == DATA_SYNC_PROTECTED_SCREEN){
							require_once DIR.'/staff/staff.php';
							$st = new Staff();
							$staffList = $st->getStaffList();
							if(array_key_exists($result['data']['mysql_id'], $staffList)){
								switch($staffList[$result['data']['mysql_id']]['title_id']){
									case 4:
									case 6:
										if($_POST['validateFor'] == SHIFT_DATA_PROTECTED_SCREEN){
											require_once DIR.'/home/home.php';
											$hm = new home();
											$returnData = $hm->getShiftAndCashRe();
										}
									break;
									default:
										$returnData['error'] = true;
										$returnData['message'] = NOT_ALLOWED_TO_ACCESS;
								}
							}else{
								$returnData['error'] = true;
								$returnData['message'] = NOT_ALLOWED_TO_ACCESS;
							}
						}else{
							$userData["store"]['name'] = $this->store_name;
							$userData["store"]['location'] = $this->store_location['id'];
							$userData["store"]['location_name'] = $this->store_location['name'];

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
							$loginHistory['cd_doc_type'] = LOGIN_HISTORY_DOC_TYPE;
							$loginHistory['id'] = $userData["mysql_id"];
							$loginHistory['store'] = $this->store;
							$loginHistory['login_time'] = $this->getCDTime();
							$loginHistory['logout_time'] = '';
							$loginHistory['app_version'] = $config['version'];

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
							unset($userData["email"]);
							unset($userData["phone"]);
							unset($userData["address"]);
							$_SESSION['user'] = $userData;

							$this->log->trace('LOGIN HISTORY '."\r\n".json_encode($loginHistory));
							$this->log->trace('SESSION DATA '."\r\n".json_encode($userData));

							if($_POST['validateFor'] != LOGIN){
								require_once DIR.'/store/store.php';
								$store = new Store();
								$returnData = json_decode($store->storeFunction(),true);
							}else{
								require_once DIR.'/utils/utils.php';
								$design = new utils();
								$repDesign['error'] = false;
								if($repDesign['error']==true){
									$returnData['error'] = true;
									$returnData['message'] = REPLICATE_ERROR;
								}else{
									$returnData['data']['redirect'] = LOGIN_DISPATCH; 
								} 								
							}
						}
						 

					}else{
						$returnData['error'] = true;
						if(array_key_exists('message', $result)){
							$returnData['message'] = $result['message'];						
						}else{
							$returnData['message'] = ERROR;						
						}
					}
				}else{
						$returnData['error'] = true;
						$returnData['message'] = ERROR;						
				}
			}else{
				$returnData['error'] = true;
				$returnData['message'] = REQUEST_METHOD_NOT_ALLOWED;
			}
			$re = json_encode($returnData);
			$this->log->trace('RESPONSE '."\r\n".$re);
			return $re;
		}

		/* Function To Login For Senior Parter to Access(Shift Data, Sale Register, Data Sync)  */
		function form_login(){
			if(array_key_exists('user', $_SESSION)){
				require_once DIR.'/store/store.php';
				$store = new Store();
				$staff = $store->get_store_staff($_SESSION['user']['store']['id']);
				echo '<div id="login_holder_home" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">';
				$orignalModule = $this->module;
				$orignalMode = $this->mode;
				$this->mode = 'index';
				$this->module = 'login';
				$this->view();			
				$this->mode = $orignalMode;
				$this->module = $orignalModule;
				echo '</div> <script>var is_login_allowed = '.( array_key_exists($_SESSION['user']['mysql_id'], $staff) && ($_SESSION['user']['title']['id'] == 4 || $_SESSION['user']['title']['id'] == 6 ) ? 'false' : 'true').'; </script>';
			}
		}
	}