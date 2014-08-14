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
    
  <div class="container">
    
    <!--Progress Bar Div -->

      <div class="progress" align="center" id="progress" style="margin-top:80px;display:none;">
        <div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
          <span class="sr-only">45% Complete</span>
        </div>
      </div>
    <!--Progress Bar Div End -->

      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
      <h2>Dashboard</h2>

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
        <h4>Today's Sale (Rs.<?php echo $totalSale;?>)</h4>
        <div class="panel panel-default" style="float:left;width:45%;">
  <!-- Default panel contents -->
  <div class="panel-heading">Top 5 Stores</div>
  
<!-- Table -->
  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Store Name</th>
        <th>Sale Amount(Rs)</th>
      </tr>
    </thead>
    <tbody>
      <?php if(is_array($topStoreArray)&& count($topStoreArray)>0) { $i=1;
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

<div class="panel panel-default" style="float:right;width:45%;">
  <!-- Default panel contents -->
  <div class="panel-heading" >Top 5 Items</div>
  
<!-- Table -->
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

<div class="panel panel-default" style="float:left;width:45%;">
  <!-- Default panel contents -->
  <div class="panel-heading" >Top 5 Channel</div>
  
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


<div class="panel panel-default" style="float:right;width:45%;">
  <!-- Default panel contents -->
  <div class="panel-heading" >Top 5 Saler</div>
  
<!-- Table -->
  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Saler Name</th>
        
        <th>Sale Amount(Rs)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>Saler 1</td>
        
        <td></td>
      </tr>
      <tr>
        <td>2</td>
        <td>Saler 2</td>
        
        <td></td>
      </tr>
      <tr>
        <td>3</td>
        <td>Saler 3</td>
        
        <td></td>
      </tr>
      <tr>
        <td>4</td>
        <td>Saler 4</td>
        
        <td></td>
      </tr>
      <tr>
        <td>5</td>
        <td>Saler 5</td>
        
        <td></td>
      </tr>

    </tbody>
  </table>

</div>
</div>
</div>
<!-- Body Container End -->

<!-- footer Start -->
<div style="text-align:center;">
<?php require_once 'common/footer.php';?>
</div>
<!-- footer End -->

