<!DOCTYPE html>
<html>
	<head>
		<title>vente | ChaiPoint POS</title>	
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
				<div class="text-center hide" id="loading_image">
					<img src="<?php echo IMG;?>loader.gif"/>
				</div>

					<table class="table table-condensed">
						<thead><tr><th>Download</th><th>Upload</th></tr></thead>
						<tbody>
							<tr>
								<td class="text-center"><a class="btn btn-success sync-bt" id="store_sync_bt">Store</a></td>
								<td class="text-center"><a class="btn btn-success sync-bt" id="billing_sync_bt">Start Billing</a></td>
							</tr>
							<tr>
								<td class="text-center"><a class="btn btn-success sync-bt" id="staff_sync_bt">Staff</a></td>
								<td class="text-center"><a class="btn btn-success sync-bt" id="billing_stop_sync_bt">Stop Billing</a></td>
							</tr>
							<tr>
								<td class="text-center"><a class="btn btn-success sync-bt" id="config_sync_bt">Config</a></td>
								<td class="text-center">Attendance</td>
							</tr>
							<tr>
								<td class="text-center"><a class="btn btn-success sync-bt" id="design_sync_bt">Design Docs</a></td>
								<td class="text-center"></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" data-dismiss="modal" aria-hidden="true">
						Close
					</button>
				</div>
			</div>
		</div>
	</div>