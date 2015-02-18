<style>
	.btn-product {
		background: #EEE;
		border: 1px solid #EEE;
		border-bottom: 0;}
	.navbar {
		margin-bottom: 1px;
	}
	
</style>
<script> var printUtility = <?php echo $printUtility; ?>; </script>
<script type="text/javascript" src="<?php echo JS;?>pos/billing.js"></script>
<?php //echo '<pre>'; print_r($productList);echo '</pre>';
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
				var ertList = \''.json_encode($ertList).'\';
				var ertArray = $.parseJSON(ertList);
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
		
		<?php 
		if(!empty($_GET['bill'])){ 
			echo '<button class="btn btn-sm btn-success" type="button"><b>Bill No</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge">'.$_GET['bill'].'</span></button>';
		}

		if(!empty($_GET['order'])){ 
			echo '<button class="btn btn-sm btn-primary" type="button"><b>Order No</b>&nbsp;<span class="badge">'.$_GET['order'].'</span></button>';
		}

		if(!empty($lastBillNo) && !empty($lastBillTime)){
			//echo $lastBillNo." ".$lastBillTime;
			echo '&nbsp;&nbsp;<button class="btn btn-sm btn-info" type="button"><b>Last Bill No:</b>&nbsp;<span id="last_bill_no"><strong>'.$lastBillNo.'</strong></span>&nbsp;&nbsp;&nbsp;&nbsp;<b>Time:</b>&nbsp;<strong>'.date('H:i:s',strtotime($lastBillTime)).'</strong></button>';
		}
		?>
		
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
												style="margin: 5px 0 0 0;background-color:#A9A9A9;" id="menu-table">
												<thead>
													<tr class="">
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
											<div id="totaldiv" style="background-color:#A9A9A9;">
												<table id="totaltbl"
													class="table table-striped table-condensed totals"
													style="margin-bottom:10px;color:black;">
													<tbody>
														<tr class="">
															<td width="25%">Total Items</td>
															<td><span id="count">0</span></td>
															<td width="25%">SubTotal</td>
															<td class="text_right" colspan="2"><span id="total">0</span>
															</td>
														</tr>
														<tr class="">
															<td width="25%" rowspan="2">
																<div class="input-group">
																			<input type="text" class="form-control input-sm" id="discount_input_box" placeholder="%" name="discount_input_box"/>
																			<span class="input-group-addon">
																			<i class="glyphicon glyphicon-remove" id="discount-close"></i>
																		</span>
																</div>
															</td>
															<td rowspan="2"></td>
															<td width="25%">Discount (-)</td>
															<td class="text_right"><span id="ds_con">0</span></td>
														</tr>

														<tr class="">
															<td width="25%"> <a href="#" id="add_tax"
																style="color:black;">Tax <i
																	class="glyphicon glyphicon-pencil"></i> 
																</a> (+)
															</td>
															<td class="text_right"><span id="ts_con">0</span></td>
														</tr>
														<tr class="">
															<td width="25%" class="text-left"> <button class='btn btn-sm btn-primary'  id='reward_redemption'>Reward</button> </td>
															<td width="25%" class="text-left"><input type='text' class="form-control hide" name='redemption_code' placeholder='Scan Code' id='redemption_code' style="width:101px" /></td>
															<td width="25%" id="image_loading" class="text-left hide"><img src="<?php echo IMG ?>loader.gif"></td>
															<td width="30%" class="text-left"><strong>Total Payable</strong></td>
															<td class="text_right"><strong><span id="total-payable">0</span></strong></td>
														</tr>
														
														
													</tbody>
												</table>
											</div>
										</div>
										<div id="botbuttons" style="text-align:center;margin-top:-5px;">
											<button type="button" class="btn btn-danger" id="cancel"
												style="width:90px;height:40px"><?php if(!empty($_GET['bill_no'])){ echo 'Cancel';}else {echo 'Reset';}?></button>
											<button type="button" class="btn btn-info <?php echo (!empty($_GET['bill_no']) ? '' : 'hide'); ?>" id="print-can" style="width:100px;height:40px;">Reprint</button>
											<button type="button" class="btn btn-success" id="payment" style="margin-right: 90px; width: 180px;height:40px">Payment</button>
											<button type="button" class="btn btn-success hide" id="claim_reward" style="margin-right: 90px; width: 180px;height:40px;">Claim Reward</button>
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
											<div id="proajax" style="overflow:scroll;height:442px;">
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
	<div id="printBill" style="display:none;" ></div>
	