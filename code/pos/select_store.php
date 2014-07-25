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
	<div class="container">
		<div class="col-md-4 col-md-offset-4" id="login-box">
			<div class="padded" style="text-align: center; margin-top: 40px;">
				<img src="http://tecdemo.com/spos3/assets/images/logo1.png"
					alt="Simple POS" />
				<div class="panel panel-primary" style="margin-top: 20px;">
					
					<div class="panel-body" style="padding-bottom: 0;">

						<div class="alert alert-danger" id="error_message">
							<ul>
							</ul>
						</div>
					
						<form id="select_store" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal">
    
    <div class="input-group">
								<span class="input-group-addon"> <i
									class="glyphicon glyphicon-hand-right"></i>
								</span> 
									<select name="store" id="store" class="form-control">
	                                 <option value="">Select Store</option>
	                                 <?php foreach($storeList['rows'] as $key => $value) {?>
	                                 <option value="<?php echo $value['key'];?>"><?php echo $value['value'];?></option>
 	                                 <?php }?>

									</select> 
							</div>&nbsp;&nbsp;&nbsp;&nbsp;
							
							<div class="row">

								<div class="col-md-12">
									<button type="submit" class="btn btn-success btn-block btn-lg">
										Enter/Open Store <i class="glyphicon glyphicon-log-in"></i>
									</button>
								</div>
								
							</div>
						</form>

						
						
						
						</div>

				</div>
				<div class="row"><div class="col-md-8 col-md-offset-2">&copy; 2014 Simple POS</div></div>
			</div>
		</div>
	</div>
</body>
</html>
