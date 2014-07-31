<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>
				<h4 class="modal-title" id="payModalLabel">Payment</h4>
			</div>
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
								<span id="twt" class="label label-warning"></span> 
							</td>
						</tr>
						<tr>
							<td>Total Purchased Items :</td>
							<td>
								<span class="label label-warning" id="fcount"></span> 
							</td>
						</tr>
						<tr>
							<td>Paid by (* Need Change) :</td>
							<td>
								<select name="paid_by" id="paid_by" class="form-control">
									<option value="cash">Cash</option>
								</select>
							</td>
						</tr>
						<tr class="pcash">
							<td>Paid :</td>
							<td><input type="text" id="paid-amount" class="form-control"/></td>
						</tr>
						<tr class="pcash">
							<td>Return Change :</td>
							<td>
								<span class="label label-warning" id="balance">0</span>
							</td>
						</tr>
						<!--	<tr class="pcc" style="display: none;">
								<td>Credit Card No :</td>
								<td><input type="text" id="pcc" class="form-control"
									style="padding: 2px !important; height: auto !important;" /></td>
							</tr>
							<tr class="pcc" style="display: none;">
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
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					<button class="btn btn-success" id="submit-sale">Submit</button>
				</div>
			</div>
		</div>
	</div>