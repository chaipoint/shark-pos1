<?php
    include_once 'common/connection.php' ;
	require_once 'common/couchdb.phpclass.php';
	require_once 'common/logger.php';

	$couch = new CouchPHP();

    $logger = Logger::getLogger("HO|HO_TO_CPOS");
    $logger->trace("HO to CPOS Updating");

 	
 	$billData = $couch->getDesign('design_ho')->getView('handle_updated_bills')->setParam(array('descending'=>'true'))->execute();
 	if(array_key_exists('rows', $billData)){
 		$billList = array();
 		foreach($billData['rows'] as $key => $value){
 			$data = $value['value'];
 			$bill_no = $value['key'][1];
 			if(!array_key_exists($bill_no, $billList)){
	 			$billList[$bill_no] = '';
 				if($data['mysql'] == 0){
	 				$updateBill = "update cp_pos_storeorders set bill_status = '".$data['bill_status']."', cancel_reason = '".$data['cancel_reason']."', reprint = '".$data['reprint']."' where _id = '".$data['parent']."'";
	 				$db->db_query($updateBill);	
	 				if($db->db_affected_rows() > 0){
	 					$select = "SELECT id from cp_pos_storeorders where _id = '".$data['parent']."'";
	 					$selectData = $db->func_query($select);
	 					;
					    $returnResult = $couch->getDesign('design_ho')->getUpdate('insert_mysql_id', $value['id'])->setParam(array('mysql_id'=>$selectData[0]['id']))->execute();				
	 				}
	 			}
 				/*
            [bill_status] => Cancelled
            [cancel_reason] => tetsing purpose
            [reprint] => 0*/

 			}

		}
	}