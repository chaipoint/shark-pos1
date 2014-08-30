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
}

/* Function To Upload Bill On CPOS*/

function uploadBill(){
	global $logger;
	$logger->debug("Calling Upload Bill Function");

    $couch = new CouchPHP();
	$result = $couch->getDesign('billing')->getView('bill_by_no_mid')->setParam(array('include_docs'=>'true'))->execute();
	

	
	$docsData = array();
	$html = array();
	if(array_key_exists('rows', $result)){
		$docs = $result['rows'];
		$doc_idList = array();
		$itemList = array(); 
		foreach($docs as $dKey => $dValue){
			
			$doc_idList[$dValue['doc']['_id']] = '';
			$docsData[] = " ('".$dValue['doc']['_id']."',
				             '".$dValue['doc']['_rev']."',
				             '".$dValue['doc']['bill_no']."',
				             '".$dValue['doc']['bill_time']."',
				             '".$dValue['doc']['store_id']."',
				             '".$dValue['doc']['store_name']."',
				             '".$dValue['doc']['counter']."',
				             '".$dValue['doc']['staff_id']."',
				             '".$dValue['doc']['staff_name']."',
				             '".$dValue['doc']['location_id']."',
				             '".$dValue['doc']['location_name']."',
				             '".$dValue['doc']['is_updated']."',
				             '".$dValue['doc']['channel']."',
				             '".$dValue['doc']['payment_type']."',
				             '".$dValue['doc']['card_no']."',
				             '".'1'."',
				             '".$dValue['doc']['coupon_code']."',
				             '".$dValue['doc']['shift']."',
				             '".$dValue['doc']['total_qty']."',
				             '".$dValue['doc']['total_amount']."',
				             '".$dValue['doc']['sub_total']."',
				             '".$dValue['doc']['total_discount']."',
                             '".$dValue['doc']['discount']."',
                             '".$dValue['doc']['total_tax']."',
                             '".$dValue['doc']['round_off']."',
                             '".$dValue['doc']['due_amount']."',
                             '".$dValue['doc']['bill_status']."',
                             '".$dValue['doc']['reprint']."') ";

			$itemList[$dValue['doc']['_id']] = $dValue['doc']['items'];
		}
	}
	if( count($docsData) >0 ){
		//Insert and get ID of first Inserted Element
		$insertQuery = 'insert into cp_pos_storeorders(_id,_rev,bill_no,bill_time,store_id,store_name,
			            counter,staff_id,staff_name,location_id,location_name,is_updated,channel,payment_type,
			            card_no,coupon_id,coupon_code,shift,total_qty,total_amount,sub_total,total_discount,discount,
			            total_tax,round_off,due_amount,bill_status,reprint) 
		values '. implode(',',$docsData);
		$logger->trace("Query To Insert Order In cp_pos_storeorders Table: ".($insertQuery));
		$result = mysql_query($insertQuery);
		$lastInsertID = mysql_insert_id();
        
        $productsArray = array();
		foreach($itemList as $lKey => $lValue){
			$doc_idList[$lKey] = $lastInsertID;
			foreach($lValue as $pKey => $pValue){
				$productsArray[] = "('".$lastInsertID."','".$pValue['id']."','".$pValue['name']."','".$pValue['category_id']."','".$pValue['category_name']."','".$pValue['qty']."','".$pValue['price']."','".$pValue['tax']."','".$pValue['priceBT']."','".$pValue['discount']."','".$pValue['discountAmount']."','".$pValue['taxAbleAmount']."','".$pValue['taxAmount']."','".$pValue['totalAmount']."','".$pValue['netAmount']."')";
			}
		    $couch->getDesign('billing')->getUpdate('insert_mysql_id',$lKey)->setParam(array('mysql_id'=>$lastInsertID))->execute();
			//echo "<br/>".$couch->getLastUrl();
			$lastInsertID++;
		}

		$insertProducst = 'insert into cp_pos_storeorders_products (order_id, product_id, product_name, category_id, category_name, qty, price, tax, priceBT, discount, discount_amount, taxable_amount, tax_amount, total_amount, net_amount) values '.implode(',',$productsArray);
		$logger->trace("Query To Insert Order Product In cp_pos_storeorders_products Table: ".($insertProducst)); 
		$insertProducstResult = mysql_query($insertProducst);
		if($insertProducstResult)
		{
		 $logger->debug("Bill Uploaded Successfully In CPOS Database");	
         $html['error'] = false;
	     $html['update'] = true;
	     $html['msg'] = "BILL UPDATED SUCCESSFULLY"; 
		}else{
		$logger->debug("ERROR:Bill Not Uploaded In CPOS Database");
        $html['error'] = true;
	    $html['update'] = false;
	    $html['msg'] = 'Some Error Please Contact Admin';
		}
		//print_r($doc_idList);
	}
	else{
    $logger->debug("No Bill To Upload");
    $html['error'] = true;
	$html['update'] = false;
	$html['msg'] = 'Sorry No Bill To Be Upload';
	}
$result = json_encode($html,true);
$logger->debug("End OF Uplaod Bill Function");
return $result;
}
