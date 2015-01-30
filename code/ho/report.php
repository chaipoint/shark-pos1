<?php 
	require_once 'common/header.php';
	require_once 'constant.php';
    require_once 'common/couchdb.phpclass.php';
	$couch = new CouchPHP(); 
?>
	<link rel="stylesheet" href="css/common.css" type="text/css">
	<body>
		<!--Body Header Start -->
<?php 
		require_once 'common/html_header.php';
?>
		<div class="container-fluid">
     
		<!--Progress Bar Div -->

		<div class="progress" align="center" id="progress" style="margin-top:80px;display:none;">
			<div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
				<span class="sr-only">45% Complete</span>
			</div>
		</div>
		<!--Progress Bar Div End -->

		

		<form class="form-inline" id="report-form" name="report-form" method="post"> 
			<ol class="breadcrumb" style="margin-top:60px;">
				<li>
					<div class="form-group">
						<label  class="control-label" for="sales_reg_search">Date</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<div class="input-group">
							<input type="text" name="date" id="date" class="form-control datepicker" required data-provide="datepicker-inline" data-date-format="dd-MM-yyyy"  data-date-autoclose = "true" data-date-end-date="+0d" readonly/>
						</div>
						<label  class="control-label" for="sales_reg_search">Store</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<div class="input-group">
							<select name="store" id="store" class="form-control">
								<option value=''>All</option>
								<option value='88'>Old Airport Road</option>
							</select>
						</div>
						<div class="input-group">
							<span class="input-group-btn">
							<button class="btn btn-primary" type="submit" style="padding-top:4px; padding-bottom:5px;" id="search_button"><i class="glyphicon glyphicon-search"></i></button>
							</span>
						</div>
					</div>
				</li>
			</ol>
		</form>

    
      <!--<h2>Dashboard</h2>-->
      


<?php 
	if(!empty($_REQUEST['store'])){
		$date = date('Y-m-d', strtotime($_REQUEST['date']));
		$store = $_REQUEST['store'];
		$csv = '';
		$csv = '"StoreId", "StoreName", "BillNo", "BillDate", "BillTime"'. "\r";
		$getRecord = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_BILL_BY_STORE)->setParam(array("include_docs"=>"true","key"=>'["'.$date.'", "'.$store.'"]'))->execute();
		if(array_key_exists('rows', $getRecord)){
			foreach($getRecord['rows'] as $key=>$value){
				$doc = $value['doc'];
				$csv .= ''.$doc['store_id'].' , '.$doc['store_name'].', '.$doc['bill_no'].', '.$doc['time']['created'].', '.$doc['time']['updated'].''. "\r";
			}
		}
		header("cache-control: private");
        header('content-Disposition:attachment;filename=Bill_Wise_Report_'.$date.'.csv');
        header('content-type: application/csv,UTF-8');
        header('content-length: ' . strlen($csv));
        header('content-Transfer-Encoding:binary');
        //ob_clean();
        //flush();
        echo $csv;
		//echo '<pre>';
		//print_r($getRecord);
		//echo '</pre>';
	}
?>
       

		</div>
	</body>
	<!-- Body Container End -->
	<!-- footer Start -->

<?php 
	require_once 'common/footer.php';
?>
	<!-- footer End -->

