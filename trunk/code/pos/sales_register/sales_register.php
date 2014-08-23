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
			$resultJSON = $couch->getDesign('billing')->getView('bill_by_current_date')->setParam(array("include_docs"=>"true","endkey"=>'["'.(date('Y-m-d')).'"]',"descending"=>"true"))->execute();
			$resultCashHand = $couch->getDesign('billing')->getList('get_cash_in_hand','bill_by_current_date')->setParam(array("include_docs"=>"true","start"=>'["'.(date('Y-m-d')).'"]',"endkey"=>'["'.(date('Y-m-d')).'",{}]'))->execute();
			$resultCashDelivery = $couch->getDesign('billing')->getList('get_cash_in_delivery','bill_by_current_date')->setParam(array("include_docs"=>"true","start"=>'["'.(date('Y-m-d')).'"]',"endkey"=>'["'.(date('Y-m-d')).'",{}]'))->execute();
			//print_r($resultJSON);
			if(array_key_exists('error', $resultJSON)){
            echo 'opps some problem please contact admin';
			}else{
			$result = $resultJSON['rows'];
			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->view(array("bill_data"=>$result,"cash_in_hand"=>$resultCashHand,"cash_in_delivery"=>$resultCashDelivery));
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		}
		}
	}