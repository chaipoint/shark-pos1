<style>
	.btn {
		width:83px;
		}
	.operation_div {	
		margin-top:.5%;
		margin-left:13.5%;
		width:600px;
	   }
	.operation_div button {
		margin:10px;
		float:left;
		line-height:4px; height:16px; width:150px; text-align:center; color:black;
		}
	 li {
 	   list-style-type: none;
	 }
	 ul {
	   padding-left:0px
	}
	
	.size {font-size:10px}
	
	.
  
	
</style>
<script type="text/javascript" src="<?php echo JS?>pos/coc.js"></script>
<div class="panel panel-info" style="margin-left:192px;margin-top:36px;height:485px;margin-right:10px;"> 
  
	 <div class="panel-body tabbable" style="width:80%">
        <ul class="nav nav-pills" role="" style="margin-top:-23px;">
		  <li class="disabled"><a style="color:#aaaaaa">New&nbsp;&nbsp;<span id="New" style="color:green"  ><?php echo $order_count['New']; ?></span></a></li>
		  <li class="disabled"><a style="color:#aaaaaa">Confirmed&nbsp;&nbsp;<span id="Confirmed" style="color:green"  ><?php echo $order_count['Confirmed']; ?></span></a></li>
		  <li class="disabled"><a style="color:#aaaaaa">Cancelled&nbsp;&nbsp;<span id="Cancelled" style="color:green"><?php echo $order_count['Cancelled']; ?></span></a></li>
		  <li class="disabled"><a style="color:#aaaaaa">Dispatched&nbsp;&nbsp;<span id="Dispatched" style="color:green"><?php echo $order_count['Dispatched']; ?></span></a></li>
		  <li class="disabled"><a style="color:#aaaaaa">Delivered&nbsp;&nbsp;<span id="Delivered" style="color:green"><?php echo $order_count['Delivered']; ?></span></a></li>
		  <li class="disabled"><a style="color:#aaaaaa">Paid&nbsp;&nbsp;<span id="Paid" style="color:green"><?php echo $order_count['Paid']; ?></span></a></li>
	    </ul>

         <table class="table table-bordered table-stripped" id="order-holder" style="font-size:10px;">
           <thead>
		     <tr  style="font-size:12px;background-color:#428bca;color:white;">
			   <th style="width:120px;">Order No / Bill No</th>
			   <th style="width:120px;">Customer Details</th>
			   <th style="width:200px;">Order Details</th>
			   <th style="width:60px;">Amount</th>
			   <th style="width:220px;">Item Summary</th>
			   <th style="width:120px;">Comment</th>
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
							<td class="text-left"><strong>'.$data['order_id'].' / '.$bill_no.'</strong><br>
								<strong><span id="order_status-'.$data['order_id'].'">'.$data['status'].'</span></strong>
								
								'.(($data['status'] == 'Dispatched') ?
								"<ul>
									<li id='Cancelled-".$data['order_id']."'><input type='radio' name='status-".$data['order_id']."'  value='Cancelled' class='size bt-update-status ".(($data['status']=='Cancelled' || $data['status']=='Delivered') ? 'hide' : ''). "' data-new_status='Cancelled' data-current_status='".$data['status']."'> Cancel&nbsp&nbsp&nbsp&nbsp&nbsp</li>
									<li id='Delivered-".$data['order_id']."'><input type='radio' name='status-".$data['order_id']."'  value='Delivered' class='bt-update-status ".(($data['status']=='Cancelled' || $data['status']=='Delivered') ? 'hide' : '')."' data-new_status='Delivered' data-current_status='".$data['status']."'> Delivered&nbsp&nbsp</li>
								</ul>" : (($data['status'] == 'Confirmed') ?
								
								"<ul >
									<li id='Dispatched-".$data['order_id']."'><input type='radio' name='status-".$data['order_id']."'  value='Dispatched' class='size bt-update-status ".(($data['status']=='Cancelled' || $data['status']=='Delivered') ? 'hide' : '')."' data-new_status='Dispatched' data-current_status='".$data['status']."'> Dispatch&nbsp&nbsp&nbsp</li>
									<li id='Cancelled-".$data['order_id']."'><input type='radio' name='status-".$data['order_id']."'  value='Cancelled' class='size bt-update-status ".(($data['status']=='Cancelled' || $data['status']=='Delivered') ? 'hide' : '')."' data-new_status='Cancelled' data-current_status='".$data['status']."'> Cancel&nbsp&nbsp&nbsp&nbsp&nbsp</li>
									<li id='Delivered-".$data['order_id']."'><input type='radio' name='status-".$data['order_id']."'  value='Delivered' class='size bt-update-status ".(($data['status']=='Cancelled' || $data['status']=='Delivered') ? 'hide' : '')."' data-new_status='Delivered' data-current_status='".$data['status']."'> Delivered&nbsp&nbsp</li>
								</ul>"
									  : (($data['status'] == 'New') ?
								"<ul>
									<li id='Confirmed-".$data['order_id']."'><input type='radio' name='status-".$data['order_id']."'  value='Confirmed' class='size bt-update-status ".(($data['status']=='Cancelled' || $data['status']=='Delivered') ? 'hide' : '')."' data-new_status='Confirmed' data-current_status='".$data['status']."'> Confirmed</li>
									<li id='Dispatched-".$data['order_id']."'><input type='radio' name='status-".$data['order_id']."'  value='Dispatched' class='size bt-update-status ".(($data['status']=='Cancelled' || $data['status']=='Delivered') ? 'hide' : '')."' data-new_status='Dispatched' data-current_status='".$data['status']."'> Dispatch&nbsp&nbsp&nbsp</li>
									<li id='Cancelled-".$data['order_id']."'><input type='radio' name='status-".$data['order_id']."'  value='Cancelled' class='size bt-update-status ".(($data['status']=='Cancelled' || $data['status']=='Delivered') ? 'hide' : '')."' data-new_status='Cancelled' data-current_status='".$data['status']."'> Cancel&nbsp&nbsp&nbsp&nbsp&nbsp</li>
									<li id='Delivered-".$data['order_id']."'><input type='radio' name='status-".$data['order_id']."'  value='Delivered' class='size bt-update-status ".(($data['status']=='Cancelled' || $data['status']=='Delivered') ? 'hide' : '')."' data-new_status='Delivered' data-current_status='".$data['status']."'> Delivered&nbsp&nbsp</li>
								</ul>"
									
									: ''))).'
								
								<button id="bill_button-'.$data['order_id'].'" class="btn btn-primary btn-sm generate-bill '.(($data['status']=='Cancelled' || $data['status']=='Delivered') ? 'hide' : '').'"  data-order-id='.$data['order_id'].'><i class="glyphicon glyphicon-list-alt"></i>&nbsp;Bill</button>
							</td>
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
