<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'common.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'ui-lightness/jquery-ui-1.8.20.custom.css'); ?>" />
<link rel="stylesheet" href="<?php echo (CSS.'jquery.dataTables_themeroller.css');?>">
<script type="text/javascript" src="<?php echo (JS.'jquery.dataTables.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.tableTools.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.jqueryui.js');?>" ></script>

<h3>Sales on <?php echo date('d-M-Y');?></h3>
<span><label>Cash In Hand:&nbsp;</label><label style="font-size:18px;color:blue"><?php echo $cash_in_hand;?></label><span></br>
<span><label>Cash In Delivery:&nbsp;</label><label style="font-size:18px;color:green"><?php echo $cash_in_delivery;?></label><span>    

  <table id="fileData" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
	  <thead>
        <tr class="active">
            <th>Bill No</th>
            <th>Item Count</th>
            <th>Sub Total</th>
            <th>Tax</th>
            <th>Total Amount</th>
            <th>Delivery Channel</th>
            <th>Booking Channel</th>
            <th>Paid By</th>
            <th>Status</th>
            <th>Is Cod</th>
            <th>Is PrePaid</th>
            <th>Is Credit</th>
            <th style="width:120px; text-align:left;">Actions</th>
        </tr>
    </thead>
	<tbody>

<?php $sub_total = $total_amount = $total_tax =0;
if(is_array($bill_data) && count($bill_data)>0) { 
    foreach ($bill_data as $key => $value) { ?>
	      <tr class="text-center">
		        <td style="text-align:center"><?php echo $value['doc']['bill_no']; ?></td>
		        <td style="text-align:center"><?php echo $value['doc']['total_qty']; ?></td>
		        <td style="text-align:right"><?php echo  number_format($value['doc']['sub_total'],2); ?></td>
		        <td class="text-right"><?php echo number_format($value['doc']['total_tax'],2); ?></td>
            <td class="text-right"><?php echo $value['doc']['total_amount']; ?></td>
            <td style="text-align:center"><?php echo $value['doc']['delivery_channel']; ?></td>
            <td style="text-align:center"><?php echo $value['doc']['booking_channel']; ?></td>
            <td style="text-align:center"><?php echo $value['doc']['payment_type']; ?></td>
            <td style="text-align:center"><?php echo $value['doc']['bill_status']; ?></td>
            <td style="text-align:center"><?php echo $value['doc']['is_cod']; ?></td>
            <td style="text-align:center"><?php echo $value['doc']['is_prepaid']; ?></td>
            <td style="text-align:center"><?php echo $value['doc']['is_credit']; ?></td>
		        <td>
			         <a href="#"  class="tip btn btn-primary btn-xs" title="View Invoice">
			             <i class="glyphicon glyphicon-list"></i>
               </a>
               <a href="#"  class="tip btn btn-warning btn-xs" style="width:25px;" title="Edit Invoice">
		               <i class="glyphicon glyphicon-edit"></i>
		           </a>
		           <a href="#"  class="tip btn btn-danger btn-xs" title="Cancel Sale">
		               <i class="glyphicon glyphicon-trash"></i>
		           </a>
		        </td>
        </tr>
<?php	  $sub_total += $value['doc']['sub_total'];
        $total_tax += $value['doc']['total_tax']; 
        $total_amount += $value['doc']['total_amount'];  
   } 
}
?>		
      <tfoot>
        <tr class="text-right">
           <th>Total</td>
           <th></th>
           <th class="text-right"><?php echo number_format($sub_total,2); ?></th>
           <th class="text-right"><?php echo number_format($total_tax,2); ?></th>
           <th class="text-right"><?php echo $total_amount?></th>
           <th></th>
           <th></th>
           <th></th>
           <th></th>
           <th></th>
           <th></th>
           <th></th>
           <th></th>
        </tr> 
      </tfoot>
   </tbody>
</table>

<script>
var oTable = null;
oTable = createDataTable();
</script>