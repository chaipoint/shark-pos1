<script>var is_rep_running = <?php echo array_key_exists(0, $at) ? 'true' : 'false';?>;</script>
<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'common.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'ui-lightness/jquery-ui-1.8.20.custom.css'); ?>" />
<link rel="stylesheet" href="<?php echo (CSS.'jquery.dataTables_themeroller.css');?>">
<link rel="stylesheet" href="<?php echo CSS ;?>bootstrapValidator.css"/>
<script type="text/javascript" src="<?php echo (JS.'jquery.dataTables.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.tableTools.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.jqueryui.js');?>" ></script>
<script type="text/javascript" src="<?php echo JS; ?>bootstrapValidator.js"></script>
<script type="text/javascript" src="<?php echo (JS.'pos/sale_register.js');?>" ></script>

<div class="container-fluid">
<ol class="breadcrumb">
<li>Sales ON <?php echo date('d-M-Y');?></li>
</ol>
<div class='row'>
      
      <div class="col-lg-2 col-sm-4">
        <div class="smallstat box">
          <i class="fa fa-usd green"></i>
          <span class="title">Cash Sale</span>
          <span class="value"><?php echo $cash_sale;?></span>
        </div>
      </div>

      <div class="col-lg-2 col-sm-4">
        <div class="smallstat box">
          <i class="glyphicon glyphicon-usd fa pink"></i>
          <span class="title">Cash In Delivery</span>
          <span class="value"><?php echo $cash_indelivery;?></span>
        </div>
      </div>

      <div class="col-lg-2 col-sm-4">
        <div class="smallstat box">
          <i class="glyphicon glyphicon-usd fa blue"></i>
          <span class="title">PPC Sale</span>
          <span class="value">2000</span>
        </div>
      </div>

      <div class="col-lg-2 col-sm-4">
        <div class="smallstat box">
          <i class="glyphicon glyphicon-usd fa red"></i>
          <span class="title">Petty Expense</span>
          <span class="value">3000 &nbsp;<em data-toggle="dropdown" id="pe_tg"class="glyphicon glyphicon-chevron-right"></em> 
            <ul class="dropdown-menu" role="menu">
            <li><a href='#' id="add_expense">Add Expense</a></li>
            <li><a href='#' id="view_expense">View Expense</a></li>
          </ul>

            </span>
        </div>
      </div>

      <div class="col-lg-2 col-sm-4">
        <div class="smallstat box">
          <i class="glyphicon glyphicon-usd fa orange"></i>
          <span class="title">Total Sale</span>
          <span class="value">4000</span>
        </div>
      </div>

      
</div>

<div class="row">
  <div class="panel-group" id="accordion">
    <div class="col-sm-6">
  <div class="panel panel-success">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="col" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Sale Summary
        <i class="glyphicon glyphicon-chevron-up pull-right"></i>
        </a>
      </h4>
    </div>
     <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body">
       <div class="col-md-6 col-lg-6">
        <table class="table">
          <thead><tr><th>Bill Status</th><th>Count</th><th class="text-center">Amount</th></tr></thead>
            <tbody>
              <?php $total = 0; foreach($bill_status['count'] as $key => $value){
                $total += ($key == 'Cancelled') ? 0 : $bill_status['amount'][$key];;
               ?>
              <tr>
                <td class="text-center"><?php echo $key;?></td>
                <td class="text-center"><?php echo $value;?></td>
                <td class="text-center"><?php echo ($key == 'Cancelled') ? 0 : $bill_status['amount'][$key];?></td>
              </tr>
          <?php }?>
            </tbody>
              <tfoot><tr><th class="text-center">Total</th><th></th><th class="text-center"><?php echo $total;?></th></tr></tfoot>
          </table>
        </div>
        <div class="col-md-6 col-lg-6">
        <table class="table">
          <thead><tr><th>Payment Type</th><th>Count</th><th>Amount</th></tr></thead>
            <tbody>
                <?php $total = 0; foreach($payment_type['amount'] as $key => $value){ $total += $value;?>
              <tr>
                <td class="text-center"><?php echo $key;?></td>
                <td class="text-center"><?php echo $payment_type['count'][$key];?></td>
                <td class="text-center"><?php echo $value;?></td>
              </tr>
                   <?php }?>
            </tbody>
              <tfoot><tr><th class="text-center">Total</th><th></th><th class="text-center"><?php echo $total;?></th></tr></tfoot>
        </table>
       </div>
      </div>
     </div>
  </div>
</div>
    
<div class="col-sm-12" style="margin-top:10px;">
    <div class="panel panel-info"> 
      <div class="panel-heading col" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><a>Sale Register
      <i class="glyphicon glyphicon-chevron-up pull-right"></i></a></div>
      <div id="collapseTwo" class="panel-collapse collapse">
        <div class="panel-body">
          <table id="fileData" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
	         <thead>
              <tr class="active">
                <th>Bill No</th>
                <th>Item Count</th>
                <th>Sub Total</th>
                <th>Tax</th>
                <th>Total Amount</th>
                <th>Due Amount</th>
                <th>Delivery Channel</th>
                <th>Booking Channel</th>
                <th>Paid By</th>
                <th>Status</th>
                <th>Is Cod</th>
                <th>Is PrePaid</th>
                <th>Is Credit</th>
                <th>Actions</th>
              </tr>
            </thead>
	       <tbody>

<?php $sub_total = $total_amount = $total_tax = $due_amount= 0;
if(is_array($data) && count($data)>0) { 
    foreach ($data as $key => $value) { ?>
	      <tr class="text-center">
		        <td style="text-align:center"><?php echo $value['bill_no']; ?></td>
		        <td style="text-align:center"><?php echo $value['total_qty']; ?></td>
		        <td style="text-align:right"><?php echo  number_format($value['sub_total'],2); ?></td>
		        <td class="text-right"><?php echo number_format($value['total_tax'],2); ?></td>
            <td class="text-right"><?php echo number_format($value['total_amount'],2); ?></td>
            <td class="text-right"><?php echo number_format($value['due_amount'],2); ?></td>

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
          $due_amount += $value['due_amount'];  
        }
   } 
}
?>		
      <tfoot>
        <tr class="text-right">
           <th style="font-size:14px;">Total</td>
           <th></th>
           <th class="text-right" style="font-size:14px;"><?php echo number_format($sub_total,2); ?></th>
           <th class="text-right" style="font-size:14px;"><?php echo number_format($total_tax,2); ?></th>
           <th class="text-right" style="font-size:14px;"><?php echo number_format($total_amount,2);?></th>
           <th class="text-right" style="font-size:14px;"><?php echo number_format($due_amount,2);?></th>
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
</div>
</div>
</div>
</div>
</div>
</div>
<?php require_once 'modal_expense.php';?>
<script>
var oTable = null;
var media_path = "<?php echo JS;?>";
oTable = createDataTable(media_path);
</script>