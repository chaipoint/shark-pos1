<div class="panel panel-info" style="margin-left:168px;margin-right:184px;margin-top:20px;" id="dashboard_panel">
	<div class="panel-body tabbable">
		<?php list($first) = explode(',', $_SESSION['user']['store']['bill_type']); ?>
		<button class="alert alert-success store-operation" data-menu="<?php echo $first;?>" id="start_billing" style="margin-left:468px;width:160px;margin-top:6px;color:black">Start Billing</button>
		<button class="alert alert-success" id="billing_sync" style="margin-left:468px;width:160px;margin-top:6px;color:black">Start Data Sync</button>
	</div>
</div>