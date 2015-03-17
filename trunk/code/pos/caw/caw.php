<?php
	class CAW extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|RETAIL CUSTOMER");
		}
		function index(){
			if(!array_key_exists('user', $_SESSION)){
				header("LOCATION:index.php");
			}else if(!array_key_exists('id', $_SESSION['user']['store'])){
				$result = $this->getSessionData();
				if($result['error']){
					header("LOCATION:index.php");
				}
			}
			$data = array('error' => false, 'customerList'=>array());
			$getCustomer = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID)->setParam(array('include_docs'=>'true', "key"=>'"'.$_SESSION['user']['store']['id'].'"'))->execute();
			$this->log->trace('CUSTOMER LIST'."\r\n".json_encode($getCustomer));
			$result = $getCustomer['rows'][0]['doc'];
			$customerList= array();
			if(array_key_exists('retail_customer', $result) && count($result['retail_customer'])>0){
				foreach($result['retail_customer'] as $key => $value){
					$customerList[$key]['name'] = $value['name'];
					$customerList[$key]['id'] = $value['id'];
					$customerList[$key]['type'] = $value['type'];
				}
			}else{
				$data['error'] = false;
				$data['message'] = NO_CUSTOMER_ASSIGN;
			}
			$data['customerList']=$customerList;
			$this->commonView('header_html',array('error'=>$data['error']));
			$this->commonView('navbar');
			$this->commonView('menu');
			if(!$data['error']){
				$this->view($data);
			}
			$this->commonView('operation');
			//$this->commonView('footer_inner');
			$this->commonView('footer_html');

		}
		
		function schedule(){
			$return = array('error' => false, 'schedule'=>array());
			if(empty($_SESSION['user']['store']['id'])){
				$result = $this->getSessionData();
				if($result['error']){
					$return['error'] = true;
					$return['message'] = $result['message'];
					return json_encode($return);
				}
			}
			if(!empty($_POST['customer_name'])){
				$getScheduleData = $this->cDB->getDesign(CUSTOMERS_DESIGN_DOCUMENT)->getView(CUSTOMERS_DESIGN_DOCUMENT_VIEW_RETAIL_CUSTOMER_LIST)->setParam(array('include_docs'=>'true', 'key'=>'"'.$_REQUEST['customer_name'].'"'))->execute();
				$this->log->trace('CUSTOMER DATA'."\r\n".json_encode($getScheduleData));
				
				$getChallanNo  = $this->cDB->getDesign(BILLING_DESIGN_DOCUMENT)->getList(BILLING_DESIGN_DOCUMENT_LIST_CAW_SALE,BILLING_DESIGN_DOCUMENT_VIEW_BILL_BY_STORE_COUNTER)->setParam(array("include_docs"=>"true","startkey" => '["'.$this->getCDate().'", "'.$_SESSION['user']['store']['id'].'" ,"'.$_SESSION['user']['counter'].'"]',"endkey" => '["'.$this->getCDate().'","'.$_SESSION['user']['store']['id'].'" ,"'.$_SESSION['user']['counter'].'"]'))->execute();
				$return['store_id'] = $_SESSION['user']['store']['id'];
				
				if(array_key_exists('rows', $getScheduleData) && count($getScheduleData['rows'])>0){
					$scheduleList = array();
					$scheduleList['mysql_id'] = $getScheduleData['rows'][0]['doc']['mysql_id'];
					$scheduleList['name'] = $getScheduleData['rows'][0]['doc']['name'];
					$scheduleList['address'] = $getScheduleData['rows'][0]['doc']['address'];
					$scheduleList['phone'] = $getScheduleData['rows'][0]['doc']['phone'];
					$scheduleList['e_mail'] = $getScheduleData['rows'][0]['doc']['e_mail'];
					$scheduleList['contact_person'] = $getScheduleData['rows'][0]['doc']['contact_person'];
					$rows = $getScheduleData['rows'][0]['doc']['schedule'];
					foreach($rows as $key => $value){
						foreach($value as $k => $val){
							$scheduleList['product'][$key][] = $val;
						}
					}
					$customer_name = $_POST['customer_name'];
					$customer_id = $_POST['customer_id'];
					$challan_details = (array_key_exists($_POST['customer_id'], $getChallanNo) ? $getChallanNo[$_POST['customer_id']] : array());
					
					if(is_array($scheduleList) && count($scheduleList)>0){
						$display = '';
						$i=1;
						foreach($scheduleList['product'] as $key => $value){
							$display .= '<tr rowid='.$i.'>
										<td class="text-center"><strong>'.$value[0]['production_start_time'].'</strong></td>
										<td class="text-center"><strong>'.$value[0]['store_leaving_time'].'</strong></td>
										<td class="text-center"><strong>'.$value[0]['onsite_time'].'</strong></td>
										<td class="text-right">
											<table class="table toggle-table">
												<thead><tr><th>Name</th><th>Qty</th><th>Price</th></tr></thead>
												<tbody>';
										$product =  $value;
										$productArray = array();
										$productArray['mysql_id'] = $scheduleList['mysql_id'];
										$productArray['name'] = $scheduleList['name'];
										$productArray['address'] = $scheduleList['address'];
										$productArray['phone'] = $scheduleList['phone'];
										$productArray['e_mail'] = $scheduleList['e_mail'];
										$productArray['contact_person'] = $scheduleList['contact_person'];
										$productArray['onsite_time'] = $value[0]['onsite_time'];
										foreach($product as $pKey => $pValues){
											$display .='<tr><td>'.$pValues['name'].'</td><td>'.$pValues['qty'].'</td><td>'.$pValues['price'].'</td></tr>';
											$productArray['product'][$pKey]['id'] = $pValues['mysql_id'];
											$productArray['product'][$pKey]['name'] = $pValues['name'];
											$productArray['product'][$pKey]['price'] = $pValues['price'];
											$productArray['product'][$pKey]['qty'] = $pValues['qty'];
										}
										$display .='</tbody></table>
										</td> '.(!array_key_exists($value[0]['onsite_time'], $challan_details) ?
										'<td class="text-center">
										<input type="text" class="challan" id="challan_'.$i.'" placeholder="Enter Challan No" /><br>
										<button  class="btn btn-primary btn-sm generate-bill" id="button_'.$i.'" data-customer_id="'.$customer_id.'"  data-order_details=\''.json_encode($productArray).'\'><i class="glyphicon glyphicon-list-alt"></i>&nbsp;Print Bill</button>
										</td>' : 
										"<td class='text-center'>".$challan_details[$value[0]['onsite_time']]."</td>" ).'
									
									</tr>';
									$i++;
						}
						$return['data'] = $display;
						$return['customer_name'] = $_POST['customer_name'];
						
					}else{
						$return['error'] = true;
						$return['message'] = APPROVED_SCHEDULE_NOT_FOUND;
					}
			
				}else{
					$return['error'] = true;
					$return['message'] = APPROVED_SCHEDULE_NOT_FOUND;
				}
			}else{
				$return['error'] = true;
				$return['message'] = ERROR;
			}
			
			return json_encode($return);
		
		}
		
	}