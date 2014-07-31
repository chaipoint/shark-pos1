<?php
	session_start();
	class store extends App_config{
		function __construct(){
			parent::__construct();
		}
		function select(){
			
			if($_SERVER['REQUEST_METHOD'] == "POST"){
				// Executes When User Selectes Store
				if(array_key_exists('store', $_POST) && is_numeric($_POST['store']) && !empty($_POST['store'])){
					$_SESSION['user']['store']['id'] = $_POST['store'];
					header("LOCATION:index.php?dispatch=billing.index");
					return;		
				}
			}
			global $couch;
			$storeResult = json_decode($couch->getDesign('store')->getView('store_as_option')->execute(),true);
			$storeList = $storeResult['rows'];
			$this->commonView('html_header');
			$this->view(array('storeList'=>$storeList));
			$this->commonView('html_footer');
		}
	}