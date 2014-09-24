<?php
	session_start();
	class store extends App_config{
		function __construct(){
			parent::__construct();
		}
		function select(){
			$storeResult = json_decode($this->cDB->getDesign('store')->getView('store_as_option', array_key_exists('store', $_POST)? $_POST['store'] : 0)->execute(),true);
			$storeList = $storeResult['rows'];
			
			if($_SERVER['REQUEST_METHOD'] == "POST"){
				// Executes When User Selectes Store
				if(array_key_exists('store', $_POST) && is_numeric($_POST['store']) && !empty($_POST['store'])){
					$_SESSION['user']['store'] = $storeList[0]['value'];
					$_SESSION['user']['store']['id'] = $_POST['store'];
					header("LOCATION:index.php?dispatch=billing.index");
					return;		
				}
			}
			$this->commonView('html_header');
			$this->view(array('storeList'=>$storeList));
			$this->commonView('html_footer');
		}
	}