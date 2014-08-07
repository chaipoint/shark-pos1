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
		$finalreturn[$i]['location']['id'] = $finalreturn[$i]['location_id'];
		$finalreturn[$i]['location']['name'] = $finalreturn[$i]['location_name'];
		$finalreturn[$i]['title']['id'] = $finalreturn[$i]['title_id'];
		$finalreturn[$i]['title']['name'] = $finalreturn[$i]['title_name'];
		unset($finalreturn[$i]['location_id']);
		unset($finalreturn[$i]['location_name']);
		unset($finalreturn[$i]['title_id']);
		unset($finalreturn[$i]['title_name']);
		unset($finalreturn[$i]['phone_1']);
		unset($finalreturn[$i]['phone_2']);
		$i++;
	}
	mysql_close($con);
	return $finalreturn;
}
echo "<pre>";
	//$couch->saveDocument()->execute(array("docs"=>getStaffData('staff_master')));

echo "</pre>";
