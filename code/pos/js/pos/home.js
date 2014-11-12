var keyboard = new Array();
var resData = '';
$(document).ready(function(){
	$('#error_message_shift, #error_message_card').hide();
	$("#active_bill_table_wrapper").on('click', '.edit-bill', function(event){
		if(!is_shift_running){
			event.preventDefault();
			bootbox.alert('Please Start Shift Before Modify Bill.');
		}
	});	
	if(is_store_open){
		$('#shift_breadcrumb').text($('#shift_nav li:nth-child(2)').text());
		$('#store_shift_message').text('Store is Opened.');
		if(is_shift_running){
			$('#store_shift_message').html('Store Shift <b>'+$('#shift_count').text()+' </b>is in Process. Started by <span class="label label-warning">'+$('#shift_starter').text()+' </span><a href="index.php?dispatch=billing" class="btn btn-sm btn-primary">Start Billing</a>');
			$('#shift_breadcrumb').text($('#shift_nav li:nth-child(3) a').addClass('btn-primary').text());
			$('#store_shift_start_form').hide();
			//$('#shift_nav li:nth-child(2) a').addClass('bg-success');
		}else{
			$('#store_shift_end_form').hide();
			$('#shift_nav li:nth-child(2) a').addClass('btn-primary');
			$('#shift_nav li:nth-child(4) a').addClass('btn-primary');
		}
//		$('#shift_nav li:nth-child(1) a').addClass('bg-success');

	}else{
		$('#shift_nav li:first a').addClass('btn-primary');
	}
	$('#shift_nav a:not(.btn-primary,.apart_day_shift)').attr("disabled","disabled");
	$('#shift_nav a[disabled="disabled"]').css('color','black');
	$('#shift_nav li a.btn-primary').click(function(){ 
		$('#store_shift_logic form').addClass('hide');
		$('#store_'+$(this).attr('id')+"_form").removeClass('hide');	
	});
	keyboard.push($('input[name="username1"],input[name="password"],input[name="first_name"],input[name="last_name"],input[name="mobile_no"],input[name="amount"],input[name="original_card_no"],#petty_cash, #counter_no, #petty_cash_end, #box_cash, #opening_box_cash, #box_cash_end').cKeyboard());
	

$('#tab_selection_menu').on('click','.home_tabs',function(){
	var active = $('li.active',$(this).closest('ul'));
	active.removeClass('active');
	$(this).parent().addClass('active');
	$('.tabs_data').addClass('hidden');
	var thisId = $(this).attr('id');
	$('#'+thisId+'_data').removeClass('hidden');
	if(thisId == 'shift_data_tab' && (($('#'+thisId+'_data').html()).trim() == '' || is_login_allowed)){
		$('#'+thisId+'_data').html('');
	}else{
		if(thisId == 'shift_data_tab'){
			$('#shift_data_tab_holder').removeClass('hidden');			
		}else{
			$('#shift_data_tab_holder').addClass('hidden');
		}
	}
});



	/* Function To Add Prety Inward */
	$('#add_inward').click(function(event){
		event.preventDefault();
		$('#addInwardModal').modal();
		$('#inward_amount').val('');
		$('#inward_amount').cKeyboard();
		setTimeout(function(){
			$('#inward_amount').focus();				
		},500);

	});

/* Function To View Prety Expense */
	$('#view_inward').click(function(event){
		event.preventDefault();
		$('#viewInwardModal').modal();
	});
		
	$('.close-model').click(function(){
		$("div.ui-keyboard").hide();
	});

	/* Function To Save Petty Inward */
	$('#add-inward-form').on('submit', function(event){
		event.preventDefault();
        $('#add-inward-form').bootstrapValidator();
		var data = $('form#add-inward-form').serializeArray();
		console.log(data);
		$.ajax({
				type: 'POST',
				url: "index.php?dispatch=sales_register.save",
		  		data : data ,
			 }).done(function(response) {
				console.log(response);
				result = $.parseJSON(response);
				if(result.error){
					bootbox.alert(result.message);
				}else{
					$('#addInwardModal').modal('hide');
					bootbox.alert('Inward Successfully Saved');
					window.location.reload(true);
				}
			});

	});

	/* Cancel Form */
	$('.cancel-btn').click(function(){
		var form_name = $(this).closest('form').attr('id');
		$('#'+form_name).addClass('hide');
		$('#error_message_shift').hide();
	});

	/*Function To Validate Card Transaction Form */
	$('.card_form').on('submit',function(event){
		event.preventDefault(); 	
		var form = $(this);
		var formID = form.attr('id'); 
		var errorHolder = 'error_message_card';
		$("#"+errorHolder).hide();
		var msg = "";
		var formData = form.serializeObject(); 
		switch(formID){

			case 'store_ppc_card_issue_form':
				formData.request_type = 'issue_ppc_card';
				if((formData.card_group_name).trim() == ""){msg += "<li>Provide Card Group Name</li>";}
				else if((formData.first_name).trim() == ""){msg += "<li>Provide First Name</li>";}
				else if((formData.last_name).trim() == ""){msg += "<li>Provide Last Name</li>";}
				else if((formData.mobile_no).trim() == ""){msg += "<li>Provide Mobile NUmber</li>";}
				else if((formData.amount).trim() == ""){msg += "<li>Provide Amount</li>";}
				break;

			case 'store_ppc_card_reissue_form':
				formData.request_type = 'get_customer_info';
				if((formData.card_number).trim() == ""){msg += "<li>Provide New Card No</li>";}
				break;

			case 'store_ppc_card_activate_form':
				formData.request_type = 'activate_ppc_card';
				if((formData.first_name).trim() == ""){msg += "<li>Provide First Name</li>";}
				else if((formData.last_name).trim() == ""){msg += "<li>Provide Last Name</li>";}
				else if((formData.mobile_no).trim() == ""){msg += "<li>Provide Mobile NUmber</li>";}
				else if((formData.card_number).trim() == ""){msg += "<li>Provide Currect Card No</li>";}
				else if((formData.amount).trim() == ""){msg += "<li>Provide Amount</li>";}
				break;
			
			case 'store_ppc_card_load_form':
				formData.request_type = 'load_ppc_card';
				if((formData.card_number).trim() == ""){msg += "<li>Provide Currect Card No</li>";}
				else if((formData.amount).trim() == ""){msg += "<li>Provide Amount</li>";}
				break;

			case 'store_ppc_card_balance_check_form':
				formData.request_type = 'balance_check_ppc_card';
				if((formData.card_number).trim() == ""){msg += "<li>Provide Currect Card No</li>";}
				break;

			case 'store_ppa_card_load_form':
				formData.request_type = 'load_ppa_card';
				if((formData.card_number).trim() == ""){msg += "<li>Provide Currect Card No</li>";}
				else if((formData.amount).trim() == ""){msg += "<li>Provide Amount</li>";}
				break;

			case 'store_ppa_card_balance_check_form':
				formData.request_type = 'balance_check_ppa_card';
				if((formData.card_number).trim() == ""){msg += "<li>Provide Currect Card No</li>";}
				break;
		} 
		if(msg){
			$("#"+errorHolder).show();$("#"+errorHolder+" ul").html(msg);
		}else{  
			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=billing.loadCard",
				data : formData
			}).done(function(response) { 
				$('#'+formID+'')[0].reset();
				var $res =  $.parseJSON($.trim(response));
				console.log($res); 
				if($res.error){ 
					$("#"+errorHolder).show();$("#"+errorHolder+" ul").html($res.data['message']);
				}else if($res.data['success']=='False'){
					bootbox.alert($res.data['message']);
				}else{
					bootbox.alert($res.data['message']+'.Your Balance is:'+$res.data['balance']);

				}
			})
		}
	});


});
function shift_data_tab(response){ 
	if(!response){
		searchShiftData();
		return;
	}
	$('#login_holder_home').modal('hide');
	$('#shift_data_tab_data').html(response.shift_table+response.cash_reconciliation_table);
	$('#shift_data_tab_holder').removeClass('hidden');
}
function shift(response){ 
	$('#store_shift_message').html(response.message);
	active = '';
	$("#store_shift_logic form:input").val('');
	$('#store_shift_logic form').addClass('hide');
	switch(response.mode){
		case 'day_start':
			is_store_open = true;
			active = 'shift_start';
			$('#store_shift_logic form#store_shift_start_form').show();
		break;
		case 'day_end':
			is_store_open = false;
			active = 'day_start';
			$('#logout').trigger('click');
		break;
		case 'shift_start':
			is_shift_running = true;
			active = 'shift_end';
			$('#store_shift_logic form#store_shift_end_form').show();
			window.location.reload(true);
		break;
		case 'shift_end':
			is_shift_running = false;
			active = 'shift_start';
			$('#store_shift_logic form#store_shift_start_form').show();
			window.location.reload(true);
		break;
	}	
	$('#shift_nav li a.btn-primary').removeClass('btn-primary');
	$('#shift_nav li a#'+active).addClass('btn-primary');
	window.location.reload(true);	
}
function searchShiftData(){
	var date = $("#shift_data_search").val();
	$.ajax({
			type: 'POST',
			url: "index.php?dispatch=home.getShiftAndCashRe",
	  		data : {date:date} ,
		}).done(function(response) {
			var result = $.parseJSON(response);
			if(result.error){
				bootbox.alert(result.message);
			}else{
				shift_data_tab(result.data);
			}
		});
}