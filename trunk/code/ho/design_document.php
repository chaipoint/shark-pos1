<?php 
include_once 'common/connection.php' ;
	require_once 'common/couchdb.phpclass.php';
	require_once 'common/logger.php';
	require_once 'constant.php';
	
$param = $_GET['param'];
echo $param;
switch ($param){
	case 'init':
		echo init();
		break;

	case 'deleteDoc' :
		echo deleteDoc();
		break;
		
	case 'cardSale':
		echo cardSale();
		break;
}

function deleteDoc(){
	$couch = new CouchPHP();
	$deleteArray = array();
	$result = $couch->getDesign('billing')->getView('handle_updated_bills')->setParam(array("startkey" => '["2015-02-01"]',"endkey" => '["2015-02-28",{},{},{}]'))->execute();
	$i=0;
	echo '<pre>';print_r($result);echo '</pre>';die();
	foreach($result['rows'] as $key => $value){
		$deleteArray[$i]["_id"] = $value['id'];
		$deleteArray[$i]["_rev"] = $value['value'];
		$deleteArray[$i]["_deleted"] = true;
		$i++;
	}
	//$res = $couch->saveDocument(true)->execute(array("docs"=>$deleteArray));
	//echo'<pre>'; print_r($deleteArray); echo'</pre>';
		
}
