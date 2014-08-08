<?php include 'header.php'; ?>
<body>
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

$('#dashboard').on('click',function(){
window.location = 'dashboard.php';
});


$('#synk').on('click',function(){
window.location = 'synk.php';
});

});
	</script>
	<div class="container">
		<div class="col-md-4 col-md-offset-4" id="login-box">
			<div class="padded" style="text-align: center; margin-top: 40px;">
				
				<div class="panel panel-primary" style="margin-top: 20px;">
					
					<div class="panel-body" style="padding-bottom: 0;">

					<form id="select_store" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal">
    
                <div class="row">

								<div class="col-md-12">
									<button type="button" id="dashboard" class="btn btn-success btn-block btn-lg">
										DashBoard<i class="glyphicon glyphicon-log-in"></i>
									</button>
								</div>
								
							</div>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<div class="row">

								<div class="col-md-12">
									<button type="button" id="synk" class="btn btn-success btn-block btn-lg">
										Synk<i class="glyphicon glyphicon-log-in"></i>
									</button>
								</div>
								
							</div>
						</form>
						</div>
				</div>
				
			</div>
		</div>
	</div>		<!--	<link rel="stylesheet" href="http://localhost/pos/css/bootstrap-theme.min.css">-->
		
		
	</body>
</html>