<script type="text/javascript" src="<?php echo JS?>pos/coc.js"></script>
<table class="table table-bordered table-stripped" id="order-holder" style="font-size:10px;">
	<caption class="text-left">
		<a class="btn btn-sm <?php echo ($status == 'New' ? 'btn-default' : 'btn-primary');?>" href="<?php echo URL;?>?dispatch=orders.coc&status=New">New</a>
		<a class="btn btn-sm <?php echo ($status == 'Confirmed' ? 'btn-default' : 'btn-primary');?>" href="<?php echo URL;?>?dispatch=orders.coc&status=Confirmed">Confirmed</a>
		<a class="btn btn-sm <?php echo ($status == 'Cancelled' ? 'btn-default' : 'btn-primary');?>" href="<?php echo URL;?>?dispatch=orders.coc&status=Cancelled">Cancelled</a>
		<a class="btn btn-sm <?php echo ($status == 'Dispatched' ? 'btn-default' : 'btn-primary');?>" href="<?php echo URL;?>?dispatch=orders.coc&status=Dispatched">Dispatched</a>
		<a class="btn btn-sm <?php echo ($status == 'Delivered' ? 'btn-default' : 'btn-primary');?>" href="<?php echo URL;?>?dispatch=orders.coc&status=Delivered">Delivered</a>
		<a class="btn btn-sm <?php echo ($status == 'Paid' ? 'btn-default' : 'btn-primary');?>" href="<?php echo URL;?>?dispatch=orders.coc&status=Paid">Paid</a>
	</caption>
	<thead>
		<tr class="success">
			<th>Order No</th>
			<th>Customer Details</th>
			<th>Expected Delivery time</th>
			<th>Products</th>
			<th><?php echo ($status == 'Cancelled' ? 'Reason' : 'Action');?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$display = '';
			foreach($orders as $key => $data){
			$display .= '<tr data-order-id="'.$data['order_id'].'" data-order-details=\''.json_encode($data).'\'>
							<td class="text-right">'.$data['order_id'].'<button class="btn btn-primary btn-sm generate-bill" data-order-id="'.$data['order_id'].'">Bill</button></td>
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
							<td><span class="label label-primary">Delivery Time</span><br/>'.$data['actual_delivery_time'].'<br/><br/><span class="label label-primary">Booking Time</span><br/>'.$data['order_date'].' '.$data['order_time'].'</td>
							<td><a class="products_list_toggle" data-target="product_list_'.$data['order_id'].'">'.$data['net_amount'].'</a>
								<table class="hide table toggle-table" id="product_list_'.$data['order_id'].'">
									<tbody>
							';
								$product =  $data['products'];
								foreach($product as $pKey => $pValues){
									$display .='<tr><td>'.$pValues['name'].'</td><td>'.$pValues['qty'].'</td></tr>';
								}
					$display .='</tbody><tfoot><tr><td></td><td>'.$data['net_amount'].'</td></tr></tfoot></table>
								</td>
								<td>
									'.($status == 'Cancelled' ? $data['cancel_reason'] : '')." ".$action.'
									
										<!--<button class="btn btn-sm btn-primary">Confirm</button>
										<button class="btn btn-sm btn-primary">Cancel</button>
										<button class="btn btn-sm btn-primary">Dispatch</button>
										<button class="btn btn-sm btn-primary">Delivered</button>
										<button class="btn btn-sm btn-primary">Paid</button>-->
									
								</td>
							</tr>';
			}
			echo $display;
		?>
	</tbody>
</table>