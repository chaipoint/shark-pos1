<?php
    include_once 'common/connection.php' ;
	require_once 'common/couchdb.phpclass.php';
	require_once 'common/logger.php';

	$couch = new CouchPHP();

    $logger = Logger::getLogger("HO|HO_TO_CPOS");
    $logger->trace("HO to CPOS Transfering");

 	
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
					$db->db_query($insertProducst);	
				    $returnResult = $couch->getDesign('design_ho')->getUpdate('insert_mysql_id', $docsData['_id'])->setParam(array('mysql_id'=>$insertId))->execute();				
				    if(!array_key_exists('error', $returnResult)){				    	
					    $logger->trace("Bill No ".$docsData['bill_no']." \tBill Id".$docsData['_id']." \tStore".$docsData['store_id']." \tBill Time".$docsData['bill_time']." \tMySQL ID".$insertId);
				    }
				}
			}else{
				die();
			}
 		}
 	}
