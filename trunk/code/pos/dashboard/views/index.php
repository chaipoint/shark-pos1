<style>
.white-bg{
    background:#ffffff !important;
	height :35px;
	text-align:center;
	line-height:36px;
}
.active-bg {
	background:#B0E57C !important;
	height :35px;
	text-align:center;
	line-height:36px;
	
}
.inactive-bg {
	background:#FFAEAE !important;
	height :35px;
	text-align:center;
	line-height:36px;
}
.dashboard_div {
	width: 1050px;
	margin: 0 auto;
	padding: 0 0 9px 5px;
	
}
</style>
<script src="<?php echo JS;?>pos/home.js"></script>
<?php 
//echo '<pre>';print_r($reconcilation);echo '</pre>';?>
<!--<span class="label label-info">Devatha Plaza.. Your Data</span>-->

<?php if(!@$error) {?>
<div class="padded dashboard_div" id="dashboard_div" >
	<span class="padded">Message From the office for you</span>
	<div class="panel panel-info" style="min-height:80px;width:84%;margin-left:15px;background-color:#efefef">
		<div class="panel-body">
			<?php if(array_key_exists('data', $activity_data)){
					if(array_key_exists('StoreMessage', $activity_data['data']) && count($activity_data['data']['StoreMessage'])>0) {
						foreach($activity_data['data']['StoreMessage'] as $key => $value) {
						?>
			<h4><?php echo $value['Message']; ?></h4>
			<?php }}} else {?>
			<div class="alert alert-danger hide" style="width:40%;height:40%">No Message Found For Your Store.</div>
			<?php }?>
		</div>
	</div>
	
	<span class="padded">Activity Tracker of your Store</span>
	<div class="panel panel-info" style="min-height:80px;width:84%;margin-left:15px;background-color:#efefef">
		<div class="panel-body" style="margin-left:14%;">
			<?php if(array_key_exists('data', $activity_data)){
					if(array_key_exists('StoreActivity', $activity_data['data']) && count($activity_data['data']['StoreActivity'])>0) {
						foreach($activity_data['data']['StoreActivity'] as $key => $value) {
						?>
						
			<span class="col-md-3 <?php echo($value['Status']=='Y' ? 'active-bg' : 'inactive-bg');?>" style="min-width:30%;margin-top:5px;margin-left:5px"><strong><?php echo $value['Activity']; ?></strong></span>
			<?php }}} else {?>
			<div class="alert alert-danger hide" style="width:40%;height:40%">No Activity Found For Your Store.</div>
			<?php }?>
		</div>
	</div>
	
	<span class="padded">Your Store Sales on <?php echo date('d-m-Y', strtotime("-1 days")); ?></span>
	<div class="panel panel-info" style="min-height:80px;width:84%;margin-left:15px;background-color:#efefef">
		<div class="panel-body" style="margin-left:14%;">
			<span class="white-bg col-md-5" ><strong>Total Sales: <?php if(array_key_exists('data', $activity_data)){
					if(array_key_exists('TotalSale', $activity_data['data'])){ echo $activity_data['data']['TotalSale']; ?><?php }}?></strong></span>
			<span class="white-bg col-md-5" style="margin-left:5px"><strong>Target Sales: <?php if(array_key_exists('data', $activity_data)){
					if(array_key_exists('TargetSale', $activity_data['data'])){ echo $activity_data['data']['TargetSale']; ?><?php }}?></strong></span>
			</br>
			<span class="white-bg col-md-5" style="margin-top:5px;"><strong>Total COGS: <?php if(array_key_exists('data', $activity_data)){
					if(array_key_exists('COGS', $activity_data['data'])){ echo $activity_data['data']['COGS']; ?><?php }}?></strong></span>
			<span class="white-bg col-md-5" style="margin-top:5px;margin-left:5px"><strong>Gross Margin: <?php if(array_key_exists('data', $activity_data)){
					if(array_key_exists('GrossMargin', $activity_data['data'])){ echo $activity_data['data']['GrossMargin']; ?><?php }}?></strong></span>
		</div>
	</div>
	
</div>
<?php } else {?>
<div class="alert alert-danger text-center dashboard_div" style="width:55%;min-height:450px;margin-bottom:3px;"><?php echo INTERNET_ERROR;?></div>
<?php }?>
<div class="padded hide" id="reconcilation_div" style="min-height:400px;padding:5px;">
	<?php
		echo $reconcilation['data']['shift_table'];
		echo $reconcilation['data']['cash_reconciliation_table'];
	?>
</div>
<div class="padded hide" id="report_div" style="min-height:500px;padding:5px;">
	<?php $data = $data['data'];
		  require_once DIR.'/sales_register/views/paid_bills_table.php';
	?>
</div>

<?php 
	if(empty($_SESSION['user']['store']['id'])){
		$result = $this->getSessionData();
	}
	list($first) = explode(',', $_SESSION['user']['store']['bill_type']); 
?>
<div class="padded dashboard_div" >
		<button class="btn" id="dashboard" style="margin-left:13px;">Dashboard</button>
		<button class="btn" id="shift_data">Shift Data</button>
		<button class="btn" id="report_data">Sales Reports</button>
		<button class="btn" id="billing_sync">Data Sync</button>
		<button class="btn" id="caw_sync" data-store_id="<?php echo $_SESSION['user']['store']['id'];?>">Get Latest Store Data</button>
		<div class="btn-group" >
  			<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
				Petty Expense & Inward &nbsp;<span class="caret"></span>
			</button>
  			<ul class="dropdown-menu" role="menu">
    			<li><a href="#" id="add_expense">Add Expense</a></li>
    			<li><a href="#" id="view_expense">View Expense</a></li>
				<li><a href="#" id="add_inward">Add Inward</a></li>
			</ul>
		</div>
		<button class="btn" data-menu="<?php echo $first;?>" id="start_billing">Start Billing</button>
		
		
		
</div>
<?php require_once DIR.'/sales_register/views/modal_expense.php';?>
<?php require_once DIR.'/home/views/modal_inward.php';?>