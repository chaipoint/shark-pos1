<?php	
	class Staff extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|STAFF");
			$this->getDBConnection($this->cDB);
		}
		
		/*function getStaff(){
			$staffList = $this->cDB->getDesign(STAFF_DESIGN_DOCUMENT)->getView(STAFF_DESIGN_DOCUMENT_VIEW_STAFF_USERNAME)->setParam(array("include_docs"=>"true"))->execute();
			return $staffList;
		}*/

		/* Function To Get Store Staff */
		function getStaffList(){
				$staffList = $this->cDB->getDesign(STORE_DESIGN_DOCUMENT)->getView(STORE_DESIGN_DOCUMENT_VIEW_STORE_MYSQL_ID)->setParam(array("include_docs"=>"true"))->execute();
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
			$token = $_REQUEST['token'];
			$getStaff = "SELECT name label, id FROM staff_master 
						 WHERE active = 'Y' 
						 AND location_id = '".$_SESSION['user']['location']['id']."' 
						 AND name LIKE '%$token%'";
			$result = $this->db->func_query($getStaff);
			$this->log->trace('GET DELIVERY BOY'."\r\n".$getStaff);
			echo json_encode($result);
		}

		

}