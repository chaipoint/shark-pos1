<div class="panel panel-info"> 
  <div class="panel-heading col" data-toggle="collapse" data-parent="#accordion" href="#collapseCard"><a>Card Load Details
    <i class="glyphicon glyphicon-chevron-down pull-right"></i></a></div>
    <div id="collapseCard" class="panel-collapse collapse">
      <div class="panel-body">
        <table id="load_card_table" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
	         <thead>
                <tr id="filter_active">
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr>
                <tr class="active">
                  <th>Time</th>
                  <th>Card Type</th>
                  <th>Txn Type</th>
                  <th>Amount</th>
                  <th>Action</th>
                </tr>
              </thead>
            <tbody>

<?php   
if(array_key_exists('rows', $card_load_data) && count($card_load_data['rows'])>0) { 
    $data = $card_load_data['rows'];
    foreach ($data as $key => $value) { if($value['doc']['status']!='cancel'){ ?>
	      <tr class="text-center" data-doc_id="<?php echo $value['doc']['_id']; ?>" data-txn_no="<?php echo $value['doc']['txn_no']; ?>" data-approval_code="<?php echo $value['doc']['approval_code']; ?>" data-amount="<?php echo $value['doc']['amount']; ?>" data-card_no="<?php echo $value['doc']['card_no']; ?>" data-invoice_number="<?php echo $value['doc']['invoice_number']; ?>">
		        <td style="text-align:center"><?php echo DATE('H:i:s',strtotime($value['doc']['time'])); ?></td>
		        <td style="text-align:center"><?php echo $value['doc']['card_type']; ?></td>
            <td style="text-align:center"><?php echo $value['doc']['txn_type']; ?></td>
            <td style="text-align:center"><?php echo $value['doc']['amount']; ?></td>
            <td>
            <?php if(empty($_GET['sales_reg_search']) || (!empty($_GET['sales_reg_search']) && $_GET['sales_reg_search']==date('d-F-Y'))) { ?>
                 <a class="tip btn btn-warning btn-xs edit-bill cancel-transaction text-center" style="width:17px;height:17px;" title="Cancel Transaction" href="javascript:void(0);">
  		              C
  		           </a>
            <?php } ?>
		          
		        </td>
        </tr>
    <?php	  } } } ?>		
      
      </tbody>
   
   
   <tfoot>
        <tr class="text-right">
          <th style="font-size:11px;"><strong>Total</strong></td>
          <th class="text-right" style="font-size:11px;"></th>
          <th class="text-right" style="font-size:11px;"></th>
          <th class="text-right" style="font-size:11px;"></th>
          <th class="text-right" style="font-size:11px;"></th>
        </tr> 
      </tfoot>
  
</table>
</div>
</div>
</div>
<script>
  var oTable = null;
  var footerRow = [3];
  var filteRow = 'filter_active';
  var media_path = "<?php echo JS;?>";  
  oTable = createDataTable(media_path,'load_card_table',footerRow,filteRow);
</script>