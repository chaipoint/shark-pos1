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

		case "uploadPettyExpense":
		echo uploadPettyExpense();
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
								"customer_name" => $doc['customer']['name'],
								"customer_city" => $doc['customer']['city'],
								"customer_locality" => $doc['customer']['locality'],
								"customer_sublocality" => $doc['customer']['sub_locality'],
								"customer_landmark" => $doc['customer']['land_mark'],
								"customer_phone" => $doc['customer']['phone_no'],
								"customer_company" => $doc['customer']['company_name'],
								"location_id" => $doc['location_id'],
								"location_name" => $doc['location_name'],
								"is_updated" => $doc['is_updated'],
								"booking_channel" => $doc['booking_channel_name'],
								"delivery_channel" => $doc['delivery_channel_name'],
								"is_cod" => $doc['is_cod'],
								"is_prepaid" => $doc['is_prepaid'],
								"is_credit" => $doc['is_credit'],
								"payment_type" => $doc['payment_type'],
			            		"card_no" => $doc['card_no'],
			            		"coupon_id" => 1,
			            		"coupon_code" => $doc['coupon_code'],
			            		"shift" => $doc['shift'],
			            		"total_qty" => $doc['total_qty'],
			            		"total_amount" => $doc['total_amount'],
			            		"sub_total" => $doc['sub_total'],
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
		$html['msg'] = "Bill Updated Successfully";
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

/* Function To Upload Petty Expense On CPOS*/
function uploadPettyExpense(){
	global $logger, $db;
	$logger->debug("Calling Upload Petty Expense Function");
	$couch = new CouchPHP();
	$html = array();
	$expenseData = $couch->getDesign('petty_expense')->getView('expense_no_mysql_id')->setParam(array('include_docs'=>'true'))->execute();
	//print_r($expenseData);
	$success = $counter = 0;
	if(array_key_exists('rows', $expenseData) && count($expenseData['rows'])>0){
 		foreach($expenseData['rows'] as $key => $value){
 			$doc = $value['doc'];
 			$docKey = $value['key'];
 			$dValue['doc'] = $doc;
			$docsData = array(	"_id"  => $doc['_id'],
								"_rev" => $doc['_rev'],
								"expense_date" => $doc['expense_date'].' '.$doc['expense_time'],
								"expense_head" => $doc['expense_head'],
								"expense_purpose" => $doc['expense_purpose'],
								"expense_amount" => $doc['expense_amount'],
								"expense_done_by" => $doc['expense_done_by_id'],
								"expense_approved_by" => $doc['expense_approved_by_id'],
								"created_date" => date('Y-m-d H:i:s'),
								"created_by" => '' 
							);
			$db->func_array2insert("cp_pos_petty_expense", $docsData);
			$insertId = $db->db_insert_id();
			if($insertId > 0){
				$returnResult = $couch->getDesign('design_ho')->getUpdate('insert_mysql_id', $docsData['_id'])->setParam(array('mysql_id'=>$insertId))->execute();
				$counter++;
				$success = 1;
			}else{
				$logger->debug("ERROR: SOME ERROR");
  				$html['error'] = true;
				$html['update'] = false;
				$html['msg'] = 'Sorry! Some Error Please Contact Admin';
				$result = json_encode($html,true);
				$logger->debug("End OF Petty Expense Function");
				return $result;
			}
		}
	}else{
		$logger->debug("ERROR: NO Expense To Be Upload");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = 'Sorry! No Expense To Be Upload';
		$result = json_encode($html,true);
		$logger->debug("End OF Petty Expense Function");
		return $result;
	}
	//echo $success;
	if($success==1){
		$logger->debug("Success: Petty Expense Uploaded Successfully");
  		$html['error'] = false;
		$html['update'] = true;
		$html['msg'] = "$counter Petty Expense Uplaoded Successfully";
		$result = json_encode($html,true);
		$logger->debug("End OF Petty Expense Function");
		return $result;
	}
}