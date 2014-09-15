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
						if($_POST['validateFor'] == 'sales_register'){

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
								$returnData['data']['redirect'] = 'index.php?dispatch=staff'; 								
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