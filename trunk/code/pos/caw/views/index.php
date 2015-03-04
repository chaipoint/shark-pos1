<style>
.panel-body {
padding: 0px;
}
.list-inline > li {
display: inline-block;
padding-right: 2px;
padding-left: 1px;
padding-top: 20px;
}
.btn3d {
transition: all .08s linear;
position: relative;
outline: medium none;
-moz-outline-style: none;
border: 0px;
margin-top: -15px;
top: 0;
width : auto;
font-size: 14px;
font-weight:bold;
}
.customer-selection {
color: #ffffff;
background-color: #00bb5e;
border-color: #222222;
}

.btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {
color: white;

}
</style>
<?php //print_r($customerList);?>
<script src="<?php echo JS;?>pos/caw.js" type="text/javascript"></script>
<div class="padded menu_div"  style="min-height:450px;">
<?php if(!empty($customerList)) {?>
	<div class="panel panel-info" style="width:85%"> 
		<div class="panel-heading">Select Your CAW</div>
			<div class="col-md-16" id="login-box" >
				<form name='customer_selection' id='customer_selection' action='index.php?dispatch=caw.schedule' method='post'>
					<div class="padded">
						<input name="customer_name" id="customer_name" type="hidden" />
						<input name="customer_id" id="customer_id" type="hidden" />
						<div class="panel-body" style="padding-bottom:0;">
							<ul class="list-unstyled list-inline" role="tablist" id="store_nav">
								<?php foreach($customerList as $key => $value) { ?>
								<li><a class="customer-selection btn btn-default btn-lg btn3d" data-type="<?php echo $value['type'] ?>" id="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></a></li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</form>
			</div>
<?php } else { ?>
	<div class="alert alert-danger" id="error_message" style="width:40%;height:40%">No Customer Found. &nbsp&nbsp&nbsp&nbsp
		<input type="button" class="btn btn-success" id="caw_sync" data-store_id="<?php echo $_SESSION['user']['store']['id'];?>" value="Get Latest CAW Data"/>
	</div>
<?php }?>
	<div class="hide" id="schedule_div" style="width:88%;margin-left:10px">
		<table class="table table-bordered table-stripped" id="schedule_table" style="font-size:10px;">
			<thead>
				<tr style="font-size:12px;background-color:#428bca;color:white;">
					<th style="width:120px;">Production Start Time</th>
					<th style="width:120px;">Dispatch Time</th>
					<th style="width:120px;">Delivery Time</th>
					<th style="width:120px;">Item Details</th>
					<th style="width:120px;">Action</th>
					
				</tr>
			</thead>
			<tbody>
			</tbody>
	   </table>
	</div>
</div>
 </div>


	