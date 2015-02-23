<?php 
	class Dashboard extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|DASHBOARD");
		}
		function index(){
			/*echo'<pre>';
			print_r($_SESSION);
			echo '</pre>';*/
			$data = array('error' => false, 'message' => '', 'data'=> array());
			$this->commonView('header_html', array('error'=>$data['error']));
			$this->commonView('navbar');
			//$this->commonView('menu');
			if(!$data['error']){
				$this->view();
			}
			//$this->commonView('operation');
			//$this->commonView('footer_inner');
			$this->commonView('footer_html');
			
		}
	}