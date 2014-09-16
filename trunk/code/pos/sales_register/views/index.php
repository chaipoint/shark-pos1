<script>var is_rep_running = <?php echo array_key_exists(0, $at) ? 'true' : 'false';?>;</script>
<div class="container-fluid">
<form class="form-inline" id="search_form" action="?dispatch=sales_register"> 
  <input type="hidden" name="dispatch" value="sales_register"/>
<ol class="breadcrumb">
<li>
<div class="form-group">
  <label  class="control-label" for="sales_reg_search">Sales ON</label>&nbsp;&nbsp;&nbsp;&nbsp;
<div class="input-group">
      <input type="text" name = "sales_reg_search" id="sales_reg_search" class="form-control datepicker" required data-provide="datepicker-inline" data-date-format="dd-MM-yyyy"  data-date-autoclose = "true" data-date-end-date="+0d" name="expense_date" readonly/>      
      <span class="input-group-btn">
        <button class="btn btn-primary" type="button" style="padding-top:4px; padding-bottom:5px;" id="search_button"><i class="glyphicon glyphicon-search"></i></button>
      </span>
</div>
</div>
</li>
</ol>
</form>
<div class='row'>
      
     
        <div class="smallstat box col-lg-2 col-sm-4" style="width:110px;margin-left:14px;">
          <i class="glyphicon glyphicon-usd fa green"></i>
          <span class="title">Cash Sale</span>
          <span class="value"><?php echo $cash_sale;?></span>
        </div>
     

     
        <div class="smallstat box col-lg-2 col-sm-4" style="width:130px;margin-left:8px;" >
          <i class="glyphicon glyphicon-usd fa pink"></i>
          <span class="title">Pending Cash</span>
          <span class="value"><?php echo $cash_indelivery;?></span>
        </div>
     

      
        <div class="smallstat box col-lg-2 col-sm-4" style="width:125px;margin-left:8px;">
          <i class="glyphicon glyphicon-usd fa blue"></i>
          <span class="title">PPC Rdmptn</span>
          <span class="value"><?php echo $ppcSale; ?></span>
        </div>
     
      
        <div class="smallstat box col-lg-2 col-sm-4" style="width:115px;margin-left:8px;" >
          <i class="glyphicon glyphicon-usd fa gray"></i>
          <span class="title">Credit Sale</span>
          <span class="value"><?php echo $ppcSale; ?></span>
        </div>
      

      
        <div class="smallstat box col-lg-2 col-sm-4" style="width:135px;margin-left:8px;">
          <i class="glyphicon glyphicon-usd fa red"></i>
          <span class="title">Petty Expense</span>
          <span class="value"><?php echo $p_ex;?> &nbsp;
          <?php if(empty($_GET['sales_reg_search']) || (!empty($_GET['sales_reg_search']) && $_GET['sales_reg_search']==date('d-F-Y'))) { ?>
            <em data-toggle="dropdown" id="pe_tg" class="glyphicon glyphicon-chevron-right"></em> 
            <ul class="dropdown-menu" role="menu">
            <li><a href='#' id="add_expense">Add Expense</a></li>
            <li><a href='#' id="view_expense">View Expense</a></li>
          </ul>
           <?php } ?>
            </span>
        </div>
      
	  
	 
        <div class="smallstat box col-lg-2 col-sm-4" style="width:130px;margin-left:8px;">
          <i class="glyphicon glyphicon-usd fa yel"></i>
          <span class="title">Cash In Hand</span>
          <span class="value"><?php echo ($cash_sale - ($p_ex)) ;?></span>
        </div>
      

      
        <div class="smallstat box col-lg-2 col-sm-4" style="width:110px;margin-left:8px;">
          <i class="glyphicon glyphicon-usd fa orange"></i>
          <span class="title">Total Sale</span>
          <span class="value"><?php echo ($cash_sale + $cash_indelivery + $ppcSale) ;?></span>
        </div>
      

       
      
</div>

<div class="row">
  <div class="panel-group" id="accordion">
    <div class="col-sm-6">
  <div class="panel panel-success">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="col" data-value="sale_summary" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Sale Summary
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

<div class="col-sm-6">
  <div class="panel panel-success">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="col" id="todays_sale" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Item Summary
        <i class="glyphicon glyphicon-chevron-up pull-right"></i>
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
       <div class="col-md-6 col-lg-6">
        <table class="table" id="today-sale-table">
          <thead></thead>
          <tbody></tbody>
          <tfoot></tfoot>            
        </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-sm-12" style="margin-top:10px;">
          <?php require_once 'paid_bills_table.php';?>
</div>

<div class="col-sm-12" style="margin-top:10px;">
    <div class="panel panel-info"> 
      <div class="panel-heading col" data-toggle="collapse" data-parent="#accordion" href="#collapseCancel"><a>Cancelled Bill Details
      <i class="glyphicon glyphicon-chevron-up pull-right"></i></a></div>
      <div id="collapseCancel" class="panel-collapse collapse">
        <div class="panel-body">
          <table id="cancel_bill_table" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
           <thead>
              <tr id="filter_row11">
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
              </tr>
            </thead>
            
         <tbody>

<?php $sub_total = $total_amount = $total_tax = $due_amount = $counter = 0; //print_r($data);
if(is_array($data) && count($data)>0) { 
    foreach ($data as $key => $value) { 
      if($value['bill_status'] == 'Cancelled') { ?>
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
            <td style="text-align:center"><?php echo $value['is_cod']; ?></td>
            <td style="text-align:center"><?php echo $value['is_prepaid']; ?></td>
            <td style="text-align:center"><?php echo $value['is_credit']; ?></td>
          
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
           
           <th style="font-size:12px;">Avg Bill Value</th>
           <th class="text-right" style="font-size:12px;"><?php echo ($counter!=0 ? number_format($due_amount/$counter,2) : 0);?></th>
           
        </tr> 
      </tfoot>
</table>
<?php // echo '010010';?>
</div>
</div>
</div>
</div>

</div>
</div>
<?php require_once 'modal_expense.php';?>
<div id="login_holder" class="modal fade" id="sync-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <?php require_once DIR.'/login/views/index.php';?>
</div>