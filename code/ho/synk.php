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

$('#cpos_to_ho').on('click',function(){
    $('#select').hide();
	$('#cpos_ho').show();
});

/* Function To Update Staff Data */
$('#staff_synk').on('click',function(){
 
$.ajax({
	 type: 'POST',
	 url: "cpos_to_ho.php",
	 data : {'action':'updateStaff'}
	 
  }).done(function(response) {
  	 var $res =  $.parseJSON(response); //Parse result of response
	 console.log(response);
	 if($res.error){ //If Their Exists any problem in Update then show errors
	    bootbox.alert($res.msg); 
	 }else{
		 bootbox.alert($res.msg); 
	}
	
	});
});

/* Function To Update Store Data */
$('#store_synk').on('click',function(){
 
 $.ajax({
	 type: 'POST',
	 url: "cpos_to_ho.php",
	 data : {'action':'updateStore'}

  }).done(function(response) {
	 var $res =  $.parseJSON(response); //Parse result of response
	 console.log(response);
	 if($res.error){ //If Their Exists any problem in Update then show errors
	    bootbox.alert($res.msg); 
	 }else{
		 bootbox.alert($res.msg); 
	}
	
	});
});

/* Function To Update Config Data */
$('#config_synk').on('click',function(){
 
 $.ajax({
	 type: 'POST',
	 url: "cpos_to_ho.php",
	 data : {'action':'updateConfig'}

  }).done(function(response) {
	 var $res =  $.parseJSON(response); //Parse result of response
	 console.log(response);
	 if($res.error){ //If Their Exists any problem in Update then show errors
	    bootbox.alert($res.msg); 
	 }else{
		 bootbox.alert($res.msg); 
	}
	
	});
});

   
});
</script>

	<div class="container">
		<div class="col-md-4 col-md-offset-4" id="login-box">
			<div class="padded" style="text-align: center; margin-top: 40px;">
				
				<div class="panel panel-primary" style="margin-top: 20px;">
					
					<div class="panel-body" style="padding-bottom: 0;">

					<form id="select_store" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal">
                
                <div id="select">
                     <div class="row">
                     	<div class="col-md-12">
							<button type="button" id="cpos_to_ho" class="btn btn-success btn-block btn-lg">
										CPOS -> HO<i class="glyphicon glyphicon-log-in"></i>
							</button>
						</div>
					 </div>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					 <div class="row">
					    <div class="col-md-12">
							<button type="button" id="ho_to_cpos" class="btn btn-success btn-block btn-lg">
										HO -> CPOS<i class="glyphicon glyphicon-log-in"></i>
							</button>
						</div>
					 </div>
				</div>
                <div id="cpos_ho" style="display:none;">
					 <div class="row">
					 	<div class="col-md-12">
							<button type="button" id="staff_synk" class="btn btn-success btn-block btn-lg">
										Staff<i class="glyphicon glyphicon-log-in"></i>
							</button>
						</div>
					 </div>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					 <div class="row">
					 	<div class="col-md-12">
							<button type="button" id="store_synk" class="btn btn-success btn-block btn-lg">
									  Store<i class="glyphicon glyphicon-log-in"></i>
							</button>
						</div>
					</div>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<div class="row">
					    <div class="col-md-12">
							<button type="button" id="config_synk" class="btn btn-success btn-block btn-lg">
									  Config<i class="glyphicon glyphicon-log-in"></i>
							</button>
						</div>
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