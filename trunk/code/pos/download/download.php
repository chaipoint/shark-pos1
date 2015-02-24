<?php
$con = mysql_connect('54.178.189.25', 'root', 'mtf@9081');
if(!$con) die(mysql_errno());
mysql_select_db('cabbeein_cpos', $con);

require_once '../lib/log4php/Logger.php';
require_once '../config.php';
require_once '../common/couchdb_phpclass.php';
require_once '../constant.php';

//$locationId = $argv[1]; /*Contain Location ID*/
//$storeId_id = $argv[2]; /*Contain STORE ID*/
$param=$_GET['param'];
$arguments = explode("-", $param);
$argv[1]=$arguments[0];
$argv[2]=(!empty($arguments[1]) ? $arguments[1] : '');
switch ($argv[1]){
	case 'updateConfig':
	echo updateConfig();
	break;

	case 'updateStore' :
	echo updateStore($argv[2]);
		break;

	case 'updateStaff' :
	echo updateStaff($argv[2]);
		break;

	case 'updateCustomers' :
	echo updateCustomers($argv[2]);
		break;
}

/* Function To Download Retail Customer From CPOS */
function updateCustomers($location){
	$couch = new CouchPHP();
    $existingRC = array(); 
	$listRC = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getview(DESIGN_HO_DESIGN_DOCUMENT_VIEW_RETAIL_CUSTOMER_LIST)->setParam(array('include_docs'=>'true'))->execute();
	
	if(array_key_exists('rows', $listRC)){
		foreach ($listRC['rows'] as $rkey => $rvalue) {
					$existingRC[$rvalue['doc']['mysql_id']]['_id'] = $rvalue['doc']['_id'];
					$existingRC[$rvalue['doc']['mysql_id']]['_rev'] = $rvalue['doc']['_rev'];
				}
	}

	$cusQuery = "SELECT cm.id, cm.name, cm.address, cm.phone, cm.contact_person,
				cm.e_mail, cm.location_id, cm.customer_id, cm.type, cm.billing_type, cm.note, GROUP_CONCAT(DISTINCT(isb.day)) day 
				FROM customer_master cm 
				LEFT JOIN isb_delivery isb ON isb.customer_id = cm.id AND isb.active = 'Y'
				WHERE cm.active = 'Y' AND cm.location_id = $location 
				GROUP BY isb.customer_id
				ORDER BY `cm`.`id` ASC";
				
	$result = mysql_query($cusQuery);
	//$dbResult = array();
	/*while ($row = mysql_fetch_array($result)) {
		$dbResult[] = $row;
	}*/
	
	$insertArray = array();
	$updateCounter = 0;
	$insertCounter = 0;
	$deleteCounter = 0;
	$i = 0;
	
	while($value=mysql_fetch_assoc($result)){
		if(array_key_exists($value['id'], $existingRC)){
			$value['_id'] = $existingRC[$value['id']]['_id'];
			$value['_rev'] = $existingRC[$value['id']]['_rev'];
			unset($existingRC[$value['id']]);
			$updateCounter++;
		}else{
		$insertCounter++;
	}
		$value['cd_doc_type'] = RETAIL_CUSTOMERS_DOC_TYPE;
		$value['mysql_id'] = $value['id'];
		$value['address'] = str_replace(array('"',"'"), ' ', $value['address']);
		unset($value['id']);
		$insertArray[$i] = $value;
		$day = array();
		$day = explode(',', $value['day']);
		foreach($day as $inkey => $inValue){
			$getProduct = "SELECT product_id id, cim.name, cim.code, isb.price, isb.pro_start_time, 
							isb.st_leaving_time, isb.onsite_time, isb.tax tax_id,
							ctm.name tax_name, ctm.rate tax_rate, cim.item_group_code category_id, crm.name as category_name
							FROM `isb_delivery` isb
							LEFT JOIN cp_item_master cim ON cim.id = isb.product_id AND cim.active = 'Y'
							LEFT JOIN cp_tax_master ctm ON ctm.id = isb.tax AND ctm.active = 'Y'
							LEFT JOIN cp_reference_master crm ON crm.id = cim.item_group_code AND crm.active = 'Y'
							WHERE isb.active = 'Y' 
							AND isb.customer_id = '".$value['mysql_id']."' AND isb.day = '".$inValue."'";
			
			$res = mysql_query($getProduct);
			$dbResult = array();
			while ($row = mysql_fetch_array($res)) {
				$dbResult[] = $row;
			}
			$j=0;
			
			if(is_array($dbResult) && count($dbResult)>0){
				foreach($dbResult as $k => $v){ 
					$insertArray[$i]['schedule'][$inValue][$j]['mysql_id'] = $v['id'];
					$insertArray[$i]['schedule'][$inValue][$j]['code'] = $v['code'];
					$insertArray[$i]['schedule'][$inValue][$j]['name'] = $v['name'];
					$insertArray[$i]['schedule'][$inValue][$j]['price'] = $v['price'];
					$insertArray[$i]['schedule'][$inValue][$j]['tax']['id'] = $v['tax_id'];
					$insertArray[$i]['schedule'][$inValue][$j]['tax']['name'] = $v['tax_name'];
					$insertArray[$i]['schedule'][$inValue][$j]['tax']['rate'] = $v['tax_rate'];
					$insertArray[$i]['schedule'][$inValue][$j]['category']['id'] = $v['category_id'];
					$insertArray[$i]['schedule'][$inValue][$j]['category']['name'] = $v['category_name'];
					$insertArray[$i]['schedule'][$inValue][$j]['production_start_time'] = $v['pro_start_time'];
					$insertArray[$i]['schedule'][$inValue][$j]['store_leaving_time'] = $v['st_leaving_time'];
					$insertArray[$i]['schedule'][$inValue][$j]['onsite_time'] = $v['onsite_time'];
					$j++;
				}
			}
		}
		$i++;
	}
	if(count($existingRC)>0){
		foreach($existingRC as $keyR => $valueR){
			$res = $couch->deleteDoc($valueR['_id'])->setParam(array('rev'=>$valueR['_rev']))->execute();
			$deleteCounter++;
		}
	}
	$totalInsert = count($insertArray);
	if($totalInsert > 0){
		$result=$couch->saveDocument(true)->execute(array("docs"=>$insertArray));
  		if(array_key_exists('error', $result)){
  				
  				$html['error'] = true;
				$html['update'] = false;
				$html['msg'] = ERROR.' '.$result['error'];
   		}else{
  			//$logger->debug("Retail Customers Updated Successfully IN CouchDb");
  			$html['error'] = false;
			$html['update'] = true;
			$html['data']['inserted'] = $insertCounter;
			$html['data']['updated'] = $updateCounter;
			$html['data']['deleted'] = $deleteCounter;
  		}
	}
	$result = json_encode($html);
	//$logger->debug("End OF updateStaff Function");
	mysql_close();
	return $result;
}

/* Function To Download Staff From CPOS*/
function updateStaff($location){
	$couch = new CouchPHP();
	$result = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_STAFF_BY_MYSQL_ID)->setParam(array("include_docs"=>"true"))->execute();
	
	$itemList = array();
	$_idList = array();
    if(array_key_exists('rows', $result)){
		$docs = $result['rows'];
		foreach($docs as $dKey => $dValue){
	    	$itemList[$dValue['doc']['mysql_id']] = $dValue['doc'];
	    	$_idList[$dValue['doc']['_id']] = $dValue['doc']['_rev'];
       }
      
	}
	
 	
 	$getStaff = "SELECT   sm.id mysql_id, sm.name, sm.username, sm.code,
	    	      sm.password, sm.address, sm.photo, sm.email,sm.phone_1,
		          sm.phone_2,lm.id location_id, lm.name location_name,tm.id title_id,
	        	  tm.name title_name, sm.active 
			  	  from staff_master sm
			   	  LEFT JOIN  location_master lm ON lm.id = sm.location_id 
                  LEFT JOIN  title_master tm ON tm.id = sm.title_id AND tm.active ='Y' 
                  where sm.active = 'Y' AND sm.location_id = $location";
    $result = mysql_query($getStaff);
    $updateArray = array();
	$html = array();
	$i = 0;
	$updateCounter = 0;
	
	while($row = mysql_fetch_assoc($result)){
	  $updateArray[$i] = $row;
	  if(array_key_exists($row['mysql_id'],$itemList)){
      	$updateArray[$i]['_id'] = $itemList[$row['mysql_id']]['_id'];
      	$updateArray[$i]['_rev'] = $itemList[$row['mysql_id']]['_rev'];
      	unset($_idList[$itemList[$row['mysql_id']]['_id']]);
      	$updateCounter++;
      }
      $updateArray[$i]['cd_doc_type'] = STAFF_MASTER_DOC_TYPE;
      $updateArray[$i]['password'] = md5(base64_decode($row['password']));
      $updateArray[$i]['phone'][] = $updateArray[$i]['phone_1'];
	  $updateArray[$i]['phone'][] = $updateArray[$i]['phone_2'];
	  $updateArray[$i]['title']['id'] = $updateArray[$i]['title_id'];
	  $updateArray[$i]['title']['name'] = $updateArray[$i]['title_name'];
	  unset($updateArray[$i]['title_id']);
	  unset($updateArray[$i]['title_name']);
	  unset($updateArray[$i]['phone_1']);
	  unset($updateArray[$i]['phone_2']);
	  $i++;
	  
	}
	if(count($_idList)>0){
		foreach ($_idList as $key => $value) {
			$res = $couch->deleteDoc($key)->setParam(array('rev'=>$value))->execute();
			$i++;
		}
	}
    $insertCounter = $i-$updateCounter;	
	if(is_array($updateArray) && count($updateArray)>0){
		$result = $couch->saveDocument(true)->execute(array("docs"=>$updateArray));
		if(array_key_exists('error', $result)){
  			$html['error'] = true;
			$html['update'] = false;
			$html['msg'] = ERROR.' '.$result['error'];
   		}
  		else{
  			$html['error'] = false;
			$html['update'] = true;
			$html['msg'] = ( $insertCounter>0 ? ($updateCounter==0 ? "$insertCounter ".INSERT_SUCCESS."" : "$insertCounter ".INSERT_SUCCESS." AND $updateCounter ".UPDATE_SUCCESS."" ) : "$updateCounter ".UPDATE_SUCCESS."");
	  	}
	}
	$result = json_encode($html);
	mysql_close();
	return $result;
}

/* Function To Download Store From CPOS */
function updateStore($location_id){
    $couch = new CouchPHP();
	$result = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_STORE_BY_MYSQL_ID)->setParam(array("include_docs"=>"true"))->execute();
	$storeList = array();
	$_idList = array();

    if(array_key_exists('rows', $result)){
		$docs = $result['rows'];
		 foreach($docs as $dKey => $dValue){
	    $storeList[$dValue['doc']['mysql_id']] = $dValue['doc'];
	    $_idList[$dValue['doc']['_id']] = $dValue['doc']['_rev'];	
	  	}
	}
  
	$getStoreNameQuery = "SELECT sm.id mysql_id, sm.name, sm.code,
	                      sm.type, sm.address, sm.phone_1, sm.phone_2, photo,
	                      weekly_off, lm.id location_id, lm.name location_name,
	                      sm.sms, sm.foe_allowed is_foe, sm.active, sm.store_time store_open_schedule,
	                      sm.ppa_uid, sm.ppa_pwd, sm.ppc_uid, sm.ppc_pwd, sm.ppc_tid, sm.billing_type, sm.store_message
                          FROM store_master sm
                          LEFT JOIN  location_master lm ON lm.id = sm.location_id 
                          WHERE sm.active = 'Y' AND sm.type != 'WHO' AND sm.location_id = $location_id";
						
	$storeResult = mysql_query($getStoreNameQuery);
	$getProductRecipe = "SELECT id, product_id, store_id 
						 FROM cp_recipes_master WHERE active = 'Y'";
						
						$recipesResult = mysql_query($getProductRecipe);
						$productRecipeArray = array();
						while($row = mysql_fetch_assoc($recipesResult)){
							$productRecipeArray[$row['store_id']][$row['product_id']] = $row['id'];
						}
						$updateArray = array();
	                    $html = array();
						$i = 0;
						$updateCounter = 0;
						$insertCounter = 0;
						
					    while($row = mysql_fetch_assoc($storeResult)){
                            $storeDetails = $row;
                            $updateArray[$i] = $storeDetails;
                            
                            if(array_key_exists($storeDetails['mysql_id'],$storeList)){
							$updateArray[$i]['_id'] = $storeList[$storeDetails['mysql_id']]['_id'];
                            $updateArray[$i]['_rev'] = $storeList[$storeDetails['mysql_id']]['_rev'];
                            unset($_idList[$storeList[$storeDetails['mysql_id']]['_id']]);
                            $updateCounter++;
						    }

							$updateArray[$i]['cd_doc_type'] = STORE_MASTER_DOC_TYPE;
							$updateArray[$i]['address'] = $storeDetails['address'];
							$updateArray[$i]['bill_type'] = $storeDetails['billing_type'];
							$updateArray[$i]['store_message'] = $storeDetails['store_message'];
							$updateArray[$i]['location']['id'] = $storeDetails['location_id'];
							$updateArray[$i]['location']['name'] = $storeDetails['location_name'];
							$updateArray[$i]['ppa_details']['uid'] = $storeDetails['ppa_uid'];
							$updateArray[$i]['ppa_details']['pwd'] = $storeDetails['ppa_pwd'];
							$updateArray[$i]['ppc_details']['tid'] = $storeDetails['ppc_tid'];
							$updateArray[$i]['ppc_details']['uid'] = $storeDetails['ppc_uid'];
							$updateArray[$i]['ppc_details']['pwd'] = $storeDetails['ppc_pwd'];
							
							$updateArray[$i]['phone'][] = $updateArray[$i]['phone_1'];
							$updateArray[$i]['phone'][] = $updateArray[$i]['phone_2'];
							unset($updateArray[$i]['phone_1']);
							unset($updateArray[$i]['phone_2']);
							unset($updateArray[$i]['location_id']);
							unset($updateArray[$i]['location_name']);
							unset($updateArray[$i]['address']);
							unset($updateArray[$i]['ppa_uid']);
							unset($updateArray[$i]['ppa_pwd']);
							unset($updateArray[$i]['ppc_tid']);
							unset($updateArray[$i]['ppc_uid']);
							unset($updateArray[$i]['ppc_pwd']);

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
										$updateArray[$i]['menu_items'][$j]['recipe_id'] = '';
										if(array_key_exists($storeDetails['mysql_id'], $productRecipeArray) && array_key_exists($productDetails['id'], $productRecipeArray[$storeDetails['mysql_id']])){
											$updateArray[$i]['menu_items'][$j]['recipe_id'] = $productRecipeArray[$storeDetails['mysql_id']][$productDetails['id']] ;
										}
										$updateArray[$i]['menu_items'][$j]['is_coc'] = $productDetails['is_coc'];
										$updateArray[$i]['menu_items'][$j]['is_foe'] = $productDetails['is_foe'];
										$updateArray[$i]['menu_items'][$j]['is_web'] = $productDetails['is_web'];
										$updateArray[$i]['menu_items'][$j]['image'] = $productDetails['image'];
										$j++;
									}
                                }

                        	$getStaff = "SELECT ss.staff_id id, sm.code, sm.name name, sm.title_id, tm.name title_name  FROM store_staff ss
										 LEFT JOIN staff_master sm ON sm.id = ss.staff_id 
										 LEFT JOIN title_master tm ON tm.id = sm.title_id 
										 WHERE ss.store_id = '".$storeDetails['mysql_id']."' AND ss.active = 'Y' AND sm.active = 'Y' AND tm.active = 'Y'";
							
							$staffList = mysql_query($getStaff);
							$k = 0;
							while ($row = mysql_fetch_assoc($staffList)) {
								$updateArray[$i]['store_staff'][$k]['mysql_id'] = $row['id'];
								$updateArray[$i]['store_staff'][$k]['code'] = $row['code']; 
								$updateArray[$i]['store_staff'][$k]['name'] = $row['name'];
								$updateArray[$i]['store_staff'][$k]['title_id'] = $row['title_id'];
								$updateArray[$i]['store_staff'][$k]['title_name'] = $row['title_name'];
								$k++;
							}
				
						$i++;
					}
			
			if(count($_idList)>0){
				foreach ($_idList as $key => $value) {
				$res = $couch->deleteDoc($key)->setParam(array('rev'=>$value))->execute();
				$i++;
			}
		} 
	$insertCounter = $i-$updateCounter;
	if (is_array($updateArray) && count($updateArray)>0){
		$result=$couch->saveDocument(true)->execute(array("docs"=>$updateArray));
 		if(array_key_exists('error', $result)){
  			
  			$html['error'] = true;
			$html['update'] = false;
			$html['msg'] = ERROR.' '.$result['error'];
    	}
  		else{
  			
  			$html['error'] = false;
			$html['update'] = true;
			$html['msg'] = $html['msg'] = ($insertCounter>0 ? ($updateCounter==0 ? "$insertCounter ".INSERT_SUCCESS."" : "$insertCounter ".INSERT_SUCCESS." AND $updateCounter ".UPDATE_SUCCESS."" ) : "$updateCounter ".UPDATE_SUCCESS."");
  		}
	}
	$result = json_encode($html);
	mysql_close();
	return $result;
}

/* Function To Download Configration Setting From CPOS */
function updateConfig(){ 
	$couch = new CouchPHP();
	$result = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_CONFIG_LIST)->setParam(array('include_docs'=>'true','limit'=>1))->execute();
	$categoryList = array();
	$updateArray = array();
    if(array_key_exists('rows', $result)){
		$docs = $result['rows'];
		foreach($docs as $dKey => $dValue){
			$updateArray['_id'] = $dValue['doc']['_id'];
			$updateArray['_rev'] = $dValue['doc']['_rev'];
	  	}
	   
	}
	
    $getConfigDetail = 'SELECT mode, GROUP_CONCAT(id) AS id, GROUP_CONCAT(name) AS name, GROUP_CONCAT(code) AS code 
                        FROM `cp_reference_master` 
                        WHERE is_pos = "Y" 
                        AND active = "Y"
                        GROUP BY mode';

    $result = mysql_query($getConfigDetail);
	$updateCounter = 0;
	$i = 0;
	
	while($row = mysql_fetch_assoc($result)){
       	$updateArray['cd_doc_type'] = CONFIG_MASTER_DOC_TYPE;
		$idexplode = explode(',', $row['id']);
        $nameexplode = explode(',', $row['name']);
        $codeexplode = explode(',', $row['code']);
        $count = count($idexplode);
        
        for ($j=0; $j < $count ; $j++){
        	if($row['mode']=='ppc_api' || $row['mode']=='ppa_api' || $row['mode']=='sms_api' || $row['mode']=='db_detail' || $row['mode']=='company_details' || $row['mode']=='app_version'){
 				$updateArray[$row['mode']][$codeexplode[$j]] = $nameexplode[$j];
        	}else{
		     	$updateArray[$row['mode']][$idexplode[$j]] = $nameexplode[$j];
        	}
    	}
      
	}
	$insertCounter = $i-$updateCounter;
    
  
   	if(is_array($updateArray) && count($updateArray)>0){
		$couch->saveDocument();
		if(array_key_exists('_rev', $updateArray)){
			$couch->setParam(array('rev'=>$updateArray['_rev']));		
		}
		$result = $couch->execute($updateArray);
  		print_r($result);
  		if(array_key_exists('error', $result)){
  			$html['error'] = true;
			$html['update'] = false;
			$html['msg'] = ERROR.' '.$result['error'];
  		}
  		else{
  			
  			$html['error'] = false;
			$html['update'] = true;
			$html['msg'] = $html['msg'] = $html['msg'] = ($insertCounter>0 ? ($updateCounter==0 ? "".INSERT_SUCCESS."" : "$insertCounter ".INSERT_SUCCESS." AND $updateCounter ".UPDATE_SUCCESS."" ) : "".UPDATE_SUCCESS."");
  	
  		}
	}	
	$result = json_encode($html);
	mysql_close();
	return $result;
}


?>