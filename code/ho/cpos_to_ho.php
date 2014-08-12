<?php
    include_once 'common/connection.php' ;
	require_once 'common/couchdb.phpclass.php';
    $action = @$_REQUEST['action'];

switch ($action){
        
        case "updateStaff":
		echo updateStaff();
		break;

		case "updateStore":
		echo updateStore();
		break;

		case "updateConfig":
		echo updateConfig();
		break;


}
/* Function To Update Staff Data */

function updateStaff(){
	$couch = new CouchPHP();
	$result = $couch->getDesign('staff')->getView('staff_mysql_id')->setParam(array("include_docs"=>"true"))->execute();
	
	$itemList = array();

	if(array_key_exists('rows', $result)){
		$docs = $result['rows'];
		foreach($docs as $dKey => $dValue){
	    $itemList[$dValue['doc']['mysql_id']] = $dValue['doc'];	
	  }
	}
	
 $getStaff = 'SELECT   sm.id mysql_id, sm.name, sm.username, sm.code,
	          sm.password, sm.address, sm.photo, sm.email,sm.phone_1,
	          sm.phone_2,lm.id location_id, lm.name location_name,tm.id title_id,
	          tm.name title_name, sm.active 
			  from staff_master sm
			  LEFT JOIN  location_master lm ON lm.id = sm.location_id 
              LEFT JOIN  title_master tm ON tm.id = sm.title_id AND tm.active ="Y" 
              where sm.active = "Y"';
    $result = mysql_query($getStaff);
	$updateArray = array();
	$html = array();
	$i = 0;
	$updateCounter = 0;
	$insertCounter = 0;
	while($row = mysql_fetch_assoc($result)){
	  $updateArray[$i] = $row;
	  if(array_key_exists($row['mysql_id'],$itemList)){
      $updateArray[$i]['_id'] = $itemList[$row['mysql_id']]['_id'];
      $updateArray[$i]['_rev'] = $itemList[$row['mysql_id']]['_rev'];
      $updateCounter++;
      }
      
      $updateArray[$i]['cd_doc_type'] = 'staff_master';
      //$updateArray[$i]['password'] = md5(base64_decode($row['password']));
      $updateArray[$i]['password'] = md5(123);
      
      $updateArray[$i]['phone'][] = $updateArray[$i]['phone_1'];
	  $updateArray[$i]['phone'][] = $updateArray[$i]['phone_2'];
	//  $updateArray[$i]['location']['id'] = $updateArray[$i]['location_id'];
	 // $updateArray[$i]['location']['name'] = $updateArray[$i]['location_name'];
	  $updateArray[$i]['title']['id'] = $updateArray[$i]['title_id'];
	  $updateArray[$i]['title']['name'] = $updateArray[$i]['title_name'];
	 // unset($updateArray[$i]['location_id']);
	  //unset($updateArray[$i]['location_name']);
	  unset($updateArray[$i]['title_id']);
	  unset($updateArray[$i]['title_name']);
	  unset($updateArray[$i]['phone_1']);
	  unset($updateArray[$i]['phone_2']);
	  $i++;
	  $insertCounter++;
	}

if (is_array($updateArray) && count($updateArray)>0){
 $result=$couch->saveDocument(true)->execute(array("docs"=>$updateArray));

  if(array_key_exists('error', $result)){
  	$html['error'] = true;
	$html['update'] = false;
	$html['msg'] = 'Some Error Please Contact Admin';
    
  }
  else{
  	$html['error'] = false;
	$html['update'] = true;
	$html['msg'] = "$updateCounter RECORD UPDATED SUCCESSFULLY";
  }
}
$result = json_encode($html,true);
return $result;
}

/* Function To Update Stre Data */

function updateStore(){
    $couch = new CouchPHP();
	$result = $couch->getDesign('store')->getView('store_mysql_id')->setParam(array("include_docs"=>"true"))->execute();
	$storeList = array();

    if(array_key_exists('rows', $result)){
		$docs = $result['rows'];
		 
		foreach($docs as $dKey => $dValue){
	$storeList[$dValue['doc']['mysql_id']] = $dValue['doc'];	
	  }
	}

	$getStoreNameQuery = "select sm.id mysql_id, sm.name, sm.code,
	                      sm.type, sm.address, sm.phone_1, sm.phone_2, photo,
	                      weekly_off, lm.id location_id, lm.name location_name,
	                      sm.sms, sm.foe_allowed is_foe, sm.active, sm.store_time store_open_schedule
                          from store_master sm
                          LEFT JOIN  location_master lm ON lm.id = sm.location_id 
                          where sm.active = 'Y'";
						
						$storeResult = mysql_query($getStoreNameQuery);
						$updateArray = array();
	                    $html = array();
						$i = 0;
						$updateCounter = 0;
						$insertCounter = 0;
					    while($row = mysql_fetch_assoc($storeResult)){
                            $storeDetails = $row;
                            $updateArray[$i] = $storeDetails;
                            
                            if(array_key_exists($row['mysql_id'],$storeList)){
							$updateArray[$i]['_id'] = $storeList[$storeDetails['mysql_id']]['_id'];
                            $updateArray[$i]['_rev'] = $storeList[$storeDetails['mysql_id']]['_rev'];
                            $updateCounter++;
						    }

							$updateArray[$i]['cd_doc_type'] = 'store_master';
							$updateArray[$i]['location']['id'] = $storeDetails['location_id'];
							$updateArray[$i]['location']['name'] = $storeDetails['location_name'];
                            $updateArray[$i]['phone'][] = $updateArray[$i]['phone_1'];
							$updateArray[$i]['phone'][] = $updateArray[$i]['phone_2'];
							unset($updateArray[$i]['phone_1']);
							unset($updateArray[$i]['phone_2']);
							unset($updateArray[$i]['location_id']);
							unset($updateArray[$i]['location_name']);
							unset($updateArray[$i]['address']);

							$selectSchedule = "SELECT * FROM  `cp_store_timings` WHERE store_id =".$storeDetails['mysql_id'];
							$resultSchedule = mysql_query($selectSchedule);
							while($rowSchedule = mysql_fetch_assoc($resultSchedule)){
								if($rowSchedule['type'] == 7){
									$updateArray[$i]['schedule']['opening_time'][0] = $rowSchedule['opening_time'];
									$updateArray[$i]['schedule']['closing_time'][0] = $rowSchedule['closing_time'];
								}elseif($rowSchedule['type'] == 0){
									$updateArray[$i]['schedule']['opening_time'][1] = $rowSchedule['opening_time'];
									$updateArray[$i]['schedule']['closing_time'][1] = $rowSchedule['closing_time'];
									$updateArray[$i]['schedule']['opening_time'][2] = $rowSchedule['opening_time'];
									$updateArray[$i]['schedule']['closing_time'][2] = $rowSchedule['closing_time'];
									$updateArray[$i]['schedule']['opening_time'][3] = $rowSchedule['opening_time'];
									$updateArray[$i]['schedule']['closing_time'][3] = $rowSchedule['closing_time'];
									$updateArray[$i]['schedule']['opening_time'][4] = $rowSchedule['opening_time'];
									$updateArray[$i]['schedule']['closing_time'][4] = $rowSchedule['closing_time'];
									$updateArray[$i]['schedule']['opening_time'][5] = $rowSchedule['opening_time'];
									$updateArray[$i]['schedule']['closing_time'][5] = $rowSchedule['closing_time'];
								}elseif($rowSchedule['type'] == 6){
									$updateArray[$i]['schedule']['opening_time'][6] = $rowSchedule['opening_time'];
									$updateArray[$i]['schedule']['closing_time'][6] = $rowSchedule['closing_time'];
								}
							}
						//	ksort($updateArray[$i]['schedule']['opening_time']);
						//	ksort($updateArray[$i]['schedule']['closing_time']);
							
				    $products = "select pm.id, if(cpsp.display_name = '' or cpsp.display_name is null,
				                 pm.display_name, cpsp.display_name) name, 
				                 cpsp.store_id, pm.code, if(cpsp.price is null, pm.price,if(cpsp.price = 0, 'R' , cpsp.price)) price, 
				                 ctm.name tax, ctm.id tax_id,  crm.name category, crm.id as category_id, ctm.rate tax_rate, 
                                 pm.packaging, pm.is_coc, pm.is_foe, pm.is_web, pm.product_image image
								 from product_master pm
								 LEFT JOIN cp_product_store_price cpsp on cpsp.product_id = pm.id and cpsp.store_id = ".$storeDetails['mysql_id']." and cpsp.active = 'Y'
								LEFT JOIN cp_tax_master ctm on ctm.id = if(cpsp.price is null, pm.tax,cpsp.tax_rate)
								LEFT JOIN cp_reference_master crm on crm.id = pm.type 
								where  pm.active = 'Y'
								order by  pm.id asc";
							
							$productList = mysql_query($products);
							$j = 0;
							while($innerRow = mysql_fetch_assoc($productList)){	
								$productDetails = $innerRow;

										if(is_numeric($productDetails['price'])){
										$updateArray[$i]['menu_items'][$j]['mysql_id'] = $productDetails['id']; 
										$updateArray[$i]['menu_items'][$j]['code'] = $productDetails['code'];
										$updateArray[$i]['menu_items'][$j]['name'] = $productDetails['name'];
										$updateArray[$i]['menu_items'][$j]['price'] = $productDetails['price'];


										$updateArray[$i]['menu_items'][$j]['tax']['id'] = $productDetails['tax_id'];
										$updateArray[$i]['menu_items'][$j]['tax']['name'] = $productDetails['tax'];
										$updateArray[$i]['menu_items'][$j]['tax']['rate'] = $productDetails['tax_rate'];
										
										$updateArray[$i]['menu_items'][$j]['category']['id'] = $productDetails['category_id'];
										$updateArray[$i]['menu_items'][$j]['category']['name'] = $productDetails['category'];
					
										$updateArray[$i]['menu_items'][$j]['packaging'] = $productDetails['packaging'];
										$updateArray[$i]['menu_items'][$j]['is_coc'] = $productDetails['is_coc'];
										$updateArray[$i]['menu_items'][$j]['is_foe'] = $productDetails['is_foe'];
										$updateArray[$i]['menu_items'][$j]['is_web'] = $productDetails['is_web'];
										$updateArray[$i]['menu_items'][$j]['image'] = $productDetails['image'];
										$j++;
									}
                                }
				
						$i++;
					}
    
if (is_array($updateArray) && count($updateArray)>0){

$result=$couch->saveDocument(true)->execute(array("docs"=>$updateArray));
 
  if(array_key_exists('error', $result)){
  	$html['error'] = true;
	$html['update'] = false;
	$html['msg'] = 'Some Error Please Contact Admin';
    
  }
  else{
  	$html['error'] = false;
	$html['update'] = true;
	$html['msg'] = "$updateCounter RECORD UPDATED SUCCESSFULLY";
  }
}
$result = json_encode($html,true);
return $result;
}

/* Function To Update Stre Data */

function updateConfig(){

    $couch = new CouchPHP();
	$result = $couch->getDesign('config')->getView('config_list')->execute();
	$categoryList = array();

    if(array_key_exists('rows', $result)){
		$docs = $result['rows'];
		 
		foreach($docs as $dKey => $dValue){
	$categoryList[$dValue['value']['category_name']] = $dValue['value'];	
	  }
	}

    $getConfigDetail = 'SELECT GROUP_CONCAT(cpc.id) as mysql_id, category as category_id, crm.name as category_name,
	                    GROUP_CONCAT(doc_key)AS doc_key, GROUP_CONCAT(doc_value)AS doc_value
	                    FROM cp_pos_config cpc
	                    LEFT JOIN cp_reference_master crm ON crm.id = cpc.category AND crm.active = "Y"
	                    WHERE cpc.active = "Y"
	                    GROUP BY cpc.category';

	$result = mysql_query($getConfigDetail);
	$updateArray = array();
	$updateCounter = 0;
	$j = 0;
	while($row = mysql_fetch_assoc($result)){
       if(array_key_exists($row['category_name'],$categoryList)){
		$updateArray[$j]['_id'] = $categoryList[$row['category_name']]['_id'];
        $updateArray[$j]['_rev'] = $categoryList[$row['category_name']]['_rev'];
        $updateCounter++;
	    }

		$keyexplode = explode(',', $row['doc_key']);
        $valueexplode = explode(',', $row['doc_value']);
        $idexplode = explode(',', $row['mysql_id']);

        $updateArray[$j]['category_id'] = $row['category_id'];
        $updateArray[$j]['category_name'] = $row['category_name'];
        $updateArray[$j]['cd_doc_type'] = 'config_master';
		
		for ($i=0; $i <count($keyexplode) ; $i++) {
		$updateArray[$j][$i]['mysql_id'] = $idexplode[$i]; 
		$updateArray[$j][$i]['key'] = $keyexplode[$i];
		$updateArray[$j][$i]['value'] = $valueexplode[$i];

		}
		//$finalreturn[$row['category_name']] = $row;
      $j++;
	}
  
   if (is_array($updateArray) && count($updateArray)>0){
   	
   $result=$couch->saveDocument(true)->execute(array("docs"=>$updateArray));
  // print_r($result1);
  
  if(array_key_exists('error', $result)){
    $html['error'] = true;
	$html['update'] = false;
	$html['msg'] = 'Some Error Please Contact Admin';
  }
  else{
  	$html['error'] = false;
	$html['update'] = true;
	$html['msg'] = "$updateCounter RECORD UPDATED SUCCESSFULLY";
  	
  }
}
$result = json_encode($html,true);
return $result;
}

?>