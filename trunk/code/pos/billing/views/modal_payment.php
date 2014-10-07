<style>
.modal-header {
	padding: 5px;
}
.modal-body {
	padding: 5px;
}
.modal-footer {
	padding: 3px;
}
.table {
	margin-bottom: -2px;
}
.table > tbody > tr > td {
	padding: 4px;
}
</style>
<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close close-model" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>
				<h4 class="modal-title" id="payModalLabel">Payment <?php if(!empty($_GET['order'])) echo 'For Order No: '.$_GET['order']; ?></h4>
			</div>
			<form method="post" name="payment-form" id="payment-form">
				<input type="hidden" name="delivery_channel" id="delivery_channel">
				<input type="hidden" name="delivery_channel_name" id="delivery_channel_name">
				<input type="hidden" name="booking_channel" id="booking_channel">
				<input type="hidden" name="booking_channel_name" id="booking_channel_name">
				<input type="hidden" name="is_cod" id="is_cod">
				<input type="hidden" name="is_prepaid" id="is_prepaid">
				<input type="hidden" name="is_credit" id="is_credit">
				<input type="hidden" name="cancel_reason" id="cancel_reason">
				<input type="hidden" name="bill_status" id="bill_status">
				<input type="hidden" name="bill_status_id" id="bill_status_id">
				<input type="hidden" name="billing_customer_city" id="billing_customer_city">
				<input type="hidden" name="billing_customer_locality" id="billing_customer_locality">
				<input type="hidden" name="billing_customer_sub_locality" id="billing_customer_sub_locality">
				<input type="hidden" name="billing_customer_landmark" id="billing_customer_landmark">
				<input type="hidden" name="billing_customer_company_name" id="billing_customer_company_name">
			<div class="modal-body">
				<table class="table table-striped" style="font-size:12px;">
					<tbody>
						<tr class="row">
							<td>Customer Type</td>
							<td>
								<select class="form-control" name="customer_type" id="customer_type">
									<option value="walk_in">Walk In</option>
									<option  value="caw">CAW</option>
								</select>
							</td>
						</tr>
						<tr class="row">
							<td width="50%">Customer <!--<a href="#"
									class="btn btn-primary btn-xs showCModal"><i
										class="glyphicon glyphicon-plus-sign"></i> Add Customer </a>-->
							</td>
							<td width="50%">
								<span class="inv_cus_con"> 
									<input type="text" id="billing_customer" class="form-control" name="billing_customer" value="Walkin Client"/>	
									<select name="customer_name" class="form-control" id="customer_name">
										<option value="">Select One</option>
										<?php
											foreach($customer as $key => $value){
												?>
												<option value="<?php echo $value['id'];?>"><?php echo $value['label'];?></option>
												<?php
											}
										?>										
									</select>
								</span>
								<span class="inv_cus_con hide" id="customer"> </span>
							</td>
						</tr>

						<tr class="row">
							<td width="50%">Phone Number <!--<a href="#"
									class="btn btn-primary btn-xs showCModal"><i
										class="glyphicon glyphicon-plus-sign"></i> Add Customer </a>-->
							</td>
							<td width="50%">
								<span class="inv_cus_con"> 
									<input type="text" id="phone_number" class="form-control" name="phone_number" autofocus />	
								</span>
								<span class="inv_cus_con hide" id="phone"> </span>
							</td>
						</tr>

						<tr class='row hide' > 
							<td width="50%">City </td>
							<td width="50%">
								<span class="inv_cus_con" id="city"> </span>
							</td>
						</tr>

						<tr class='row hide'>
							<td width="50%">Locality / Sublocality </td>
							<td width="50%">
								<span class="inv_cus_con" id="locality"> </span> /
								<span class="inv_cus_con" id="sublocality"> </span>
							</td>
						</tr>
						
						<tr class='row hide'>
							<td width="50%">Company / Landmark </td>
							<td width="50%">
								<span class="inv_cus_con" id="company"> </span> / 
								<span class="inv_cus_con" id="landmark"> </span>
							</td>
						</tr>

						<tr class="row">
							<td>Total Payable Amount :</td>
							<td>
								<h3 style="margin:0px;"><span id="twt" class="label label-warning"></span></h3> 
							</td>
						</tr>

						<tr class="row pcash">
							<td>Paid :</td>
							<td><input type="text" id="paid-amount" class="form-control"/></td>
						</tr>

						<tr class="row">

							<td>Payment Mode:</td>
							<td><?php if(is_array($config_data['payment_mode']) && count($config_data['payment_mode'])>0){
										foreach ($config_data['payment_mode'] as $key => $value) { ?>
										
									<a class="btn btn-sm btn-primary payment-type-bt <?php echo ($value=='Cash' ? 'btn-success' : ''); ?>" data-value="<?php echo strtolower($value); ?>" data-id="<?php echo $key; ?>"><?php echo $value; ?></a>
								<?php }} ?>
								<input type="hidden" name="paid_by" id="paid_by" value="cash">
							</td>
						</tr>
						
						<tr class="row pcash">
							<td>Return Change :</td>
							<td>
								<h3 style="margin:0px;"><span class="label label-warning" id="balance">0</span></h3>
							</td>
						</tr>
						<tr class="ppc row" style="display:none;">
								<td>Card No :</td>
								<td>
									<input type="text" id="ppc" class="form-control"/>
									<span id="loading_image" class="hide"><img class="text-center" src="<?php echo IMG;?>loader.gif"/></span>
								</td>
						</tr>
                        <tr class="ppc_balance row" style="display:none;">
								<td>Your Balance :</td>
								<td>
                                  <span id="ac_balance"></span>
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
					<button class="btn btn-success" id="submit-sale">Save</button>
					<button type="button" class="close-model btn btn-primary" data-dismiss="modal">Close</button>
					
				</div>
				</form>
			</div>
		</div>
	</div>