<?php
	class utils extends App_config{
		function __construct(){
			parent::__construct();
		}
		function sync(){
			$return = array('error'=>false, 'message'=>'');
			$result = array('error'=>false, 'message'=>'');
			if(array_key_exists('mode', $_GET) && !empty($_GET['mode']))	{
				switch($_GET['mode']){
					case 'staff_sync_bt':
						$location = $_SESSION['user']['location']['id'];
						$result = $this->getStaff($location,true);
						break;
					case 'store_sync_bt':
						//$store_data = $this->getInstallationConfig();
						$store = $_SESSION['user']['store']['id'];//= $store_data['data']['store_config']['store_id'];
						$_GET['store'] = $store;
						$_GET['only_store'] = true;
						$result = $this->getStore(true);
						break;
					case 'billing_sync_bt':
							$result = $this->billing();
						break;
					case 'config_sync_bt':
							$result = $this->repConfig();
					break;
					case 'design_sync_bt':
							$result = $this->repDesign();
					break;
					case 'retail_customers_sync_bt':
							$result = $this->repRetailCustomers();
					break;
					case 'billing_stop_sync_bt':
							$rep = $this->cDB->getActiveTask();//json_decode('[{"pid":"<0.19819.0>","checkpoint_interval":5000,"checkpointed_source_seq":259,"continuous":true,"doc_id":null,"doc_write_failures":0,"docs_read":1,"docs_written":1,"missing_revisions_found":1,"progress":100,"replication_id":"21b9ee56f33d6ec7e12c737f46f4a7b6+continuous","revisions_checked":1,"source":"http://127.0.0.1:5984/testing/","source_seq":259,"started_on":1408442813,"target":"http://pos:*****@54.249.247.15:5984/vente_ho_db/","type":"replication","updated_on":1408443075},{"pid":"<0.20189.0>","checkpoint_interval":5000,"checkpointed_source_seq":259,"continuous":true,"doc_id":null,"doc_write_failures":0,"docs_read":256,"docs_written":256,"missing_revisions_found":256,"progress":100,"replication_id":"cee10645c91448e7e411aff39d56a9f1+continuous","revisions_checked":257,"source":"testing","source_seq":259,"started_on":1408443071,"target":"rk","type":"replication","updated_on":1408443076}]',true);
							//print_r($rep);
							if(array_key_exists(0, $rep)){
								foreach($rep as $key => $value){
									$result = $this->cDB->replicate()->execute(array('replication_id'=>$value['replication_id'], 'cancel'=>true));
								}
							}
					break;
				}
			}else{
				$return['error'] = true;
				$return['message'] = "Not allowed to follow this action";
			}
			//print_r($result);
			if(array_key_exists('error', $result) && $result['error']==true){
				$return['error'] = true;
				$return['message'] = 'OOPS! Some Problem Please Cotact Admin.';								
			}
			return  json_encode($return);
		}
		function repRetailCustomers(){
			//echo $_SESSION['user']['store']['location']['id'];
			//echo '<pre>';
			//print_r($_SESSION['user']);
			//echo '</pre>';
			$return = array('error'=>false, 'message'=>'');
			$source = $this->cDB->getRemote();
			$target = $this->cDB->getUrl().$this->cDB->getDB();
			$result = $this->cDB->replicate()->execute(array('source'=>$source, 'target'=>$target, 'filter'=>'doc_replication/retail_customer_replication', 'query_params'=>array("location"=>$_SESSION['user']['location']['id'])));
			if(array_key_exists('ok', $result) && $result['ok']){

			}else{
				$return = array('error'=>true, 'message'=>'OOPS! Some Problem Contact Admin');
			}			
			return $return;
		}
		function repConfig(){
			$return = array('error'=>false, 'message'=>'');
			$source = $this->cDB->getRemote();
			$target = $this->cDB->getUrl().$this->cDB->getDB();
			$result = $this->cDB->replicate()->execute(array('source'=>$source, 'target'=>$target, 'filter'=>'doc_replication/config_replication', "continuous" => true));
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
				$return = array('error'=>true,'message'=>'OOPS! Some Problem Contact Admin');
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
		}
		function init(){
			$designDocs = array();
			$designList = $this->cDB->getDocs()->setParam(array('startkey'=>'"_design/"','endkey'=>'"_design0"'))->execute();
 			$designs = array();
		      if(array_key_exists('rows', $designList) && count($designList['rows']) >0){
		        $rows = $designList['rows'];
		        foreach($rows as $k => $v){
		          $designs[$v['key']] = $v['value']['rev']; 
		        }
		      }
		      
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
		              'getuser' =>  "function(head, req) { var userfound = false;    var jData = (JSON.parse(req.body));var username = jData.username; var password = jData.password; var obj = new Object(); obj.error = false; obj.message = ''; obj.data = new Object();      while(row = getRow()){ if(row.key == username) { if((row.value.password).toUpperCase() == (password).toUpperCase()){ obj.data = row.value; } else{ obj.error = true; obj.message = 'Password Wrong';} userfound = true; break;} } if( !userfound ) { obj.error = true; obj.message = 'User Not Authorized To Login';} return JSON.stringify(obj);}"
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
		              'sales_register' =>  "function(head,req) { var billList = new Object(); var shift_cash = new Object(); billList.data = new Object(); var payment_type = new Object(); payment_type.count = new Object(); payment_type.amount = new Object(); var bill_status = new Object(); bill_status.count = new Object(); bill_status.amount = new Object(); var cashSale = 0; var creditSale = 0; var ppcSale = 0; var ppaSale = 0;  var cashinDelivery = 0; while(row = getRow()) { if(row.doc) { if( !( row.doc.bill_no in billList.data ) ) {  if(row.doc.bill_status != 'Cancelled'){ if(row.doc.payment_type in payment_type.count) { payment_type.amount[row.doc.payment_type] += (1 * row.doc.due_amount); payment_type.count[row.doc.payment_type] += 1; } else { payment_type.amount[row.doc.payment_type] = (1 * row.doc.due_amount); payment_type.count[row.doc.payment_type] = 1; } } if(row.doc.bill_status in bill_status.count) { bill_status.count[row.doc.bill_status] += 1; bill_status.amount[row.doc.bill_status] += (1 * row.doc.due_amount); }else{ bill_status.count[row.doc.bill_status] = 1; bill_status.amount[row.doc.bill_status] = (1 * row.doc.due_amount); } billList.data[row.doc.bill_no] = new Object(); billList.data[row.doc.bill_no] = row.doc; if(row.doc.bill_status == 'Paid' && row.doc.payment_type == 'cash') { if( row.doc.shift in shift_cash) {shift_cash[row.doc.shift] += (1* row.doc.due_amount);} else{ shift_cash[row.doc.shift] = (1* row.doc.due_amount);} cashSale += (1* row.doc.due_amount); } if(row.doc.bill_status == 'Paid' && row.doc.payment_type == 'ppc') { ppcSale += (1* row.doc.due_amount); } if(row.doc.bill_status == 'Paid' && row.doc.payment_type == 'ppa') { ppaSale += (1* row.doc.due_amount); } if(row.doc.is_credit == 'Y' && row.doc.payment_type == 'credit') { creditSale += (1* row.doc.due_amount); } if(row.doc.is_cod == 'Y' && row.doc.bill_status == 'CoD') { cashinDelivery += (1* row.doc.due_amount); } } } } billList.bill_status = bill_status; billList.payment_type = payment_type; billList.cash_sale = cashSale; billList.creditSale = creditSale; billList.ppcSale = ppcSale; billList.ppaSale = ppaSale; billList.cash_indelivery = cashinDelivery; billList.shift_cash = shift_cash; return JSON.stringify(billList);}",
		              'todays_sale' =>  "function(head,req) { var billList = new Object(); var billProcessed = new Object();  while(row = getRow()){ if(row.doc) { if( ! (row.key[1] in billProcessed)){ billProcessed[row.key[1]] = '';  if (row.doc.bill_status == 'Paid') { var itemList = row.doc.items; for(var items in itemList){ if((typeof billList[itemList[items].category_name]) != 'object'){ billList[itemList[items].category_name] = new Object();  billList[itemList[items].category_name][row.doc.payment_type] = ( 1 * itemList[items].netAmount); }else{ if(row.doc.payment_type in billList[itemList[items].category_name]){  billList[itemList[items].category_name][row.doc.payment_type] += (1 * itemList[items].netAmount); }else{ billList[itemList[items].category_name][row.doc.payment_type] = (1 * itemList[items].netAmount); } } } } } } } return JSON.stringify(billList);}"
		          )
		      );
		      $designDocs[] = array(
		        '_id'=>'_design/store',
		        'language' => 'javascript', 
		        'views' => array(
		          "store_mysql_id"=>array(
		            "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'store_master'){ emit(doc.mysql_id,{name:doc.name, staff:doc.store_staff, location:doc.location}); } }"
		            ),
		          "store_shift"=>array(
		            "map"=>"function(doc) { if(doc.cd_doc_type == 'store_shift'){ var date = doc.login_time.split(' '); emit(date[0], null); } }"
		            ),
		        ),
		        'updates' => array(
		          "store_shift"=>"function(doc,req){ if(doc) { var data = new Object(); if(req.query.type == 'day_end') { doc.day.petty_cash_balance.petty_expense = req.query.petty_expense; doc.day.petty_cash_balance.closing_petty_cash = req.query.closing_petty_cash; doc.day.petty_cash_balance.inward_petty_cash = req.query.inward_petty_cash; doc.day.end_time = req.query.time; doc.day.end_fullcash = req.query.cash; doc.day.end_login_id = req.query.login; doc.day.end_staff_name = req.query.name } else if(req.query.type == 'shift_start') {data.shift_no = doc.shift.length+1;  doc.shift.push({end_petty_cash:'',end_cash_inbox:'',start_time:req.query.time, start_login_id:req.query.login, start_staff_name:req.query.name, counter_no:req.query.counter_no,shift_no:data.shift_no, end_staff_name:'', end_time:'',petty_cash_balance:{opening_petty_cash:req.query.opening_petty_cash,petty_expense:'',closing_petty_cash:'',inward_petty_cash:''}}); }else if(req.query.type == 'shift_end') { req.query.shift_no = doc.shift.length-1; doc.shift[req.query.shift_no].end_time = req.query.time; doc.shift[req.query.shift_no].end_petty_cash = req.query.end_petty_cash; doc.shift[req.query.shift_no].end_cash_inbox = req.query.box_cash;doc.shift[req.query.shift_no].end_login_id = req.query.login;doc.shift[req.query.shift_no].end_staff_name = req.query.name;  doc.shift[req.query.shift_no].petty_cash_balance.petty_expense = req.query.petty_expense; doc.shift[req.query.shift_no].petty_cash_balance.closing_petty_cash = req.query.closing_petty_cash; doc.shift[req.query.shift_no].petty_cash_balance.inward_petty_cash = req.query.inward_petty_cash;} else if(req.query.type == 'cash_reconciliation') {delete req.query.type; doc.day.cash_reconciliation = new Object(); for (var name in req.query) { doc.day.cash_reconciliation[name] = req.query[name]; }} return [doc,JSON.stringify(data)];}}"
		        )
		      );

		      /*"store_shift": {
		           "map": "function(doc) { if(doc.cd_doc_type == 'store_shift'){ emit(doc.date, null); } }"
		       }
		   },
		   "updates": {
		       "store_shift": "function(doc,req){ if(doc) { if(req.query.type == 'day_end') { doc.time.end = req.query.time; doc.time.end_cash = req.query.cash; doc.time.end_staff = req.query.end_staff;  doc.end_staff = req.query.end_staff; } else if(req.query.type == 'shift_start') { doc.shift.push({start:req.query.time, end:'' , start_cash: req.query.cash, end_cash: '', start_staff:req.query.start_staff, end_staff:'' }) }else if(req.query.type == 'shift_end') { req.query.shift_no = doc.shift.length-1; doc.shift[req.query.shift_no].end = req.query.time; doc.shift[req.query.shift_no].end_cash = req.query.cash; doc.shift[req.query.shift_no].end_staff = req.query.end_staff; }  return [doc,JSON.stringify(req)];}}"
		   }
		}*/
		      $designDocs[] = array(
		        '_id'=>'_design/login',
		        'language' => 'javascript', 
		        'views' => array(
		          "logout"=>array(
		            "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'login_history'){ emit(doc._id, doc); } }"
		            ),
		          "login_no_mysql_id"=>array(
		            "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'login_history' && !doc.mysql_id && doc.logout_time!=''){ emit(doc._id,null); } }"
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
		              	"bill_replication" => "function(doc, req){ if(doc.cd_doc_type && ( doc.cd_doc_type == 'store_bill' || doc.cd_doc_type == 'petty_expense' || doc.cd_doc_type == 'petty_inward' || doc.cd_doc_type == 'store_shift' || doc.cd_doc_type == 'login_history' ) && !doc.mysql_id){ return true;} else { return false;}}",
		              	"config_replication" => "function(doc, req){ if(doc.cd_doc_type && doc.cd_doc_type == 'config_master'){ return true;} else { return false;}} ",
		              	"retail_customer_replication" => "function(doc, req){ if(doc.cd_doc_type && doc.cd_doc_type == 'retail_customers' && doc.location_id == req.query.location){ return true;} else { return false;}} "
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
		                ),
		              "handle_all_bills" => array(
		                  "map" => "function(doc){ if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill') { var created_time = (doc.time.created).split(' '); var updated_time = doc.time.updated; emit([created_time[0], doc.bill_no, (doc.parent ? 1 : 0) , updated_time],null);} }"
		                ),
		              "get_expense" => array(
		                  "map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type=='petty_expense'){ emit(doc.expense_date, null); } }"
		                ),
		              "store_shift" => array(
		                  "map" => "function(doc) { if(doc.cd_doc_type == 'store_shift'){ var date = doc.login_time.split(' '); emit(date[0], null); } }"
		                ),
					"retail_customer_list" =>  array(
				           "map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'retail_customers'){ emit(1*doc.mysql_id, null); } }"
       					)

		            ),
		          "updates" => array(
		              "insert_mysql_id" => "function(doc,req){ if(doc) { if(doc) { doc.mysql_id = req.query.mysql_id; return [doc,req.query.mysql_id]; } } }"
		            ),
		          "lists" => array(
		              "sales_register" => "function(head, req) { var billList=new Object;billList.data=new Object;var payment_type=new Object;payment_type.count=new Object;payment_type.amount=new Object;var bill_status=new Object;bill_status.count=new Object;bill_status.amount=new Object;var cashSale=0;var ppcSale=0;var cashinDelivery=0;var store_bill_list=new Object;while(row=getRow()){if(row.doc){if(!(row.doc.store_id in billList.data)){billList.data[row.doc.store_id]=new Object}if(!(row.doc.bill_no in billList.data[row.doc.store_id])){billList.data[row.doc.store_id][row.doc.bill_no]=new Object;if(row.doc.bill_status_id==68&&row.doc.payment_type=='cash'){cashSale+=1*row.doc.due_amount}if(row.doc.bill_status_id==68&&row.doc.payment_type=='ppc'){ppcSale+=1*row.doc.due_amount}if(row.doc.bill_status_id==65&&row.doc.payment_type=='ppc'){cashinDelivery+=1*row.doc.due_amount}}}}billList.payment_type=payment_type;billList.cash_sale=cashSale;billList.ppcSale=ppcSale;billList.cash_indelivery=cashinDelivery;delete billList.data; return JSON.stringify(billList); }",
		              "petty_expense" => "function(head, req) {var sum=0;while(row=getRow()){if(row.doc){sum+=(1*row.doc.expense_amount);}} return sum.toString();}"
		            )
		      );

		      $designDocs[] = array(
		        "_id" => "_design/petty_expense",
		          "language" => "javascript",
		          "views" => array(
		              "get_expense" => array(
		                  "map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type=='petty_expense'){ emit(doc.expense_date, null); } }"
		                ),
		              "expense_no_mysql_id" => array(
		                "map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'petty_expense' && !doc.mysql_id){ emit(doc.expense_date,null); } }"
		                ),
		              "get_inward" => array(
		                "map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type=='petty_inward'){ emit(doc.inward_date, doc.inward_amount); } }"
		                )
		            )
		      );
             $designDocs[] = array(
		        "_id" => "_design/customers",
        		"language" => "javascript",
          		"views" => array(
              		"retail_customer_list" => array(
                  		"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'retail_customers'){ emit(doc.name,doc.mysql_id * 1); } }",
	                )
            	)
      		);


		      if(count($designDocs) > 0){
		        foreach($designDocs as $key => $value){
		          if(array_key_exists($value['_id'], $designs)){
		            $designDocs[$key]['_rev'] = $designs[$value['_id']];
		          }
		        }
		      }



			$arrayBulk = array("docs"=>$designDocs);
			$result = $this->cDB->saveDocument(true)->execute($arrayBulk);


//			$bulkDocs = $array();
			//$result = $this->cDB->saveDocument()->execute(array('_id'=>'generateBill','cd_doc_type' => 'bill_counter', 'current' => 0, 'current_month' => 0));
			echo "<pre>";
//			$result = $this->cDB->saveDocument()->execute($replication);
			print_r($result);			
			echo "</pre>";

		}/**/
		function generate_rep_running_flag(){
			$activeTask = $this->cDB->getActiveTask();
			echo '<script>var is_rep_running = '.(array_key_exists(0, $activeTask) ? 'true' : 'false').'; $(document).ready(function(){toggleRepBT ();});</script>';
		}
	}