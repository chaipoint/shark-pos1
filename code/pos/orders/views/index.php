<style>
	 li {
 	   list-style-type: none;
	 }
	 ul {
	   padding-left:0px
	}
	.size {font-size:10px}
	.bt-update-status {width:80%;margin-top:5%;}
	.print {width:80%}
	
</style>
<script type="text/javascript" src="<?php echo JS?>pos/coc.js"></script>
<div class="padded menu_div"  style="min-height:450px;">
<div class="panel panel-info" style="width:85%"> 
  
	 <div class="panel-body tabbable">
        <ul class="nav nav-pills" role="tablist" style="margin-top:-13px;">
		  <li class="<?php echo ($status == 'New' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=New">New&nbsp;&nbsp;<span id="New" class="" <?php echo ($order_count['New']!=0) ? 'style="color:red"' : ''; ?>><?php echo $order_count['New']; ?></span></a></li>
		  <li class="<?php echo ($status == 'Confirmed' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=Confirmed">Confirmed&nbsp;&nbsp;<span id="Confirmed" class="" <?php echo ($order_count['Confirmed']!=0) ? 'style="color:red"' : ''; ?>><?php echo $order_count['Confirmed']; ?></span></a></li>
		  <li class="<?php echo ($status == 'Cancelled' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=Cancelled">Cancelled&nbsp;&nbsp;<span id="Cancelled" class="" <?php echo ($order_count['Cancelled']!=0) ? 'style="color:red"' : ''; ?>><?php echo $order_count['Cancelled']; ?></span></a></li>
		  <li class="<?php echo ($status == 'Dispatched' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=Dispatched">Dispatched&nbsp;&nbsp;<span id="Dispatched" class="" <?php echo ($order_count['Dispatched']!=0) ? 'style="color:red"' : ''; ?>><?php echo $order_count['Dispatched']; ?></span></a></li>
		  <li class="<?php echo ($status == 'Delivered' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=Delivered">Delivered&nbsp;&nbsp;<span id="Delivered" class="" <?php echo ($order_count['Delivered']!=0) ? 'style="color:#1a1a1a"' : ''; ?>><?php echo $order_count['Delivered']; ?></span></a></li>
		  <li class="<?php echo ($status == 'Paid' ? 'active' : '');?>"><a href="<?php echo URL;?>?dispatch=orders&status=Paid">Paid&nbsp;&nbsp;<span id="Paid" class="" <?php echo ($order_count['Paid']!=0) ? 'style="color:#1a1a1a"' : ''; ?>><?php echo $order_count['Paid']; ?></span></a></li>
	    </ul>

         <table class="table table-bordered table-stripped" id="order-holder" style="font-size:10px;">
           <thead>
		     <tr  style="font-size:12px;background-color:#428bca;color:white;">
			   <th style="width:120px;">Order No / Bill No</th>
			   <th style="width:142px;">Customer Details</th>
			   <th style="width:158px;">Order Details</th>
			   <th style="width:60px;">Amount</th>
			   <th style="width:160px;">Item Summary</th>
			   <th style="width:120px;">Comment</th>
			   <th style="width:120px;"><?php echo ($status == 'Cancelled' ? 'Reason' : 'Action');?></th>
		     </tr>
	       </thead>
	     <tbody>
		<?php
			$display = '';
			$total = $total_qty = 0; $bill_no ='';
			if(is_array($orders) && count($orders)){
			foreach($orders as $key => $data){
				$bill_no = (array_key_exists($data['order_id'], $billArray) ? $billArray[$data['order_id']] : '');
				
			$display .= '<tr data-order-id="'.$data['order_id'].'" data-order-details=\''.json_encode($data).'\'>
							<td class="text-center"><strong>'.$data['order_id'].' / '.$bill_no.'</strong></td>
							<td>
								<table>
									<tr>
										<td class="size">'.$data['name'].'</td>
									</tr>
			                        <tr style="border-bottom:1px solid #000">
										<td class="size">'.$data['phone'].'</td>
									</tr>
									<tr>
										<td class="size">'.$data['company'].'</td>
									</tr>
									<tr>
										<td class="size">'.$data['building'].'</td>
									</tr>
									<tr>
										<td class="size">'.$data['floor'].",".$data['flat'].'</td>
									</tr>
									<tr>
										<td class="size">'.$data['locality'].",".$data['sublocality'].'</td>
									</tr>
									<tr>
										<td class="size">'.$data['landmark'].'</td>
									</tr>
								</table>
							</td>
							<td class="text-center">
								<table class="table toggle-table">
									<tbody>
										<tr>
											<td style="float:left;">Booking Time:</td>
											<td><b style="font-size:12px;float:right;">'.date('h:i A',strtotime($data['order_time'])).'</b></td>
										</tr>
										<tr>
											<td style="float:left;">Channel</td>
											<td><b style="font-size:12px;float:right;">'.$data['channel_name'].'</b></td>
										</tr>
										<tr>
											<td style="float:left;">Delivery Time</td>
											<td><b style="font-size:12px;float:right;">'.date('h:i A',strtotime($data['actual_delivery_time'])).'</b></td>
										</tr>
										
									</tbody>
								</table>
							</td>
							<td class="text-right"><b>'.$data['net_amount'].'</b></td>
							<td><a class="products_list_toggle" data-target="product_list_'.$data['order_id'].'" href="javascript:void(0);" style="float:right;font-size:12px;">Hide Detail</a>
									<table class="table toggle-table" id="product_list_'.$data['order_id'].'">
										<tbody>';
								$product =  $data['products'];
								foreach($product as $pKey => $pValues){
									$display .='<tr><td>'.$pValues['name'].'</td><td>'.$pValues['qty'].'</td></tr>';
								}
					$display .='</tbody></table>
								</td>
								
								<td>'.$data['comment'].'</td>
								
								<td>'.($status == 'Delivered' ? "<span class='text-center'>".$data['delivery_boy']."</span>" : '')." ".($status == 'Cancelled' ? $data['cancel_reason'] : '')." ".sprintf($action,$data['order_id']).'
									
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
		}
			echo $display;
		?>
	   </tbody>
	   <?php if($total<>0){ ?>
	   <tfoot>
	   	<tr>
	   		<th class="text-center">Total</th>
	   		<th colspan="2"></th>
	   		<th class="text-right" id="tot-amt"><?php echo number_format($total,2); ?></th>
	   		<th colspan="3"></th>
	   	</tr>
	   </tfoot>
	   <?php } ?>
    </table>
  </div>
</div>
</div>