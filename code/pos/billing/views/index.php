<style>
	.btn-product {
		background: #EEE;
		border: 1px solid #EEE;
		border-bottom: 0;
	}
</style>
<script type="text/javascript" src="<?php echo JS;?>pos/billing.js"></script>
<?php
	$script = "";
	if(count($bill)>0){
		$script .= "\n".'var due_amount = '.$bill['due_amount'].'; var doc = \''.$bill['_id']."';\nvar bill_status_id = ".$bill['bill_status_id'].";\n";
		foreach($bill['items'] as $key => $value){
			$script .= '$billingItems['.$value['id'].'] = new Object();'."\n"; 
			$script .= '$billingItems['.$value['id'].'] = $.parseJSON(\''.json_encode($value)."')\n"; 
		}
	}
	if(count($catList)>0){
		echo '
			<script>
				var catList = \''.json_encode($catList).'\';
				var catArray = $.parseJSON(catList);
				var productList = \''.json_encode($productList).'\';
				var productArray = $.parseJSON(productList);
				var selectedCat = '.$firstCat.';
				var config_data = $.parseJSON(\''.json_encode($config_data).'\');
				'.$script.';
			</script>
		';
	}
?>
	<div class="container"> 
		<?php if(!empty($_GET['bill'])){ echo '<button class="btn btn-sm btn-primary" type="button"><b>Bill No</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge">'.$_GET['bill'].'</span></button>';}?>
		
			<div id="wrapper">
				<div id="content">
					<div class="c1">
						<div class="pos">
							<div id="pos">
								<form action="" method="post" accept-charset="utf-8" class="form-inline">
									<div class="well well-sm" id="leftdiv">
										<div id="printhead">
											<h2>
												<strong>Simple POS</strong>
											</h2>
											<p>
												My Shop Lot, Shopping Mall,<br> Post Code, City
											</p>
											<p>Date: 18/07/2014</p>
										</div>
										<div id="print">
											<table width="100%" border="0" cellpadding="0"
												cellspacing="0"
												class="table table-striped table-condensed table-hover miantable"
												style="margin: 5px 0 0 0;" id="menu-table">
												<thead>
													<tr class="success">
														<th style="width: 9%" class="satu">X</th>
														<th>Product</th>
														<th style="width: 12%">Qty</th>
														<th style="width: 24%">Price</th>
														<th style="width: 19px; padding: 0;">&nbsp;</th>
													</tr>
												</thead>
											</table>
											<div id="prodiv">
												<div id="protbldiv" class="nano">
													<div class="content">
														<table width="100%" border="0" cellpadding="0"
															cellspacing="0"
															class="table table-striped table-condensed table-hover protable"
															id="saletbl" style="margin: 0;">
															<tbody>
															</tbody>
														</table>
													</div>
													<div style="clear: both;"></div>
												</div>
											</div>
											<div style="clear: both;"></div>
											<div id="totaldiv">
												<table id="totaltbl"
													class="table table-striped table-condensed totals"
													style="margin-bottom: 10px;">
													<tbody>
														<tr class="success">
															<td width="25%">Total Items</td>
															<td><span id="count">0</span></td>
															<td width="25%">SubTotal</td>
															<td class="text_right" colspan="2"><span id="total">0</span>
															</td>
														</tr>
														<tr class="success">
															<td width="25%">
																<div class="input-group">
																			<input type="text" class="form-control input-sm" id="discount_input_box" placeholder="%" name="discount_input_box"/>
																			<span class="input-group-addon">
																			<i class="glyphicon glyphicon-remove" id="discount-close"></i>
																		</span>
																</div>
															</td>
															<td><span id="ds_con">0</span></td>
															<td width="25%"> <a href="#" id="add_tax"
																style="color: #FFF;">Tax <i
																	class="glyphicon glyphicon-pencil"></i> 
																</a>
															</td>
															<td class="text_right"><span id="ts_con">0</span></td>
														</tr>
														<tr class="success">
															<td colspan="2">Total Payable</td>
															<td class="text_right" colspan="2"><span
																id="total-payable">0</span></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
										<div id="botbuttons" style="text-align: center;">
											<button type="button" class="btn btn-danger" id="cancel"
												style="width: 90px;"><?php if(!empty($_GET['bill_no'])){ echo 'Cancel';}else {echo 'Reset';}?></button>
											<!--<button type="button" class="btn btn-info" id="hold"
												style="width: 90px;">Hold</button>-->
											<button type="button" class="btn btn-success" id="payment"
												style="margin-right: 90px; width: 180px;">Payment</button>
										</div>
									</div>
								</form>
								<div id="cp">
									<div id="slider">
										<div class="btn-category" data-catSelected = "<?php echo $firstCat; ?>">
											<?php
												foreach($catList as $catKey => $catValue){
													echo '<button type="button" class="btn '.($firstCat == $catKey ? 'btn-primary' : '').' category-selection" value="'.$catKey.'"
												id="category-'.$catKey.'" data-category="'.$catKey.'" >'.$catValue.'</button>
';
												}

											?>
										</div>
										<div style="clear: both;"></div>
									</div>
									<div style="clear: both;"></div>
									<div id="ajaxproducts">
										<div class="btn-product clearfix">
											<div id="proajax" style="overflow:scroll;height:500px;">
													<?php
														if(array_key_exists($firstCat, $productList))
														foreach($productList[$firstCat] as $pKey => $pValue){
															echo '<button type="button" class="btn btn-success btn-lg btn3d btn25 category-product" value="'.$pValue['mysql_id'].'" category-product-sequence="'.$pKey.'">'.$pValue['name'].'</button>';
														}

													?>

												<div style="clear: both;"></div>
											</div>
											<!--<div class="btn-con">
												<button id="previous" type="button" class="btn btn-default">
													<i class="glyphicon glyphicon-chevron-left"></i>
												</button>
												<button id="next" type="button" class="btn btn-default">
													<i class="glyphicon glyphicon-chevron-right"></i>
												</button>
											</div>-->
										</div>
									</div>
								</div>
							</div>
							<div style="clear: both;"></div>
						</div>
						<div style="clear: both;"></div>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
	</div>
	<?php require_once 'modal_payment.php';?>
	