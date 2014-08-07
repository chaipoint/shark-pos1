<?php
function getStaffData($type){
	$con = mysql_connect('localhost','root','root');
	mysql_select_db('cabbeein_cpos');
	$query = 'SELECT   sm.id mysql_id, sm.name, sm.username, sm.code, sm.password, sm.address, sm.photo, sm.email,sm.phone_1,sm.phone_2,lm.id location_id, lm.name location_name,tm.id title_id, tm.name title_name, sm.active 
			from staff_master sm
			LEFT JOIN  location_master lm 
			ON lm.id = sm.location_id 

			LEFT JOIN  title_master tm 
			ON tm.id = sm.title_id 

			where sm.active = "Y" and tm.active ="Y"';

	$result = mysql_query($query);
	$finalreturn = array();
	$i = 0;
	while($row = mysql_fetch_assoc($result)){
		$finalreturn[$i] = $row;
		$finalreturn[$i]['password'] = md5(base64_decode($row['password']));
		$finalreturn[$i]['cd_doc_type'] = $type;
		$finalreturn[$i]['phone'][] = $finalreturn[$i]['phone_1'];
		$finalreturn[$i]['phone'][] = $finalreturn[$i]['phone_2'];
		unset($finalreturn[$i]['phone_1']);
		unset($finalreturn[$i]['phone_2']);
		$i++;
	}
	mysql_close($con);
	return $finalreturn;
}
echo "<pre>";
	//require_once 'httpapi.php';
	//$couch->saveDocument()->execute(array("docs"=>getStaffData('staff_master')));
	//print_r(curl("http://54.249.247.15:5984/cpos_ho/_bulk_docs",array("docs"=>getTableData('staff_master')),array('contentType'=>'application/json','is_content_type_allowed'=>true)));

echo "</pre>";

/*function getTableData($table,$type){
	$con = mysql_connect('localhost','root','');
	mysql_select_db('cabbeein_cpos');
	$query = "SELECT * from ".$table;
	$result = mysql_query($query);
	$finalreturn = array();
	$i = 0;
	while($row = mysql_fetch_assoc($result)){
		$id = $row['id'];
		unset($row['id']);
		$finalreturn[$i] = $row;
		$finalreturn[$i]['password'] = md5(base64_decode($row['password']));
		$finalreturn[$i]['mysql_id'] = $id;
		$finalreturn[$i]['cd_doc_type'] = $type;
		$i++;
	}
	mysql_close($con);
	return $finalreturn;
}
require_once 'httpapi.php';
/*STAFF_QUERY

SELECT   
sm.id mysql_id, 
sm.name, 
sm.code, 
sm.password, 
sm.address, 
sm.email,
sm.phone_1,
sm.phone_2,
lm.id location_id, 
lm.name location_name,
tm.id title_id, 
tm.name title_name 

from staff_master sm
LEFT JOIN  location_master lm 
ON lm.id = sm.location_id 

LEFT JOIN  title_master tm 
ON tm.id = sm.title_id 

where sm.active = 'Y'

*/


/*
select sm.id mysql_id, sm.name, sm.code, sm.address, sm.phone_1, sm.phone_2, photo, weekly_off, lm.id location_id, lm.name location_name
from store_master sm
LEFT JOIN  location_master lm 
ON lm.id = sm.location_id 


where sm.active = 'Y'

*/

//print_r(curl("http://127.0.0.1:5984/pos/_bulk_docs",array("docs"=>getTableData('staff_master','staff_master')),array('contentType'=>'application/json','is_content_type_allowed'=>true)));
//print_r(getTableData('product_master','product_master'));
/*
function(keys,values){
	var productIDS = new Object();
	forEach(function(v){
		productIDS[v.mysql_id] = v._id;
	});
	return productIDS;
}
function(doc) {
	if(doc.cd_doc_type && doc.cd_doc_type == "product_master"){
	  emit([doc._id, doc.mysql_id], doc);
	}
}
*/



