var keyboard = new Array();
$(document).ready(function(){	
	$('#store_day_end_form').hide();
	
	if(is_store_open){
		$('#shift_breadcrumb').text($('#shift_nav li:nth-child(2)').text());
		$('#store_shift_message').text('Store is Opened.');
		$('#store_day_start_form').hide();
		if(is_shift_running){
			$('#store_shift_message').html('Store Shift <b>'+$('#shift_count').text()+' </b>is in Process. Started by <span class="label label-warning">'+$('#shift_starter').text()+' </span><a href="index.php?dispatch=billing" class="btn btn-sm btn-primary">Start Billing</a>');
			$('#shift_breadcrumb').text($('#shift_nav li:nth-child(3) a').addClass('btn-primary').text());
			$('#store_shift_start_form').hide();
			$('#shift_nav li:nth-child(2) a').addClass('bg-success');
		}else{
			$('#shift_nav li:last').click(function(){
				$('#shift_nav li.active').removeClass('active');
				$(this).addClass('active');
				$('#shift_nav li.active').trigger('click');
			});
			$('#store_shift_end_form').hide();
			$('#shift_nav li:nth-child(2)').addClass('active');
		}
		$('#shift_nav li:nth-child(1) a').addClass('bg-success');

	}else{
		$('#store_shift_start_form').hide();
		$('#store_shift_end_form').hide();
		$('#shift_nav li:first').addClass('active');
	}
	$('#shift_nav a:not(.btn-primary)').attr("disabled","disabled");
	$('#shift_nav a[disabled="disabled"]').css('color','black');
	$('#shift_nav li a.btn-primary').click(function(){
		$('#store_'+$(this).attr('id')+"_form").removeClass('hide');	
		console.log('#store_'+$('a',this).attr('id')+"_form");
	});
	keyboard.push($('input[name="identity"],input[name="password"],#petty_cash, #counter_no, #petty_cash_end, #box_cash, #box_cash_end').cKeyboard());
});
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
	$('#shift_nav li.active').removeClass('active');
	$('#shift_nav li a#'+active).closest('li').addClass('active');
	window.location.reload(true);	
}