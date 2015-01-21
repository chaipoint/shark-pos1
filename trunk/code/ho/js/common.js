$(document).ready( function(){

/* Function For glyphicon Icon Up And Down*/
	$('.col').click(function(){
		if($(this).find('i').attr('class')=='glyphicon glyphicon-chevron-up pull-right'){
			$(this).find('i').removeClass('glyphicon glyphicon-chevron-up pull-right');
			$(this).find('i').addClass('glyphicon glyphicon-chevron-down pull-right');
		}else if($(this).find('i').attr('class')=='glyphicon glyphicon-chevron-down pull-right'){
			$(this).find('i').removeClass('glyphicon glyphicon-chevron-down pull-right');
			$(this).find('i').addClass('glyphicon glyphicon-chevron-up pull-right');
		}
	});
});