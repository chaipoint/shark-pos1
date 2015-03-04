<style>
	.btn-product {
		background: #EEE;
		border: 1px solid #EEE;
		border-bottom: 0;
		}
	#wrapper {
	margin:34px;
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
	<?php //$this->commonView('menu');
	//require_once 'C:\inetpub\wwwroot\pos\common/menu.php';?>
	
	<div id="wrapper" style="">
		<div id="content">
			<div class="c1">
				<div class="pos">
					<div id="pos">
						<form action="" method="post" accept-charset="utf-8" class="form-inline">
							<div class="well well-sm" id="leftdiv" style="margin-top:10px;height:481px;">
								<?php 
									if(!empty($_GET['bill'])){ 
										echo '<strong style="color:#3071a9">Bill No: '.$_GET['bill'].'</strong>';
									}

									if(!empty($_GET['order'])){ 
										echo '<strong style="color:#3071a9">Order No: '.$_GET['order'].'&nbsp;&nbsp;</strong>';
									}

									if(!empty($lastBillNo) && !empty($lastBillTime)){
										echo '<strong style="color:#3071a9">Bill Printed Today: '.$lastBillNo.'</strong>';
									}
								?>
								
								<div id="print">
									<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-condensed table-hover miantable" style="margin: 5px 0 0 0;background-color:#A9A9A9;" id="menu-table">
										<thead>
											<tr class="">
												<th style="width:9%" class="satu">X</th>
												<th>Product</th>
												<th style="width: 12%">Qty</th>
												<th style="width: 24%">Price</th>
												<th style="width: 19px; padding: 0;">&nbsp;</th>
											</tr>
										</thead>
									</table>
									<div id="prodiv">
										<div id="protbldiv" class="nano" style="height:247px">
											<div class="content">
												<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-condensed table-hover protable" id="saletbl" style="margin: 0;">
													<tbody></tbody>
												</table>
											</div>
											<div style="clear: both;"></div>
										</div>
									</div>
									<div style="clear: both;"></div>
									<div id="totaldiv" style="background-color:#A9A9A9;">
										<table id="totaltbl" class="table table-striped table-condensed totals" style="margin-bottom:10px;color:black;margin-top:-72px;">
											<tbody>
												<tr class="hide">
													<td width="25%" >Discount</td>
													<td width="25%"> 
														<div class="input-group">
														<input type="text" class="form-control input-sm" style="height:22px;width:61px;margin-left:-52px;" id="discount_input_box" name="discount_input_box"/>
														</div><span class="label label-default" style="margin-left:6px" id="apply_discount" >Apply</span>
													</td>
													<td width="25%">Discount Amnt</td>
													<td class="text_right" colspan="2"><span id="ds_con">0</span></td>
												</tr>
												<tr class="">
													<td width="25%" class="text-left" >Total Items</td>
													<td width="25%" class="text-left"><span id="count">0</span></td>
													<td width="25%">SubTotal</td>
													<td class="text_right" colspan="2"><span id="total">0</span></td>
												</tr>
												<tr class="">
													<td width="25%" class="text-left"></td>
													<td width="25%" class="text-left"></td>
													<td width="25%"> 
														<a href="#" id="add_tax" style="color:black;">Tax <i class="glyphicon glyphicon-pencil"></i> </a> (+)
													</td>
													<td class="text_right"><span id="ts_con">0</span></td>
												</tr>
												<tr class="">
													<td width="30%" class="text-left"><strong>Total Payable</strong></td>
															<!--<td width="25%" class="text-left"> <button class='btn btn-sm btn-primary'  id='reward_redemption'>Reward</button> </td>-->
													<td width="25%" class="text-left"><input type='text' class="form-control hide" name='redemption_code' placeholder='Scan Code' id='redemption_code' style="width:101px" /></td>
													<td width="25%" id="image_loading" class="text-left hide"><img src="<?php echo IMG ?>loader.gif"></td>
													<td width="30%" class="text-left"></td>
													<td class="text_right"><strong><span id="total-payable">0</span></strong></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								
								<div id="botbuttons" style="margin-top:-9px;">
									<button type="button" class="btn btn-danger" id="cancel" style="width:134px;height:35px;line-height:13px"><?php if(!empty($_GET['bill_no'])){ echo 'Cancel';}else {echo 'Clear Items';}?></button>
									<!--<button type="button" class="btn btn-info <?php echo (!empty($_GET['bill_no']) ? '' : 'hide'); ?>" id="print-can" style="width:100px;height:35px;;line-height:13px">Reprint</button>-->
									<button type="button" class="btn btn-success <?php echo (!empty($_GET['bill_no']) ? 'hide' : ''); ?>" id="payment" style="margin-right: 90px; width: 130px;height:35px;line-height:13px;margin-right:0px;float:right; ">Print Bills</button>
									<button type="button" class="btn btn-success hide" id="claim_reward" style="margin-right: 90px; width: 180px;height:40px;line-height:13px">Claim Reward</button>
								</div>
								
							 </div>
						</form>
						<div id="cp">
							<div id="slider" style="height:38px;">
								<div class="btn-category" data-catSelected = "<?php echo $firstCat; ?>">
								<?php
									foreach($catList as $catKey => $catValue){
										echo '<button type="button" class="btn '.($firstCat == $catKey ? 'btn-primary' : '').' category-selection" style="height:36px;width:137px;" value="'.$catKey.'"
												id="category-'.$catKey.'" data-category="'.$catKey.'" >'.$catValue.'</button>';
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
												echo '<button type="button" class="btn btn-success btn-lg btn3d btn25 category-product" style="height:46px" value="'.$pValue['mysql_id'].'" category-product-sequence="'.$pKey.'">'.$pValue['name'].'</button>';
											}
									?>
									<div style="clear: both;"></div>
									</div>
								</div>
							</div>
						</div>
						
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
</div>

<?php require_once 'modal_payment.php';?>
