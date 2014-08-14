<h3>Sales on <?php echo date('d-M-Y');?></h3>
    
  <table id="fileData" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
	 <thead>
        <tr class="active">
            <th>Bill Id</th>
            <th>Bill Date</th>
            <th>Total Qty</th>
            <th>Total Discount</th>
            <th>Sub Total</th>
            <th>Total Tax</th>
            <th>Total Amount</th>
            <th>Due Amount</th>
            <th>Payment Type</th>
            <th style="width:120px; text-align:left;">Actions</th>
        </tr>
     </thead>
	 <tbody>

	<?php $totaAmount = $totalTax = $totalDiscount = $totalSubTotal= $totalDueAmount= 0;
	   
     if(is_array($bill_data) && count($bill_data)>0){ 
		  foreach ($bill_data as $key => $value) { ?>
	<tr class="text-right">
		<td class="text-center"><?php echo $value['value']['bill_no']; ?></td>
    <td class="text-center"><?php echo $value['value']['bill_time']; ?></td>
		<td class="text-center"><?php echo $value['value']['total_qty']; ?></td>
    <td><?php echo number_format($value['value']['total_discount'],2); ?></td>
    <td><?php echo number_format($value['value']['sub_total'],2); ?></td>
    <td><?php echo number_format($value['value']['total_tax'],2); ?></td>
    <td><?php echo number_format($value['value']['total_amount'],2); ?></td>
		<td><?php echo number_format($value['value']['due_amount'],2); ?></td>
		<td class="text-center"><?php echo $value['value']['payment_type']; ?></td>
		<td class="text-left">
			<a href="#"  class="tip btn btn-primary btn-xs" title="View Invoice">
			  <i class="glyphicon glyphicon-list"></i>
            </a>
            <a href="#"  class="tip btn btn-warning btn-xs" style="width:25px;" title="Edit Invoice">
		      <i class="glyphicon glyphicon-edit"></i>
		    </a>
		    <a href="#"  class="tip btn btn-danger btn-xs" title="Delete Sale">
		      <i class="glyphicon glyphicon-trash"></i>
		    </a>
		</td>
    </tr>
    <?php $totaAmount+=$value['value']['total_amount'];
          $totalTax+=$value['value']['total_tax'];
          $totalDiscount+=$value['value']['total_discount'];
          $totalSubTotal+=$value['value']['sub_total'];
          $totalDueAmount+=$value['value']['due_amount'];
      } 
  }
?>
    <tr>
        <th class="text-center">Total</th>
        <th></th>
        <th></th>
        <th class="text-right"><?php echo number_format($totalDiscount,2); ?></th>
        <th class="text-right"><?php echo number_format($totalSubTotal,2); ?></th>
        <th class="text-right"><?php echo number_format($totalTax,2); ?></th>
        <th class="text-right"><?php echo number_format($totaAmount,2); ?></th>
        <th class="text-right"><?php echo number_format($totalDueAmount,2); ?></th>
        <th></th>
        <th></th>
    </tr>		
  </tbody>
</table>
