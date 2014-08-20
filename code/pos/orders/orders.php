<?php
	class Orders extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|ORDERS");
		}	
		function updateOrderStatus(){
			print_r($_POST);
			/*Array( [new_status] => Dispatched [current_status] => Confirmed)*/
			if(array_key_exists('new_status', $_POST) && array_key_exists('current_status', $_POST)){
				$current = $_POST['current_status'];
				$new = $_POST['new_status'];
				if(array_key_exists('order', $_POST) && is_numeric($_POST['order'])){
					$order = $_POST['order'];
				}else{

				}


			}
		}
	}
