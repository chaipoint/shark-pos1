<?php
	require_once 'constant.php';
    require_once 'common/couchdb.phpclass.php';
	$couch = new CouchPHP(); 
	
	if(!empty($_REQUEST['date1'])){
		set_time_limit(0);
        ini_set('memory_limit','1024M');
		error_reporting(E_ALL);
		$date1 = date('Y-m-d', strtotime($_REQUEST['date1']));
		//$date2 = date('Y-m-d', strtotime($_REQUEST['date2']));
		//$store = $_REQUEST['store'];
		$csv = '';
		$csv .= 'StoreId, StoreName, BillNo, NewBillNo, BillDate, BillTime, ItemName, ItemQty, ItemPrice, SaleValue'. "\r";
		$getRecord = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_BILL_BY_STORE)->setParam(array("include_docs"=>"true","startkey"=>'["'.$date1.'"]', "endkey"=>'["'.$date1.'", {}]'))->execute();
		if(array_key_exists('rows', $getRecord)){
			foreach($getRecord['rows'] as $key=>$value){
				$doc = $value['doc'];
				//if($doc['bill_status']!='Cancelled'){
					foreach ($doc['items'] as $itemKey => $itemValue) {
						$csv .= ''.$doc['store_id'].' , '.$doc['store_name'].', '.$doc['bill_no'].' ,'.$doc['bill'].', '.date('d-M-Y', strtotime($doc['time']['created'])).', '.date('h:i:s', strtotime($doc['time']['created'])).', '.$itemValue['name'].', '.$itemValue['qty'].', '.$itemValue['price'].', '.($itemValue['netAmount']).''. "\r";
					}
				//}
			}
		}
		header("cache-control: private");
        header('content-Disposition:attachment;filename=Bill_Wise_Report:'.$date1.'.csv');
        header('content-type: application/csv,UTF-8');
        header('content-length: ' . strlen($csv));
        header('content-Transfer-Encoding:binary');
        ob_clean();
        flush();
        echo $csv;
		
	}
?>