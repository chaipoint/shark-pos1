<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'common.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'ui-lightness/jquery-ui-1.8.20.custom.css'); ?>" />
<link rel="stylesheet" href="<?php echo (CSS.'jquery.dataTables_themeroller.css');?>">
<script type="text/javascript" src="<?php echo (JS.'jquery.dataTables.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.tableTools.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.jqueryui.js');?>" ></script>
<script type="text/javascript" src="<?php echo (JS.'pos/sale_register.js');?>" ></script>

<div class="col-md-12 col-lg-12">
  <h3>Sales on <?php echo date('d-M-Y');?></h3> 
  <!-- Single button -->
<div class="btn-group pull-right">
  <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
    Petty Expense <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <li><a href='#' id="add_expense">Add Expense</a></li>
    <li><a href='#' id="view_expense">View Expense</a></li>
    </ul>
</div>
  Cash Sale : <span class="lead" style="color:blue"><?php echo $cash_sale;?></span><br/>
  Cash In Delivery : <span class="lead" style="color:green"><?php echo $cash_indelivery;?></span>
</div>

<div class="col-md-6 col-lg-6">
  <table class="table">
      <thead><tr><th>Bill Status</th><th>Count</th><th>Amount</th></tr></thead>
      <tbody>
          <?php $total = 0; foreach($bill_status['count'] as $key => $value){
              $total += ($key == 'Cancelled') ? 0 : $bill_status['amount'][$key];;
            ?>
          <tr>
              <td><?php echo $key;?></td>
              <td class="text-right"><?php echo $value;?></td>
              <td class="text-right"><?php echo ($key == 'Cancelled') ? 0 : $bill_status['amount'][$key];?></td>
          </tr>
          <?php }?>
      </tbody>
      <tfoot><tr><td>Total</td><td></td><td class="text-right"><?php echo $total;?></td></tr></tfoot>
  </table>
</div>
<div class="col-md-6 col-lg-6">
    <table class="table">
      <thead><tr><th>Payment Type</th><th>Count</th><th>Amount</th></tr></thead>
      <tbody>
          <?php $total = 0; foreach($payment_type['amount'] as $key => $value){ $total += $value;?>
          <tr>
              <td><?php echo $key;?></td>
              <td class="text-right"><?php echo $payment_type['count'][$key];?></td>
              <td class="text-right"><?php echo $value;?></td>
          </tr>
          <?php }?>
      </tbody>
      <tfoot><tr><td>Total</td><td></td><td class="text-right"><?php echo $total;?></td></tr></tfoot>
  </table>
</div>

  
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
if(is_array($data) && count($data)>0) { 
    foreach ($data as $key => $value) { ?>
	      <tr class="text-center">
		        <td style="text-align:center"><?php echo $value['bill_no']; ?></td>
		        <td style="text-align:center"><?php echo $value['total_qty']; ?></td>
		        <td style="text-align:right"><?php echo  number_format($value['sub_total'],2); ?></td>
		        <td class="text-right"><?php echo number_format($value['total_tax'],2); ?></td>
            <td class="text-right"><?php echo number_format($value['total_amount'],2); ?></td>
            <td style="text-align:center"><?php echo $value['delivery_channel_name']; ?></td>
            <td style="text-align:center"><?php echo $value['booking_channel_name']; ?></td>
            <td style="text-align:center"><?php echo $value['payment_type']; ?></td>
            <td style="text-align:center"><?php echo $value['bill_status']; ?></td>
            <td style="text-align:center"><?php echo $value['is_cod']; ?></td>
            <td style="text-align:center"><?php echo $value['is_prepaid']; ?></td>
            <td style="text-align:center"><?php echo $value['is_credit']; ?></td>
		        <td>
			         <a href="#"  class="tip btn btn-primary btn-xs" title="View Invoice">
			             <i class="glyphicon glyphicon-list"></i>
               </a>
               <?php if($value['bill_status'] != 'Cancelled') {?>
                 <a class="tip btn btn-warning btn-xs edit-bill" style="width:25px;" title="Edit Invoice" href="<?php echo URL;?>?dispatch=billing&bill_no=<?php echo $value['_id']; ?>">
  		               <i class="glyphicon glyphicon-edit"></i>
  		           </a>
               <?php }?>
		           <a href="#"  class="tip btn btn-danger btn-xs" title="Cancel Sale">
		               <i class="glyphicon glyphicon-trash"></i>
		           </a>
		        </td>
        </tr>
<?php	  
      if($value['bill_status'] != 'Cancelled'){
          $sub_total += $value['sub_total'];
          $total_tax += $value['total_tax']; 
          $total_amount += $value['total_amount'];  
        }
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
<?php require_once 'modal_expense.php';?>
<script>
var oTable = null;
var media_path = "<?php echo JS;?>";
oTable = createDataTable(media_path);
</script>