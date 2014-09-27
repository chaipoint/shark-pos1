<?php session_start();
	class App_config{
 		public $db;
		public $module = 'login';
		public $mode = 'index'; 
		private $app;
		private $root;
		private $base_path;
		private $url;
		private $css;
		private $js;
		private $img;
		private $defaultViewDir = 'views';
		private $commonDir = 'common';
		private $cDTime;
		private $cTime;
		private $cDate;
		private $cMonth;
		private $cYear;
		private $cDay;
		public $store;
		public $iniConfigFile;
		public $userIdField = 'mysql_id';
		protected $return;
		protected $cDB = null;

		public function __construct(){
			require_once 'couchdb_phpclass.php';
			$this->cDB = new CouchPHP();
			
			$this->return = array('error'=>false,'message'=>'','data'=>array());

			/*Set all Config things like application base folder and Others Directory as URL, CSS, JS*/
			$this->app 	= 	dirname($_SERVER['SCRIPT_NAME']);	
			$this->base_path = $_SERVER['DOCUMENT_ROOT'].$this->app."/";
			$this->url 	= 	"http://".$_SERVER['HTTP_HOST'].$this->app."/";
			$this->css 	= 	$this->url.'css/';	
			$this->js 	= 	$this->url.'js/';
			$this->img 	= 	$this->url.'images/';
			$this->root 	= 	$_SERVER['DOCUMENT_ROOT'];

			if(array_key_exists('dispatch', $_GET)){


				$queryArray = explode(".",$_GET['dispatch']);
				if(empty($queryArray[0])){
						die("Redirect to not Found");
				}else{
					$this->module = $queryArray[0];
					if($queryArray[0] == 'login' && !array_key_exists(1, $queryArray)){
						$this->mode = 'index';
					}elseif(array_key_exists(1, $queryArray)){
						$this->mode = $queryArray[1];	
					}
				}
			}else{

			}
			/*
			if($this->module != 'login' && $this->module != 'utils'){
				if(!array_key_exists('user', is_array(@$_SESSION) ? $_SESSION : array())){
					header("Location:".$this->url);
				}elseif(array_key_exists('user', $_SESSION) && !array_key_exists('mysql_id', $_SESSION['user'])){
					header("Location:".$this->url);
				}
			}/**/

		}
		
		public function getInstallationConfig(){
			$iniFile = $this->root."/pos.ini";
			$this->iniConfigFile = $iniFile;
			$return = array('error'=>false, 'message'=>'');
			if(file_exists($iniFile)){
				if(is_writable($iniFile) && is_readable($iniFile)){
					$configData = parse_ini_file($this->root."/pos.ini", true);
					if(array_key_exists('store_config', $configData) && array_key_exists('is_configured', $configData['store_config']) && array_key_exists('store_id', $configData['store_config']) && !empty($configData['store_config']['store_id']) && preg_match("/^[0-9]{1,5}$/", $configData['store_config']['store_id']) && !preg_match("/^[0]{1,100}$/", $configData['store_config']['store_id'])){
						$return['data'] = $configData;
					}else{
						$return['error'] = true;
						$return['message'] = 'OPPS! Some Problem Please Contact Admin.';
					}
				}else{
					$return['error'] = true;
					$return['message'] = 'Unable to read or write file';					
				}
			}else{
				$return['error'] = true;
				$return['message'] = 'Configuration File Not exists';
			}
			return $return;
		}

		public function setIniFile($file,$data){
			$result = '';
			foreach($data as $key => $value){
				$result .= '['.$key.']'."\r\n";
				foreach($value as $innerKey => $innerValue){
					if($innerKey == 'is_configured'){
						$result .= $innerKey.' = true'."\r\n";						
					}else{						
						$result .= $innerKey.' = '.$innerValue."\r\n";						
					}
				}
			}
			file_put_contents($file, $result);
		}
		public function getApp(){
			return $this->app;
		}

		public function getUrl(){
			return $this->url;
		}
		public function getCss(){
			return $this->css;
		}
		public function getImg(){
			return $this->img;
		}
		public function getJs(){
			return $this->js;
		}
		public function getModule(){
			return $this->module;
		}
		public function getMode(){
			return $this->mode;
		}

		protected function view($var  = array()){
			$viewFile = $this->base_path.$this->module."/".$this->defaultViewDir."/".$this->mode.".php";
			if(file_exists($viewFile)){
				if(count($var)>0){
					foreach($var as $varKeys => $varValues){
						if(!is_numeric($varKeys)){
							$$varKeys = $varValues;
						}
					}
				}
				require $viewFile;
			}
		}

		protected function commonView($view, $var = array()){			
			$viewFile = $this->base_path.$this->commonDir."/".$view.".php";
			if(file_exists($viewFile)){
				if(count($var)>0){
					foreach($var as $varKeys => $varValues){
						if(!is_numeric($varKeys)){
							$$varKeys = $varValues;
						}
					}
				}
				require_once $viewFile;
			}		
		}
		
		public function getCDTime($format = '24'){
			if($format == '24'){
				$this->cDTime = Date("Y-m-d H:i:s");
			}elseif($format = '12'){
				$this->cDTime =  Date("d-M-Y h:i a");
			}
			return $this->cDTime;
		}
		public function getCTime($format = '24'){
			if($format == '24'){
				$this->cTime = Date("H:i:s");
			}elseif($format = '12'){
				$this->cTime =  Date("h:i a");
			}
			return $this->cTime;
		}

		public function getCDate(){
			$this->cDate =  Date("Y-m-d");
			return $this->cDate;
		}
		public function getCMonth(){
			$this->cMonth =  Date("m");
			return $this->cMonth;
		}
		public function getDBConnection($connection){
			global $sql_host, $sql_user, $sql_password, $sql_db; 
			
			$details = $this->getConfig($connection,'db_detail');
			if(array_key_exists('message', $details) && !empty($details['message'])){
				$this->db = $details;
			}else{
				$dt = $details['data']['db_detail'];
				$sql_host = $dt['host'];
				$sql_user = $dt['username'];
				$sql_password = $dt['password'];
				$sql_db = $dt['db'];			
				$this->db = new Database();
			}
		}
		public function getConfig($connection, $key){
			$arr = array('error'=>false, 'message'=>'', 'data' => array());
			$error = false;
			if(!is_object($connection)){
				$arr['error'] = true;
				$arr['message'] = 'connection_null';
				$error = true;				
			}
			$param = array();
			if(!is_array($key)){
				$key = trim($key);
				if(empty($key)){
					$arr['error'] = true;
					$arr['message'] = 'key_null';
					$error = true;								
				}
				$param['key'] = '"'.$key.'"';
			}else{
				$param['keys'] = '["'.implode('","',$key).'"]';				
			}
			if(!$error){
				$result = $connection->getDesign('config')->getView('config_list')->setParam($param)->execute();
				if(array_key_exists('cMessage', $result)){
					$arr['error'] = true;
					$arr['message'] = 'server_down';
				}else{
					foreach($result['rows'] as $key => $value){
						$arr['data'][$value['key']] = $value['value'];
					}
				}
			}
			return $arr;
		}
		public function checkRepStatus($con){
			$arr = array('error'=>false, 'message'=>'', 'data' => array());
			$error = false;
			if(!is_object($con)){
				$arr['error'] = true;
				$arr['message'] = 'connection_null';
				$error = true;				
			}
			if(!error){
				$arr['data'] = $con->getActiveTask();
			}
			return $arr;
		}
	}