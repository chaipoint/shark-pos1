<?php

function getTableData($table,$type){
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
