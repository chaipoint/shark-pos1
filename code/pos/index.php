<?php
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		$_POST['password'] = md5($_POST['password']);
		require_once "httpapi.php";
		echo curl("http://127.0.0.1:5984/pos/_design/staff/_list/getuser/staff_code",$_POST); 
		return;
	}
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
			$("#loginform").submit(function(event){
				var username = $(this).find("#username").val();
				var password = $(this).find("#password").val();
				var msg = "";
				if(username.trim() == ""){msg += "<li>Provide Username</li>";}
				if(password.trim() == ""){msg += "<li>Provide Password</li>"}
				if(msg){$("#error_message").show();$("#error_message ul").html(msg);}else{$("#error_message").hide();$("#error_message ul").html("");}
				$.ajax({
			  		type: 'POST',
			  		url: "index.php",
			  		data : {username:username,password:password}

				}).done(function(response) {
					var $res =  $.parseJSON(response);
					console.log($res);
					if($res.error){
						msg = $res.message;
						$("#error_message").show();$("#error_message ul").html(msg);
					}else{
						window.location = "select_store.php";
					}
			  		//$( this ).addClass( "done" );
				});
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
					<div class="panel-heading">Login</div>
					<div class="panel-body" style="padding-bottom: 0;">

						<div class="alert alert-danger" id="error_message">
							<ul>
							</ul>
						</div>
					
						<form id="loginform" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal">
    
    <div class="input-group">
								<span class="input-group-addon"> <i
									class="glyphicon glyphicon-user"></i>
								</span> <input type="text" name="identity" value=""
									id="username" class="form-control" placeholder="Email" />
							</div>
							<div class="input-group">
								<span class="input-group-addon"><i
									class="glyphicon glyphicon-lock"></i> </span> <input
									type="password" name="password" value="" id="password"
									class="form-control" placeholder="Password" />
							</div>
							<div class="row">

								<div class="col-md-12">
									<button type="submit" class="btn btn-success btn-block btn-lg">
										Login <i class="glyphicon glyphicon-log-in"></i>
									</button>
								</div>
								<div class="col-md-12">
									<a href="" class="">
										
										Forgot Password? 
									</a>
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
