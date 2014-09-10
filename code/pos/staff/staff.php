<?php	
	class Staff extends App_config{
		private $cDB;
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
		}
		public function index(){
			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->view();			
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		} 
		function getStaff(){
			$name = $_REQUEST['token'];
			//$cDB
			/*			$query = "SELECT name label, id FROM staff_master WHERE active='Y' AND name LIKE '$name%' ORDER BY name ASC";
			$result = $db->func_query($query);/**/
			return json_encode($result,true);
		}	

}