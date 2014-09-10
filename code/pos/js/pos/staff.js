$(document).ready(function(){
	$('#shift_nav li').click(function(){
		$('li',$(this).closest('ul')).removeClass('active');
		$(this).addClass('active');
		$('#shift_breadcrumb').text($(this).text());
	//	$('#shift_breadcrumb_module').text('Login');
	});
/*	$('#petty_cash').click(function(){
		$('#shift_breadcrumb_module').text($(this).text());
	});/**/
});