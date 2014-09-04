<?php 
require_once 'common/couchdb.phpclass.php';

echo init();
function init(){
  $couch = new CouchPHP();
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