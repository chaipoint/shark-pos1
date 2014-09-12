<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'common.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'ui-lightness/jquery-ui-1.8.20.custom.css'); ?>" />
<link rel="stylesheet" href="<?php echo (CSS.'jquery.dataTables_themeroller.css');?>">
<link rel="stylesheet" href="<?php echo CSS ;?>bootstrapValidator.css"/>
<script type="text/javascript" src="<?php echo (JS.'jquery.dataTables.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.tableTools.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.jqueryui.js');?>" ></script>
<script type="text/javascript" src="<?php echo JS; ?>bootstrapValidator.js"></script>
<script type="text/javascript" src="<?php echo (JS.'pos/sale.register.js');?>" ></script>
    <div class="panel panel-info"> 
      <div class="panel-heading col" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><a>Paid Bill Details
      <i class="glyphicon glyphicon-chevron-up pull-right"></i></a></div>
      <div id="collapseTwo" class="panel-collapse collapse">
        <div class="panel-body">

          <table id="active_bill_table" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
	         <thead>
              <tr id="filter_row">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
              <tr class="active">
                <th>Bill No</th>
                <th>Bill Time</th>
                <th>Item Count</th>
                <th>Sub Total</th>
                <th>Tax</th>
                <th>Total Amount</th>
                <th>Due Amount</th>
                <th>Sales Channel</th>
                <th>Booking Channel</th>
                <th>Paid By</th>
                <th>Status</th>
                <th>Is CoD</th>
                <th>Is PrePaid</th>
                <th>Is Credit</th>
                <th>Actions</th>
              </tr>
            </thead>
            
	       <tbody>

<?php $sub_total = $total_amount = $total_tax = $due_amount = $counter = 0; //print_r($data);
if(is_array($data) && count($data)>0) { 
    foreach ($data as $key => $value) { 
      if($value['bill_status'] != 'Cancelled') { ?>
	      <tr class="text-center">
		        <td style="text-align:center"><?php echo $value['bill_no']; ?></td>
            <td style="text-align:center"><?php echo DATE('H:i:s',strtotime($value['time']['created'])); ?></td>
		        <td style="text-align:center"><?php echo $value['total_qty']; ?></td>
		        <td style="text-align:right"><?php echo  number_format($value['sub_total'],2); ?></td>
		        <td class="text-right"><?php echo number_format($value['total_tax'],2); ?></td>
            <td class="text-right"><?php echo number_format($value['total_amount'],2); ?></td>
            <td class="text-right"><?php echo number_format($value['due_amount'],2); ?></td>

            <td style="text-align:center"><?php echo $value['delivery_channel_name']; ?></td>
            <td style="text-align:center"><?php echo $value['booking_channel_name']; ?></td>
            <td style="text-align:center"><?php echo $value['payment_type']; ?></td>
            <td style="text-align:center"><?php echo $value['bill_status']; ?></td>
            <td style="text-align:center"><?php echo $value['is_cod']; echo '</br><b>'.($value['is_cod']=='Y' ? $value['order_no'] : '').'</b>'; ?></td>
            <td style="text-align:center"><?php echo $value['is_prepaid']; ?></td>
            <td style="text-align:center"><?php echo $value['is_credit']; ?></td>
		        <td>
			         <!--<a href="#"  class="tip btn btn-primary btn-xs" title="View Invoice">
			             <i class="glyphicon glyphicon-list"></i>
               </a>-->
                <?php if(empty($_GET['sales_reg_search']) || (!empty($_GET['sales_reg_search']) && $_GET['sales_reg_search']==date('d-F-Y'))) { ?>
                 <a class="tip btn btn-warning btn-xs edit-bill text-center" style="width:25px;" title="Cancel Bill" href="<?php echo URL;?>?dispatch=billing&bill_no=<?php echo $value['_id']; ?>&bill=<?php echo $value['bill_no']; ?>">
  		              C
  		           </a>
               <?php  
                if($value['bill_status'] == "CoD") {
                  ?>
                 <a class="tip btn btn-warning btn-xs pay_bill text-center" style="width:25px;" title="Pay Bill" data-href="<?php echo $value['_id']; ?>">
                     P
                 </a>
              <?php
                 }
               }
              ?>
		          <!-- <a href="#"  class="tip btn btn-danger btn-xs" title="Cancel Sale">
		               <i class="glyphicon glyphicon-trash"></i>-->
		           </a>
		        </td>
        </tr>
<?php	  
      
          $sub_total += $value['sub_total'];
          $total_tax += $value['total_tax']; 
          $total_amount += $value['total_amount'];
          $due_amount += $value['due_amount']; 
          $counter++; 
        }
   } 
}
?>		
      
   </tbody>
   <tfoot>
        <tr class="text-right">
           <th style="font-size:12px;">Total</td>
           <th></th>
           <th></th>
           <th class="text-right" style="font-size:12px;"></th>
           <th class="text-right" style="font-size:12px;"></th>
           <th class="text-right" style="font-size:12px;"></th>
           <th class="text-right" style="font-size:12px;"></th>
           <th></th>
           <th></th>
           <th></th>
           <th></th>
           <th></th>
           <th></th>
           <th style="font-size:12px;">Avg Bill Value</th>
           <th class="text-right" style="font-size:12px;"><?php echo number_format($due_amount/$counter,2);?></th>
           
        </tr> 
      </tfoot>
</table>
</div>
</div>
</div>
<script>
var oTable = null;
var footerRow = [3,4,5,6];
var media_path = "<?php echo JS;?>";
oTable = createDataTable(media_path,'active_bill_table',footerRow);
oTable = createDataTable(media_path,'cancel_bill_table',footerRow);
</script>