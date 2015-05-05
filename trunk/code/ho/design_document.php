<?php 
require_once 'common/couchdb.phpclass.php';
require_once 'constant.php';
require_once 'common/logger.php';
	
$param = $_GET['param'];
echo $param;
switch ($param){
	case 'init':
		echo init();
		break;

	case 'deleteDoc' :
		echo deleteDoc();
		break;
		
	case 'cardSale':
		echo cardSale();
		break;
}
echo cardSale();
function cardSale(){
	$couch = new CouchPHP();
	$html = array();
	$no_bill = $unsuccessful = $successful = $counter = 0;
	echo 'sdf';
	$billData = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_CARD_NO_MYSQL_ID)->setParam(array('include_docs'=>'true'))->execute();
	//echo '<pre>'; print_r($billData); echo '</pre>'; die();
	//$logger->debug("URL to sccess data ".$couch->getLastUrl());
	if(array_key_exists('rows', $billData)){ 
 		foreach($billData['rows'] as $key => $value){ 
			$doc = $value['doc'];
 			$docKey = $value['key'];
 			$dValue['doc'] = $doc;
			if(empty($doc['mysql_id'])){
			$docsData = array(	"_id"  => $doc['_id'],
								"_rev" => $doc['_rev'],
								"bill_no" => $doc['invoice_number'],
								"bill_seq" => $doc['bill'],
								"bill_time" => $doc['time'],
								"store_id" => $doc['store_id'],
								"store_name" => $doc['store_name'],
								"counter" => $doc['counter'],
								"shift" => $doc['shift'],
								"staff_id" => $doc['staff_id'],
								"staff_name" => $doc['staff_name'],
								"card_no" => $doc['card_no'],
								"card_type" => $doc['card_type'],
								"txn_type" => $doc['txn_type'], 
								"txn_no" => $doc['txn_no'], 
								"amount" => $doc['amount'],
								"approval_code" => $doc['approval_code'],
								"status" => $doc['status'],
							);
			
			$logger->debug("INSERT ORDER ARRAY ".json_encode($docsData));
			$db->func_array2insert("cp_pos_cardsale", $docsData);
			$insertId = $db->db_insert_id();	
			if($insertId > 0){
				$returnResult = $couch->getDesign('design_ho')->getUpdate('insert_mysql_id', $docsData['_id'])->setParam(array('mysql_id'=>$insertId))->execute();				
				if($returnResult){				    	
					$successful = 1;
				}else{
				    $unsuccessful = 1;
				}
			}
		}else{
			$no_bill = 1;
		}
 	$counter++;}}
	
	if($successful==1){
		$logger->debug("Success: Bill Uplaoded Successfully");
  		
  		$html['error'] = false;
		$html['update'] = true;
		$html['msg'] = "$counter Bill ".DATA_UPLOADED."";
    } else if($unsuccessful==1){

    	$logger->debug("ERROR: Some Error! Please Contact Admin");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = ERROR;

    }else{

    	$logger->debug("ERROR: NO Bill To Be Upload");
  		$html['error'] = true;
		$html['update'] = false;
		$html['msg'] = NO_DATA_AVAILABLE_ERROR;
    }
	
	$result = json_encode($html,true);
	$logger->debug("End OF Uplaod Bill Function");
	echo $result;
	
	


}
//echo init();
function deleteDoc(){
	$couch = new CouchPHP();
	$deleteArray = array();
	$result = $couch->getDesign('billing')->getView('handle_updated_bills')->setParam(array("startkey" => '["2015-01-01"]',"endkey" => '["2015-01-31",{},{},{}]'))->execute();
	$i=0;
	echo '<pre>';print_r($result);echo '</pre>';die();
	foreach($result['rows'] as $key => $value){
		$deleteArray[$i]["_id"] = $value['id'];
		$deleteArray[$i]["_rev"] = $value['value'];
		$deleteArray[$i]["_deleted"] = true;
		$i++;
	}
	//$res = $couch->saveDocument(true)->execute(array("docs"=>$deleteArray));
	//echo'<pre>'; print_r($deleteArray); echo'</pre>';
		
}
function init(){
      $couch = new CouchPHP();
      $designDocs = array();
      $designList = $couch->getDocs()->setParam(array('startkey'=>'"_design/"','endkey'=>'"_design0"'))->execute();
      $designs = array();
      if(array_key_exists('rows', $designList) && count($designList['rows']) >0){
        $rows = $designList['rows'];
        foreach($rows as $k => $v){
          $designs[$v['key']] = $v['value']['rev']; 
        }
      }
      $obj = new stdClass();
      $designDocs[] = array('_id'=>'generateBill','cd_doc_type' => 'bill_counter', 'current' => 0, 'current_month' => 0, 'bill_array'=>$obj);
      $designDocs[] = array('_id'=>'generateppcBill','cd_doc_type' => 'ppc_bill_counter', 'current' => 0, 'current_date' => 0);

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
          "staff_name"=>array(
              "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'staff_master'){ emit(doc.name, doc.mysql_id); } }",
            ),
        ),
        'lists' => array( 
              'getuser' =>  "function(head, req) { var userfound = false;    var jData = (JSON.parse(req.body));var username = jData.username; var password = jData.password; var obj = new Object(); obj.error = false; obj.message = ''; obj.data = new Object();      while(row = getRow()){ if(row.key == username) { if((row.value.password).toUpperCase() == (password).toUpperCase()){ obj.data = row.value; } else{ obj.error = true; obj.message = 'Password Wrong';} userfound = true; break;} } if( !userfound ) { obj.error = true; obj.message = 'User Not Authorized To Login';} return JSON.stringify(obj);}"
          )
      );
      $designDocs[] = array(
        '_id'=>'_design/card_sale',
        'language' => 'javascript', 
        'views' => array(
          "get_sale"=>array(
              "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'card_sale'){ var created_date = (doc.time).split(' '); emit([created_date[0],doc.store_id,doc.counter], null); } }",
            )
          ),
        'lists' => array( 
              'todays_sale' =>  "function(head, req) { var saleList = new Object(); var shift_cash = new Object(); var ppcLoad = 0; var ppcActive = 0; var ppaLoad = 0; var ppaActive = 0;   while(row = getRow()){ if(row.doc.status!='cancel') { if(row.doc.card_type=='ppa' && row.doc.txn_type=='load'){ if( row.doc.shift in shift_cash) { shift_cash[row.doc.shift] += (1* row.doc.amount);} else{ shift_cash[row.doc.shift] = (1* row.doc.amount);} ppaLoad += (1 * row.doc.amount);} if(row.doc.card_type=='ppa' && row.doc.txn_type=='active'){ if( row.doc.shift in shift_cash) { shift_cash[row.doc.shift] += (1* row.doc.amount);} else{ shift_cash[row.doc.shift] = (1* row.doc.amount);} ppaActive += (1 * row.doc.amount);} if(row.doc.card_type=='ppc' && row.doc.txn_type=='load'){ if( row.doc.shift in shift_cash) { shift_cash[row.doc.shift] += (1* row.doc.amount);} else{ shift_cash[row.doc.shift] = (1* row.doc.amount);} ppcLoad += (1 * row.doc.amount);} if(row.doc.card_type=='ppc' && row.doc.txn_type=='active'){ if( row.doc.shift in shift_cash) { shift_cash[row.doc.shift] += (1* row.doc.amount);} else{ shift_cash[row.doc.shift] = (1* row.doc.amount);} ppcActive += (1 * row.doc.amount);}}}  if(ppcLoad!=0){saleList.ppcLoad = ppcLoad;} if(ppcActive!=0){saleList.ppcActive = ppcActive;} if(ppaLoad!=0){saleList.ppaLoad = ppaLoad;} if(ppaActive!=0){saleList.ppaActive = ppaActive;} saleList.shift_cash = shift_cash; return JSON.stringify(saleList);}"
          )
      );
      $designDocs[] = array(
        '_id'=>'_design/ppc_detail',
        'language' => 'javascript', 
        'views' => array(
          "initialize_detail"=>array(
              "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'last_initialize'){ var date = doc.time.split(' '); emit([date[0],doc.store_id,doc.counter], null); } }",
            ),
          "last_bill"=>array(
              "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'last_ppc_bill'){ var date = doc.time.split(' '); emit(date[0], null); } }",
            )
            ),
        'updates' => array( 
              'getbillno' =>  "function(doc,req){ if(req.query.date) { if(doc.current_date != req.query.date){doc.current = 0; doc.current_date = req.query.date;} var newCurrent =  doc.current+1; doc.current = newCurrent; return [doc,newCurrent.toString()];}}",
              'change_status' => "function(doc,req){ if(doc) {  doc.status = req.query.status; doc.balance = req.query.balance; doc.txn_no = req.query.txn_no; doc.approval_code = req.query.approval_code; return [doc,req.query.status];  } }",
          )
        
      );
      $designDocs[] = array(
        '_id'=>'_design/billing',
        'language' => 'javascript', 
        'views' => array(
              "handle_updated_bills" => array(
                "map" => "function(doc){ if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill') { var created_time = (doc.time.created).split(' '); var updated_time = doc.time.updated; emit([created_time[0], doc.bill_no, (doc.parent ? 1 : 0) , updated_time],null);} }"
              ),
			  "bill_by_store_counter" => array(
				"map" => "function(doc){ if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill') { var created_time = (doc.time.created).split(' '); var updated_time = doc.time.updated; emit([created_time[0], doc.store_id, doc.counter], doc.bill_no);} }"
				),
              "bill_by_order" => array(
                "map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill' && doc.order_no > 0){ emit(doc.order_no,null); } }"
              )
        ),
        'updates' => array( 
              'getbillno' =>  "function(doc,req){ if(req.query.month) { if(doc.current_month != req.query.month){doc.current = 0; doc.current_month = req.query.month;} var newCurrent =  doc.current+1; doc.current = newCurrent; return [doc,newCurrent.toString()];}}",
          ),
        'lists' => array( 
              'sales_register' =>  "function(head,req) { var billList = new Object(); var shift_cash = new Object(); billList.data = new Object(); var payment_type = new Object(); payment_type.count = new Object(); payment_type.amount = new Object(); var bill_status = new Object(); bill_status.count = new Object(); bill_status.amount = new Object(); var cashSale = 0; var creditSale = 0; var ppcSale = 0; var ppaSale = 0;  var cashinDelivery = 0; while(row = getRow()) { if(row.doc) { if( !( row.doc.bill_no in billList.data ) ) {  if(row.doc.bill_status != 'Cancelled'){ if(row.doc.payment_type in payment_type.count) { payment_type.amount[row.doc.payment_type] += (1 * row.doc.due_amount); payment_type.count[row.doc.payment_type] += 1; } else { payment_type.amount[row.doc.payment_type] = (1 * row.doc.due_amount); payment_type.count[row.doc.payment_type] = 1; } } if(row.doc.bill_status in bill_status.count) { bill_status.count[row.doc.bill_status] += 1; bill_status.amount[row.doc.bill_status] += (1 * row.doc.due_amount); }else{ bill_status.count[row.doc.bill_status] = 1; bill_status.amount[row.doc.bill_status] = (1 * row.doc.due_amount); } billList.data[row.doc.bill_no] = new Object(); billList.data[row.doc.bill_no] = row.doc; if(row.doc.bill_status == 'Paid' && row.doc.payment_type == 'cash') { if( row.doc.shift in shift_cash) {shift_cash[row.doc.shift] += (1* row.doc.due_amount);} else{ shift_cash[row.doc.shift] = (1* row.doc.due_amount);} cashSale += (1* row.doc.due_amount); } if(row.doc.bill_status == 'Paid' && row.doc.payment_type == 'ppc') { ppcSale += (1* row.doc.due_amount); } if(row.doc.bill_status == 'Paid' && row.doc.payment_type == 'ppa') { ppaSale += (1* row.doc.due_amount); } if(row.doc.is_credit == 'Y' && (row.doc.payment_type == 'credit' || row.doc.payment_type == 'caw')) { creditSale += (1* row.doc.due_amount); } if(row.doc.is_cod == 'Y' && row.doc.bill_status == 'CoD') { cashinDelivery += (1* row.doc.due_amount); } } } } billList.bill_status = bill_status; billList.payment_type = payment_type; billList.cash_sale = cashSale; billList.creditSale = creditSale; billList.ppcSale = ppcSale; billList.ppaSale = ppaSale; billList.cash_indelivery = cashinDelivery; billList.shift_cash = shift_cash; return JSON.stringify(billList);}",
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
            "map"=>"function(doc) { if(doc.cd_doc_type == 'store_shift'){ var date = doc.login_time.split(' '); emit([date[0],doc.store_id], null); } }"
            ),
        ),
        'updates' => array(
          "store_shift"=>"function(doc,req){ if(doc) { var data = new Object(); if(req.query.type == 'day_end') { doc.day.petty_cash_balance.petty_expense = req.query.petty_expense; doc.day.petty_cash_balance.closing_petty_cash = req.query.closing_petty_cash; doc.day.petty_cash_balance.inward_petty_cash = req.query.inward_petty_cash; doc.day.end_time = req.query.time; doc.day.end_fullcash = req.query.cash; doc.day.end_login_id = req.query.login; doc.day.end_staff_name = req.query.name } else if(req.query.type == 'shift_start') {data.shift_no = doc.shift.length+1;  doc.shift.push({end_petty_cash:'',opening_cash_inbox:req.query.opening_box_cash,end_cash_inbox:'',start_time:req.query.time, start_login_id:req.query.login, start_staff_name:req.query.name, counter_no:req.query.counter_no,shift_no:data.shift_no, end_staff_name:'', end_time:'',petty_cash_balance:{opening_petty_cash:req.query.opening_petty_cash,petty_expense:'',closing_petty_cash:'',inward_petty_cash:''},cash_denomination:{qty_10:req.query.qty_10,qty_20:req.query.qty_20,qty_50:req.query.qty_50,qty_100:req.query.qty_100,qty_500:req.query.qty_500,qty_sodex:req.query.qty_sodex,qty_ticket_res:req.query.qty_restaurent}}); }else if(req.query.type == 'shift_end') { req.query.shift_no = doc.shift.length-1; doc.shift[req.query.shift_no].end_time = req.query.time; doc.shift[req.query.shift_no].end_petty_cash = req.query.end_petty_cash; doc.shift[req.query.shift_no].end_cash_inbox = req.query.box_cash;doc.shift[req.query.shift_no].end_login_id = req.query.login;doc.shift[req.query.shift_no].end_staff_name = req.query.name;  doc.shift[req.query.shift_no].petty_cash_balance.petty_expense = req.query.petty_expense; doc.shift[req.query.shift_no].petty_cash_balance.closing_petty_cash = req.query.closing_petty_cash; doc.shift[req.query.shift_no].petty_cash_balance.inward_petty_cash = req.query.inward_petty_cash;doc.shift[req.query.shift_no].cash_denomination.qty_10 = req.query.qty_10;doc.shift[req.query.shift_no].cash_denomination.qty_20 = req.query.qty_20;doc.shift[req.query.shift_no].cash_denomination.qty_50 = req.query.qty_50;doc.shift[req.query.shift_no].cash_denomination.qty_100 = req.query.qty_100;doc.shift[req.query.shift_no].cash_denomination.qty_500 = req.query.qty_500;doc.shift[req.query.shift_no].cash_denomination.qty_sodex = req.query.qty_sodex;doc.shift[req.query.shift_no].cash_denomination.qty_ticket_res = req.query.qty_restaurent;} else if(req.query.type == 'cash_reconciliation') {delete req.query.type; doc.day.cash_reconciliation = new Object(); for (var name in req.query) { doc.day.cash_reconciliation[name] = req.query[name]; }} return [doc,JSON.stringify(data)];}}"
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
              "design_replication" => "function(doc,req){ if(doc.language || (doc.cd_doc_type && ( doc.cd_doc_type == 'bill_counter' || doc.cd_doc_type == 'ppc_bill_counter' || doc.cd_doc_type == 'config_master'))) {return  true;}else{return  false;} }",
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
                  "map" => "function(doc) {if(doc.cd_doc_type && doc.cd_doc_type=='store_bill'){ var bill_date=doc.time.created.split(' ');emit([bill_date[0],doc.store_name], parseInt(doc.due_amount));}}",
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
			  "bill_by_store" => array(
				  "map" => "function(doc){ if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill') { var created_time = (doc.time.created).split(' ');   emit([created_time[0],doc.store_id],null);} }"
				),
			  "store_by_name" => array(
				  "map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'store_master'){ emit(doc.mysql_id,doc.name); } }"
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
              "retail_customer_list" => array(
                  "map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'retail_customers'){ emit(1*doc.mysql_id, null); } }"
                )

            ),
          "updates" => array(
              "insert_mysql_id" => "function(doc,req){ if(doc) { if(doc) { doc.mysql_id = req.query.mysql_id; return [doc,req.query.mysql_id]; } } }"
            ),
          "lists" => array(
              "sales_register" => "function(head, req) { var billList=new Object;billList.data=new Object;var payment_type=new Object;payment_type.count=new Object;payment_type.amount=new Object;var bill_status=new Object;bill_status.count=new Object;bill_status.amount=new Object;var cashSale=0; var creditSale = 0; var ppaSale = 0;  var ppcSale=0;var cashinDelivery=0;var store_bill_list=new Object;while(row=getRow()){if(row.doc){if(!(row.doc.store_id in billList.data)){billList.data[row.doc.store_id]=new Object}if(!(row.doc.bill_no in billList.data[row.doc.store_id])){billList.data[row.doc.store_id][row.doc.bill_no]=new Object;if(row.doc.bill_status_id==80 && row.doc.payment_type=='cash'){cashSale+=1*row.doc.due_amount}if(row.doc.bill_status_id==80 && row.doc.payment_type=='ppc'){ppcSale+=1*row.doc.due_amount} if(row.doc.bill_status == 'Paid' && row.doc.payment_type == 'ppa') { ppaSale += (1* row.doc.due_amount); } if((row.doc.payment_type == 'credit' || row.doc.payment_type == 'caw') && row.doc.bill_status != 'CoD') { creditSale += (1* row.doc.due_amount); }  if(row.doc.bill_status_id==77 && row.doc.bill_status=='CoD'){cashinDelivery+=1*row.doc.due_amount}}}}billList.payment_type=payment_type;billList.cash_sale=cashSale; billList.creditSale = creditSale; billList.ppaSale = ppaSale; billList.ppcSale=ppcSale;billList.cashinDelivery=cashinDelivery;delete billList.data; return JSON.stringify(billList); }",
              "petty_expense" => "function(head, req) {var sum=0;while(row=getRow()){if(row.doc){sum+=(1*row.doc.expense_amount);}} return sum.toString();}"
            )
      );

      $designDocs[] = array(
				"_id" => "_design/petty_expense",
				"language" => "javascript",
				"views" => array(
					"get_expense" => array(
						"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type=='petty_expense'){ emit([doc.expense_date,doc.store_id,doc.counter], null); } }"
					),
					"expense_no_mysql_id" => array(
						"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'petty_expense' && !doc.mysql_id){ emit(doc.expense_date,null); } }"
					),
					"inward_no_mysql_id" => array(
						"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'petty_inward' && !doc.mysql_id){ emit(doc.inward_date,null); } }"
					),
					"get_inward" => array(
						"map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type=='petty_inward'){ emit([doc.inward_date,doc.store_id,doc.counter], doc.inward_amount); } }"
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
      $result = $couch->saveDocument(true)->execute($arrayBulk);


//   $bulkDocs = $array();
   //$result = $this->cDB->saveDocument()->execute(array('_id'=>'generateBill','cd_doc_type' => 'bill_counter', 'current' => 0, 'current_month' => 0));
   echo "<pre>";
//   $result = $this->cDB->saveDocument()->execute($replication);
   print_r($result);   
   echo "</pre>";

  }