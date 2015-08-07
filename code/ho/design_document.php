<?php
	//error_reporting(-1); 
	include_once 'common/connection.php' ;
	require_once 'common/couchdb.phpclass.php';
	require_once 'common/logger.php';
	require_once 'constant.php';
	

echo 'here';
echo deleteDoc1();
error_reporting(-1); 
//echo init();
function deleteDoc1(){ echo 'eneter';
//global $logger, $db;
error_reporting(-1);
	require_once 'common/couchdb.phpclass.php';
	$couch = new CouchPHP();
	$deleteArray = array();
	echo $couch;
	//$result = $couch->getDesign('billing')->getView('handle_updated_bills')->setParam(array("startkey" => '["2015-03-01"]',"endkey" => '["2015-03-31",{},{},{}]'))->execute();
	$i=0;
	echo 'ccchchh';
	echo '<pre>';print_r($result);echo '</pre>';
	foreach($result['rows'] as $key => $value){
		$deleteArray[$i]["_id"] = $value['id'];
		$deleteArray[$i]["_rev"] = $value['value'];
		$deleteArray[$i]["_deleted"] = true;
		$i++;
	}
	//$res = $couch->saveDocument(true)->execute(array("docs"=>$deleteArray));
	echo'<pre>'; print_r($deleteArray); echo'</pre>';
		
}
