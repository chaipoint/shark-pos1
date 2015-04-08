<?php
	class Orders extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|ORDERS");
		}

		/* This Function Is Automatically Called When We Come On COC Module */
		function index(){
			$status = NEW_ORDER;
			if(array_key_exists('status', $_GET) && !empty($_GET['status'])){
				$status = $_GET['status'];
			}
			$getData = $this->getCocOrder($status , COC);
			if(!$getData['error']){
				$actionButtons = $this->actionButton($status);
			}
	    	$billArray = array();
	    	$resultGetBill = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getView(BILLING_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS)->setParam(array("include_docs"=>"true","descending"=>"true","endkey" => '["'.$this->getCDate().'"]',"startkey" => '["'.$this->getCDate().'",{},{},{}]'))->execute();
	    	if(array_key_exists('rows', $resultGetBill) && count($resultGetBill['rows'])>0){
	    		$docs = $resultGetBill['rows'];
	    		foreach ($docs as $key => $value) {
	    			$billArray[$value['doc']['order_no']] = $value['doc']['bill_no']; 	
	    		} 
	    	} 
	    
			$this->commonView('header_html',array('error'=>$getData['error']));
			$this->commonView('navbar');
			$this->commonView('menu');
			if(!$getData['error']){
				$this->view(array('orders'=>$getData['data']['orderList'],'billArray'=>$billArray,'action'=>$actionButtons, 'status'=>$status, 'order_count'=>$getData['data']['orderCount']));
			}
			$this->commonView('operation');
			//$this->commonView('footer_inner');
			$this->commonView('footer_html');
		}
        
		function olo(){
			$status = NEW_ORDER;
			if(array_key_exists('status', $_GET) && !empty($_GET['status'])){
				$status = $_GET['status'];
			}
			$getData = $this->getCocOrder($status , OLO);
			if(!$getData['error']){
				$actionButtons = $this->actionButton($status);
			}
	    	$billArray = array();
	    	$resultGetBill = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getView(BILLING_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS)->setParam(array("include_docs"=>"true","descending"=>"true","endkey" => '["'.$this->getCDate().'"]',"startkey" => '["'.$this->getCDate().'",{},{},{}]'))->execute();
	    	if(array_key_exists('rows', $resultGetBill) && count($resultGetBill['rows'])>0){
	    		$docs = $resultGetBill['rows'];
	    		foreach ($docs as $key => $value) {
	    			$billArray[$value['doc']['order_no']] = $value['doc']['bill_no']; 	
	    		} 
	    	} 
	    
			$this->commonView('header_html',array('error'=>$getData['error']));
			$this->commonView('navbar');
			$this->commonView('menu');
			if(!$getData['error']){
				$this->view(array('orders'=>$getData['data']['orderList'],'billArray'=>$billArray,'action'=>$actionButtons, 'status'=>$status, 'order_count'=>$getData['data']['orderCount']));
			}
			$this->commonView('operation');
			//$this->commonView('footer_inner');
			$this->commonView('footer_html');
			
		}
		
        /* Function To Change COC Order Status */
		function updateOrderStatus(){
			require_once dirname(__FILE__).'/../lib/api/sms_api.php';
			$return = array('error' => false, 'message' => '', 'data' => array());
			$details = $this->getConfig($this->cDB, 'sms_api');

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
								$_POST['bill_status_id'] = 80;
								$_POST['bill_status_name'] = 'Paid';
							}else if($_POST['new_status']=='Cancelled'){
								$_POST['bill_status_id'] = 79;
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
						
					$_POST['updatedBy'] = $_SESSION['user']['mysql_id'];
					$_POST['updatedDate'] = $this->getCDTime();
					$_POST['currentStatus'] = $current;
					$_POST['newStatus'] = $new;
					$_POST['orderId'] = $order;
					$_POST['action'] = 'changeOrderStatus';
					$changeStatus = $this->changeOrderStatus($_POST);
					if($changeStatus['error']){
						$return['error'] = true;
						$return['message'] = $changeStatus['message'];
					}/*else if($_POST['new_status']=='Confirmed'){ 
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
					}*/
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
		

		function getCocOrder($status='',$order_type=''){
			if(empty($_SESSION['user']['store']['id'])){
				$result = $this->getSessionData();
				if($result['error']){
					$return['error'] = true;
					$return['message'] = $result['message'];
					return json_encode($return);
				}
			}
			$status = (!empty($status) ? $status : 'New');
			$order_type = (!empty($order_type) ? $order_type : 'COC');
			$storeId = $_SESSION['user']['store']['id'];
			$postData = array('action'=>'getCocOrder', 'store_id'=>$storeId, 'status'=>$status, 'order_type'=>$order_type);
			$url = API_URL;
			$ch = curl_init();
			curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_CONNECTTIMEOUT=>8, CURLOPT_TIMEOUT=>8, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData));
			$response = curl_exec($ch);
			curl_close($ch);
			$response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);
			$data = json_decode($response, true);
			return $data;
		}
		
		function getNewOrder(){
			if(empty($_SESSION['user']['store']['id'])){
				$result = $this->getSessionData();
				if($result['error']){
					$return['error'] = true;
					$return['message'] = $result['message'];
					return json_encode($return);
				}
			}
			$storeId = $_SESSION['user']['store']['id'];
			$postData = array('action'=>'getNewOrder', 'store_id'=>$storeId);
			$url = API_URL;
			$ch = curl_init();
			curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_CONNECTTIMEOUT=>2, CURLOPT_TIMEOUT=>4,CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData));
			$response = curl_exec($ch);
			curl_close($ch);
			$response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);
			$data = json_decode($response, true);
			return $data;
		}

		function changeOrderStatus($postData){
			if(empty($postData)){
				$return = array('error'=>true, 'message'=>VALID_INPUT_ERROR);
				return $return;
			}
			$url = API_URL;
			$ch = curl_init();
			curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData));
			$response = curl_exec($ch);
			curl_close($ch);
			$response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);
			$data = json_decode($response, true);
			return $data;
		}
		
		function actionButton($status){
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
					$actionButtons .= '</div><div style="margin-top:5px;" class="btn-group-horizontal"><button class="btn-sm btn btn-default print"><i class="glyphicon glyphicon-print"></i>&nbsp;Print</button>&nbsp;&nbsp;'.$print.'</div>';
				return $actionButtons;
			
		}

	}

 ?>
