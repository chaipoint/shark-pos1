<?php
	require_once 'common/couchdb.phpclass.php';

	mysql_connect('localhost','root','root');
	mysql_select_db('cabbeein_cpos');


	$couch = new CouchPHP();
	$result = json_decode($couch->getDesign('billing')->getView('bill_by_no_mid')->execute(),true);
	echo "<pre>";
	//print_r($result);
	$docsData = array();
	if(array_key_exists('rows', $result)){
		$docs = $result['rows'];
		$doc_idList = array();
		$itemList = array(); 
		foreach($docs as $dKey => $dValue){
			//print_r($dValue['value']);
			$doc_idList[$dValue['value']['_id']] = '';
			$docsData[] = " ('".$dValue['value']['bill_no']."','".
	        				$dValue['value']['bill_time']."','".
	        				$dValue['value']['store']."','".
	        				'1'."','".
	        $dValue['value']['billed_by']."','".
	        '1'."','".
	        $dValue['value']['payment_type']."','".
	        '123'."','".
	        $dValue['value']['total_qty']."','".
	        $dValue['value']['total_amount']."','".
	        '2'."','". '200'."','". '1000'."','".'Y'."','".'N'."','".'200'."','".'active'."','".
	        '10'."','".'1'."') ";
			$itemList[$dValue['value']['_id']] = $dValue['value']['items'];
		}
	}
	if( count($docsData) >0 ){
		$insertQuery = 'insert into cp_pos_storeorders (bill_no,bill_datetime,store_id,counter_id,employee_id,channel_id,paymentmethod,card_no,total_products,total_amount,total_discount,total_tax,net_amount,cash_payment,card_payment,card_balancebeforepayment,status,reason_code,reprint_count) 
		values '. implode(',',$docsData);
		$result = mysql_query($insertQuery);
		$lastInsertID = mysql_insert_id();
		$productsArray = array();
		foreach($itemList as $lKey => $lValue){
			$doc_idList[$lKey] = $lastInsertID;
			foreach($lValue as $pKey => $pValue){
				$productsArray[] = "(".$lastInsertID.",".$pValue['p_id'].",".$pValue['qty'].",".$pValue['price'].",".$pValue['total_amount'].")";
			}
			print_r($couch->getDesign('billing')->getUpdate('insert_mysql_id',$lKey,'mysql_id='.$lastInsertID)->execute(array('mysql_id'=>$lastInsertID)));
			$lastInsertID++;
		}
		$insertProducst = 'insert into cp_pos_storeorders_products (order_id, product_id, qty, product_cost, cost) values '.implode(',',$productsArray);
		$insertProducstResult = mysql_query($insertProducst);
		//print_r($doc_idList);
	}
/*

 curl -X POST http://127.0.0.1:5984/en/_design/billing/_update/insert_mysql_id/d9f339937a869d61241dde14d2004026?mysql_id=5

*/
	echo "</pre>";
