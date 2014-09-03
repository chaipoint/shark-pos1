<?php
	class utils extends App_config{
		function __construct(){
			parent::__construct();
			global $couch;
			$this->cDB = $couch;
		}
		function sync(){
			$return = array('error'=>false, 'message'=>'');
			if(array_key_exists('mode', $_GET) && !empty($_GET['mode']))	{
				switch($_GET['mode']){
					case 'staff_sync_bt':
						$location = $_SESSION['user']['location']['id'];
						$return = $this->getStaff($location,true);
						break;
					case 'store_sync_bt':
						//$store_data = $this->getInstallationConfig();
						$store = $_SESSION['user']['store']['id'];//= $store_data['data']['store_config']['store_id'];
						$_GET['store'] = $store;
						$_GET['only_store'] = true;
						$return = $this->getStore(true);
						break;
					case 'billing_sync_bt':
							$result = $this->billing();
							if($result['error']){
								$return['message'] = $result['message'];								
							}
						break;
					case 'config_sync_bt':
							$result = $this->repConfig();
					break;
					case 'design_sync_bt':
							$result = $this->repDesign();
					break;
					case 'billing_stop_sync_bt':
							$rep = $this->cDB->getActiveTask();//json_decode('[{"pid":"<0.19819.0>","checkpoint_interval":5000,"checkpointed_source_seq":259,"continuous":true,"doc_id":null,"doc_write_failures":0,"docs_read":1,"docs_written":1,"missing_revisions_found":1,"progress":100,"replication_id":"21b9ee56f33d6ec7e12c737f46f4a7b6+continuous","revisions_checked":1,"source":"http://127.0.0.1:5984/testing/","source_seq":259,"started_on":1408442813,"target":"http://pos:*****@54.249.247.15:5984/vente_ho_db/","type":"replication","updated_on":1408443075},{"pid":"<0.20189.0>","checkpoint_interval":5000,"checkpointed_source_seq":259,"continuous":true,"doc_id":null,"doc_write_failures":0,"docs_read":256,"docs_written":256,"missing_revisions_found":256,"progress":100,"replication_id":"cee10645c91448e7e411aff39d56a9f1+continuous","revisions_checked":257,"source":"testing","source_seq":259,"started_on":1408443071,"target":"rk","type":"replication","updated_on":1408443076}]',true);
							//print_r($rep);
							if(array_key_exists(0, $rep)){
								foreach($rep as $key => $value){
									$this->cDB->replicate()->execute(array('replication_id'=>$value['replication_id'], 'cancel'=>true));
								}
							}
					break;
				}
			}else{
				$return['error'] = true;
				$return['message'] = "Not allowed to follow this action";
			}
			return  json_encode($return);
		}
		function repConfig(){
			$return = array('error'=>false, 'message'=>'');
			$source = $this->cDB->getRemote();
			$target = $this->cDB->getUrl().$this->cDB->getDB();
			$result = $this->cDB->replicate()->execute(array('source'=>$source, 'target'=>$target, 'filter'=>'doc_replication/config_replication'));
			if(array_key_exists('ok', $result) && $result['ok']){

			}else{
				$return = array('error'=>true, 'message'=>'OOPS! Some Problem Contact Admin');
			}			
			return $return;
		}
		function initial(){
			$return = array('error'=>false, 'message'=>'');
			if(array_key_exists('store', $_GET) && is_numeric($_GET['store']) && $_GET['store'] > 0){
				$rtData = $this->repDesign(true);

				if($rtData['error']){
					$return = array('error'=>true, 'message'=> $rtData['message']);					
				}else{
					$rtConfig = $this->repConfig();
					if($rtConfig['error']){
						$return = array('error'=>true, 'message'=>$return['message']);												
					}else{
						$return = $this->getStore(true);
						if($return['error']){
							$return = array('error'=>true, 'message'=>$return['message']);						
						}else{
							$data = $this->getInstallationConfig();
							$this->setIniFile($this->iniConfigFile,$data['data']);						
						}						
					}
				}
			}else{	
				$return = array('error'=>true, 'message'=>'Please Provide Valid Store');				
			}
			return json_encode($return); 
		}
		function billing(){
			$return = array('error'=>false,'message'=>'');
			$source = $this->cDB->getUrl().$this->cDB->getDB();
			$result = $this->cDB->replicate()->execute(array('source'=>$source, 'target'=>$this->cDB->getRemote(), 'filter'=>'doc_replication/bill_replication', "continuous" => true));
			if(array_key_exists('ok', is_null($result) ? array() : $result)){
				//echo "Billing Replication Started SuccessFully";
			}else{
				$return = array('error'=>false,'message'=>'OOPS! Some Problem Contact Admin');
//				echo "Opps! Some Problem Try Later";				
			}
			return $return; 

		}

		function getStaff($location, $rep){
			$return = array('error'=>false,'message'=>'');
			if(empty($location)){
				$return = array('error'=>true,'message'=>'Provide Staff Location to replicate');
			}else{
				if($rep){
					$source = $this->cDB->getRemote();
					$target = $this->cDB->getUrl().$this->cDB->getDB();
					$result = $this->cDB->replicate()->execute(array('source'=>$source, 'target'=>$target, 'filter'=>'doc_replication/staff_replication', 'query_params'=>array("location"=>$location)));
					if(array_key_exists('ok', $result) && $result['ok']){
					}else{
						$return = array('error'=>true, 'message'=>'OOPS! Some Problem Contact Admin');
					}	
				}else{
					$result = $this->cDB->executeRemote('_design/staff/_view/staff_location?include_docs=true&key="'.$location.'"');
					if(count($result['rows'])>0){
					//	print_r($result['rows']);
						$latArray = array();
						foreach($result['rows'] as $key => $value){
							$latArray[] = $value['doc'];
						}

	//					$staffData = $result['rows'];					
						$newResult = $this->cDB->saveDocument('true')->execute(array('docs'=>$latArray));
					}else{
						echo "No Staff Data Found With Associated Store";
					}
				}							
			}
//			http://54.249.247.15:5984/rakesh_cpos_ho/_design/staff/_view/staff_location?key=%222%22
			return $return;
		}
		function getStore($rep = false){
			$return = array('error'=>false, 'message'=>'');
			if($rep){
				$store = $_GET['store'];
				$source = $this->cDB->getRemote();
				$target = $this->cDB->getUrl().$this->cDB->getDB();
				$result = $this->cDB->replicate()->execute(array('source'=>$source, 'target'=>$target, 'filter'=>'doc_replication/store_replication', 'query_params'=>array("mysql_id"=>$store)));
				if(array_key_exists('ok', $result) && $result['ok']){
					if(array_key_exists('only_store', $_GET) && $_GET['only_store']){
					}else{
						$resultJSON = $this->cDB->getDesign('store')->getView('store_mysql_id')->setParam(array('include_docs'=>'true',"key"=>'"'.$store.'"'))->execute();
						$result = $resultJSON['rows'][0]['doc'];
						$resultFromStaff = $this->getStaff($result['location']['id'],true);
						if($resultFromStaff['error']){
							$return = array('error'=>true, 'message'=>$resultFromStaff['message']);						
						}
					}
				}else{
					$return = array('error'=>true, 'message'=>'OOPS! Some Problem Contact Admin');
				}	
			}else{
			//	$target = $this->cDB->getUrl().$this->cDB->getDB();
			//	$result = $this->cDB->replicate()->execute(array('source'=>"http://54.249.247.15:5984/rakesh_cpos_ho", 'target'=>$target, 'filter'=>'doc_replication/store_replication', 'query_params'=>array("mysql_id"=>"1")));
				$result = $this->cDB->executeRemote('_design/store/_view/store_mysql_id?include_docs=true&key="1"');
				if(count($result['rows'])>0){
					$storeData = $result['rows'][0]['doc'];
					$storeData['address'] = '';
					$newResult = $this->cDB->saveDocument()->execute($storeData);
					$location = $storeData['location']['id'];
					$this->getStaff($location);
					echo "<pre>";
					print_r($newResult);
					echo "</pre>";
				}else{
					echo "No Data Found With Associated Store";
				}
			}
			return $return;
		}
		function repDesign($rev = false){
			$return = array('error'=>false, 'message'=>'');
			//if($rev){
				$source = $this->cDB->getRemote();
				$target = $this->cDB->getUrl().$this->cDB->getDB();
		//	}else{
				//$source = $this->cDB->getUrl().$this->cDB->getDB();
				//$target = $this->cDB->getRemote();
		//	}
			$result = $this->cDB->replicate()->execute(array('source'=>$source, 'target'=>$target, 'filter'=>'doc_replication/design_replication'));
			if(array_key_exists('ok', $result) && $result['ok']){

			}else{
				$return = array('error'=>true, 'message'=>'OOPS! Some Problem Contact Admin');
			}			
			return $return;
/*Array
(
    [ok] => 1
    [session_id] => 0460031c6cc7d26daf5681bf99a9de63
    [source_last_seq] => 2085
    [replication_id_version] => 3
    [history] => Array
        (
            [0] => Array
                (
                    [session_id] => 0460031c6cc7d26daf5681bf99a9de63
                    [start_time] => Tue, 12 Aug 2014 06:01:28 GMT
                    [end_time] => Tue, 12 Aug 2014 06:01:33 GMT
                    [start_last_seq] => 0
                    [end_last_seq] => 2085
                    [recorded_seq] => 2085
                    [missing_checked] => 6
                    [missing_found] => 6
                    [docs_read] => 6
                    [docs_written] => 6
                    [doc_write_failures] => 0
                )

        )

)
			echo "<pre>";
			print_r($result);
			echo "</pre>";/**/

			/*curl -X POST http://127.0.0.1:5984/_replicate -H "Content-Type:application/json" -d '{"source":"http://127.0.0.1:5984/cpos_pos","target":"http://54.249.247.15:5984/rakesh_cpos_ho", "filter":"doc_replication/design_replication"}'*/
		}
		function init(){
			$designDocs = array();

			$designDocs[] = array('_id'=>'generateBill','cd_doc_type' => 'bill_counter', 'current' => 0, 'current_month' => 0);

			$designDocs[] = array(
				'_id'=>'_design/staff',
				'language' => 'javascript', 
				'views' => array(
					"staff_username"=>array(
							"map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'staff_master'){ emit(doc.username, doc); } }",
						),
					"staff_location"=>array(
							"map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'staff_master'){ emit(doc.location_id, null); } }",
						),
				),
				'lists' => array( 
       				'getuser' =>  "function(head, req) { var userfound = false;    var jData = (JSON.parse(req.body));var username = jData.username; var password = jData.password; var obj = new Object(); obj.error = false; obj.message = ''; obj.data = new Object();      while(row = getRow()){ if(row.key == username) { if((row.value.password).toUpperCase() == (password).toUpperCase()){ obj.data = row.value; } else{ obj.error = true; obj.message = 'Password Wrong';} userfound = true; break;} } if( !userfound ) { obj.error = true; obj.message = 'User Unavailable';} return JSON.stringify(obj);}"
   				)
			);
			$designDocs[] = array(
				'_id'=>'_design/billing',
				'language' => 'javascript', 
				'views' => array(
       				"handle_updated_bills" => array(
       					"map" => "function(doc){ if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill') { var created_time = (doc.time.created).split(' '); var updated_time = doc.time.updated; emit([created_time[0], doc.bill_no, (doc.parent ? 1 : 0) , updated_time],null);} }"
       				),

       				"bill_by_order" => array(
       					"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill' && doc.order_no > 0){ emit(doc.order_no,null); } }"
       				)
				),
				'updates' => array( 
       				'getbillno' =>  "function(doc,req){ if(req.query.month) { if(doc.current_month != req.query.month){doc.current = 0; doc.current_month = req.query.month;} var newCurrent =  doc.current+1; doc.current = newCurrent; return [doc,newCurrent.toString()];}}",
   				),
				'lists' => array( 
       				'sales_register' =>  "function(head,req) { var billList = new Object(); billList.data = new Object(); var payment_type = new Object(); payment_type.count = new Object(); payment_type.amount = new Object(); var bill_status = new Object(); bill_status.count = new Object(); bill_status.amount = new Object(); var cashSale = 0; var ppcSale = 0;  var cashinDelivery = 0; while(row = getRow()) { if(row.doc) { if( !( row.doc.bill_no in billList.data ) ) {  if(row.doc.bill_status != 'Cancelled'){ if(row.doc.payment_type in payment_type.count) { payment_type.amount[row.doc.payment_type] += (1 * row.doc.due_amount); payment_type.count[row.doc.payment_type] += 1; } else { payment_type.amount[row.doc.payment_type] = (1 * row.doc.due_amount); payment_type.count[row.doc.payment_type] = 1; } } if(row.doc.bill_status in bill_status.count) { bill_status.count[row.doc.bill_status] += 1; bill_status.amount[row.doc.bill_status] += (1 * row.doc.due_amount); }else{ bill_status.count[row.doc.bill_status] = 1; bill_status.amount[row.doc.bill_status] = (1 * row.doc.due_amount); } billList.data[row.doc.bill_no] = new Object(); billList.data[row.doc.bill_no] = row.doc; if(row.doc.bill_status == 'Paid' && row.doc.payment_type == 'cash') { cashSale += (1* row.doc.due_amount); } if(row.doc.bill_status == 'Paid' && row.doc.payment_type == 'ppc') { ppcSale += (1* row.doc.due_amount); } if(row.doc.is_cod == 'Y' && row.doc.bill_status == 'CoD') { cashinDelivery += (1* row.doc.due_amount); } } } } billList.bill_status = bill_status; billList.payment_type = payment_type; billList.cash_sale = cashSale; billList.ppcSale = ppcSale; billList.cash_indelivery = cashinDelivery;  return JSON.stringify(billList);} ",
       				'todays_sale' =>  "function(head,req) { var billList = new Object(); var billProcessed = new Object();  while(row = getRow()){ if(row.doc) { if( ! (row.key[1] in billProcessed)){ billProcessed[row.key[1]] = '';  if (row.doc.bill_status == 'Paid') { var itemList = row.doc.items; for(var items in itemList){ if((typeof billList[itemList[items].category_name]) != 'object'){ billList[itemList[items].category_name] = new Object();  billList[itemList[items].category_name][row.doc.payment_type] = ( 1 * itemList[items].due_amount); }else{ if(row.doc.payment_type in billList[itemList[items].category_name]){  billList[itemList[items].category_name][row.doc.payment_type] += (1 * itemList[items].due_amount); }else{ billList[itemList[items].category_name][row.doc.payment_type] = (1 * itemList[items].due_amount); } } } } } } } return JSON.stringify(billList);}"
   				)
			);
			$designDocs[] = array(
				'_id'=>'_design/store',
				'language' => 'javascript', 
				'views' => array(
					"store_mysql_id"=>array(
						"map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'store_master'){ emit(doc.mysql_id,null); } }"
						)
				)
			);
			$designDocs[] = array(
				'_id'=>'_design/login',
				'language' => 'javascript', 
				'views' => array(
					"logout"=>array(
						"map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'login_history'){ emit(doc._id, doc); } }"
						)
				),
				"updates" => array('login_history'=>"function(doc, req){ doc.logout_time = req.query.logout_time; return [doc,'true'];}"),
			);
			$designDocs[] = array(
				'_id'=>'_design/doc_replication',
				'language' => 'javascript', 
				'views' => array(
					"replication"=>array(
						"map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'master'){ emit(doc.location.id, doc); } }"
						)
				), 
				'filters'=> array(
					"staff_replication" => "function(doc,req){ if(doc.cd_doc_type && doc.cd_doc_type == 'staff_master' && doc.location_id == req.query.location) { return  true;}else{return  false;} }",
       				"store_replication" => "function(doc,req){ if(doc.cd_doc_type && doc.cd_doc_type == 'store_master' && doc.mysql_id == req.query.mysql_id) {return  true;}else{return  false;} }",
       				"design_replication" => "function(doc,req){ if(doc.language || (doc.cd_doc_type && ( doc.cd_doc_type == 'bill_counter' || doc.cd_doc_type == 'config_master'))) {return  true;}else{return  false;} }",
       				"bill_replication" => "function(doc, req){ if(doc.cd_doc_type && ( doc.cd_doc_type == 'store_bill' || doc.cd_doc_type == 'petty_expense' || doc.cd_doc_type == 'login_history' ) && !doc.mysql_id){ return true;} else { return false;}} ",
       				"config_replication" => "function(doc, req){ if(doc.cd_doc_type && doc.cd_doc_type == 'config_master'){ return true;} else { return false;}} "				
       				)
			);

			$designDocs[] = array(
				"_id" => "_design/config",
   				"language" => "javascript",
   				"views" => array(
   						"config_list" => array(
   								"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type=='config_master'){ for( var key in doc){ if(key != '_id' && key != '_rev' && key != 'cd_doc_type'){ emit(key, doc[key]); } } } }"
   							)
   					)
			);

			$designDocs[] = array(
				"_id" => "_design/sales",
   				"language" => "javascript",
   				"views" => array(
   						"top_store" => array(
   								"map" => "function(doc) {if(doc.cd_doc_type && doc.cd_doc_type=='store_bill'){ var bill_date=doc.bill_time.split(' ');emit([bill_date[0],doc.store_name], parseInt(doc.due_amount));}}",
   								"reduce" => "function(key,value) {var sum=0; value.forEach(function(v){sum+= parseInt(v);}); return (sum); }"
   							)
   					)
			);

			$designDocs[] = array(
				"_id" => "_design/design_ho",
   				"language" => "javascript",
   				"views" => array(
   						"no_mysql_id" => array(
   								"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill' && !doc.mysql_id && !doc.parent){ var cDT = (doc.time.created).split(' '); emit([(1 * doc.store_id) ,doc.bill_no, cDT[0]], null); } }"
   							),
   						"handle_updated_bills" => array(
   								"map" => "function(doc){ if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill' && doc.parent) { var created_time = doc.time.created; var updated_time = doc.time.updated; emit([(1 * doc.store_id), doc.bill_no , updated_time],{bill_status:doc.bill_status, cancel_reason: (doc.cancel_reason ? doc.cancel_reason :''), reprint: doc.reprint, parent: doc.parent.id, mysql: (doc.mysql_id ? doc.mysql_id : 0)});} }"
   							),
   						"staff_by_mysql_id" => array(
   								"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'staff_master'){ emit([doc.mysql_id,doc.code], null); } }"
   							),
   						"store_by_mysql_id" => array(
   								"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'store_master'){ emit(doc.mysql_id,doc); } }"
   							),
   						"config_list" => array(
   								"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type=='config_master'){ for( var key in doc){ if(key != '_id' && key != '_rev' && key != 'cd_doc_type'){ emit(key, doc[key]); } } } }"
   							)
   					),
   				"updates" => array(
   						"insert_mysql_id" => "function(doc,req){ if(doc) { if(doc) { doc.mysql_id = req.query.mysql_id; return [doc,req.query.mysql_id]; } } }"
   					)
			);
			$designDocs[] = array(
				"_id" => "_design/petty_expense",
   				"language" => "javascript",
   				"views" => array(
   						"get_expense" => array(
   								"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type=='petty_expense'){ emit(doc.expense_date, null); } }"
   							)
   					)
			);




			$arrayBulk = array("docs"=>$designDocs);
			$result = $this->cDB->saveDocument(true)->execute($arrayBulk);


//			$bulkDocs = $array();
			//$result = $this->cDB->saveDocument()->execute(array('_id'=>'generateBill','cd_doc_type' => 'bill_counter', 'current' => 0, 'current_month' => 0));
			echo "<pre>";
//			$result = $this->cDB->saveDocument()->execute($replication);
			print_r($result);			
			echo "</pre>";

		}
	}