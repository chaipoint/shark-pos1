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
	<script src="<?php echo JS;?>pos/store.js" type="text/javascript"></script>
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
	                                 <?php foreach($storeList as $key => $value) {?>
	                                 <option value="<?php echo $value['key'];?>"><?php echo $value['value']['name'];?></option>
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