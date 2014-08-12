<!DOCTYPE html>
<html>
	<head>
		<title>POS Version 1.1</title>	
		<?php require_once 'header_externals.php';?>
	</head>
	<body>

	<div class="modal fade" id="sync-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>
					<h4 class="modal-title" id="today-sale-header">Data Sync</h4>
				</div>
				<div class="modal-body">
							<a class="btn btn-success sync-bt" id="store_sync_bt">Store</a>
							<a class="btn btn-success sync-bt" id="staff_sync_bt">Staff</a>
							<a class="btn btn-success sync-bt" id="billing_sync_bt">Billing</a>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" data-dismiss="modal" aria-hidden="true">
						Close
					</button>
				</div>
			</div>
		</div>
	</div>