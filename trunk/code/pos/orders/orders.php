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

				function coc(){
			global $sql_host, $sql_user, $sql_password, $sql_db;
			$sql_host = 'localhost' ;
			$sql_user = 'root';
			$sql_password = 'root';
			$sql_db = 'cpos';			
			$db = new Database();

			$status = 'New';
			if(array_key_exists('status', $_GET) && !empty($_GET['status'])){
				$status = $_GET['status'];
			}

			$actionButtons = '';
			switch($status){
				case 'New':
					$actionButtons .= '<button class="btn btn-sm btn-primary bt-update-status" data-new_status="Confirmed" data-current_status="'.$status.'">Confirm</button>';
				case 'Confirmed':
					$actionButtons .= '<button class="btn btn-sm btn-primary bt-update-status" data-new_status="Cancelled" data-current_status="'.$status.'">Cancel</button>';
					$actionButtons .= '<button class="btn btn-sm btn-primary bt-update-status" data-new_status="Dispatched" data-current_status="'.$status.'">Dispatch</button>';
				case 'Dispatched':
					$actionButtons .= '<button class="btn btn-sm btn-primary" data-new_status="Delivered" data-current_status="'.$status.'">Deliver</button>';
				case 'Delivered':
				case 'Paid':
					$actionButtons .= '<button class="btn btn-sm btn-primary">Paid</button>';
				case 'Cancelled':
			}

			$getList = "SELECT 	co.id order_id, co.comment comment, sm.name store_name, co.store_id, co.comment, 
	            delivery_time, delivery_date, status, payment_method,  co.net_amount, co.total_amount,
	            coa.locality,coa.sublocality,coa.floor,coa.flat,coa.building,coa.email,
			    date_format(date(co.created_date),'%d-%b-%y') order_date,
			    time_format(time(co.created_date), '%r') order_time,
			    if(status = 'Delivered', timediff(co.final_delivery_time,co.created_date), if(status != 'Cancelled', timediff(now(),co.created_date),'')) time_passed,
			    date_format((concat(delivery_date,' ',delivery_time)), '%d-%b-%y %h:%i %p') final_delivery_time,
			    if(status = 'Delivered', if(final_delivery_time > concat(delivery_date,' ',delivery_time), 'Delayed', 'On time'), '') delivery_status,
			    pm.name product_name, pm.id product_id, cop.qty, cop.cost,cop.total_product_cost,
			    date_format(CONCAT(delivery_date,' ', delivery_time), '%d-%b-%y %h:%i %p') delivery_time_details,
			    coa.name name,coa.street, coa.landmark, coa.city, coa.address,
			    rc.id customer_id, rc.name rc_name, rc.phone,rc.company_name company,
			    rc.street rc_street, rc.landmark rc_landmark, rc.city rc_city, rc.address rc_address, rc.ppc_no rc_ppc_no, crm.name AS channel_name
		    FROM cp_orders co
			   LEFT JOIN cp_retial_customer rc on co.customer_id = rc.id
			   LEFT JOIN store_master sm on co.store_id = sm.id
			   LEFT JOIN cp_order_address coa on coa.order_id = co.id
			   LEFT JOIN cp_order_products cop on cop.order_id = co.id
			   LEFT JOIN  product_master pm on pm.id = cop.product_id
			   LEFT JOIN cp_reference_master crm ON crm.id = co.channel AND crm.active='Y'
		    where co.status = '".$status."' and date(co.delivery_date) = curdate() and co.customer_id is not null and co.store_id = ".$_SESSION['user']['store']['id']." order by time_passed desc";
			
			$orderListDetailed = $db->func_query($getList);

			$orderList = array();

				if(is_array($orderListDetailed) && count($orderListDetailed)>0){
		$j = 0;
		foreach($orderListDetailed as $key => $orderDetails){
			$i = $orderListDetailed [$key]['order_id'];
			$orderList[$i]['order_id'] = $orderListDetailed [$key]['order_id'];
			$orderList[$i]['delivery_status'] = $orderListDetailed [$key]['delivery_status'];
			$orderList[$i]['actual_delivery_time'] = $orderListDetailed [$key]['final_delivery_time'];
			$orderList[$i]['time_passed'] = $orderListDetailed [$key]['time_passed'];
			$orderList[$i]['comment'] = $orderListDetailed [$key]['comment'];
			$orderList[$i]['order_time'] = $orderListDetailed [$key]['order_time'];
			$orderList[$i]['status'] = $orderListDetailed [$key]['status'];
			$orderList[$i]['order_date'] = $orderListDetailed [$key]['order_date'];
			$orderList[$i]['payment_method'] = $orderListDetailed [$key]['payment_method'];
			$orderList[$i]['net_amount'] = $orderListDetailed [$key]['net_amount'];
			$orderList[$i]['comment'] = $orderListDetailed [$key]['comment'];
			$orderList[$i]['channel_name'] = $orderListDetailed [$key]['channel_name'];
			$orderList[$i]['t_amount'] = $orderListDetailed [$key]['total_amount'];
			$orderList[$i]['store_name'] = $orderListDetailed [$key]['store_name'];
			$orderList[$i]['store_id'] = $orderListDetailed [$key]['store_id'];
			$orderList[$i]['customer_id'] = $orderListDetailed [$key]['customer_id'];
			$orderList[$i]['phone'] = $orderListDetailed [$key]['phone'];
			$orderList[$i]['company'] = $orderListDetailed [$key]['company'];
			$orderList[$i]['delivery_time_details'] = $orderListDetailed [$key]['delivery_time_details'];
			$orderList[$i]['delivery_date'] = $orderListDetailed [$key]['delivery_date'];
			$orderList[$i]['delivery_time'] = $orderListDetailed [$key]['delivery_time'];
			$orderList[$i]['name'] = $orderListDetailed [$key]['name'];
			$orderList[$i]['address'] = $orderListDetailed [$key]['address'];
			$orderList[$i]['street'] = $orderListDetailed [$key]['street'];
			$orderList[$i]['landmark'] = $orderListDetailed [$key]['landmark'];
			$orderList[$i]['city'] = $orderListDetailed [$key]['city'];
			$orderList[$i]['ppc_no'] = $orderListDetailed [$key]['rc_ppc_no'];
			$orderList[$i]['products'][$j]['id'] = $orderDetails['product_id'];
			$orderList[$i]['products'][$j]['qty'] = $orderDetails['qty'];
			$orderList[$i]['products'][$j]['cost'] = $orderDetails['cost'];
			$orderList[$i]['products'][$j]['total'] = $orderDetails['total_product_cost'];
			$orderList[$i]['products'][$j]['name'] = $orderDetails['product_name'];
			$orderList[$i]['locality'] = $orderDetails['locality'];
			$orderList[$i]['sublocality'] = $orderDetails['sublocality'];
			$orderList[$i]['email'] = $orderDetails['email'];
			$orderList[$i]['building'] = $orderDetails['building'];
			$orderList[$i]['floor'] = $orderDetails['floor'];
			$orderList[$i]['flat'] = $orderDetails['flat'];
			$j++;
		}
	}

//			print_r($orderList);

			$this->commonView('header_html');
			$this->commonView('navbar');
			$this->view(array('orders'=>$orderList,'action'=>$actionButtons));
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		}
	}
