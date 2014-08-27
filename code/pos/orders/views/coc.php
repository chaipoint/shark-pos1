<script type="text/javascript" src="<?php echo JS?>pos/coc.js"></script>
<div class="panel panel-info" style="margin-left:20px;margin-right:20px;"> 
  <div class="panel-heading">COC Orders</div>
	 <div class="panel-body tabbable">
        <ul class="nav nav-pills" role="tablist">
		  <li class="<?php echo ($status == 'New' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders.coc&status=New">New (<span id="New"><?php echo $order_count['New']; ?></span>)</a></li>
		  <li class="<?php echo ($status == 'Confirmed' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders.coc&status=Confirmed">Confirmed (<span id="Confirmed"><?php echo $order_count['Confirmed']; ?></span>)</a></li>
		  <li class="<?php echo ($status == 'Cancelled' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders.coc&status=Cancelled">Cancelled (<span id="Cancelled"><?php echo $order_count['Cancelled']; ?></span>)</a></li>
		  <li class="<?php echo ($status == 'Dispatched' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders.coc&status=Dispatched">Dispatched (<span id="Dispatched"><?php echo $order_count['Dispatched']; ?></span>)</a></li>
		  <li class="<?php echo ($status == 'Delivered' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders.coc&status=Delivered">Delivered (<span id="Delivered"><?php echo $order_count['Delivered']; ?></span>)</a></li>
		  <li class="<?php echo ($status == 'Paid' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders.coc&status=Paid">Paid (<span id="Paid"><?php echo $order_count['Paid']; ?></span>)</a></li>
	    </ul>

         <table class="table table-bordered table-stripped" id="order-holder" style="font-size:10px;">
           <thead>
		     <tr  style="font-size:12px;background-color:#428bca;color:white;">
			   <th>Order No</th>
			   <th style="width:300px;">Customer Details</th>
			   <th style="width:260px;">Schedule</th>
			   <th style="width:280px;">Products Detail</th>
			   <th><?php echo ($status == 'Cancelled' ? 'Reason' : 'Action');?></th>
		     </tr>
	       </thead>
	     <tbody>
		<?php
			$display = '';
			$total = 0;
			foreach($orders as $key => $data){
			$display .= '<tr data-order-id="'.$data['order_id'].'" data-order-details=\''.json_encode($data).'\'>
							<td class="text-center">'.$data['order_id'].'</td>
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
							<td><span class="label label-primary">Delivery Time</span><b style="font-size:12px;margin-left:22px;">'.$data['actual_delivery_time'].'</b><br/><br/><span class="label label-primary">Booking Time</span><b style="font-size:12px;margin-left:20px;">'.$data['order_date'].' '.$data['order_time'].'</b></td>
							<td>'.$data['net_amount'].' <a class="products_list_toggle" data-target="product_list_'.$data['order_id'].'" style="float:right;font-size:12px;">Show Detail</a>
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
									'.($status == 'Delivered' ? "<span style='float:right'>".$data['delivery_boy']."</span>" : '')." ".($status == 'Cancelled' ? $data['cancel_reason'] : '')." ".sprintf($action,$data['order_id']).'
									
										<!--<button class="btn btn-sm btn-primary">Confirm</button>
										<button class="btn btn-sm btn-primary">Cancel</button>
										<button class="btn btn-sm btn-primary">Dispatch</button>
										<button class="btn btn-sm btn-primary">Delivered</button>
										<button class="btn btn-sm btn-primary">Paid</button>-->
									
								</td>
							</tr>';
							$total +=$data['net_amount'];
			}
			echo $display;
		?>
	   </tbody>
	   <?php if($total<>0){ ?>
	   <tfoot>
	   	<tr>
	   		<th class="text-center">Total</th>
	   		<th colspan="2"></th>
	   		<th colspan="2"><?php echo $total; ?></th>
	   	</tr>
	   </tfoot>
	   <?php } ?>
    </table>
  </div>
</div>
