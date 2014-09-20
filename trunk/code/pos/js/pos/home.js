var keyboard = new Array();
var resData = '';
$(document).ready(function(){	
	
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
		console.log('#store_'+$('a',this).attr('id')+"_form");
	});
	keyboard.push($('input[name="identity"],input[name="password"],#petty_cash, #counter_no, #petty_cash_end, #box_cash, #box_cash_end').cKeyboard());
	
	$('#login_holder_home div#error_message').attr('id','error_message_modal');
	resData = $("#wrapper_restricted").html();
	$("#wrapper_restricted").html('');
	$('#details_button_tabs').click(function(){
		$("#error_message_modal").hide();
		$('#login_holder_home').modal('show');
	});





$('.home_tabs').click(function(){
	var active = $('li.active',$(this).closest('ul'));
	var eClass = $(this).attr('class');
	var id = $(this).attr('id');
	console.log(eClass);
	if( eClass == 'home_tabs remove'){
		$('#'+id+'_data').html('');
		$('#cash_reconciliation_tab').addClass('remove');
	}
	active.removeClass('active');
	$(this).parent().addClass('active');
	$('.tabs_data').addClass('hidden');
	$('#'+$(this).attr('id')+'_data').removeClass('hidden');

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
				url: "index.php?dispatch=home.save",
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


});
function CashRHandleResponse(response){
	$('#login_holder_home').modal('hide');
	$('#shift_data_tab').trigger('click');
	$('#shift_data_tab').addClass('remove');
	$('#shift_data_tab_data').html(response.data.shift_table);
	$('#cash_reconciliation_tab_data').html(response.data.cash_reconciliation_table);
}
function staffHandleResponse(response){

	$('#store_shift_message').html(response.message);
	active = '';
	$("#store_shift_logic form:input").val('');
	$('#store_shift_logic form').addClass('hide');
	switch(response.data.mode){
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