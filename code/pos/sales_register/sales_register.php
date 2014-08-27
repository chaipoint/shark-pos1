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
		//	http://127.0.0.1:5984/testing/_design/billing/_list/handle_updated_bills/handle_updated_bills?descending=true&include_docs=true
			$resultBillList = $this->cDB->getDesign('billing')->getList('handle_updated_bills','handle_updated_bills')->setParam(array("include_docs"=>"true","descending"=>"true", "endkey" => '["'.$this->getCDate().'"]'))->execute();
			//print_r($resultJSON);
			//print_r($resultBillList);
			if(array_key_exists('error', $resultBillList)){
				echo 'opps some problem please contact admin';
			}else{
				$this->commonView('header_html');
				$this->commonView('navbar');
				$this->view(array("bill_data"=>$resultBillList['data'],"cash_in_hand"=>$resultBillList['cash_inhand'],"cash_in_delivery"=> $resultBillList['cash_indelivery']));
				$this->commonView('footer_inner');
				$this->commonView('footer_html');
		}
		}
	}