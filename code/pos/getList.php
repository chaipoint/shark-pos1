<?php			
	$con = mysql_connect('localhost','root','root');
	mysql_select_db('cpos');

			$getStoreNameQuery = "select sm.id mysql_id, sm.name, sm.code, sm.type, sm.address, sm.phone_1, sm.phone_2, photo, weekly_off, lm.id location_id, lm.name location_name, sm.sms, sm.foe_allowed is_foe, sm.active, sm.store_time store_open_schedule
from store_master sm
LEFT JOIN  location_master lm 
ON lm.id = sm.location_id 


where sm.active = 'Y'";
						$storeResult = mysql_query($getStoreNameQuery);
						$i = 0;
					while($row = mysql_fetch_assoc($storeResult)){
							//$storeResult1 = $row;
						$storeDetails = $row;
				//		foreach($row  as $key => $storeDetails){
							//$i = $storeDetails['id'];
							$return[$i] = $storeDetails;
							$return[$i]['cd_doc_type'] = 'store_master';
							$return[$i]['location']['id'] = $storeDetails['location_id'];
							$return[$i]['location']['name'] = $storeDetails['location_name'];

							$return[$i]['phone'][] = $return[$i]['phone_1'];
							$return[$i]['phone'][] = $return[$i]['phone_2'];
							unset($return[$i]['phone_1']);
							unset($return[$i]['phone_2']);
							unset($return[$i]['location_id']);
							unset($return[$i]['location_name']);

							$selectSchedule = "SELECT * FROM  `cp_store_timings` WHERE store_id =".$storeDetails['mysql_id'];
							$resultSchedule = mysql_query($selectSchedule);
							while($rowSchedule = mysql_fetch_assoc($resultSchedule)){
								if($rowSchedule['type'] == 7){
									$return[$i]['schedule']['opening_time'][0] = $rowSchedule['opening_time'];
									$return[$i]['schedule']['closing_time'][0] = $rowSchedule['closing_time'];
								}elseif($rowSchedule['type'] == 0){
									$return[$i]['schedule']['opening_time'][1] = $rowSchedule['opening_time'];
									$return[$i]['schedule']['closing_time'][1] = $rowSchedule['closing_time'];
									$return[$i]['schedule']['opening_time'][2] = $rowSchedule['opening_time'];
									$return[$i]['schedule']['closing_time'][2] = $rowSchedule['closing_time'];
									$return[$i]['schedule']['opening_time'][3] = $rowSchedule['opening_time'];
									$return[$i]['schedule']['closing_time'][3] = $rowSchedule['closing_time'];
									$return[$i]['schedule']['opening_time'][4] = $rowSchedule['opening_time'];
									$return[$i]['schedule']['closing_time'][4] = $rowSchedule['closing_time'];
									$return[$i]['schedule']['opening_time'][5] = $rowSchedule['opening_time'];
									$return[$i]['schedule']['closing_time'][5] = $rowSchedule['closing_time'];
								}elseif($rowSchedule['type'] == 6){
									$return[$i]['schedule']['opening_time'][6] = $rowSchedule['opening_time'];
									$return[$i]['schedule']['closing_time'][6] = $rowSchedule['closing_time'];
								}
							}
							ksort($return[$i]['schedule']['opening_time']);
							ksort($return[$i]['schedule']['closing_time']);
							//print_r($return);
							

//							$j = $storeDetails['id'];
							$products = "select pm.id, if(cpsp.display_name = '' or cpsp.display_name is null,  pm.display_name, cpsp.display_name) name, 
				                cpsp.store_id, pm.code, if(cpsp.price is null, pm.price,if(cpsp.price = 0, 'R' , cpsp.price)) price, 
				                ctm.name tax, ctm.id tax_id,  crm.name category, crm.id as category_id, ctm.rate tax_rate, 

				                pm.packaging, pm.is_coc, pm.is_foe, pm.is_web, pm.product_image image
								
								from product_master pm
								LEFT JOIN cp_product_store_price cpsp on cpsp.product_id = pm.id and cpsp.store_id = ".$storeDetails['mysql_id']." and cpsp.active = 'Y'
								LEFT JOIN cp_tax_master ctm on ctm.id = if(cpsp.price is null, pm.tax,cpsp.tax_rate)
								LEFT JOIN cp_reference_master crm on crm.id = pm.type 
								
								where  pm.active = 'Y'
								order by  pm.id asc
							";
							$productList = mysql_query($products);
							$j = 0;
							while($innerRow = mysql_fetch_assoc($productList)){	
								$productDetails = $innerRow;
//								foreach($innerRow as $productIndex => $productDetails){
									//$j						= $productDetails['id'];
										if(is_numeric($productDetails['price'])){
										$return[$i]['menu_items'][$j]['mysql_id'] = $productDetails['id']; 
										$return[$i]['menu_items'][$j]['code'] = $productDetails['code'];
										$return[$i]['menu_items'][$j]['name'] = $productDetails['name'];
										$return[$i]['menu_items'][$j]['price'] = $productDetails['price'];


										$return[$i]['menu_items'][$j]['tax']['id'] = $productDetails['tax_id'];
										$return[$i]['menu_items'][$j]['tax']['name'] = $productDetails['tax'];
										$return[$i]['menu_items'][$j]['tax']['rate'] = $productDetails['tax_rate'];
										
										$return[$i]['menu_items'][$j]['category']['id'] = $productDetails['category_id'];
										$return[$i]['menu_items'][$j]['category']['name'] = $productDetails['category'];
					
										$return[$i]['menu_items'][$j]['packaging'] = $productDetails['packaging'];
										$return[$i]['menu_items'][$j]['is_coc'] = $productDetails['is_coc'];
										$return[$i]['menu_items'][$j]['is_foe'] = $productDetails['is_foe'];
										$return[$i]['menu_items'][$j]['is_web'] = $productDetails['is_web'];
										$return[$i]['menu_items'][$j]['image'] = $productDetails['image'];
										$j++;
									}
//								}
								
							
								}
				//			}
								$i++;
					}

					//print_r($return);
					require_once 'httpapi.php';
//			print_r(curl("http://127.0.0.1:5984/pos/_bulk_docs",array("docs"=>$return),array('contentType'=>'application/json','is_content_type_allowed'=>true)));

						echo "<pre>";
//							print_r($return);
						echo "</pre>";

						?>
						<!--

{"_id":"_design/staff","_rev":"80-117d19e2905a4677ea4d07c43c602f54","language":"javascript","views":{"staff_code":{"map":"function(doc) {\n\tif(doc.type && doc.type == 'staff_master')\n  \t\temit(doc.username, doc);\n}"}},"shows":{"validateuser":"function(res, req) { var msg = ''; for (var i in req){\n /*msg += '\\n\\n'+i+'=>'+( typeof req[i] == 'string' ? req[i] : '');  /**/ if(typeof req[i] != 'string') {  msg+='\\n';  for(var j in req[i]) { msg+= '\\n\\t\\t['+i+']['+j+'] =>'+ req[i][j];} } }  msg+= '\\n\\n'; return msg; /*return { body: req.query.name, headers : {'Content-Type' : 'application/json'} }/**/}","other":"function(res, req) {return 'hello\\n';} "},"lists":{"getuser":"function(head, req) { var userfound = false; var reqData = req.form; var obj = new Object(); obj.error = false; obj.message = ''; obj.data = new Object(); var data = ''; for(var i in reqData){ data = JSON.parse(i); break;}  while(row = getRow()){  if(row.key == data.username) {  if(row.value.password == (data.password).toUpperCase()){ obj.data = row.value; } else{ obj.error = true; obj.message = 'Password Wrong';}  userfound = true; break;} } if( !userfound ) { obj.error = true; obj.message = 'User Unavailable';} return JSON.stringify(obj);}"}}
						-->