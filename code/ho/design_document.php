<?php 
require_once 'common/couchdb.phpclass.php';

echo init();
function init(){
  $couch = new CouchPHP();
   $billCounter = array('_id'=>'generateBill','cd_doc_type' => 'bill_counter', 'current' => 0, 'current_month' => 0);
   $staff = array(
    '_id'=>'_design/staff',
    'language' => 'javascript', 
    'views' => array(
     "staff_mysql_id"=>array(
      "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'staff_master'){ emit([doc.mysql_id,doc.code], null); } }"
      ),
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
   $billing = array(
    '_id'=>'_design/billing',
    'language' => 'javascript', 
    'views' => array(
     "bill_no"=>array(
      "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill'){ emit(doc.bill_no,null); } }"
      ),
     "bill_by_date"=>array(
       "map"=>"function(doc) {if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill') { var bill_date = doc.bill_time.split(' '); emit([bill_date[0],doc.payment_type],parseInt(doc.due_amount)); }}",
                "reduce" => "function(key, value){ return sum(value);}"
      ),
     "bill_by_no_mid" => array(
               "map"=> "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill' && !doc.mysql_id){ emit(doc.bill_no, null); } }"
           ),
           "bill_by_current_date" => array(
            "map" => "function(doc) {if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill') { var bill_date = doc.bill_time.split(' '); emit([bill_date[0],doc.payment_type],doc); }}"
           ),
           "sales_summary" => array(
            "map" => "function(doc) {if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill') { var bill_date = doc.bill_time.split(' '); for(var product_id in doc.items){ emit([bill_date[0],doc.items[product_id].categroy_name,doc.payment_type],parseInt(doc.items[product_id].netAmount));}}}",
            "reduce" => "function(key,value){ var sum=0; value.forEach(function(v){sum+= parseInt(v);}); return sum; }"
           )
    ),
    'updates' => array( 
           'getbillno' =>  "function(doc,req){ if(req.query.month) { if(doc.current_month != req.query.month){doc.current = 0; doc.current_month = req.query.month;} var newCurrent =  doc.current+1; doc.current = newCurrent; return [doc,newCurrent.toString()];}}",
           'insert_mysql_id' => "function(doc,req){ doc.mysql_id = req.query.mysql_id; return [doc,req.query.mysql_id]}",
       )
   );
   $store = array(
    '_id'=>'_design/store',
    'language' => 'javascript', 
    'views' => array(
     "store_mysql_id"=>array(
      "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'store_master'){ emit(doc.mysql_id,doc); } }"
      )
    )
   );
   $logout = array(
    '_id'=>'_design/login',
    'language' => 'javascript', 
    'views' => array(
     "logout"=>array(
      "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'login_history'){ emit(doc._id, null); } }"
      )
    ),
    "updates" => array('login_history'=>"function(doc, req){ doc.logout_time = req.query.logout_time; return [doc,'true'];"),
   );
   $replication = array(
    '_id'=>'_design/doc_replication',
    'language' => 'javascript', 
    'views' => array(
     "replication"=>array(
      "map"=>"function(doc) { if(doc.cd_doc_type && doc.cd_doc_type == 'master'){ emit(doc.location.id, doc); } }"
      )
    ), 
    'filters'=> array(
     'staff_replication' => "function(doc,req){ if(doc.cd_doc_type && doc.cd_doc_type == 'staff_master' && doc.location_id == req.query.location) { return  true;}else{return  false;} }",
     'store_replication' => "function(doc,req){ if(doc.cd_doc_type && doc.cd_doc_type == 'store_master' && doc.mysql_id == req.query.mysql_id) {return  true;}else{return  false;} }",
     'design_replication' => "function(doc,req){ if(doc.language || (doc.cd_doc_type && doc.cd_doc_type == 'bill_counter')) {return  true;}else{return  false;} }",
     'bill_replication' =>"function(doc, req){ if(doc.cd_doc_type && doc.cd_doc_type == 'store_bill' && !doc.mysql_id){ return true;} else { return false;}} "
    ),
   );
   $config = array(
    "_id" => "_design/config",
       "language" => "javascript",
       "views" => array(
         "config_list" => array(
           "map" => "function(doc) { if(doc.cd_doc_type && doc.cd_doc_type=='config_master'){  emit(doc.category_name, doc); }}"
          )
        )
   );

   $sales = array(
    "_id" => "_design/sales",
       "language" => "javascript",
       "views" => array(
         "top_store" => array(
           "map" => "function(doc) {if(doc.cd_doc_type && doc.cd_doc_type=='store_bill'){ var bill_date=doc.bill_time.split(' ');emit([bill_date[0],doc.store_name], parseInt(doc.due_amount));}}",
           "reduce" => "function(key,value) {var sum=0; value.forEach(function(v){sum+= parseInt(v);}); return (sum); }"
          )
        )
   );

   $arrayBulk = array("docs"=>array($billCounter, $billing, $store, $staff ,$replication,$logout,$sales, $config));
   $result = $couch->saveDocument(true)->execute($arrayBulk);


//   $bulkDocs = $array();
   //$result = $this->cDB->saveDocument()->execute(array('_id'=>'generateBill','cd_doc_type' => 'bill_counter', 'current' => 0, 'current_month' => 0));
   echo "<pre>";
//   $result = $this->cDB->saveDocument()->execute($replication);
   print_r($result);   
   echo "</pre>";

  }