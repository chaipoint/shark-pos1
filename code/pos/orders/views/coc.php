<script type="text/javascript" src="<?php echo JS?>pos/coc.js"></script>
<table class="table table-bordered table-stripped small">
	<caption class="text-right">
		<a class="btn btn-primary" href="<?php echo URL;?>?dispatch=orders.coc&status=New">New</a>
		<a class="btn btn-primary" href="<?php echo URL;?>?dispatch=orders.coc&status=Confirmed">Confirmed</a>
		<a class="btn btn-primary" href="<?php echo URL;?>?dispatch=orders.coc&status=Cancelled">Cancelled</a>
		<a class="btn btn-primary" href="<?php echo URL;?>?dispatch=orders.coc&status=Dispatched">Dispatched</a>
		<a class="btn btn-primary" href="<?php echo URL;?>?dispatch=orders.coc&status=Delivered">Delivered</a>
		<a class="btn btn-primary" href="<?php echo URL;?>?dispatch=orders.coc&status=Paid">Paid</a>
	</caption>
	<thead>
		<tr class="success">
			<th>Order No</th>
			<th>Customer Details</th>
			<th>Expected Delivery time</th>
			<th>Total</th>
			<th>Products</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$display = '';
			foreach($orders as $key => $data){
			$display .= '<tr data-order-id="'.$data['order_id'].'">
							<td class="text-right">'.$data['order_id'].'<br/><br/><div class="col-lg-12 btn-group-vertical"><button class="btn-sm btn btn-primary">Print</button></div></td>
							<td>
								<table>
									<tr><td>'.$data['name'].'</td></tr>
			                        <tr><td>'.$data['phone'].'</td></tr>
									<tr><td>'.$data['building'].'</td></tr>
									<tr><td>'.$data['floor'].'</td></tr>
									<tr><td>'.$data['flat'].'</td></tr>
									<tr><td>'.$data['landmark'].'</td></tr>
									<tr><td>'.$data['locality'].'</td></tr>
									<tr><td>'.$data['sublocality'].'</td></tr>
									<tr><td>'.$data['city'].'</td></tr>
								</table>
							</td>
							<td>'.$data['actual_delivery_time'].'</span></td>
							<td class="text-right">'.$data['net_amount'].'</td>
							<td>
								<table class="table">
									<tbody>
							';
								$product =  $data['products'];
								foreach($product as $pKey => $pValues){
									$display .='<tr><td>'.$pValues['name'].'</td><td>'.$pValues['qty'].'</td></tr>';
								}
					$display .='</tbody></table>
								</td>
								<td>
									<div class="btn-group-vertical">'.$action.'
										<!--<button class="btn btn-sm btn-primary">Confirm</button>
										<button class="btn btn-sm btn-primary">Cancel</button>
										<button class="btn btn-sm btn-primary">Dispatch</button>
										<button class="btn btn-sm btn-primary">Delivered</button>
										<button class="btn btn-sm btn-primary">Paid</button>-->
									</div>
								</td>
							</tr>';
			}
			echo $display;
		?>
	</tbody>
</table>