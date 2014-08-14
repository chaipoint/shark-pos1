<?php require_once 'header.php';
      require_once 'common/couchdb.phpclass.php';
      $couch = new CouchPHP(); ?>
<script type="text/javascript" src="js/sync.js"></script>
<style> 
.navbar-default{ background-color:#007fff; }
.panel-heading{ background-color:#FF6600 !important ;color:#ffffff !important;  }
</style>
  
  <body>
  <!-- Fixed navbar -->
  <div class="navbar navbar-default navbar-fixed-top"  role="navigation">
        <div class="container" >
        <div class="navbar-header" >
           <a class="navbar-brand" style="color:#ffffff;margin-right:40px;font-size:22px;font-weight:bold" href="#">Vente</a>
        </div>
        <div class="navbar-collapse collapse" >
            <ul class="nav navbar-nav" >
            <li class="active"><a  href="#">Home</a></li>
            <li><a href="#about" style="color:#ffffff;">Reports</a></li>
            <li><a href="#contact" style="color:#ffffff;">Analytics</a></li>
            <li class="dropdown">
             <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:#ffffff;">Data Set <span class="caret"></span></a>
             <ul class="dropdown-menu" role="menu">
             <li><a href="#">Staff Master</a></li>
                <li><a href="#">Store Master</a></li>
                <li><a href="#">Settings</a></li>
                <li class="divider"></li>
                <!--<li class="dropdown-header">Nav header</li>-->
                <li><a href="#">Bill Details</a></li>
                <li><a href="#">Attendance Sheet</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:#ffffff;">Data Sync <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="javascript:void(0);" id="staff_synk">Download Staff From CPOS</a></li>
                <li><a href="javascript:void(0);" id="store_synk">Download Store From CPOS</a></li>
                <li><a href="javascript:void(0);" id="config_synk">Download Settings From CPOS</a></li>
                <li class="divider"></li>
                <!--<li class="dropdown-header">Nav header</li>-->
                <li><a href="javascript:void(0);" id="bill_synk">Upload Bill</a></li>
                <li><a href="#">Upload Attendance</a></li>
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <!--<li><a href="../navbar/">Default</a></li>
            <li><a href="../navbar-static-top/">Static top</a></li>-->
            <li class="active"><a href="./" >Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class="container">

      <div class="progress" align="center" id="progress" style="margin-top:80px;display:none;">
        <div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
        <span class="sr-only">45% Complete</span>
       </div>
      </div>
  
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
        <h4>Todays Sale(Rs.<?php echo $totalSale;?>)</h4>
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
        <td>Masala Chai</td>
        <td>20</td>
        <td>500</td>
      </tr>
      <tr>
        <td>2</td>
        <td>Ginger Chai</td>
        <td>15</td>
        <td>400</td>
      </tr>
      <tr>
        <td>3</td>
        <td>Vegg Puff</td>
        <td>10</td>
        <td>300</td>
      </tr>
      <tr>
        <td>4</td>
        <td>Egg Puff</td>
        <td>5</td>
        <td>200</td>
      </tr>
      <tr>
        <td>5</td>
        <td>Samosa</td>
        <td>4</td>
        <td>100</td>
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
        <td>30</td>
        <td>10000</td>
      </tr>
      <tr>
        <td>2</td>
        <td>Food Court</td>
        <td>20</td>
        <td>8000</td>
      </tr>
      <tr>
        <td>3</td>
        <td>Facebook</td>
        <td>15</td>
        <td>5000</td>
      </tr>
      <tr>
        <td>4</td>
        <td>Website</td>
        <td>10</td>
        <td>2000</td>
      </tr>
      <tr>
        <td>5</td>
        <td>Food Panda</td>
        <td>6</td>
        <td>1000</td>
      </tr>

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
        <td>Masala Chai</td>
        <td>20</td>
        <td>500</td>
      </tr>
      <tr>
        <td>2</td>
        <td>Ginger Chai</td>
        <td>15</td>
        <td>400</td>
      </tr>
      <tr>
        <td>3</td>
        <td>Vegg Puff</td>
        <td>10</td>
        <td>300</td>
      </tr>
      <tr>
        <td>4</td>
        <td>Egg Puff</td>
        <td>5</td>
        <td>200</td>
      </tr>
      <tr>
        <td>5</td>
        <td>Samosa</td>
        <td>4</td>
        <td>100</td>
      </tr>

    </tbody>
  </table>

</div>
</div>
</div>
<!-- /container -->
<!-- footer -->
<div style="text-align:center;">
<?php include 'footer.php';?>
</div>

