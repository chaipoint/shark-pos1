<?php
	class App_config{
		private $module = 'login';
		private $mode = 'index'; 

		private $app;
		private $base_path;
		private $url;
		private $css;
		private $js;
		private $defaultViewDir = 'views';
		private $commonDir = 'common';
		private $cDTime;
		private $cTime;
		private $cDate;
		private $cMonth;
		private $cYear;
		private $cDay;
		public $userIdField = 'mysql_id';
		protected $return;
		public function __construct(){
			$this->return = array('error'=>false,'message'=>'','data'=>array());

			/*Set all Config things like application base folder and Others Directory as URL, CSS, JS*/
			$this->app 	= 	dirname($_SERVER['SCRIPT_NAME']);	
			$this->base_path = $_SERVER['DOCUMENT_ROOT'].$this->app."/";
			$this->url 	= 	"http://".$_SERVER['HTTP_HOST'].$this->app."/";
			$this->css 	= 	$this->url.'css/';	
			$this->js 	= 	$this->url.'js/';

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
			}

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

		protected function commonView($view){
			$viewFile = $this->base_path.$this->commonDir."/".$view.".php";
			if(file_exists($viewFile)){
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