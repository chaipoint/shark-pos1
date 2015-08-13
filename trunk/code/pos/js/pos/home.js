var keyboard = new Array();
var resData = '';
$(document).ready(function(){
	
	$(window).load( function(){
		$('input#card_number').val(' '); 
		setTimeout( function(){ $('input#card_number').val( '' ); }, 20 );
	});
	
	$('#error_message_shift, #error_message_card').hide();
	$("#active_bill_table_wrapper").on('click', '.edit-bill', function(event){
		if(!is_shift_running){
			event.preventDefault();
			bootbox.alert('Please Start Shift Before Modify Bill.');
		}
	});	
	
	$('.alert-info.store-operation').click(function(){ 
		$('#store_shift_logic form').addClass('hide');
		$('#store_'+$(this).attr('id')+"_form").removeClass('hide');	
	});
	keyboard.push($('.total-ticket,.cash-qty,.sodex,.tr,input[name="username1"],input[name="password"],input[name="first_name"],input[name="last_name"],input[name="mobile_no"],input[name="amount"],input[name="original_card_no"],#petty_cash, #counter_no,#box_cash, #petty_cash_end, #opening_box_cash, #box_cash_end').cKeyboard());
	
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
	//alert(exeMode); alert(printUtility); return false; 
	/* Function For Re-Print */
	$('#active_bill_table').on('click','.reprint-bill', function(event){
		var doc = $(this).attr('id');
		event.preventDefault();
		$.ajax({
			type: 'POST',
			url: "index.php?dispatch=billing.rePrint",
			data : {request_type:'print_bill', doc:doc},
		}).done(function(response) {
			response1 = $.parseJSON(response);
			if(response1.error){
				bootbox.alert(response1.message);
			}else{
				if(!exeMode){
						printBill(response, true);
					}
				
				if(response1.message!=''){		
					bootbox.alert(response1.message,function(){
					window.location.reload(true);													
					});
				}else{
					window.location.reload(true);
				}
			}
		});
	});
		
	/* Function To View Prety Expense */
	$('#view_inward').click(function(event){
		event.preventDefault();
		$('#viewInwardModal').modal();
	});
		
	$('.close-model').click(function(){
		$("div.ui-keyboard").hide();
	});
	
	$('.card').click(function(){ 
		$('.card').removeClass('active3');
		$(this).addClass('active3');
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
				if(!/^[a-zA-Z\s]+$/.test(formData.first_name)){msg += "<li>Provide Correct First Name</li>";}
				else if(!/^[a-zA-Z\s]+$/.test(formData.last_name)){msg += "<li>Provide Correct Last Name</li>";}
				else if(!/^\d{10}$/.test(formData.mobile_no)){msg += "<li>Provide Valid Mobile Number</li>";}
				else if((formData.amount).trim() == ""){msg += "<li>Provide Valid Amount</li>";}
				else if(!/^[0-9;=?]+$/.test(formData.card_number)){msg += "<li>Provide Correct Card No</li>";}
				break;
			
			case 'store_ppc_card_load_form':
				formData.request_type = 'load_ppc_card';
				if((formData.amount).trim() == ""){msg += "<li>Provide Amount</li>";}
				//else if(!/^[0-9;=?]+$/.test(formData.card_number)){msg += "<li>Provide Correct Card No</li>";}
				break;

			case 'store_ppc_card_balance_check_form':
				formData.request_type = 'balance_check_ppc_card';
				//if(!/^[0-9;=?]+$/.test(formData.card_number)){msg += "<li>Provide Correct Card No</li>";}
				break;

			case 'store_ppa_card_load_form':
				formData.request_type = 'load_ppa_card';
				if((formData.card_number).trim() == ""){msg += "<li>Provide Correct Card No</li>";}
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
			$("#ajaxfadediv").addClass('ajaxfadeclass'); 
			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=billing.loadCard",
				data : formData
			}).done(function(response) {
				//alert(response);
				$("#ajaxfadediv").removeClass('ajaxfadeclass');
				var IS_JSON = true;
					try
						{
							var $res = $.parseJSON($.trim(response));
						}
					catch(err)
						{
							IS_JSON = false;
						}  
				
				if(IS_JSON){
				if($res.error){ 
					$("#"+errorHolder).show();$("#"+errorHolder+" ul").html($res.message);
				}else if($res.data['success']=='False'){
					bootbox.alert($res.data['message']);
				}else{
					if(formID=='store_ppc_card_load_form' || formID=='store_ppc_card_activate_form' || formID=='store_ppa_card_load_form'){
						if(!exeMode){
							printBill(response, false);
						}
						//printBill(response, false);
						bootbox.alert($res.data['message']+'.Your Balance is:'+$res.data['balance'], function(){
							window.location.reload(true);
						});
					}else{
						
						bootbox.alert($res.data['message']+'.Your Balance is:'+$res.data['balance']);
					}
				}
				}else{
					bootbox.alert('Server Error! Please Contact Admin');
				}
			})
		}
	});

	
	$('#cash_denomination').on('click', function(){
		//alert('dsfsdf');
		$('#cashModal').modal('show');
		$('#cash-denomination-form').show();
	});
	
	
	$("#select-bt").on("change",".cash-qty",function(event){ 
			var newQty = parseInt($(this).val());
			newQty = isNaN(newQty) ? 0 : newQty;
			var cashValue = $(this).closest('tr').attr('cash-value');
			$('#qty_'+cashValue).val(newQty);
			$(this).closest('tr').find('span.total').text(newQty*cashValue);
			var totalCash=0;
			$('.total').each(function(){
				totalCash +=parseInt($(this).text());
			});
			$('.total-ticket').each(function(){
				totalCash +=parseInt($(this).val());
			});
			$('#total-cash').text(totalCash);
	});

	$("#select-bt").on("change",".total-ticket",function(event){ 
			var cashValue = parseInt($(this).val());
			var method = $(this).closest('tr').attr('cash-value');
			$('#amount_'+method).val(cashValue);
			var totalCash=0;
			$('.total').each(function(){
				totalCash +=parseInt($(this).text());
			});
			$('.total-ticket').each(function(){
				totalCash +=parseInt($(this).val());
			});
			$('#total-cash').text(totalCash);
	});

	$("#select-bt").on("change",".qunt",function(event){
		var qty = parseInt($(this).val());
		var method = $(this).closest('tr').attr('cash-value');
		$('#qty_'+method).val(qty);

	});

	$('#save-cash').click(function(event){
		event.preventDefault();
		$('#cash_denomination').val($('#total-cash').text());
		$('#cashModal').modal('hide');
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
		$("#ajaxfadediv").addClass('ajaxfadeclass');
		$('#store_shift_message').html(response.message);
		active = '';
		$("#store_shift_logic form:input").val('');
		$('#store_shift_logic form').addClass('hide');
		switch(response.mode){
			case 'day_start':
				is_store_open = true;
				//active = 'shift_start';
				//$('#store_shift_logic form#store_shift_start_form').show();
				window.location.reload(true);
			break;
			case 'day_end':
				is_store_open = false;
				active = 'day_start';
				window.location.reload(true);
				//$('#logout').trigger('click');
			break;
			case 'shift_start':
				is_shift_running = true;
				//active = 'shift_end';
				//$('#store_shift_logic form#store_shift_end_form').show();
				window.location.reload(true);
			break;
			case 'shift_end':
				is_shift_running = false;
				//active = 'shift_start';
				//$('#store_shift_logic form#store_shift_start_form').show();
				window.location.reload(true);
			break;
		}	
	//$('#shift_nav li a.btn-primary').removeClass('btn-primary');
	//$('#shift_nav li a#'+active).addClass('btn-primary');
	//window.location.reload(true);	
	}
	function searchShiftData(){
		var date = $("#shift_data_search").val();
		$("#ajaxfadediv").addClass('ajaxfadeclass');
		$.ajax({
			type: 'POST',
			url: "index.php?dispatch=home.reconcilation",
	  		data : {date:date} ,
		}).done(function(response) {
			$("#ajaxfadediv").removeClass('ajaxfadeclass');
			var result = $.parseJSON(response);
			if(result.error){
				bootbox.alert(result.message);
			}else{
				shift_data_tab(result.data);
			}
		});
	}