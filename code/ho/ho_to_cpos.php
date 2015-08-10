<?php

    include_once 'common/connection.php' ;
	require_once 'common/couchdb.phpclass.php';
	require_once 'common/logger.php';
	require_once 'constant.php';
    
    $logger = Logger::getLogger("CP-HO|HO-TO-CPOS-API");
    $logger->trace("HO-TO-CPOS-API");
   
    $action = (!empty($_REQUEST['action']) ? $_REQUEST['action'] : $argv[1]);
   
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

		case "uploadLoginHistory":
		echo uploadLoginHistory();
		break;
		
		case "uploadShiftData":
		echo uploadShiftData();
		break;
		
		case "uploadCardSale":
		echo uploadCardSale();
		break;
}

/* Function To Upload Shift Data On CPOS*/
function uploadShiftData(){
	global $logger, $db;
	$logger->debug("Shift Data From HO to CPOS");
	$couch = new CouchPHP();
	$shift_data = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_STORE_SHIFT)->setParam(array('startkey' => '"'.date("Y-m-d",(time()-(24*60*60))).'"', 'endkey' => '"'.date('Y-m-d').'"','include_docs'=>'true'))->execute();//,'key'=>'"'.date('Y-m-d').'"'
	$logger->debug("URL to sccess data ".$couch->getLastUrl());
	
	if(array_key_exists('rows', $shift_data) && count($shift_data['rows']) > 0){
		$selectFromDB = "SELECT id, if(end_time is null or end_time = '0000-00-00 00:00:00', 'N', 'Y') is_day_ended, _id, _rev, last_shift 
						 FROM cp_pos_day_data 
						 WHERE date(start_time) = curdate() or date(start_time) = date(curdate()-1)";
		$logger->debug("Query To get 2 days data  ".$selectFromDB);
		$resultDay = $db->func_query($selectFromDB);
		$dbList = array();
		if($resultDay){
			foreach($resultDay as $dKey => $dValue){
				$dbList[$dValue['_id']]['_rev'] = $dValue['_rev'];
				$dbList[$dValue['_id']]['_last_shift'] = $dValue['_rev'];
				$dbList[$dValue['_id']]['is_day_ended'] = $dValue['is_day_ended'];
				$dbList[$dValue['_id']]['id'] = $dValue['id'];
			}
		}

		$rows = $shift_data['rows'];
		foreach($rows as $key => $value){ 
			if(array_key_exists($value['id'], $dbList)){ 
				if($dbList[$value['id']]['_rev'] !== $value['doc']['_rev']){ 
					$updateQuery = "UPDATE cp_pos_day_data 
									SET end_time = '".$value['doc']['day']['end_time']."', 
									end_staff_id = '".$value['doc']['day']['end_login_id']."',
									end_full_cash = '".$value['doc']['day']['end_fullcash']."', 
									opening_petty_cash = '".$value['doc']['day']['petty_cash_balance']['opening_petty_cash']."', 
									petty_expense = '".$value['doc']['day']['petty_cash_balance']['petty_expense']."', 
									closing_petty_cash = '".$value['doc']['day']['petty_cash_balance']['closing_petty_cash']."', 
									inward_petty_cash = '".$value['doc']['day']['petty_cash_balance']['inward_petty_cash']."', last_shift  = '".count($value['doc']['shift'])."'
									where _id = '".$value['id']."'";
								
					$db->db_query($updateQuery);
					$logger->debug("Day Id Updated  ".$value['id']." on ".$value['id']." with total shifts ".count($value['doc']['shift']));
					$reconciliationInsert = array();
					$getDayId = "SELECT id FROM cp_pos_day_data WHERE _id = '".$value['id']."'";
					$resultDayId = $db->func_query($getDayId);
					$dayId = $resultDayId[0]['id'];
					$deleteCashReconciliation = "DELETE FROM cp_pos_cash_reconciliation WHERE day_id = '".$dayId."'";
					$logger->debug("Delete Cash Reconciliation WHERE day id :  ".$dayId);
					$db->db_query($deleteCashReconciliation);
					if(count($value['doc']['day']['cash_reconciliation'])>0){
						for ($i=1;$i<=count($value['doc']['shift']);$i++) {
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$dayId.",'shift_".$i."_excess_cash','".$value['doc']['day']['cash_reconciliation']["shift_".$i."_excess_cash"]."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('cash', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$dayId.",'cash','".$value['doc']['day']['cash_reconciliation']['cash']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('caw', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$dayId.",'caw','".$value['doc']['day']['cash_reconciliation']['caw']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('ppa', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$dayId.",'ppa','".$value['doc']['day']['cash_reconciliation']['ppa']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('ppc', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$dayId.",'ppc','".$value['doc']['day']['cash_reconciliation']['ppc']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('credit', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$dayId.",'credit','".$value['doc']['day']['cash_reconciliation']['credit']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('ppcLoad', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$dayId.",'ppcLoad','".$value['doc']['day']['cash_reconciliation']['ppcLoad']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('ppaLoad', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$dayId.",'ppaLoad','".$value['doc']['day']['cash_reconciliation']['ppaLoad']."','".$value['doc']['login_time']."','Y')";
						}
						$insertReconciliation = "INSERT INTO cp_pos_cash_reconciliation(store_id, day_id, head, amount, created_date, active) values ".implode(",", $reconciliationInsert); 
						$db->db_query($insertReconciliation);
					}
					
					$selectShiftData = "SELECT id, shift_no FROM cp_pos_shift_data WHERE pos_day_id = ".$dbList[$value['id']]['id'];
					$resultSelectData = $db->func_query($selectShiftData);
					$shiftList = array();
					foreach($resultSelectData as $sKey => $sValue){
						$shiftList[$sValue['shift_no']] = $sValue['id'];
					}
					if(count($shiftList)>0){
						foreach($value['doc']['shift'] as $shKey => $shValue){
							if(array_key_exists($shValue['shift_no'], $shiftList)){ 
								$upsQuery = "UPDATE cp_pos_shift_data 
											 SET end_time = '".$shValue['end_time']."', 
											 end_petty_cash = '".$shValue['end_petty_cash']."', 
											 opening_cash_inbox = '".$shValue['opening_cash_inbox']."',
											 end_cash_inbox = '".$shValue['end_cash_inbox']."',
											 opening_petty_cash='".$shValue['petty_cash_balance']['opening_petty_cash']."', 
											 petty_expense='".$shValue['petty_cash_balance']['petty_expense']."',
											 closing_petty_cash='".$shValue['petty_cash_balance']['closing_petty_cash']."',
											 inward_petty_cash='".$shValue['petty_cash_balance']['inward_petty_cash']."',
											 cash_denomination= '10:".$shValue['cash_denomination']['qty_10'].",20:".$shValue['cash_denomination']['qty_20'].",50:".$shValue['cash_denomination']['qty_50'].",100:".$shValue['cash_denomination']['qty_100'].",500:".$shValue['cash_denomination']['qty_500'].",sodex:".$shValue['cash_denomination']['qty_sodex'].",ticket_restaurent:".$shValue['cash_denomination']['qty_ticket_res']."'
											 WHERE id=".$shiftList[$shValue['shift_no']];
								$db->db_query($upsQuery);
							}else{
								$insertQuery = "INSERT INTO cp_pos_shift_data (pos_day_id, start_time, end_time, staff_id, end_petty_cash, opening_cash_inbox, end_cash_inbox, counter_no, shift_no, opening_petty_cash, petty_expense, closing_petty_cash, inward_petty_cash, cash_denomination) values ";
								$insertQuery .= "('".$dbList[$value['id']]['id']."','".$shValue['start_time']."','".$shValue['end_time']."','".$shValue['start_login_id']."','".$shValue['end_petty_cash']."','".$shValue['opening_cash_inbox']."','".$shValue['end_cash_inbox']."','".$shValue['counter_no']."','".$shValue['shift_no']."','".$shValue['petty_cash_balance']['opening_petty_cash']."', '".$shValue['petty_cash_balance']['petty_expense']."','".$shValue['petty_cash_balance']['closing_petty_cash']."','".$shValue['petty_cash_balance']['inward_petty_cash']."','10:".$shValue['cash_denomination']['qty_10'].",20:".$shValue['cash_denomination']['qty_20'].",50:".$shValue['cash_denomination']['qty_50'].",100:".$shValue['cash_denomination']['qty_100'].",500:".$shValue['cash_denomination']['qty_500'].",sodex:".$shValue['cash_denomination']['qty_sodex'].",ticket_restaurent:".$shValue['cash_denomination']['qty_ticket_res']."')";								
								//echo $insertQuery;
								$db->db_query($insertQuery);
								
							}
						}
					}
				}
			}else{ 
				$insretArray = array('_id' => $value['doc']['_id'] , 
						'_rev' => $value['doc']['_rev'], 
						'store_id' => $value['doc']['store_id'], 
						'start_time' => $value['doc']['day']['start_time'], 
						'end_time' => $value['doc']['day']['end_time'], 
						'start_staff_id' => $value['doc']['day']['start_login_id'], 
						'end_staff_id' => $value['doc']['day']['end_login_id'], 
						'pos_login_id' => $value['doc']['login_id'], 
						'start_cash' => $value['doc']['day']['start_cash'], 
						'end_full_cash' => $value['doc']['day']['end_fullcash'], 
						'opening_petty_cash' => $value['doc']['day']['petty_cash_balance']['opening_petty_cash'], 
						'petty_expense' => $value['doc']['day']['petty_cash_balance']['petty_expense'],
						'closing_petty_cash' => $value['doc']['day']['petty_cash_balance']['closing_petty_cash'],
						'inward_petty_cash' => $value['doc']['day']['petty_cash_balance']['inward_petty_cash'],
						'last_shift' => count($value['doc']['shift'])
				);
				
				$db->func_array2insert("cp_pos_day_data", $insretArray);
				$insertId = $db->db_insert_id();
				$logger->debug("Day Id Inserted  ".$value['doc']['_id']." on ".$insertId." with total shifts ".count($value['doc']['shift']));
				$shiftInsert = array();
				$reconciliationInsert = array();
				foreach($value['doc']['shift'] as $sKey => $sValue){
						$shiftInsert[] = "('".$insertId."','".$sValue['start_time']."','".$sValue['end_time']."','".$sValue['start_login_id']."','".$sValue['end_petty_cash']."','".$sValue['opening_cash_inbox']."','".$sValue['end_cash_inbox']."','".$sValue['counter_no']."','".$sValue['shift_no']."','".$sValue['petty_cash_balance']['opening_petty_cash']."', '".$sValue['petty_cash_balance']['petty_expense']."','".$sValue['petty_cash_balance']['closing_petty_cash']."','".$sValue['petty_cash_balance']['inward_petty_cash']."','10:".$sValue['cash_denomination']['qty_10'].",20:".$sValue['cash_denomination']['qty_20'].",50:".$sValue['cash_denomination']['qty_50'].",100:".$sValue['cash_denomination']['qty_100'].",500:".$sValue['cash_denomination']['qty_500'].",sodex:".$sValue['cash_denomination']['qty_sodex'].",ticket_restaurent:".$sValue['cash_denomination']['qty_ticket_res']."')";
				}
				if(count($shiftInsert)>0){
					$insertQuery = "INSERT INTO cp_pos_shift_data (pos_day_id, start_time, end_time, staff_id, end_petty_cash, opening_cash_inbox ,end_cash_inbox, counter_no, shift_no, opening_petty_cash, petty_expense, closing_petty_cash, inward_petty_cash, cash_denomination) values ".implode(",", $shiftInsert);
					$db->db_query($insertQuery);
					if(count($value['doc']['day']['cash_reconciliation'])>0){
						for ($i=1;$i<=count($shiftInsert);$i++) {
							$reconciliationInsert[] = "('".$value['doc']['store_id']."','".$insertId."','shift_".$i."_excess_cash','".$value['doc']['day']['cash_reconciliation']["shift_".$i."_excess_cash"]."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('cash', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$insertId.",'cash','".$value['doc']['day']['cash_reconciliation']['cash']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('caw', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$insertId.",'caw','".$value['doc']['day']['cash_reconciliation']['caw']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('ppa', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$insertId.",'ppa','".$value['doc']['day']['cash_reconciliation']['ppa']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('ppc', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$insertId.",'ppc','".$value['doc']['day']['cash_reconciliation']['ppc']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('credit', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$insertId.",'credit','".$value['doc']['day']['cash_reconciliation']['credit']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('ppcLoad', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$insertId.",'ppcLoad','".$value['doc']['day']['cash_reconciliation']['ppcLoad']."','".$value['doc']['login_time']."','Y')";
						}
						if(array_key_exists('ppaLoad', $value['doc']['day']['cash_reconciliation'])){
							$reconciliationInsert[] = "('".$value['doc']['store_id']."',".$insertId.",'ppaLoad','".$value['doc']['day']['cash_reconciliation']['ppaLoad']."','".$value['doc']['login_time']."','Y')";
						}
						$insertReconciliation = "INSERT INTO cp_pos_cash_reconciliation(store_id, day_id, head, amount, created_date, active) values ".implode(",", $reconciliationInsert); 
						$db->db_query($insertReconciliation);
					}
				}		
			}
		}

	}else{
		$logger->debug("ERROR: NO Data To Be Upload");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = NO_DATA_AVAILABLE_ERROR;
		$result = json_encode($html,true);
		$logger->debug("End Of Uplaod Shift Data Function");
		return $result;
	}

		$logger->debug("SUCCESS: Data Uploaded Successfully");
  		$html['error'] = false;
		$html['update'] = true;
		$html['msg'] = DATA_UPLOADED;
		$result = json_encode($html,true);
		$logger->debug("End Of Uplaod Shift Data Function");
		return $result;
}
/* Function To Upload CARD SALE On CPOS*/
function uploadCardSale(){
	global $logger, $db;
	$couch = new CouchPHP();
	$html = array();
	$no_bill = $unsuccessful = $successful = $counter = 0;
	$billData = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_CARD_NO_MYSQL_ID)->setParam(array('include_docs'=>'true'))->execute();
	//echo '<pre>'; print_r($billData); echo '</pre>'; die();
	$logger->debug("URL to sccess data ".$couch->getLastUrl());
	if(array_key_exists('rows', $billData)){ 
 		foreach($billData['rows'] as $key => $value){ 
			$doc = $value['doc'];
 			$docKey = $value['key'];
 			$dValue['doc'] = $doc;
			if(empty($doc['mysql_id'])){
			$docsData = array(	"_id"  => $doc['_id'],
								"_rev" => $doc['_rev'],
								"bill_no" => $doc['invoice_number'],
								"bill_seq" => $doc['bill'],
								"bill_time" => $doc['time'],
								"store_id" => $doc['store_id'],
								"store_name" => $doc['store_name'],
								"counter" => $doc['counter'],
								"shift" => $doc['shift'],
								"staff_id" => $doc['staff_id'],
								"staff_name" => $doc['staff_name'],
								"card_no" => $doc['card_no'],
								"card_type" => $doc['card_type'],
								"txn_type" => $doc['txn_type'], 
								"txn_no" => $doc['txn_no'], 
								"amount" => $doc['amount'],
								"approval_code" => $doc['approval_code'],
								"status" => $doc['status']
							);
			//print_r($docsData);
			$logger->debug("INSERT ORDER ARRAY ".json_encode($docsData));
			$db->func_array2insert("cp_pos_cardsale", $docsData);
			$insertId = $db->db_insert_id();	
			if($insertId > 0){
				$returnResult = $couch->getDesign('design_ho')->getUpdate('insert_mysql_id', $docsData['_id'])->setParam(array('mysql_id'=>$insertId))->execute();				
				if($returnResult){				    	
					$successful = 1;
				}else{
				    $unsuccessful = 1;
				}
			}
		}else{
			$no_bill = 1;
		}
 	$counter++;}}
	
	if($successful==1){
		$logger->debug("Success: Bill Uplaoded Successfully");
  		
  		$html['error'] = false;
		$html['update'] = true;
		$html['msg'] = "$counter Bill ".DATA_UPLOADED."";
    } else if($unsuccessful==1){

    	$logger->debug("ERROR: Some Error! Please Contact Admin");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = ERROR;

    }else{

    	$logger->debug("ERROR: NO Bill To Be Upload");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = NO_DATA_AVAILABLE_ERROR;
    }
	
	$result = json_encode($html,true);
	$logger->debug("End OF Uplaod Bill Function");
	return $result;
 }
	



/* Function To Upload Bill On CPOS*/
function uploadBill(){
	
	global $logger, $db;
	$logger->debug("Calling Upload Bill Function");
	$couch = new CouchPHP();
	$html = array();
	$no_bill = $unsuccessful = $successful = $counter = 0;
	$billData = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_NO_MYSQL_ID)->setParam(array('include_docs'=>'true','limit'=>'10'))->execute();
	$logger->debug("URL to sccess data ".$couch->getLastUrl());

 	if(array_key_exists('rows', $billData)){ 
 		foreach($billData['rows'] as $key => $value){ 
			$doc = $value['doc'];
 			$docKey = $value['key'];
 			$dValue['doc'] = $doc;
			if(empty($doc['mysql_id'])){
			$docsData = array(	"_id"  => $doc['_id'],
								"_rev" => $doc['_rev'],
								"bill_no" => $doc['bill_no'],
								"bill_seq" => $doc['bill'],
								"dc_challan" => $doc['customer']['challan_no'],
								"bill_time" => $doc['time']['created'], 
								"store_id" => $doc['store_id'], 
								"store_name" => $doc['store_name'], 
								"counter" => $doc['counter'], 
								"staff_id" => $doc['staff_id'], 
								"staff_name" => $doc['staff_name'],
								"customer_name" => $doc['customer']['name'],
								"customer_type" => $doc['customer']['type'],
								"customer_city" => $doc['customer']['city'],
								"customer_locality" => $doc['customer']['locality'],
								"customer_sublocality" => $doc['customer']['sub_locality'],
								"customer_landmark" => $doc['customer']['land_mark'],
								"customer_phone" => $doc['customer']['phone_no'],
								"customer_company" => $doc['customer']['company_name'],
								"card_type" => $doc['card']['type'],
								"card_company" => $doc['card']['company'],
								"card_redeem_amount" => $doc['card']['redeem_amount'],
								"card_txn_no" => $doc['card']['txn_no'],
								"card_no" => $doc['card']['no'],
								"card_balance" => $doc['card']['balance'],
								"location_id" => $doc['location_id'],
								"location_name" => $doc['location_name'],
								"is_updated" => $doc['is_updated'],
								"booking_channel" => $doc['booking_channel_name'],
								"delivery_channel" => $doc['delivery_channel_name'],
								"is_cod" => $doc['is_cod'],
								"is_prepaid" => $doc['is_prepaid'],
								"is_credit" => $doc['is_credit'],
								"payment_type" => $doc['payment_type'],
			            		"order_no" => $doc['order_no'],
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
			$logger->debug("INSERT ORDER ARRAY ".json_encode($docsData));
			$db->func_array2insert("cp_pos_storeorders", $docsData);
			$insertId = $db->db_insert_id();	
			$productsArray = array();
			if($insertId > 0){
				foreach ($doc['items'] as $itemKey => $itemVvalue) { 
					$pValue = $itemVvalue;
					$priceAD = (!empty($pValue['discount_price']) ? $pValue['discount_price'] : $pValue['priceAD']);
					$priceBT = (!empty($pValue['priceBT']) ? $pValue['priceBT'] : $pValue['ABP']);
					$productsArray[] = "('".$insertId."','".$doc['time']['created']."','".$doc['store_id']."','".$doc['store_name']."','".$pValue['id']."','".$pValue['name']."','".$pValue['category_id']."','".$pValue['category_name']."', '".$pValue['recipe_id']."','".$pValue['qty']."','".$pValue['price']."','".$pValue['tax']."','".$pValue['serviceTax']."','".$pValue['serviceTaxAmount']."','".$priceBT."','".$pValue['discount']."','".$pValue['discountAmount']."','".$pValue['taxAbleAmount']."','".$pValue['taxAmount']."','".$pValue['netAmount']."','".$priceAD."','".$pValue['subTotal']."')";
				}
				$logger->debug("INSERT ORDER PRODUCT ARRAY ".json_encode($productsArray));		
				if(count($productsArray) > 0){
					$insertProducst = 'INSERT INTO cp_pos_storeorders_products (order_id, bill_date, store_id, store_name, product_id, product_name, category_id, category_name, recipe_id, qty, price, tax, service_tax, service_tax_amount, priceBT, discount, discount_amount, taxable_amount, tax_amount, net_amount, priceAD, subTotal) values '.implode(',',$productsArray);
					$res = $db->db_query($insertProducst);	
				    $returnResult = $couch->getDesign('design_ho')->getUpdate('insert_mysql_id', $docsData['_id'])->setParam(array('mysql_id'=>$insertId))->execute();				
				   
				    if(array_key_exists('error', $returnResult)){				    	
					    $delete_bill = "DELETE FROM cp_pos_storeorders WHERE id = '".$insertId."' ";
					    $db->db_query($delete_bill);
					    $delete_product = "DELETE FROM cp_pos_storeorders_products WHERE order_id = '".$insertId."' ";
					    $db->db_query($delete_product);
					    $unsuccessful = 1;
				    }else {
				    	
				    	$logger->trace("Bill No ".$docsData['bill_no']." \tBill Id".$docsData['_id']." \tStore".$docsData['store_id']." \tBill Time".$docsData['bill_time']." \tMySQL ID".$insertId);
				        $successful = 1;
				    }
				}
			}else{
				$no_bill = 1;
			}
 		$counter++;}}
 	}

	if($successful==1){
		$logger->debug("Success: Bill Uplaoded Successfully");
  		
  		$html['error'] = false;
		$html['update'] = true;
		$html['msg'] = "$counter Bill ".DATA_UPLOADED."";
    } else if($unsuccessful==1){

    	$logger->debug("ERROR: Some Error! Please Contact Admin");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = ERROR;

    }else{

    	$logger->debug("ERROR: NO Bill To Be Upload");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = NO_DATA_AVAILABLE_ERROR;
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
	$billData = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_HANDLE_UPDATED_BILLS)->setParam(array('descending'=>'true'))->execute();
	$logger->debug("URL TO ACCESS DATA ".$couch->getLastUrl());

 	if(array_key_exists('rows', $billData)){
 		$billList = array();
 		foreach($billData['rows'] as $key => $value){
 			$data = $value['value'];
 			$bill_no = $value['key'][1];
 			if(!array_key_exists($bill_no, $billList)){
	 			$billList[$bill_no] = '';
 				if($data['mysql'] == 0){
	 				$updateBill = "UPDATE cp_pos_storeorders SET 
	 							   bill_status = '".$data['bill_status']."', 
	 							   cancel_reason = '".$data['cancel_reason']."', 
	 							   reprint = '".$data['reprint']."' 
	 							   WHERE _id = '".$data['parent']."'";

	 				$logger->trace("Update Query: ".$updateBill);
	 				$db->db_query($updateBill);	
	 				if($db->db_affected_rows() > 0){
	 					$select = "SELECT id from cp_pos_storeorders where _id = '".$data['parent']."'";
	 					$selectData = $db->func_query($select);
	 					$returnResult = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getUpdate(DESIGN_HO_DESIGN_DOCUMENT_UPDATE_INSERT_MYSQL_ID, $value['id'])->setParam(array('mysql_id'=>$selectData[0]['id']))->execute();				
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
		$html['msg'] = "Bill ".DATA_UPLOADED."";
    } else if($unsuccessful==1){

    	$logger->debug("ERROR: Some Error! Please Contact Admin");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = ERROR;

    }else{

    	$logger->debug("ERROR: NO Bill To Be Upload");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = NO_DATA_AVAILABLE_ERROR;
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
	$expenseData = $couch->getDesign(PETTY_EXPENSE_DESIGN_DOCUMENT)->getView(PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_EXPENSE_NO_MYSQL_ID)->setParam(array('include_docs'=>'true'))->execute();
	$logger->debug("URL TO ACCESS DATA ".$couch->getLastUrl());

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
								"item" => $doc['item'],
								"expense_purpose" => $doc['expense_purpose'],
								"expense_amount" => $doc['expense_amount'],
								"expense_done_by" => $doc['expense_done_by_id'],
								"expense_approved_by" => $doc['expense_approved_by_id'],
								"store_id" => $doc['store_id'],
								"store_name" => $doc['store_name'],
								"shift_no" => $doc['shift_no'],
								"created_date" => date('Y-m-d H:i:s'),
								"created_by" => '' 
							);
			$logger->debug("INSERT PETTY EXPENSE ARRAY ".json_encode($docsData));
			$db->func_array2insert("cp_pos_petty_expense", $docsData);
			$insertId = $db->db_insert_id();
			if($insertId > 0){
				$returnResult = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getUpdate(DESIGN_HO_DESIGN_DOCUMENT_UPDATE_INSERT_MYSQL_ID, $docsData['_id'])->setParam(array('mysql_id'=>$insertId))->execute();
				$counter++;
				$success = 1;
			}else{
				$logger->debug("ERROR: SOME ERROR");
  				$html['error'] = true;
				$html['update'] = false;
				$html['msg'] = ERROR;
				$result = json_encode($html,true);
				$logger->debug("End OF Petty Expense Function");
				return $result;
			}
		}
	}else{
		$logger->debug("ERROR: NO Expense To Be Upload");
  		$result = uploadPettyInward();
		$logger->debug("End OF Petty Expense Function");
		return $result;
	}
	
	if($success==1){
		$logger->debug("Success: Petty Expense Uploaded Successfully");
  		$result = uploadPettyInward();
		$logger->debug("End OF Petty Expense Function");
		return $result;
	}
}


/* Function To Upload Petty Inward On CPOS*/
function uploadPettyInward(){
	global $logger, $db;
	$logger->debug("Calling Upload Petty Inward Function");
	$couch = new CouchPHP();
	$html = array();
	$expenseData = $couch->getDesign(PETTY_EXPENSE_DESIGN_DOCUMENT)->getView(PETTY_EXPENSE_DESIGN_DOCUMENT_VIEW_INWARD_NO_MYSQL_ID)->setParam(array('include_docs'=>'true'))->execute();
	$logger->debug("URL TO ACCESS DATA ".$couch->getLastUrl());

	$success = $counter = 0;
	if(array_key_exists('rows', $expenseData) && count($expenseData['rows'])>0){
 		foreach($expenseData['rows'] as $key => $value){
 			$doc = $value['doc'];
 			$docKey = $value['key'];
 			$dValue['doc'] = $doc;
			$docsData = array(	"_id"  => $doc['_id'],
								"_rev" => $doc['_rev'],
								"inward_date" => $doc['inward_date'].' '.$doc['inward_time'],
								"inward_amount" => $doc['inward_amount'],
								"inward_received_by" => $doc['inward_receive_by_id'],
								"store_id" => $doc['store_id'],
								"store_name" => $doc['store_name'],
								"shift_no" => $doc['shift_no'],
								"created_date" => date('Y-m-d H:i:s'),
								"created_by" => '' 
							);
			$logger->debug("INSERT PETTY INWARD ARRAY ".json_encode($docsData));
			$db->func_array2insert("cp_pos_petty_inward", $docsData);
			$insertId = $db->db_insert_id();
			if($insertId > 0){
				$returnResult = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getUpdate(DESIGN_HO_DESIGN_DOCUMENT_UPDATE_INSERT_MYSQL_ID, $docsData['_id'])->setParam(array('mysql_id'=>$insertId))->execute();
				$counter++;
				$success = 1;
			}else{
				$logger->debug("ERROR: SOME ERROR");
  				$html['error'] = true;
				$html['update'] = false;
				$html['msg'] = ERROR;
				$result = json_encode($html,true);
				$logger->debug("End OF Petty Inward Function");
				return $result;
			}
		}
	}else{
		$logger->debug("ERROR: NO Inward To Be Upload");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = NO_DATA_AVAILABLE_ERROR;
		$result = json_encode($html,true);
		$logger->debug("End OF Petty Inward Function");
		return $result;
	}
	if($success==1){
		$logger->debug("Success: Petty Inward Uploaded Successfully");
  		$html['error'] = false;
		$html['update'] = true;
		$html['msg'] = DATA_UPLOADED;
		$result = json_encode($html,true);
		$logger->debug("End OF Petty Inward Function");
		return $result;
	}
}


/* Function To Upload Login History On CPOS*/
function uploadLoginHistory(){
	global $logger, $db;
	$logger->debug("Calling Upload Login History Function");
	$couch = new CouchPHP();
	$html = array();
	$loginData = $couch->getDesign(LOGIN_DESIGN_DOCUMENT)->getView(LOGIN_DESIGN_DOCUMENT_VIEW_LOGIN_NO_MYSQL_ID)->setParam(array('include_docs'=>'true'))->execute();
	$logger->debug("URL TO ACCESS DATA ".$couch->getLastUrl());

	$success = $counter = 0;
	if(array_key_exists('rows', $loginData) && count($loginData['rows'])>0){
 		foreach($loginData['rows'] as $key => $value){
 			$doc = $value['doc'];
 			$docKey = $value['key'];
 			$dValue['doc'] = $doc;
			$docsData = array(	"_id"  => $doc['_id'],
								"_rev" => $doc['_rev'],
								"staff_id" => $doc['id'],
								"store_id" => $doc['store'],
								"login_time" => $doc['login_time'],
								"logout_time" => $doc['logout_time'],
								"app_version" => $doc['app_version'],
								"created_date" => date('Y-m-d H:i:s'),
								"created_by" => '' 
							);
			$logger->debug("INSERT LOGIN HISTORY ARRAY ".json_encode($docsData));
			$db->func_array2insert("cp_pos_login_history", $docsData);
			$insertId = $db->db_insert_id();
			if($insertId > 0){
				$returnResult = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getUpdate(DESIGN_HO_DESIGN_DOCUMENT_UPDATE_INSERT_MYSQL_ID, $docsData['_id'])->setParam(array('mysql_id'=>$insertId))->execute();
				$counter++;
				$success = 1;
			}else{
				$logger->debug("ERROR: SOME ERROR");
  				$html['error'] = true;
				$html['update'] = false;
				$html['msg'] = ERROR;
				$result = json_encode($html,true);
				$logger->debug("End OF Login History Function");
				return $result;
			}
		}
	}else{
		$logger->debug("ERROR: NO Login History To Be Upload");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = NO_DATA_AVAILABLE_ERROR;
		$result = json_encode($html,true);
		$logger->debug("End OF Login History Function");
		return $result;
	}
	
	if($success==1){
		$logger->debug("Success: Login History Uploaded Successfully");
  		$html['error'] = false;
		$html['update'] = true;
		$html['msg'] = "$counter Login History ".DATA_UPLOADED."";
		$result = json_encode($html,true);
		$logger->debug("End OF Login History Function");
		return $result;
	}
}