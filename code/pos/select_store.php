<?php
	$url = 'http://127.0.0.1:5984/pos/_design/store/_view/store_as_option';
	require_once 'httpapi.php';
	$storeList = json_decode(curl($url),true);
	sort($storeList['rows']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<link rel="stylesheet" href="css/bootstrapValidator.css">


<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrapValidator.js"></script>

<style>
.container {
	width: 100%;
	max-width: 100%;
}

.container.padded>.row {
	margin: 0;
}

.padded {
	padding: 15px;
}

.separate-sections {
	margin: 0;
	list-style: none;
	padding-bottom: 5px;
}

.separate-sections>li,.separate-sections>div {
	margin-bottom: 15px !important;
}

.separate-sections>li:last-child,.separate-sections>div:last-child {
	margin-bottom: 0px;
}

i {
	margin: 0 10px;
}
</style>
<script>
	$(document).ready(function(){
			$("#error_message").hide();
			$("#select_store").submit(function(event){
				var store = $(this).find("#store").val();
				var msg = "";

				if(parseInt(store)){
					$("#error_message").hide();$("#error_message ul").html("");
/*					$.ajax({
				  		type: 'POST',
				  		url: "select_store.php",
				  		data : {store:store}

					}).done(function(response) {
					});/**/
							window.location = "dashboard.php?store="+store

				}else{
					msg += "Select Store From List";
					if(msg){$("#error_message").show();$("#error_message ul").html(msg);}else{$("#error_message").hide();$("#error_message ul").html("");}
				}
				event.preventDefault();
			});
	});

</script>
</head>
<body>
</body>
</html>
