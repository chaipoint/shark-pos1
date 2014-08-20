<?php session_start();
	class App_config{
		public $log;

		private $module = 'login';
		private $mode = 'index'; 



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
		public function __construct(){
			$this->log = Logger::getLogger("CP-POS|APP-CONFIG");
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

				//$this->log->trace("parameter ".$_GET['dispatch']);

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
				//$this->log->trace("default ".$this->getModule().'.'.$this->getMode());
			}
			if($this->module != 'login' && $this->module != 'utils'){
				if(!array_key_exists('user', is_array(@$_SESSION) ? $_SESSION : array())){
					header("Location:".$this->url);
				}
			}
		}
		public function getInstallationConfig(){
			$iniFile = $this->root."/pos.ini";
			$this->iniConfigFile = $iniFile;
			$return = array('error'=>false, 'message'=>'');
			if(file_exists($iniFile)){
				if(is_writable($iniFile) && is_readable($iniFile)){
					$configData = parse_ini_file($this->root."/pos.ini", true);
					$return['data'] = $configData;
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
				require_once $viewFile;
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

	}