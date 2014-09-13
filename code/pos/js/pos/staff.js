var keyboard = new Array();
$(document).ready(function(){	
	$('#store_day_end_form').hide();
	if(is_store_open){
		$('#shift_breadcrumb').text($('#shift_nav li:nth-child(2)').text());
		$('#store_shift_message').text('Store is Opened.');
		$('#store_day_start_form').hide();
		if(is_shift_running){
			$('#store_shift_message').html('Store Shift <b>'+$('#shift_count').text()+' </b>is in Process. <a href="index.php?dispatch=billing" class="btn btn-sm btn-primary">Start Billing</a>');
			$('#shift_breadcrumb').text($('#shift_nav li:nth-child(3)').addClass('active').text());
			$('#store_shift_start_form').hide();
		}else{
			$('#store_shift_end_form').hide();
			$('#shift_nav li:nth-child(2)').addClass('active')
		}
	}else{
		$('#store_shift_start_form').hide();
		$('#store_shift_end_form').hide();
		$('#shift_nav li:first').addClass('active');
	}

	$('#shift_nav li').click(function(){
		var change = false;
		var index = $(this).index();
		if(is_store_open && (index == 3 || index == 1) && !is_shift_running){
			change = true;
		}
		if(change){
			if(index == 3){
				$('#store_shift_logic form').hide();
				$('#store_day_end_form').show();
			}else if(index == 1){
				$('#store_shift_logic form').hide();
				$('#store_shift_start_form').show();
			}

			$('#shift_breadcrumb').text($(this).text());
			$('li',$(this).closest('ul')).removeClass('active');
			$(this).addClass('active');
		}
	});
	keyboard.push($('#petty_cash, #counter_no, #petty_cash_end, #box_cash, #box_cash_end').cKeyboard());

	$("#counter_no_form, #shift_end_form, #day_end_form").submit(function(event){
		event.preventDefault();
		var data = new Object();
		data.mode = $('#shift_nav li.active a').attr('id');
		if($(this).attr('id') == 'counter_no_form'){
			data.counter_no = $('input#counter_no',this).val();
		}else if($(this).attr('id') == 'shift_end_form'){
			data.petty_cash = $('input#petty_cash_end',this).val();
			data.box_cash = $('input#box_cash',this).val();
		}else if($(this).attr('id') == 'day_end_form'){
			data.box_cash = $('input#box_cash_end',this).val();
		}


		//box_cash_end
		var petty_cash = $('#petty_cash').val();
		cAjax({url:'?dispatch=staff.save_petty', data: data, callback:staffHandleResponse});
	});

});
function staffHandleResponse(response){

	$('#store_shift_message').html(response.message);
	active = '';
	$("#store_shift_logic form:input").val('');
	$('#store_shift_logic form').hide();
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
	$('#shift_breadcrumb').text($('#shift_nav li a#'+active).text());
	
}