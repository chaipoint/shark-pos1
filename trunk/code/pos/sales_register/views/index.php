<h3>Sales on <?php echo date('d-M-Y');?></h3>
    
  <table id="fileData" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
	 <thead>
        <tr class="active">
            <th>Bill Id</th>
            <th>Total Qty</th>
            <th>Total Amount</th>
            <th>Payment Type</th>
            <th style="width:120px; text-align:left;">Actions</th>
            
		</tr>
     </thead>
	 <tbody>

	<?php if(is_array($bill_data) && count($bill_data)>0){ 
		  foreach ($bill_data as $key => $value) { ?>
	<tr>
		<td style="text-align:center"><?php echo $value['value']['bill_no']; ?></td>
		<td style="text-align:center"><?php echo $value['value']['total_qty']; ?></td>
		<td style="text-align:right"><?php echo $value['value']['total_amount']; ?></td>
		<td style="text-align:center"><?php echo $value['value']['payment_type']; ?></td>
		<td>
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
    <?php	} }?>		
     </tbody>
  </table>
