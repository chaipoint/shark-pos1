var keyboard = new Array();
$(document).ready(function(){	
	if(is_store_open){
		$('#shift_breadcrumb').text($('#shift_nav li:nth-child(2)').text());
		$('#store_shift_message').text('Store is Opened.');
		if(is_shift_running){
			$('#store_shift_message').text('Store Shift '+$('#shift_count').text()+' is in Process.');
			$('#shift_breadcrumb').text($('#shift_nav li:nth-child(3)').addClass('active').text());
		}else{
			$('#shift_nav li:nth-child(2)').addClass('active')
		}
	}else{
		$('#shift_nav li:first').addClass('active');
	}

	$('#shift_nav li').click(function(){
		var change = false;
		var index = $(this).index();
		if(is_store_open && (index == 3 || index == 1) && !is_shift_running){
			change = true;
		}
		if(change){
			$('#shift_breadcrumb').text($(this).text());
			$('li',$(this).closest('ul')).removeClass('active');
			$(this).addClass('active');
		}
	});
	keyboard.push($('#petty_cash').cKeyboard());
	$("#petty_cash_form").submit(function(event){
		event.preventDefault();
		var petty_cash = $('#petty_cash').val();
		cAjax({url:'?dispatch=staff.save_petty', data: { cash:petty_cash, mode:$('#shift_nav li.active a').attr('id')}, callback:handleResponse});
	});

});
function handleResponse(response){
	$('#store_shift_message').text(response.message);
	active = '';
	switch(response.data.mode){
		case 'day_start':
			is_store_open = true;
			active = 'shift_start';
		break;
		case 'day_end':
			is_store_open = false;
			active = 'day_start';
		break;
		case 'shift_start':
			is_shift_running = true;
			active = 'shift_end';
		break;
		case 'shift_end':
			is_shift_running = false;
			active = 'shift_start';
		break;
	}	
	$('#shift_nav li.active').removeClass('active');
	$('#shift_nav li a#'+active).closest('li').addClass('active');
	$('#shift_breadcrumb').text($('#shift_nav li a#'+active).text());
	$("form#petty_cash_form").hide();
	$("form#loginform").show();
	$("form#petty_cash_form:input").val('');
	$("form#loginform:input").val('');

	console.log(response);
}