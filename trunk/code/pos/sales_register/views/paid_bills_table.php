<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'common.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'ui-lightness/jquery-ui-1.8.20.custom.css'); ?>" />
<link rel="stylesheet" href="<?php echo (CSS.'jquery.dataTables_themeroller.css');?>">
<link rel="stylesheet" href="<?php echo CSS ;?>bootstrapValidator.css"/>
<script type="text/javascript" src="<?php echo (JS.'jquery.dataTables.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.tableTools.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.jqueryui.js');?>" ></script>
<script type="text/javascript" src="<?php echo JS; ?>bootstrapValidator.js"></script>
<script type="text/javascript" src="<?php echo (JS.'pos/sale.register.js');?>" ></script>
<style>
table.dataTable thead th {
padding: 3px 0px 3px 5px;
cursor: pointer;
}
</style>
<?php //print_r($sales_data);?>
<div class="panel panel-info" style="margin-top:3.7%"> 
      <div class="panel-heading col" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><a href="javascript:void(0)">Paid Bill Details
      <i class="glyphicon glyphicon-chevron-down pull-right"></i></a></div>
      <div id="collapseTwo" class="panel-collapse collapse in">
        <div class="panel-body">

          <table id="active_bill_table" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
	         <thead>
              <tr id="filter_active">
                <th></th>
                <th></th>
                <th></th>
                <?php if(MODULE == 'sales_register'){?>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <?php }?>
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
                <?php if(MODULE == 'sales_register'){?>
                <th>Sub Total</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Total Amt</th>
                <th>Due Amt</th>
                <?php }?>
                
                <th>Customer Type</th>
                <th>Booking Chanl</th>
                <th>Payment Mode</th>
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
    foreach ($data as $key => $value) { ;
      if($value['bill_status'] != 'Cancelled') { ?>
	      <tr class="text-center">
		        <td style="text-align:center"><?php echo $value['bill_no']; ?></td>
            <td style="text-align:center"><?php echo DATE('H:i:s',strtotime($value['time']['created'])); ?></td>
		        <td style="text-align:center"><?php echo $value['total_qty']; ?></td>
            <?php if(MODULE == 'sales_register'){?>
		        <td style="text-align:right"><?php echo  number_format($value['sub_total'],2); ?></td>
            <td style="text-align:right"><?php echo  number_format($value['total_discount'],2); ?></td>
		        <td class="text-right"><?php echo number_format($value['total_tax'],2); ?></td>
            <td class="text-right"><?php echo number_format($value['total_amount'],2); ?></td>
            <td class="text-right"><?php echo number_format($value['due_amount'],2); ?></td>
            <?php }?>
            <td style="text-align:center"><?php echo $value['delivery_channel_name']; ?></td>
            <td style="text-align:center"><?php echo $value['booking_channel_name']; ?></td>
            <td style="text-align:center"><?php echo $value['payment_type']; ?></td>
            <td style="text-align:center"><?php echo $value['bill_status']; ?></td>
            <td style="text-align:center"><?php echo $value['is_cod']; echo '</br><b>'.($value['is_cod']=='Y' ? $value['order_no'] : '').'</b>'; ?></td>
            <td style="text-align:center"><?php echo $value['is_prepaid']; ?></td>
            <td style="text-align:center"><?php echo $value['is_credit']; ?></td>
		        <td style="width:132px;">
			         <!--<a href="#"  class="tip btn btn-primary btn-xs" title="View Invoice">
			             <i class="glyphicon glyphicon-list"></i>
               </a>-->
                <?php if(empty($_GET['sales_reg_search']) || (!empty($_GET['sales_reg_search']) && $_GET['sales_reg_search']==date('d-F-Y'))) { 
					if($value['payment_type']=='ppc' && $value['card']['invoice_number'] == $last_ppc_bill ){ ?>
				 <a class="tip btn btn-warning btn-xs edit-bill text-center" style="width:17px;height:17px;" title="Cancel Bill" href="<?php echo URL;?>?dispatch=billing&bill_no=<?php echo $value['_id']; ?>&bill=<?php echo $value['bill_no']; ?>&referer=<?php echo MODULE;?>">
  		              M
  		         </a>
				 <?php } else if($value['payment_type']!='ppc' && $value['payment_type']!='ppa') { ?>
				 <a class="tip btn btn-warning btn-xs edit-bill" style="width:47px;height:25px;float:left" title="MODIFY" href="<?php echo URL;?>?dispatch=billing&bill_no=<?php echo $value['_id']; ?>&bill=<?php echo $value['bill_no']; ?>&referer=<?php echo MODULE;?>">
  		              Modify
  		         </a>
				 <a class="tip btn btn-success btn-xs reprint-bill" style="width:70px;height:25px;float:right" title="REPRINT" id="<?php echo $value['_id'];?>">
  		              Re-print
  		         </a>
               <?php 
				}			   
                if($value['bill_status'] == "CoD") {
                  ?>
                 <a class="tip btn btn-warning btn-xs pay_bill text-center" style="width:20px;height:25px;" title="Pay Bill" data-href="<?php echo $value['_id']; ?>">
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
          $due_amount += $value['due_amount']; 
          $counter++; 
        }
   } 
}
?>		
      
   </tbody>
   <?php if(MODULE == 'sales_register'){?>
   <tfoot>
        <tr class="text-right">
          <th style="font-size:11px;"><strong>Total</strong></td>
          <th></th>
          <th></th>
          <th class="text-right" style="font-size:11px;"></th>
          <th class="text-right" style="font-size:11px;"></th>
          <th class="text-right" style="font-size:11px;"></th>
          <th class="text-right" style="font-size:11px;"></th>
          <th class="text-right" style="font-size:11px;"></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th style="font-size:11px;">Avg Bill Value</th>
          <th class="text-right" style="font-size:11px;"><?php echo ($counter!=0 ? number_format($due_amount/$counter,2) : 0);?></th>
           
        </tr> 
      </tfoot>
  <?php }?>
</table>
</div>
</div>
</div>

<script>
  var oTable = null;
  var footerRow = [3,4,5,6,7];
  var filteRow = 'filter_active';
  var media_path = "<?php echo JS;?>";  
  oTable = createDataTable(media_path,'active_bill_table',footerRow,filteRow);
</script>