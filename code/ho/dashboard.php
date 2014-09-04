<?php require_once 'common/header.php';
      require_once 'common/couchdb.phpclass.php';
      $couch = new CouchPHP(); ?>

<link rel="stylesheet" href="css/common.css" type="text/css">
<script type="text/javascript" src="js/sync.js"></script>

<body>
  <!--Body Header Start -->
  
  <?php require_once 'common/html_header.php';?>

  <!--Body Header End -->

  <!--Body Container Start -->
    
  <div class="container-fluid">
     
    <!--Progress Bar Div -->

      <div class="progress" align="center" id="progress" style="margin-top:80px;display:none;">
        <div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
          <span class="sr-only">45% Complete</span>
        </div>
      </div>
    <!--Progress Bar Div End -->

      <!-- Main component for a primary marketing message or call to action -->
      <ol class="breadcrumb" style="margin-top:60px;">
        <li>Sales ON <?php echo date('d-M-Y');?></li>
      </ol>
    
      <!--<h2>Dashboard</h2>-->
      <div class='row' style="margin-top:20px;">
      
      <div class="col-lg-2 col-sm-4">
        <div class="smallstat box">
          <i class="glyphicon glyphicon-usd fa green"></i>
          <span class="title">Cash Sale</span>
          <span class="value">1000<?php //echo $cash_sale;?></span>
        </div>
      </div>

      <div class="col-lg-2 col-sm-4">
        <div class="smallstat box">
          <i class="glyphicon glyphicon-usd fa pink"></i>
          <span class="title">Cash In Delivery</span>
          <span class="value">2000<?php //echo $cash_indelivery;?></span>
        </div>
      </div>

      <div class="col-lg-2 col-sm-4">
        <div class="smallstat box">
          <i class="glyphicon glyphicon-usd fa blue"></i>
          <span class="title">PPC Sale</span>
          <span class="value">3000<?php //echo $ppcSale; ?></span>
        </div>
      </div>

      <div class="col-lg-2 col-sm-4">
        <div class="smallstat box">
          <i class="glyphicon glyphicon-usd fa red"></i>
          <span class="title">Petty Expense</span>
          <span class="value">4000<?php //echo $p_ex;?></span>
        </div>
      </div>

      <div class="col-lg-2 col-sm-4">
        <div class="smallstat box">
          <i class="glyphicon glyphicon-usd fa orange"></i>
          <span class="title">Total Sale</span>
          <span class="value">10000<?php //echo (($cash_sale + $cash_indelivery + $ppcSale) - $p_ex) ;?></span>
        </div>
      </div>

      
</div>


<?php $getSale = $couch->getDesign('billing')->getView('bill_by_date')->setParam(array("group"=>"true","startkey"=>'["'.date('Y-m-d').'"]',"endkey"=>'["'.date('Y-m-d').'",{}]'))->execute();
      $topStore = $couch->getDesign('sales')->getView('top_store')->setParam(array("group"=>"true","startkey"=>'["'.date('Y-m-d').'"]',"endkey"=>'["'.date('Y-m-d').'",{}]'))->execute();
      

  $totalSale = 0;
  if(array_key_exists('rows', $getSale) && count($getSale['rows'])>0){
  $totalSale = $getSale['rows'][0]['value'];
  } 

  $topStoreArray = array();
  if(array_key_exists('rows', $topStore)) { 
       foreach ($topStore['rows'] as $key => $value) {
        $topStoreArray[$value['value']] = $value['key'][1] ;
       }
     }
     krsort($topStoreArray);
?>
        <!--<h4>Today's Sale (Rs.<?php echo $totalSale;?>)</h4>-->
<!-- Default panel contents -->
<div class="row">
<div class="panel-group" id="accordion">
  <div class="col-sm-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a class="col" data-value="sale_summary" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Top 5 Stores
          <i class="glyphicon glyphicon-chevron-up pull-right"></i>
          </a>
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse">
       <div class="panel-body">
        <div class="col-md-6 col-lg-6">
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Store Name</th>
                <th>Sale Amount(Rs)</th>
              </tr>
            </thead>
          <tbody>
      <?php $i=1; 
      if(is_array($topStoreArray)&& count($topStoreArray)>0) { 
       foreach ($topStoreArray as $key => $value) { ?>
         <tr>
           <td><?php echo $i;?></td>
           <td><?php echo $value; ?></td>
           <td><?php echo $key; ?></td>
         </tr>
      <?php $i++;}} ?>
      <?php for($j=$i;$j<=5;$j++) { ?>
         <tr>
           <td><?php echo $j;?></td>
           <td></td>
           <td></td>
         </tr>

    <?php } ?>
            </tbody>
          </table>
</div>
</div>
</div>
</div>
</div>
<div class="col-sm-6 pull-left">
  <div class="panel panel-default">
    <div class="panel-heading" >
      <h4 class="panel-title">
        <a class="col" id="todays_sale" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Top 5 Items
        <i class="glyphicon glyphicon-chevron-up pull-right"></i>
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
       <div class="col-md-6 col-lg-6">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Item Name</th>
              <th>Item Qty</th>
              <th>Sale Amount(Rs)</th>
            </tr>
          </thead>
        <tbody>
            <tr>
              <td>1</td>
              <td>Item 1</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>2</td>
              <td>Item 2</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>3</td>
              <td>Item 3</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>4</td>
              <td>Item 4</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>5</td>
              <td>Item 5</td>
              <td></td>
              <td></td>
            </tr>

    </tbody>
  </table>
</div>
</div>
</div>
</div>
</div>

<div class="col-sm-6 pull-left">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="col" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">Top 5 Channel
        <i class="glyphicon glyphicon-chevron-up pull-right"></i>
        </a>
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse">
      <div class="panel-body">
       <div class="col-md-12 col-lg-6">
  
<!-- Table -->
  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Channel Name</th>
        <th>Total Order</th>
        <th>Sale Amount(Rs)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>Direct Store</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>2</td>
        <td>Food Court</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>3</td>
        <td>Facebook</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>4</td>
        <td>Website</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>5</td>
        <td>Food Panda</td>
        <td></td>
        <td></td>
      </tr>

    </tbody>
  </table>
</div>
</div>
</div>
</div>

</div>

<div class="col-sm-6 pull-left">
  <div class="panel panel-default">
  <!-- Default panel contents -->
    <div class="panel-heading" >
      <h4 class="panel-title">
        <a class="col" id="todays_sale" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">Top 5 Seller
        <i class="glyphicon glyphicon-chevron-up pull-right"></i>
        </a>
      </h4>
    </div>
  <div id="collapseFive" class="panel-collapse collapse">
      <div class="panel-body">
       <div class="col-md-6 col-lg-6">
<!-- Table -->
  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Seller Name</th>
        
        <th>Sale Amount(Rs)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>Seller 1</td>
        
        <td></td>
      </tr>
      <tr>
        <td>2</td>
        <td>Seller 2</td>
        
        <td></td>
      </tr>
      <tr>
        <td>3</td>
        <td>Seller 3</td>
        
        <td></td>
      </tr>
      <tr>
        <td>4</td>
        <td>Seller 4</td>
        
        <td></td>
      </tr>
      <tr>
        <td>5</td>
        <td>Seller 5</td>
        
        <td></td>
      </tr>

    </tbody>
  </table>

</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</body>
<!-- Body Container End -->

<!-- footer Start -->

<?php require_once 'common/footer.php';?>

<!-- footer End -->

