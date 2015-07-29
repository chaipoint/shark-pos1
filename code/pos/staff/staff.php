<?php	
	class Staff extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|STAFF");
		}
		
		/* Function To Get Store Staff */
		function getStaffList(){
				if(!array_key_exists('id', $_SESSION['user']['store'])){
					$result = $this->getSessionData();
					if($result['error']){
						header("LOCATION:index.php");
					}
				}
				$staffList = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID)->setParam(array("include_docs"=>"true","key"=>'"'.$_SESSION['user']['store']['id'].'"'))->execute();
				$rows = $staffList['rows'][0]['doc']['store_staff'];
				$staffList = array();
				foreach($rows as $key => $value){
					$staffList[$value['mysql_id']]['mysql_id'] = $value['mysql_id'] ;
					$staffList[$value['mysql_id']]['code'] = @$value['code'] ;
					$staffList[$value['mysql_id']]['name'] = $value['name'] ;
					$staffList[$value['mysql_id']]['title_id'] = $value['title_id'] ;
					$staffList[$value['mysql_id']]['title_name'] = @$value['title_name'] ;
				}
				ksort($staffList);
				return $staffList;
		}

		/* Function To Get Delivery Boy For COC Order*/
		function getDeliveryBoy(){ 
			$return = array();	
			$options = array();
			$token='';
			if(array_key_exists('token', $_REQUEST)){
				$token=strtolower($_REQUEST['token']);
				//$options['startkey'] = '"'.$_REQUEST['token'].'a"';
				//$options['endkey'] = '"'.$_REQUEST['token'].'z"';
			}
			$test=array('include_docs'=>'true',"key"=>'"'.$_SESSION['user']['store']['id'].'"');
			//$deliveryBoy = $this->cDB->getDesign(STAFF_DESIGN_DOCUMENT)->getView(STAFF_DESIGN_DOCUMENT_VIEW_STAFF_NAME)->setParam($options)->execute();
			$deliveryBoy = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID)->setParam($test)->execute();
			$deliveryBoy = $deliveryBoy['rows'][0]['value']['staff'];
           $i=0;
			foreach($deliveryBoy as $val){
				$name=strtolower($val['name']);
				$pos=strpos($name,$token);
				if ($pos !== false) {
							$return[$i]['id']=$val['mysql_id'];
							$return[$i]['label']=$val['name'];
							$i++;
					}
			}
			
		//	$this->log->trace('GET DELIVERY BOY'."\r\n".json_encode($deliveryBoy));
		//	$this->cDB->getLastUrl();			
			/*
			if(array_key_exists('rows', $deliveryBoy)){
				$rows = $deliveryBoy['rows'];
				foreach($rows as $key => $value){
					$return[$key]['id'] = $value['value']; 
					$return[$key]['label'] = $value['key']; 
				}
			}
			*/
			return $return;
		}
		
	}