<style>
.panel-body {
padding: 0px;
}
.list-inline > li {
display: inline-block;
padding-right: 2px;
padding-left: 1px;
padding-top: 30px;
margin-left:0px !important;
}
.list-inline > ul {
margin-left:0px !important;
}
.btn3d {
transition: all .08s linear;
position: relative;
outline: medium none;
-moz-outline-style: none;
border: 0px;
margin-top: -15px;
top: 0;
width: 200px;
font-size: 14px;
font-weight:bold;
}
.store-selection {
color: #ffffff;
background-color: #00bb5e;
border-color: #222222;
}

.btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {
color: white;

}

.alert {
padding: 5px;
margin-bottom: 0px;
border: 1px solid transparent;
border-radius: 4px;
}
</style>
	<script src="<?php echo JS;?>pos/store.js" type="text/javascript"></script>
	<div class="container">
		<div class="col-md-16" id="login-box">
		 <form name='store_selection' id='store_selection' action='index.php?dispatch=home' method='post'>
		 <input type='hidden' name='store_id' id='store_id' />
		 <input type='hidden' name='store_name' id='store_name' />
		 <input type='hidden' name='store_code' id='store_code' />
		 <input type='hidden' name='bill_type' id='bill_type' />
		 <input type='hidden' name='store_message' id='store_message' />
			<div class="padded">
				<div class="panel">
					<div class="panel-body" style="padding-bottom:0;">
						<div class="alert alert-info" id="error_message" style="width:87.5%; font-size:17px; color:black;">
							Select Your Store
						</div>
					
						<ul class="list-unstyled list-inline" role="tablist" id="store_nav">
							<?php foreach($storeList as $key => $value) { ?>
							<li><a class="store-selection btn btn-default btn-lg btn3d" data-store_msg="<?php echo $value['store_message']; ?>" data-bill_type="<?php echo $value['bill_type'];?>" data-code="<?php echo $value['code']?>" id="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		</form>
		</div>
	</div>