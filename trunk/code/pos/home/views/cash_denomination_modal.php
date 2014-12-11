<style>
.btn1{width: 55%; text-align: center; font-weight: bold;}
.cash-qty{width: 40%}
.content {width: 75%}
.header {padding: 5px;}
.body {padding: 0px;}
.footer {padding: 0px;}
.table1 > tbody > tr > td {padding: 3px; vertical-align: middle; width: 32%}
</style>
<div class="modal fade" id="cashModal" tabindex="-1" role="dialog" aria-labelledby="cashModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content content">
			<div class="modal-header modal-primary header">
				<button type="button" class="close close-model" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>
				<h4 class="modal-title" id="cashModalLabel">Cash Denominations</h4>
			</div>
			<form method="post" name="cash-denomination-form" id="cash-denomination-form" autocomplete="off">
				<input type="hidden" name="card_number" id="card_number">
			<div class="modal-body body">
				<table class="table table-striped table1" id="select-bt" style="font-size:12px;">
					<tbody>
						<tr class="row">
							<td><strong>Denominations</strong></td>
							<td><strong>Quantity</strong></td>
							<td><strong>Amount</strong></td>
						</tr>
						<tr class="row" cash-value="10">
							<td><button type="button" class="btn btn-sm btn-primary btn1">10</button></td>
							<td><input type="text"  value="0" class="cash-qty form-control"></td>
							<td><span class="total pull-left">0</span></td>
						</tr>
						<tr class="row" cash-value="20">
							<td><button type="button" class="btn btn-sm btn-primary btn1">20</button></td>
							<td><input type="text" value="0" class="cash-qty form-control"></td>
							<td><span class="total pull-left">0</span></td>
						</tr>
						<tr class="row" cash-value="50">
							<td><button type="button" class="btn btn-sm btn-primary btn1">50</button></td>
							<td><input type="text" value="0" class="cash-qty form-control"></td>
							<td><span class="total pull-left">0</span></td>
						</tr>
						<tr class="row" cash-value="100">
							<td><button type="button" class="btn btn-sm btn-primary btn1">100</button></td>
							<td><input type="text" value="0" class="cash-qty form-control"></td>
							<td><span class="total pull-left">0</span></td>
						</tr>
						<tr class="row" cash-value="500">
							<td><button type="button" class="btn btn-sm btn-primary btn1">500</button></td>
							<td><input type="text" value="0" class="cash-qty form-control"></td>
							<td><span class="total pull-left">0</span></td>
						</tr>
						<tr class="row" cash-value="sodex">
							<td><button type="button" class="btn btn-sm btn-warning btn1">Sodex</button></td>
							<td><input type="text" value="0" id="quantity_sodex" style="width:40%" class="qunt sodex form-control"></td>
							<td><input type="text" value="0" style="width:50%" class="total-ticket form-control"></td>
						</tr>
						<tr class="row" cash-value="restaurent">
							<td><button type="button" class="btn btn-sm btn-warning btn1">TR</button></td>
							<td><input type="text" value="0" id="quantity_restaurent" style="width:40%" class="qunt tr form-control"></td>
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
			<div class="modal-footer footer">
					<button class="btn btn-success" id="save-cash">Save</button>
					<button type="button" class="close-model btn btn-primary" data-dismiss="modal">Close</button>
					
			</div>
			</form>
		</div>
	</div>
</div>