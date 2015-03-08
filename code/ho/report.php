<?php 
	require_once 'common/header.php';
	require_once 'constant.php';
    require_once 'common/couchdb.phpclass.php';
	$couch = new CouchPHP(); 
?>
	<link rel="stylesheet" href="css/common.css" type="text/css">
	<script type="text/javascript" src="js/sync.js"></script>
	<body>
		<!--Body Header Start -->
<?php 
		require_once 'common/html_header.php';
		$getStore = $couch->getDesign(DESIGN_HO_DESIGN_DOCUMENT)->getView(DESIGN_HO_DESIGN_DOCUMENT_VIEW_STORE_BY_NAME)->setParam()->execute();
?>
		<div class="container-fluid">
		
		<form class="form-inline" id="report_form" name="report_form" method="post" action="download.php"> 
			<ol class="breadcrumb" style="margin-top:60px;">
				<li>
					<div class="form-group">
						<label  class="control-label" for="sales_reg_search">Date</label>&nbsp;&nbsp;&nbsp;&nbsp;
						
						<div class="input-group">
							<input type="text" name="date1" id="date1" class="form-control datepicker" required data-provide="datepicker-inline" data-date-format="dd-MM-yyyy"  data-date-autoclose = "true" data-date-end-date="+0d" readonly/>
						</div>
						
						<div class="input-group">
							<input type="text" name="date2" id="date2" class="form-control datepicker" required data-provide="datepicker-inline" data-date-format="dd-MM-yyyy"  data-date-autoclose = "true" data-date-end-date="+0d" readonly/>
						</div>
						
						<label  class="control-label" for="store">Store</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<div class="input-group">
							<select name="store" id="store" class="form-control">
								<!--<option value='all'>All</option>-->
								<?php
								if(array_key_exists('rows', $getStore)){
									foreach($getStore['rows'] as $key => $value){
										echo '<option value="'.$value['key'].'">'.$value['value'].'</option>';
									}
								}
								?>
								
							</select>
						</div>
						<div class="input-group">
							<span class="input-group-btn">
							<button class="btn btn-primary" type="button" style="padding-top:4px; padding-bottom:5px;" id="report_button"><i class="glyphicon glyphicon-download-alt"></i></button>
							</span>
						</div>
					</div>
				</li>
			</ol>
		</form>

    
      <!--<h2>Dashboard</h2>-->
      </div>
	</body>
	<!-- Body Container End -->
	<!-- footer Start -->

<?php 
	require_once 'common/footer.php';
?>
	<!-- footer End -->

