<?php
	//session_start();
	//print_r($_SESSION);
	class Sales_Register extends App_config{
		private $cDB;
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
		}
		function index(){
			global $couch;
			$resultJSON = $couch->getDesign('billing')->getView('bill_by_current_date')->setParam(array("endkey"=>'["'.(date('Y-m-d')).'"]',"descending"=>"true"))->execute();
			//print_r($resultJSON);
			if(array_key_exists('error', $resultJSON)){
            echo 'opps some problem please contact admin';
			}else{
			$result = $resultJSON['rows'];
			
			$this->commonView('html_header');
			$this->commonView('navbar');
			$this->view(array("bill_data"=>$result));
			$this->commonView('inner_footer');
			$this->commonView('html_footer');
		}
		}
	}