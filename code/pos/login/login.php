<?php
	class Login extends App_config{
		public function __construct(){
			parent::__construct();
			
		}
		
		public function index(){
			$this->commonView('html_header');
			$this->view();
			$this->commonView('html_footer');

		}

		public function validate(){
			$returnData = array('error'=>false,'message'=>"",'data'=>array());
			if($_SERVER['REQUEST_METHOD'] === 'POST'){
				global $couch;
				$_POST['password'] = md5($_POST['password']);
				$resultJSON = $couch->getDesign('staff')->getList('getuser','staff_code')->execute($_POST);
				$result = json_decode($resultJSON,true);
				if(!$result['error']){
					session_start();
					$userData["_id"] = $result['data']['_id'];
					$userData["_rev"] = $result['data']['_rev'];
					$userData["code"] = $result['data']['code'];
					$userData["mysql_id"] = $result['data']['mysql_id'];
					$userData["name"] = $result['data']['name'];
					$userData["username"] = $result['data']['username'];
					$userData["email"] = $result['data']['email'];
					$userData["phone_1"] = $result['data']['phone_1'];
					$userData["phone_2"] = $result['data']['phone_2'];
					$userData["address"] = $result['data']['address'];
					$userData["location"]['id'] = $result['data']['location_id'];
					$_SESSION['user'] = $userData;
					$returnData['data']['redirect'] = 'index.php?dispatch=store.select'; 
					//$returnData['data']['redirect'] = 'index.php?dispatch=billing.index'; 
				}else{
					$returnData['error'] = $result['error'];
					$returnData['message'] = $result['message'];
				}
			}else{
				$returnData['error'] = true;
				$returnData['message'] = "Invalid Request";
			}
			return json_encode($returnData);
		}
	}