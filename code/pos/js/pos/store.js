$(document).ready(function(){
	//Store Selection Block Start
		/*Store SElection script works when user selects stores from list to enter or open*/
			$("#error_message").hide();
			$("#select_store").submit(function(event){
				var store = $(this).find("#store").val();
				var msg = "";

				if(parseInt(store)){
					$("#error_message").hide();$("#error_message ul").html("");
						//window.location = "dashboard.php?store="+store
//						$(this).submit();
				}else{
					msg += "Select Store From List";
					if(msg){$("#error_message").show();$("#error_message ul").html(msg);}else{$("#error_message").hide();$("#error_message ul").html("");}
					event.preventDefault();
				}
			});
	//Store Selection Block End

});
