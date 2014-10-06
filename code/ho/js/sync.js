$(document).ready(function(){
 	var url = $.url();
    var dateParam = (url.param('sales_reg_search'));
	var date = new Date();
	if(dateParam){
		var parmArray = dateParam.split('-');
		date = new Date(parmArray[2],getMonth(parmArray[1]),parmArray[0]);
	}
$('.datepicker').datepicker('update', new Date(date.getFullYear(), date.getMonth(), date.getDate()))
    .on('changeDate', function(e){
    	console.log(e);
    	$("#search_button").trigger('click');
    });
$("#search_button").click(function(){
	$("#search_form").submit();
	console.log('hello');
});


/* Function To Update Staff Data */
$('#retail_customer_synk').on('click',function(){
	$('#progress').show();	
 $.ajax({
	 type: 'POST',
	 url: "cpos_to_ho.php",
	 data : {'action':'updateCustomers'}
	 
  }).done(function(response) {
  	 var $res =  $.parseJSON(response); //Parse result of response
	 console.log(response);
	 $('#progress').hide();
	 if($res.error){ //If Their Exists any problem in Update then show errors
	    bootbox.alert($res.msg); 
	 }else{
		 bootbox.alert('Total<br/>Inserted Customers : '+$res.data.inserted+'<br/>Updated Customers : '+$res.data.updated+'<br/>Deleted Customers : '+$res.data.deleted); 
	}
	
	});
});


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

/* Function To Upload Updated Bill Data */
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

/* Function To Upload Petty Expense Data */
$('#petty_expense_synk').on('click',function(){
 $('#progress').show();
  $.ajax({
	 type: 'POST',
	 url: "ho_to_cpos.php",
	 data : {'action':'uploadPettyExpense'}

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

/* Function To Upload Login History Data */
$('#login_history_synk').on('click',function(){
 $('#progress').show();
  $.ajax({
	 type: 'POST',
	 url: "ho_to_cpos.php",
	 data : {'action':'uploadLoginHistory'}

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

/* Function To Upload Shift Data */
$('#shift_data_synk').on('click',function(){
 $('#progress').show();
  $.ajax({
	 type: 'POST',
	 url: "ho_to_cpos.php",
	 data : {'action':'uploadShiftData'}

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

function getMonth(monthName){
	var monthNumber = 0;
	switch(monthName){
		case 'January':
			monthNumber = 0;
			break;
		case 'February':
			monthNumber = 1;
			break;
		case 'March':
			monthNumber = 2;
			break;
		case 'April':
			monthNumber = 3;
			break;
		case 'May':
			monthNumber = 4;
			break;
		case 'June':
			monthNumber = 5;
			break;
		case 'July':
			monthNumber = 6;
			break;
		case 'August':
			monthNumber = 7;
			break;
		case 'September':
			monthNumber = 8;
			break;
		case 'October':
			monthNumber = 9;
			break;
		case 'November':
			monthNumber = 10;
			break;
		case 'December':
			monthNumber = 11;
			break;
	}
	return monthNumber;
}