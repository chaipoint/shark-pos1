<style>
.label-bg{font-size: 165%}
.cash-qty{width: 40%}
.modal-content {width: 75%}
.modal-header {padding: 5px;}
.modal-body {padding: 0px;}
.modal-footer {padding: 0px;}
.table > tbody > tr > td {padding: 3px; vertical-align: middle; width: 32%}
</style>
<div class="modal fade" id="cashModal" tabindex="-1" role="dialog" aria-labelledby="cashModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close close-model" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>
				<h4 class="modal-title" id="cashModalLabel">Cash Denominations</h4>
			</div>
			<form method="post" name="cash-denomination-form" id="cash-denomination-form" autocomplete="off">
				<input type="hidden" name="card_number" id="card_number">
			<div class="modal-body">
				<table class="table table-striped" id="select-bt" style="font-size:12px;">
					<tbody>
						<tr class="row">
							<td><strong>Denominations</strong></td>
							<td><strong>Quantity</strong></td>
							<td><strong>Amount</strong></td>
						</tr>
						<tr class="row" cash-value="10">
							<td><span class="label label-bg label-warning">10</span></td>
							<td><input type="text"  value="0" class="cash-qty form-control"></td>
							<td><span class="total pull-left">0</span></td>
						</tr>
						<tr class="row" cash-value="20">
							<td><span class="label label-bg label-warning">20</span></td>
							<td><input type="text" value="0" class="cash-qty form-control"></td>
							<td><span class="total pull-left">0</span></td>
						</tr>
						<tr class="row" cash-value="50">
							<td><span class="label label-bg label-warning">50</span></td>
							<td><input type="text" value="0" class="cash-qty form-control"></td>
							<td><span class="total pull-left">0</span></td>
						</tr>
						<tr class="row" cash-value="100">
							<td><span class="label label-bg label-warning">100</span></td>
							<td><input type="text" value="0" class="cash-qty form-control"></td>
							<td><span class="total pull-left">0</span></td>
						</tr>
						<tr class="row" cash-value="500">
							<td><span class="label label-bg label-warning">500</span></td>
							<td><input type="text" value="0" class="cash-qty form-control"></td>
							<td><span class="total pull-left">0</span></td>
						</tr>
						<tr class="row" cash-value="sodex">
							<td><span class="label label-bg label-warning">Sodex</span></td>
							<td><input type="text" value="0" id="quantity_sodex" style="width:40%" class="sodex form-control"></td>
							<td><input type="text" value="0" style="width:50%" class="total-ticket form-control"></td>
						</tr>
						<tr class="row" cash-value="restaurent">
							<td><span class="label label-bg label-warning">Ticket Restaurent</span></td>
							<td><input type="text" value="0" id="quantity_restaurent" style="width:40%" class="tr form-control"></td>
							<td><input type="text" value="0" style="width:50%" class="total-ticket form-control"></td>
						</tr>
						<tr class="row">
							<td></td>
							<td><strong>Total Amount</strong></td>
							<td><strong><span id="total-cash" class="pull-left">0</span></strong></td>
						</tr>
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
					<button class="btn btn-success" id="save-cash">Save</button>
					<button type="button" class="close-model btn btn-primary" data-dismiss="modal">Close</button>
					
			</div>
			</form>
		</div>
	</div>
</div>