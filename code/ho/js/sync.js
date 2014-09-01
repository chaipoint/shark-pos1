$(document).ready(function(){

/* Function To Update Staff Data */
$('#staff_synk').on('click',function(){
 $('#progress').show();	
 $.ajax({
	 type: 'POST',
	 url: "cpos_to_ho.php",
	 data : {'action':'updateStaff'}
	 
  }).done(function(response) {
  	 var $res =  $.parseJSON(response); //Parse result of response
	 console.log(response);
	 $('#progress').hide();
	 if($res.error){ //If Their Exists any problem in Update then show errors
	    bootbox.alert($res.msg); 
	 }else{
		 bootbox.alert($res.msg); 
	}
	
	});
});

/* Function To Update Store Data */
$('#store_synk').on('click',function(){
  $('#progress').show();
  $.ajax({
	 type: 'POST',
	 url: "cpos_to_ho.php",
	 data : {'action':'updateStore'}

  }).done(function(response) {
	 var $res =  $.parseJSON(response); //Parse result of response
	 console.log(response);
	 $('#progress').hide();
	 if($res.error){ //If Their Exists any problem in Update then show errors
	    bootbox.alert($res.msg); 
	 }else{
		 bootbox.alert($res.msg); 
	}
	
	});
});

/* Function To Update Config Data */
$('#config_synk').on('click',function(){
 $('#progress').show();
 $.ajax({
	 type: 'POST',
	 url: "cpos_to_ho.php",
	 data : {'action':'updateConfig'}

  }).done(function(response) {
	 var $res =  $.parseJSON(response); //Parse result of response
	 console.log(response);
	 $('#progress').hide();
	 if($res.error){ //If Their Exists any problem in Update then show errors
	    bootbox.alert($res.msg); 
	 }else{
		 bootbox.alert($res.msg); 
	}
	
	});
});

/* Function To Upload Bill Data */
$('#bill_synk').on('click',function(){
 $('#progress').show();
  $.ajax({
	 type: 'POST',
	 url: "ho_to_cpos.php",
	 data : {'action':'uploadBill'}

  }).done(function(response) {
	 var $res =  $.parseJSON(response); //Parse result of response
	 console.log(response);
	 $('#progress').hide();
	 if($res.error){ //If Their Exists any problem in Update then show errors
	    bootbox.alert($res.msg); 
	 }else{
		 bootbox.alert($res.msg); 
	}
	
	});
});

/* Function To Upload Bill Data */
$('#updated_bill_synk').on('click',function(){
 $('#progress').show();
  $.ajax({
	 type: 'POST',
	 url: "ho_to_cpos.php",
	 data : {'action':'uploadUpdatedBill'}

  }).done(function(response) {
	 var $res =  $.parseJSON(response); //Parse result of response
	 console.log(response);
	 $('#progress').hide();
	 if($res.error){ //If Their Exists any problem in Update then show errors
	    bootbox.alert($res.msg); 
	 }else{
		 bootbox.alert($res.msg); 
	}
	
	});
});

   
});
