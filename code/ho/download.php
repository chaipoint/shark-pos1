<?php
	require_once 'constant.php';
    require_once 'common/couchdb.phpclass.php';
	$couch = new CouchPHP(); 
	
	if(!empty($_REQUEST['store'])){
		$date = date('Y-m-d', strtotime($_REQUEST['date']));
		$store = $_REQUEST['store'];
		$csv = '';
		$csv .= 'StoreId, StoreName, BillNo, BillDate, BillTime, ItemName, ItemQty, ItemPrice'. "\r";
		$getRecord = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_BILL_BY_STORE)->setParam(array("include_docs"=>"true","key"=>'["'.$date.'", "'.$store.'"]'))->execute();
		if(array_key_exists('rows', $getRecord)){
			foreach($getRecord['rows'] as $key=>$value){
				$doc = $value['doc'];
				foreach ($doc['items'] as $itemKey => $itemValue) {
					$csv .= ''.$doc['store_id'].' , '.$doc['store_name'].', '.$doc['bill_no'].', '.date('d-M-Y', strtotime($doc['time']['created'])).', '.date('h:i:s', strtotime($doc['time']['created'])).', '.$itemValue['name'].', '.$itemValue['qty'].', '.$itemValue['price'].''. "\r";
				}
			}
		}
		header("cache-control: private");
        header('content-Disposition:attachment;filename=Bill_Wise_Report.csv');
        header('content-type: application/csv,UTF-8');
        header('content-length: ' . strlen($csv));
        header('content-Transfer-Encoding:binary');
        ob_clean();
        flush();
        echo $csv;
		
	}
?>