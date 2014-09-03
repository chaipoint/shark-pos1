<script type="text/javascript" src="<?php echo JS?>pos/coc.js"></script>
<div class="panel panel-info" style="margin-left:20px;margin-right:20px;"> 
  <div class="panel-heading">COC Orders</div>
	 <div class="panel-body tabbable">
        <ul class="nav nav-pills" role="tablist">
		  <li class="<?php echo ($status == 'New' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=New">New (<span id="New"><?php echo $order_count['New']; ?></span>)</a></li>
		  <li class="<?php echo ($status == 'Confirmed' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=Confirmed">Confirmed (<span id="Confirmed"><?php echo $order_count['Confirmed']; ?></span>)</a></li>
		  <li class="<?php echo ($status == 'Cancelled' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=Cancelled">Cancelled (<span id="Cancelled"><?php echo $order_count['Cancelled']; ?></span>)</a></li>
		  <li class="<?php echo ($status == 'Dispatched' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=Dispatched">Dispatched (<span id="Dispatched"><?php echo $order_count['Dispatched']; ?></span>)</a></li>
		  <li class="<?php echo ($status == 'Delivered' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=Delivered">Delivered (<span id="Delivered"><?php echo $order_count['Delivered']; ?></span>)</a></li>
		  <li class="<?php echo ($status == 'Paid' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=Paid">Paid (<span id="Paid"><?php echo $order_count['Paid']; ?></span>)</a></li>
	    </ul>

         <table class="table table-bordered table-stripped" id="order-holder" style="font-size:10px;">
           <thead>
		     <tr  style="font-size:12px;background-color:#428bca;color:white;">
			   <th>Order No</th>
			   <th style="width:120px;">Customer Details</th>
			   <th style="width:150px;">Address</th>
			   <th style="width:100px;">Delivery Time</th>
			   <th style="width:100px;">Booking Time</th>
			   <th style="width:100px;">Channel</th>
			   <th style="width:80px;">Amount</th>
			   <th style="width:220px;">Details</th>
			   <th style="width:150px;">Comment</th>
			   <!--<th style="width:300px;">Schedule</th>
			   <th style="width:320px;">Products Detail</th>-->
			   <th><?php echo ($status == 'Cancelled' ? 'Reason' : 'Action');?></th>
		     </tr>
	       </thead>
	     <tbody>
		<?php
			$display = '';
			$total = $total_qty = 0;
			foreach($orders as $key => $data){
			$display .= '<tr data-order-id="'.$data['order_id'].'" data-order-details=\''.json_encode($data).'\'>
							<td class="text-center">'.$data['order_id'].'</td>
							<td>
								<table>
									<tr><td>'.$data['name'].'</td></tr>
			                        <tr><td>'.$data['phone'].'</td></tr>
								</table>
							</td>
							<td>
								<table>
									<tr><td>'.$data['company'].'</td></tr>
									<tr><td>'.$data['building'].'</td></tr>
									<tr><td>'.$data['floor'].",".$data['flat'].'</td></tr>
									<tr><td>'.$data['locality'].",".$data['sublocality'].'</td></tr>
									<tr><td>'.$data['landmark'].'</td></tr>
								</table>
							</td>
							<td class="text-center"><b style="font-size:12px;">'.date('h:i A',strtotime($data['actual_delivery_time'])).'</b></td>
							<td class="text-center"><b style="font-size:12px;">'.date('h:i A',strtotime($data['order_time'])).'</b></td>
							<td class="text-center">'.$data['channel_name'].'</td>
							<td class="text-right"><b>'.$data['net_amount'].'</b></td>
							<td><a class="products_list_toggle" data-target="product_list_'.$data['order_id'].'" style="float:right;font-size:12px;">Hide Detail</a>
									<table class="table toggle-table" id="product_list_'.$data['order_id'].'">
										<tbody>';
								$product =  $data['products'];
								foreach($product as $pKey => $pValues){
									$display .='<tr><td>'.$pValues['name'].'</td><td>'.$pValues['qty'].'</td></tr>';
								}
					$display .='</tbody></table>
								</td>
								
								<td>'.$data['comment'].'</td>
								
								<td>'.($status == 'Delivered' ? "<span style='float:right'>".$data['delivery_boy']."</span>" : '')." ".($status == 'Cancelled' ? $data['cancel_reason'] : '')." ".sprintf($action,$data['order_id']).'
									
										<!--<button class="btn btn-sm btn-primary">Confirm</button>
										<button class="btn btn-sm btn-primary">Cancel</button>
										<button class="btn btn-sm btn-primary">Dispatch</button>
										<button class="btn btn-sm btn-primary">Delivered</button>
										<button class="btn btn-sm btn-primary">Paid</button>-->
									
								</td>
							</tr>';
							$total +=$data['net_amount'];
							$total_qty +=count($data['products']);
			}
			echo $display;
		?>
	   </tbody>
	   <?php if($total<>0){ ?>
	   <tfoot>
	   	<tr>
	   		<th class="text-center">Total</th>
	   		<th colspan="5"></th>
	   		<th class="text-right"><?php echo number_format($total,2); ?></th>
	   		<th colspan="3"></th>
	   	</tr>
	   </tfoot>
	   <?php } ?>
    </table>
  </div>
</div>
