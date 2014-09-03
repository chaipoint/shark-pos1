<?php
    include_once 'common/connection.php' ;
	require_once 'common/couchdb.phpclass.php';
	require_once 'common/logger.php';
    
    $logger = Logger::getLogger("HO_TO_CPOS API");
    $logger->trace("Calling HO_TO_CPOS API");
   
    $action = @$_REQUEST['action'];
   
   switch ($action){
        
        case "uploadBill":
		echo uploadBill();
		break;

		case "uploadUpdatedBill":
		echo uploadUpdatedBill();
		break;
}

/* Function To Upload Bill On CPOS*/

function uploadBill(){
	global $logger, $db;
	$logger->debug("Calling Upload Bill Function");
	$couch = new CouchPHP();
	$html = array();

	$no_bill = $unsuccessful = $successful = $counter = 0;
	
 	$billData = $couch->getDesign('design_ho')->getView('no_mysql_id')->setParam(array('include_docs'=>'true'))->execute();
 	if(array_key_exists('rows', $billData)){
 		foreach($billData['rows'] as $key => $value){
 			$doc = $value['doc'];
 			$docKey = $value['key'];
 			$dValue['doc'] = $doc;
			$docsData = array(	"_id"  => $doc['_id'],
								"_rev" => $doc['_rev'],
								"bill_no" => $doc['bill_no'],
								"bill_time" => $doc['time']['created'], 
								"store_id" => $doc['store_id'], 
								"store_name" => $doc['store_name'], 
								"counter" => $doc['counter'], 
								"staff_id" => $doc['staff_id'], 
								"staff_name" => $doc['staff_name'],
								"location_id" => $doc['location_id'],
								"location_name" => $doc['location_name'],
								"is_updated" => $doc['is_updated'],
								"channel" => $doc['booking_channel_name'],
								"payment_type" => $doc['payment_type'],
			            		"card_no" => $doc['card_no'],
			            		"coupon_id" => 1,
			            		"coupon_code" => $doc['coupon_code'],
			            		"shift" => $doc['shift'],
			            		"total_qty" => $doc['total_qty'],
			            		"total_amount" => $doc['total_amount'],
			            		"sub_total" => $doc['shift'],
			            		"total_discount" => $doc['total_discount'],
			            		"discount" => $doc['discount'],
			            		"total_tax" => $doc['total_tax'],
			            		"round_off" => $doc['round_off'],
			            		"due_amount" => $doc['due_amount'],
			            		"bill_status" => $doc['bill_status'],
			            		"reprint" => $doc['reprint']
			            );

			$db->func_array2insert("cp_pos_storeorders", $docsData);
			$insertId = $db->db_insert_id();	
			$productsArray = array();
			if($insertId > 0){
				foreach ($doc['items'] as $itemKey => $itemVvalue) {
					$pValue = $itemVvalue;
					$productsArray[] = "('".$insertId."','".$pValue['id']."','".$pValue['name']."','".$pValue['category_id']."','".$pValue['category_name']."','".$pValue['qty']."','".$pValue['price']."','".$pValue['tax']."','".$pValue['priceBT']."','".$pValue['discount']."','".$pValue['discountAmount']."','".$pValue['taxAbleAmount']."','".$pValue['taxAmount']."','".$pValue['totalAmount']."','".$pValue['netAmount']."')";
				}		
				if(count($productsArray) > 0){
					$insertProducst = 'insert into cp_pos_storeorders_products (order_id, product_id, product_name, category_id, category_name, qty, price, tax, priceBT, discount, discount_amount, taxable_amount, tax_amount, total_amount, net_amount) values '.implode(',',$productsArray);
					$res = $db->db_query($insertProducst);	
				    $returnResult = $couch->getDesign('design_ho')->getUpdate('insert_mysql_id', $docsData['_id'])->setParam(array('mysql_id'=>$insertId))->execute();				
				    //print_r($returnResult);
				    if($res){				    	
					    $logger->trace("Bill No ".$docsData['bill_no']." \tBill Id".$docsData['_id']." \tStore".$docsData['store_id']." \tBill Time".$docsData['bill_time']." \tMySQL ID".$insertId);
				        $successful = 1;
				    }else {
				    	$unsuccessful = 1;
				    }
				}
			}else{
				$no_bill = 1;
			}
 		$counter++;}
 	}

	if($successful==1){
		$logger->debug("Success: Bill Uplaoded Successfully");
  		
  		$html['error'] = false;
		$html['update'] = true;
		$html['msg'] = "$counter Bill Uplaoded Successfully";
    } else if($unsuccessful==1){

    	$logger->debug("ERROR: Some Error! Please Contact Admin");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = 'Some Error Please Contact Admin';

    }else{

    	$logger->debug("ERROR: NO Bill To Be Upload");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = 'Sorry! No Bill To Be Upload';
    }
	
$result = json_encode($html,true);
$logger->debug("End OF Uplaod Bill Function");
return $result;
}

/* Function To Upload Updated Bill On CPOS*/

function uploadUpdatedBill(){
	global $logger, $db;
	$logger->debug("Calling Upload Updated Bill Function");
	$couch = new CouchPHP();
	$html = array();
    $counter = $unsuccessful = $successful = 0;
	$billData = $couch->getDesign('design_ho')->getView('handle_updated_bills')->setParam(array('descending'=>'true'))->execute();
	//print_r($billData);
 	if(array_key_exists('rows', $billData)){
 		$billList = array();
 		foreach($billData['rows'] as $key => $value){
 			$data = $value['value'];
 			$bill_no = $value['key'][1];
 			if(!array_key_exists($bill_no, $billList)){
	 			$billList[$bill_no] = '';
 				if($data['mysql'] == 0){
	 				$updateBill = "update cp_pos_storeorders set bill_status = '".$data['bill_status']."', cancel_reason = '".$data['cancel_reason']."', reprint = '".$data['reprint']."' where _id = '".$data['parent']."'";
	 				$logger->trace("Update Query: ".$updateBill);
	 				$db->db_query($updateBill);	
	 				if($db->db_affected_rows() > 0){
	 					$select = "SELECT id from cp_pos_storeorders where _id = '".$data['parent']."'";
	 					$selectData = $db->func_query($select);
	 					$returnResult = $couch->getDesign('design_ho')->getUpdate('insert_mysql_id', $value['id'])->setParam(array('mysql_id'=>$selectData[0]['id']))->execute();				
	 					$successful = 1;
	 				} else {
	 					$unsuccessful = 1;
	 				}
	 			}
 				
           }

		$counter++;}
	}
if($successful==1){
		$logger->debug("Success: Bill Updated Successfully");
  		
  		$html['error'] = false;
		$html['update'] = true;
		$html['msg'] = "$counter Bill Updated Successfully";
    } else if($unsuccessful==1){

    	$logger->debug("ERROR: Some Error! Please Contact Admin");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = 'Some Error Please Contact Admin';

    }else{

    	$logger->debug("ERROR: NO Bill To Be Upload");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = 'Sorry! No Bill To Be Updated';
    }
	
$result = json_encode($html,true);
$logger->debug("End OF Uplaod Bill Function");
return $result;

}
