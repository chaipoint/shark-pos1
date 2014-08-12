$(document).ready(function(){
	$.ajax({
		url: "index.php?dispatch=utils.initial&store="+store,
	}).done(function(response){
		var result = $.parseJSON(response);
		if(result.error){
			alert(result.message);
		}else{
			window.location = 'index.php';
		}

	});
});