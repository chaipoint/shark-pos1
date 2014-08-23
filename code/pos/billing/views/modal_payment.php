<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close close-model" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>
				<h4 class="modal-title" id="payModalLabel">Payment</h4>
			</div>
			<form method="post" name="payment-form" id="payment-form">
				<input type="hidden" name="delivery_channel" id="delivery_channel">
				<input type="hidden" name="delivery_channel_name" id="delivery_channel_name">
				<input type="hidden" name="booking_channel" id="booking_channel">
				<input type="hidden" name="is_cod" id="is_cod">
				<input type="hidden" name="is_prepaid" id="is_prepaid">
				<input type="hidden" name="is_credit" id="is_credit">
				<input type="hidden" name="cancel_reason" id="cancel_reason">
				<input type="hidden" name="bill_status" id="bill_status">

			<div class="modal-body">
				<table class="table table-striped">
					<tbody>
						<tr>
							<td width="50%">Customer <!--<a href="#"
									class="btn btn-primary btn-xs showCModal"><i
										class="glyphicon glyphicon-plus-sign"></i> Add Customer </a>-->
							</td>
							<td width="50%">
								<span class="inv_cus_con"> 
									<select class="form-control pcustomer" id="billing_customer">
										<option value="wic">Walk-in Client</option>
									</select>
								</span>
							</td>
						</tr>
						<tr>
							<td>Total Payable Amount :</td>
							<td>
								<h3 style="margin:0px;"><span id="twt" class="label label-warning"></span></h3> 
							</td>
						</tr>
						<tr>
							<td>Total Purchased Items :</td>
							<td>
								<h3 style="margin:0px;"><span class="label label-warning" id="fcount"></span></h3>
							</td>
						</tr>
						<tr>
							<td>Paid by:</td>
							<td>
								<a class="btn btn-sm btn-primary payment-type-bt" data-value="cash">Cash</a>
								<a class="btn btn-sm btn-primary payment-type-bt" data-value="ppc">PPC</a>
								<input type="hidden" name="paid_by" id="paid_by">
							</td>
						</tr>
						<tr class="pcash">
							<td>Paid :</td>
							<td><input type="text" id="paid-amount" class="form-control"/></td>
						</tr>
						<tr class="pcash">
							<td>Return Change :</td>
							<td>
								<h3 style="margin:0px;"><span class="label label-warning" id="balance">0</span></h3>
							</td>
						</tr>
						<tr class="ppc" style="display: none;">
								<td>Card No :</td>
								<td>
									<input type="text" id="ppc" class="form-control"/>
								</td>
						</tr>
						<!--<tr class="pcc" style="display: none;">
								<td>Credit Card Holder :</td>
								<td><input type="text" id="pcc_holder" class="form-control"
									style="padding: 2px !important; height: auto !important;" /></td>
							</tr>
							<tr class="pcheque" style="display: none;">
								<td>Cheque No :</td>
								<td><input type="text" id="cheque_no" class="form-control"
									style="padding: 2px !important; height: auto !important;" /></td>
							</tr> -->
						</tbody>

					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="close-model btn btn-primary" data-dismiss="modal">Close</button>
					<button class="btn btn-success" id="submit-sale">Submit</button>
				</div>
				</form>
			</div>
		</div>
	</div>