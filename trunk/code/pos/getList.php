<?php			
	$con = mysql_connect('localhost','root','root');
	mysql_select_db('cpos');

			$getStoreNameQuery = "select id, name from store_master";
						$storeResult = mysql_query($getStoreNameQuery);
						$i = 0;
					while($row = mysql_fetch_assoc($storeResult)){
							//$storeResult1 = $row;
						$storeDetails = $row;
				//		foreach($row  as $key => $storeDetails){
							//$i = $storeDetails['id'];
							$id = $storeDetails['id'];
							unset($storeDetails['id']);
							$return[$i] = $storeDetails;
							$return[$i]['mysql_id'] = $id;
							$return[$i]['cd_doc_type'] = 'store_master';

//							$j = $storeDetails['id'];
							$products = "select pm.id, if(cpsp.display_name = '' or cpsp.display_name is null,  pm.display_name, cpsp.display_name) name, 
				                cpsp.store_id, pm.code, if(cpsp.price is null, pm.price,if(cpsp.price = 0, 'R' , cpsp.price)) price, 
				                ctm.name tax, ctm.id tax_id,  crm.name category, crm.id as category_id
								from product_master pm
								LEFT JOIN cp_product_store_price cpsp on cpsp.product_id = pm.id and cpsp.store_id = ".$id." and cpsp.active = 'Y'
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
										$return[$i]['menu_items'][$j]['tax'] = $productDetails['tax'];
										$return[$i]['menu_items'][$j]['tax_id'] = $productDetails['tax_id'];
										$return[$i]['menu_items'][$j]['category'] = $productDetails['category'];
										$return[$i]['menu_items'][$j]['category_id'] = $productDetails['category_id'];
										$j++;
									}
//								}
								
							
								}
				//			}
								$i++;
					}

					//print_r($return);
				//	require_once 'httpapi.php';
//			print_r(curl("http://127.0.0.1:5984/pos/_bulk_docs",array("docs"=>$return),array('contentType'=>'application/json','is_content_type_allowed'=>true)));

						echo "<pre>";
							print_r($return);
						echo "</pre>";

						?>
						<!--

{"_id":"_design/staff","_rev":"80-117d19e2905a4677ea4d07c43c602f54","language":"javascript","views":{"staff_code":{"map":"function(doc) {\n\tif(doc.type && doc.type == 'staff_master')\n  \t\temit(doc.username, doc);\n}"}},"shows":{"validateuser":"function(res, req) { var msg = ''; for (var i in req){\n /*msg += '\\n\\n'+i+'=>'+( typeof req[i] == 'string' ? req[i] : '');  /**/ if(typeof req[i] != 'string') {  msg+='\\n';  for(var j in req[i]) { msg+= '\\n\\t\\t['+i+']['+j+'] =>'+ req[i][j];} } }  msg+= '\\n\\n'; return msg; /*return { body: req.query.name, headers : {'Content-Type' : 'application/json'} }/**/}","other":"function(res, req) {return 'hello\\n';} "},"lists":{"getuser":"function(head, req) { var userfound = false; var reqData = req.form; var obj = new Object(); obj.error = false; obj.message = ''; obj.data = new Object(); var data = ''; for(var i in reqData){ data = JSON.parse(i); break;}  while(row = getRow()){  if(row.key == data.username) {  if(row.value.password == (data.password).toUpperCase()){ obj.data = row.value; } else{ obj.error = true; obj.message = 'Password Wrong';}  userfound = true; break;} } if( !userfound ) { obj.error = true; obj.message = 'User Unavailable';} return JSON.stringify(obj);}"}}
						-->