<?php
	require_once 'common/couchdb.phpclass.php';

	mysql_connect('localhost','root','root') or die('Unable to connect');
	mysql_select_db('cabbeein_cpos') or die('Unable to Select');

	$couch = new CouchPHP();
	$result = $couch->getDesign('billing')->getView('bill_by_no_mid')->setParam(array('include_docs'=>'true'))->execute();
	echo "<pre>";
	$docsData = array();
	if(array_key_exists('rows', $result)){
		$docs = $result['rows'];
		$doc_idList = array();
		$itemList = array(); 
		foreach($docs as $dKey => $dValue){
			//print_r($dValue['value']);
			$doc_idList[$dValue['doc']['_id']] = '';
			$docsData[] = " ('".$dValue['doc']['bill_no']."','".
	        				$dValue['doc']['bill_time']."','".
	        				$dValue['doc']['store_id']."','".
	        				'1'."','".
	        $dValue['doc']['staff_id']."','".
	        '1'."','".
	        $dValue['doc']['payment_type']."','".
	        '123'."','".
	        $dValue['doc']['total_qty']."','".
	        $dValue['doc']['total_amount']."','".
	        '2'."','". '200'."','". '1000'."','".'Y'."','".'N'."','".'200'."','".'active'."','".
	        '10'."','".'1'."') ";
			$itemList[$dValue['doc']['_id']] = $dValue['doc']['items'];
		}
	}
	if( count($docsData) >0 ){
		//Insert and get ID of first Inserted Element
		$insertQuery = 'insert into cp_pos_storeorders (bill_no,bill_datetime,store_id,counter_id,employee_id,channel_id,paymentmethod,card_no,total_products,total_amount,total_discount,total_tax,net_amount,cash_payment,card_payment,card_balancebeforepayment,status,reason_code,reprint_count) 
		values '. implode(',',$docsData);
		$result = mysql_query($insertQuery);
		$lastInsertID = mysql_insert_id();


		$productsArray = array();
		echo $lastInsertID;
		foreach($itemList as $lKey => $lValue){
			$doc_idList[$lKey] = $lastInsertID;
			foreach($lValue as $pKey => $pValue){
				$productsArray[] = "(".$lastInsertID.",".$pValue['id'].",".$pValue['qty'].",".$pValue['price'].",".$pValue['netAmount'].")";
			}
			$couch->getDesign('billing')->getUpdate('insert_mysql_id',$lKey)->setParam(array('mysql_id'=>$lastInsertID))->execute();
			echo "<br/>".$this->getLastUrl();	
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
