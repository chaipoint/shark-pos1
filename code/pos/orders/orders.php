<?php
	class Orders extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|ORDERS");
			$this->getDBConnection($this->cDB);
		}

		/* Function That Call After Every 3 Minute To Check New COC Order */
		function getCocOrder(){
        	$return = array('error'=>false,'message'=>'','count'=>false,'data'=>array());
	        	if(array_key_exists('user', $_SESSION)){
		        	if(array_key_exists('request_type', $_REQUEST) && $_REQUEST['request_type']==GET_COC_ORDER){
						if(getType($this->db->getInstance()) == RESOURCE){
		    	    		$getOrder = 'SELECT id FROM cp_orders 
		    	    					 WHERE store_id = "'.$_SESSION['user']['store']['id'].'"  
		    	    					 AND DATE(created_date) = CURDATE() 
		    	    					 AND status = "New"';
		    	    		$this->log->trace("GET COC ORDER \r\n".$getOrder);
		        			$result = $this->db->func_query($getOrder);
		        			if(is_array($result) && count($result)>0){
		        				$return['message'] = 'New Order Placed';
		        				$return['count'] = true;  
							}
						}

						return $return;
        			}
        		}
        	return $return;
        }
        
        /* Function To Change COC Order Status */
		function updateOrderStatus(){
			$dir =  dirname(__FILE__).'/../lib/api/sms_api.php';
            require_once $dir; 
            if(getType($this->db->getInstance()) != RESOURCE){
				return json_encode(array('error' => true, 'message' => SERVER_DOWN_ERROR, 'data' => array()));					
			}
            $details = $this->getConfig($this->cDB,'sms_api');
			$return = array('error' => false, 'message' => '', 'data' => array());
			if(array_key_exists('new_status', $_POST) && array_key_exists('current_status', $_POST)){
				$current = $_POST['current_status'];
				$new = $_POST['new_status'];
				if(array_key_exists('order', $_POST) && is_numeric($_POST['order'])){
					$order = $_POST['order'];
					if($_POST['new_status']=='Dispatched'){
						$orderNO = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getView(BILLING_DESIGN_DOCUMENT_VIEW_BILL_BY_ORDER)->setParam(array('key'=> '"'.$_POST['order'].'"' ))->execute();
						if(!array_key_exists(0, $orderNO['rows'])){
								$return['error'] = true;
								$return['message'] = "No Bill Exists, Please Make Bill.";
								$re = json_encode($return);
								$this->log->trace("RESPONSE \r\n".$re);
								return $re;							
						}
					}else if($_POST['new_status']=='Paid' || $_POST['new_status']=='Cancelled'){
						$getDoc = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getView(BILLING_DESIGN_DOCUMENT_VIEW_BILL_BY_ORDER)->setParam(array('key'=> '"'.$_POST['order'].'"','include_docs'=>'true'))->execute();
						if(array_key_exists(0, $getDoc['rows'])){
							$doc = $getDoc['rows'][0]['doc']['_id'];
						   	$_POST['request_type'] = UPDATE_BILL;
							$_POST['doc'] = $doc;
							if($_POST['new_status']=='Paid'){
								$_POST['bill_status_id'] = 68;
								$_POST['bill_status_name'] = 'Paid';
							}else if($_POST['new_status']=='Cancelled'){
								$_POST['bill_status_id'] = 67;
								$_POST['bill_status_name'] = 'Cancelled';
								$_POST['cancel_reason'] = $_POST['reason'];
								$_POST['due_amount'] = $_POST['net_amount'];
							}
								
							require_once DIR.'/billing/billing.php';
							$bl = new billing();
							$result = $bl->save($_POST);
							$response = json_decode($result,true); 
							if($response['error']=='true'){
								$return['error'] = true;
								$return['message'] = ERROR;
								$re = json_encode($return);
								$this->log->trace("RESPONSE \r\n".$re);
								return $re;		
							}
						}
					} 
						
						$updateStatus = "UPDATE cp_orders SET ".($_POST['new_status']=='Confirmed' ? " confirm_time = '".date('Y-m-d H:i:s')."', " : ($_POST['new_status']=='Dispatched' ? " dispatch_time = '".date('Y-m-d H:i:s')."', " : ''))." ".(array_key_exists('staff_id', $_POST) ? " delivery_boy = '".mysql_real_escape_string($_POST['staff_id'])."', " : '')." ".(array_key_exists('reason', $_POST) ? " cancel_reason = '".mysql_real_escape_string($_POST['reason'])."', cancel_time = '".date('Y-m-d H:i:s')."', " : '')." status = '".$new."', updated_by = ".$_SESSION['user']['mysql_id'].", updated_date = '".$this->getCDTime()."' WHERE id = ".$order." AND status = '".$current."'";
						$this->log->trace("UPDATE COC ORDER STATUS \r\n".$updateStatus);
						$this->db->db_query($updateStatus);
						if( ! $this->db->db_affected_rows()){
							$selectStatus = "SELECT status FROM cp_orders 
											 Where id = ".$order;
							$result = $this->db->func_query_first($selectStatus);
							
							$return = array('error' => true, 'message' => 'Status Already Changed to <b>'.$result['status'].'</b>', 'data' => array('status'=>$result['status']));
						}else if($_POST['new_status']=='Confirmed'){ 
							$data = array( 'From'   => PROVIDER_NUMBER,
										   'To'    => $_POST['customer_phone'],
						                   'Body'  => CONFIRMED_MESSAGE
				                         );
				           call_api($data,$details);

						}else if ($_POST['new_status']=='Dispatched') {
							$data = array( 'From'   => PROVIDER_NUMBER,
										   'To'    => $_POST['customer_phone'],
						                   'Body'  => DISPATCHED_MESSAGE
				                         );
				            call_api($data,$details);
						}
				}else{
					$return['error'] = true;
					$return['message'] = "Invalid Order";
				}
			}else{
				$return['error'] = true;
				$return['message'] = "Provide Status";				
			}
			return json_encode($return);
		}
		/* This Function Is Automatically Called When We Come On COC Module */
		function index(){
			global $ORDER_STATUS;
			$db = $this->db;
			$error = false;
			if(array_key_exists('message', $db) && $db['message'] == SERVER_DOWN_ERROR){
				$error = true;
			}else{
				if(getType($db->getInstance()) != RESOURCE){
					$error = true;
				}else{
					$status = 'New';
					if(array_key_exists('status', $_GET) && !empty($_GET['status'])){
						$status = $_GET['status'];
					}
					$print = '<div class="hidden">%s</div>';
					$actionButtons = '<div class="btn-group-horizontal">';
					switch($status){
						case 'New':
							$actionButtons .= '<button class="btn btn-sm btn-success bt-update-status" data-new_status="Confirmed" data-current_status="'.$status.'"><i class="glyphicon glyphicon-ok"></i>&nbsp;Confirm</button>&nbsp;&nbsp;';
							$actionButtons .= '<button class="btn btn-sm btn-danger bt-update-status" data-new_status="Cancelled" data-current_status="'.$status.'"><i class="glyphicon glyphicon-trash"></i>&nbsp;Cancel</button>&nbsp;&nbsp;';
							break;
						case 'Confirmed':
							$actionButtons .= '<button class="btn btn-sm btn-success bt-update-status" data-new_status="Dispatched" data-current_status="'.$status.'"><i class="glyphicon glyphicon-ok"></i>&nbsp;Dispatch</button>&nbsp;&nbsp;';
		                    $actionButtons .= '<button class="btn btn-sm btn-danger bt-update-status" data-new_status="Cancelled" data-current_status="'.$status.'"><i class="glyphicon glyphicon-trash"></i>&nbsp;Cancel</button>&nbsp;&nbsp;';
							$print = '<button class="btn btn-primary btn-sm generate-bill"  data-order-id="%s"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;Bill</button>';
							break;
						case 'Dispatched':
							$actionButtons .= '<button class="btn btn-sm btn-success bt-update-status" data-new_status="Delivered" data-current_status="'.$status.'">Delivered</button>';
							break;
						case 'Delivered':
						    $actionButtons .= '<button class="btn btn-sm btn-success bt-update-status" data-new_status="Paid" data-current_status="'.$status.'">Paid</button>';
						    break;
						case 'Paid':
							
						case 'Cancelled':
					}
					$actionButtons .= '</div><div style="margin-top:5px;" class="btn-group-horizontal"><button class="btn-sm btn btn-default"><i class="glyphicon glyphicon-print"></i>&nbsp;Print</button>&nbsp;&nbsp;'.$print.'</div>';


					$getList = "SELECT co.cancel_reason, co.id order_id, co.comment comment, delivery_time, delivery_date,
								status, co.net_amount, co.total_amount, date_format(date(co.created_date),'%d-%b-%y') order_date,
					    		time_format(time(co.created_date), '%r') order_time, date_format((concat(delivery_date,' ',delivery_time)), '%d-%b-%y %h:%i %p') final_delivery_time,
					    		stf.name AS delivery_boy,
			            		rc.company_name company, rc.phone,
			            		coa.name name, coa.building, coa.floor, coa.flat, coa.locality, coa.sublocality, coa.landmark, coa.city,
					    		pm.name product_name, pm.id product_id, cop.qty, cop.cost, cop.total_product_cost, crm.name channel_name, crm.id channel_id
				    		FROM cp_orders co
					   			LEFT JOIN cp_retial_customer rc on co.customer_id = rc.id
					   			LEFT JOIN cp_order_address coa on coa.order_id = co.id
					   			LEFT JOIN staff_master stf ON stf.id = co.delivery_boy AND stf.active ='Y'
					   			LEFT JOIN cp_order_products cop on cop.order_id = co.id
					   			LEFT JOIN  product_master pm on pm.id = cop.product_id
					   			LEFT JOIN cp_reference_master crm ON crm.id = co.channel AND crm.active='Y'
				    			WHERE co.status = '".$status."' and date(co.delivery_date) = curdate() and co.customer_id is not null and co.store_id = ".$_SESSION['user']['store']['id']."";
					
					$this->log->trace("GET COC ORDER DETAIL \r\n".$getList);
					$orderListDetailed = $db->func_query($getList);
					$orderList = array();

					if(is_array($orderListDetailed) && count($orderListDetailed)>0){
						$j = 0;
						foreach($orderListDetailed as $key => $orderDetails){
							$i = $orderListDetailed [$key]['order_id'];
							$orderList[$i]['order_id'] = $orderListDetailed [$key]['order_id'];
							$orderList[$i]['cancel_reason'] = $orderListDetailed [$key]['cancel_reason'];
							$orderList[$i]['delivery_boy'] = $orderListDetailed [$key]['delivery_boy'];
							//$orderList[$i]['delivery_status'] = $orderListDetailed [$key]['delivery_status'];
							$orderList[$i]['actual_delivery_time'] = $orderListDetailed [$key]['final_delivery_time'];
							//$orderList[$i]['time_passed'] = $orderListDetailed [$key]['time_passed'];
							$orderList[$i]['comment'] = $orderListDetailed [$key]['comment'];
							$orderList[$i]['order_time'] = $orderListDetailed [$key]['order_time'];
							$orderList[$i]['status'] = $orderListDetailed [$key]['status'];
							$orderList[$i]['order_date'] = $orderListDetailed [$key]['order_date'];
							//$orderList[$i]['payment_method'] = $orderListDetailed [$key]['payment_method'];
							$orderList[$i]['net_amount'] = $orderListDetailed [$key]['net_amount'];
							//$orderList[$i]['comment'] = $orderListDetailed [$key]['comment'];
							$orderList[$i]['channel_name'] = $orderListDetailed [$key]['channel_name'];
							$orderList[$i]['channel_id'] = $orderListDetailed [$key]['channel_id'];
							$orderList[$i]['t_amount'] = $orderListDetailed [$key]['total_amount'];
							//$orderList[$i]['store_name'] = $orderListDetailed [$key]['store_name'];
							//$orderList[$i]['store_id'] = $orderListDetailed [$key]['store_id'];
							//$orderList[$i]['customer_id'] = $orderListDetailed [$key]['customer_id'];
							$orderList[$i]['phone'] = $orderListDetailed [$key]['phone'];
							$orderList[$i]['company'] = $orderListDetailed [$key]['company'];
							//$orderList[$i]['delivery_time_details'] = $orderListDetailed [$key]['delivery_time_details'];
							$orderList[$i]['delivery_date'] = $orderListDetailed [$key]['delivery_date'];
							$orderList[$i]['delivery_time'] = $orderListDetailed [$key]['delivery_time'];
							$orderList[$i]['name'] = $orderListDetailed [$key]['name'];
							//$orderList[$i]['address'] = $orderListDetailed [$key]['address'];
							//$orderList[$i]['street'] = $orderListDetailed [$key]['street'];
							$orderList[$i]['landmark'] = $orderListDetailed [$key]['landmark'];
							$orderList[$i]['city'] = $orderListDetailed [$key]['city'];
							//$orderList[$i]['ppc_no'] = $orderListDetailed [$key]['rc_ppc_no'];
							$orderList[$i]['products'][$j]['id'] = $orderDetails['product_id'];
							$orderList[$i]['products'][$j]['qty'] = $orderDetails['qty'];
							$orderList[$i]['products'][$j]['cost'] = $orderDetails['cost'];
							$orderList[$i]['products'][$j]['total'] = $orderDetails['total_product_cost'];
							$orderList[$i]['products'][$j]['name'] = $orderDetails['product_name'];
							$orderList[$i]['locality'] = $orderDetails['locality'];
							$orderList[$i]['sublocality'] = $orderDetails['sublocality'];
							//$orderList[$i]['email'] = $orderDetails['email'];
							$orderList[$i]['building'] = $orderDetails['building'];
							$orderList[$i]['floor'] = $orderDetails['floor'];
							$orderList[$i]['flat'] = $orderDetails['flat'];
							$j++;
						}
					}
		   
			        $getOrderCount = "SELECT COUNT(id)AS count, status 
			        				  FROM `cp_orders` 
		                          	  WHERE DATE(created_date) = CURDATE() 
		                              AND store_id = ".$_SESSION['user']['store']['id']."
		                              GROUP BY status";
					$result = $db->func_query($getOrderCount);
		    	    $status_type_count = $ORDER_STATUS;
		        	if(is_array($result) && count($result)>0){
		        		foreach ($result as $key => $value) {
		        			$status_type_count[$value['status']] = $value['count'];
		        		}
		        	}
		        }
	    	}
	    	$billArray = array();
	    	$resultGetBill = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getView(BILLING_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS)->setParam(array("include_docs"=>"true","descending"=>"true","endkey" => '["'.$this->getCDate().'"]',"startkey" => '["'.$this->getCDate().'",{},{},{}]'))->execute();
	    		if(array_key_exists('rows', $resultGetBill) && count($resultGetBill['rows'])>0){
	    			$docs = $resultGetBill['rows'];
	    			foreach ($docs as $key => $value) {
	    			 $billArray[$value['doc']['order_no']] = $value['doc']['bill_no']; 	
	    			 } 
	    		} 
	    
			$this->commonView('header_html',array('error'=>$error));
			$this->commonView('navbar');
			if(!$error){
				$this->view(array('orders'=>$orderList,'billArray'=>$billArray,'action'=>$actionButtons, 'status'=>$status, 'order_count'=>$status_type_count));
			}
			$this->commonView('footer_inner');
			$this->commonView('footer_html');
		}
	}
