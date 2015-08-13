<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'common.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo (CSS.'ui-lightness/jquery-ui-1.8.20.custom.css'); ?>" />
<link rel="stylesheet" href="<?php echo (CSS.'jquery.dataTables_themeroller.css');?>">
<link rel="stylesheet" href="<?php echo CSS ;?>bootstrapValidator.css"/>
<script type="text/javascript" src="<?php echo (JS.'jquery.dataTables.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.tableTools.js');?>"></script>
<script type="text/javascript" src="<?php echo (JS.'dataTables.jqueryui.js');?>" ></script>
<script type="text/javascript" src="<?php echo JS; ?>bootstrapValidator.js"></script>

<style>
a.DTTT_button, button.DTTT_button, div.DTTT_button {
  margin-left: 10px!important; 
  height: 22px!important; 
  padding: 3px 5px!important; 
  color: #000!important;
}
div.DTTT_container {
  padding-top: 5px!important;
}
</style>
<div class="padded menu_div"  style="min-height:450px;">

<div class="panel panel-info" style="width:85%"> 
  <div class="panel-heading col" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
    <a href="javascript:void(0)">Item Wise Sale
      <i class="glyphicon glyphicon-chevron-down pull-right"></i>
    </a>
  </div>
  <div id="collapseTwo" class="panel-collapse collapse in"> 
    <div class="panel-body" >
      <table id="active_bill" class="table table-striped table-bordered table-condensed table-hover">
	       <thead>
            
            <tr class="active">
              <th>Store Code</th>
              <th>Store Name</th>
              <th>Bill Date</th>
              <th>Invoice No</th>
              <th>Item Name</th>
              <th>Item Qty</th>
              <th>Item Price</th>
              <th>Sale Value</th>
            </tr>
          </thead>
          <tbody>

<?php 
if(is_array($data['data']) && count($data['data'])>0) { 
    foreach ($data['data'] as $key => $value) { ;
      if($value['bill_status'] != 'Cancelled') { ?>
        <?php foreach($value['items'] as $k => $v) { ?>
	      <tr class="text-center">
          <td style="text-align:center"><?php echo $_SESSION['user']['store']['code']; ?></td>
		      <td style="text-align:center"><?php echo $value['store_name']; ?></td>
          <td style="text-align:center"><?php echo $value['time']['created']; ?></td>
          <td style="text-align:center"><?php echo $value['bill']; ?></td>
          <td style="text-align:left"><?php echo $v['name']; ?></td>
          <td style="text-align:right"><?php echo $v['qty']; ?></td>
          <td style="text-align:right"><?php echo $v['price']; ?></td>
          <td style="text-align:right"><?php echo round($v['netAmount']); ?></td>
        </tr>
        <?php } } } } ?>
		      
        
      	
      
   </tbody>
   
  </table>
</div>
</div>
</div>
<?php
  $total = 0;
  $minus = 0;
  
  $cash = (array_key_exists('cash', $data['payment_type']['amount']) ? $data['payment_type']['amount']['cash'] : 0 );
  $credit = (array_key_exists('credit', $data['payment_type']['amount']) ? $data['payment_type']['amount']['credit'] : 0 );
  $caw = (array_key_exists('caw', $data['payment_type']['amount']) ? $data['payment_type']['amount']['caw'] : 0 );
  $online = (array_key_exists('Online-Others', $data['payment_type']['amount']) ? $data['payment_type']['amount']['Online-Others'] : 0 );
  $ppa_redeem = (array_key_exists('ppa', $data['payment_type']['amount']) ? $data['payment_type']['amount']['ppa'] : 0 );
  $ppc_redeem = (array_key_exists('ppc', $data['payment_type']['amount']) ? $data['payment_type']['amount']['ppc'] : 0 );
  $ppa_load = (array_key_exists('ppaLoad', $card_sale) ? $card_sale['ppaLoad'] : 0 );
  $ppc_load = (array_key_exists('ppcLoad', $card_sale) ? $card_sale['ppcLoad'] : 0 );
  $ppc_activate = (array_key_exists('ppcActive', $card_sale) ? $card_sale['ppcActive'] : 0 );
  
  foreach($data['payment_type']['amount'] as $pKey => $pValue){ 
    $total += $pValue;
    $minus = ($pKey=='ppa' ? $minus + $pValue :
                ($pKey=='ppc' ? $minus + $pValue : 
                  ($pKey=='credit' ? $minus + $pValue : 
                    ($pKey=='Online-Others' ? $minus + $pValue : 
                      ($pKey=='caw' ? $minus + $pValue : $minus)))));
  }
  if(is_array($card_sale) && count($card_sale)>0){ 
    foreach ($card_sale as $key => $value) {
      if($key!='shift_cash'){
        $total += $value;
        $minus = ($key=='ppaActive' ? $minus + $value :
                  ($key=='ppcActive' ? $minus + $value : $minus));
      }
    }
  }

  $cash_in_box = $total-$minus;
?>
<div class="panel panel-info" style="width:85%"> 
  <div class="panel-heading col" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
    <a href="javascript:void(0)">Cash Reconciliation
      <i class="glyphicon glyphicon-chevron-down pull-right"></i>
    </a>
  </div>
  <div id="collapseThree" class="panel-collapse collapse in"> 
    <div class="panel-body" >
      <table id="active_bill1" class="table table-striped table-bordered table-condensed table-hover">
         <thead>
            
            <tr class="active">
              <th>Store Code</th>
              <th>Store Name</th>
              <th>Cash</th>
              <th>PPA Redeem</th>
              <th>PPA Load</th>
              <th>PPC Redeem</th>
              <th>PPC Load</th>
              <th>PPC Activate</th>
              <th>Credit</th>
              <th>Caw</th>
              <th>Online-Others</th>
              <th>Total Sale</th>
              <th>Cash In Box</th>
            </tr>
          </thead>

          <tbody>
            
              <tr>
                <td class='text-center'><?php echo $_SESSION['user']['store']['code']; ?></td>
                <td class='text-center'><?php echo $_SESSION['user']['store']['name']; ?></td>

                <td class='text-right'><?php echo $cash ?></td>
                <td class='text-right'><?php echo $ppa_redeem ?></td>
                <td class='text-right'><?php echo $ppa_load ?></td>
                <td class='text-right'><?php echo $ppc_redeem ?></td>
                <td class='text-right'><?php echo $ppc_load ?></td>
                <td class='text-right'><?php echo $ppc_activate ?></td>
                <td class='text-right'><?php echo $credit ?></td>
                <td class='text-right'><?php echo $caw ?></td>
                <td class='text-right'><?php echo $online ?></td>
                <td class='text-right'><?php echo $total ?></td>
                <td class='text-right'><?php echo $cash_in_box ?></td>
              </tr>
              
          </tbody>
   
      </table>
    </div>
  </div>
</div>
</div>
<script>
 var media_path = "<?php echo JS;?>"; 

  oTable = $('#active_bill, #active_bill1').DataTable({
    "dom": 'T<"H"lfr>t<"F"ip>',
    "tableTools": {
     "sSwfPath": media_path+"swf/copy_csv_xls_pdf.swf",  
     "aButtons": ["xls",{
     "sExtends": "pdf",
     "sPdfOrientation": "landscape",
     "sPdfMessage": "zfdf",
     },"print" ]
    },      
    paging: true,
    pagingType: "full_numbers",
    "jQueryUI": true,
    "bSort": true,
    "iDisplayLength" : 10,
    "aaSorting": [[ 1, "asc" ]]
   });
</script>