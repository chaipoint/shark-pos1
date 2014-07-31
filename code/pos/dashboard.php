<?php
require_once 'httpapi.php';
	
	if(array_key_exists('request_type', $_POST) && $_POST['request_type'] == 'save_bill'){
		
		$_POST['cd_doc_type'] = 'store_menu_bill';
		$_POST['billed_by'] = 2;
		$_POST['bill_time'] = date("Y-m-d H:i:s");
		require_once 'httpapi.php';

		$url = "http://127.0.0.1:5984/pos/_design/billing/_update/getbillno/bill_counter";
		$currentBillNo = curl($url,array(1=>1));
		$_POST['bill_no'] = $currentBillNo;

		$url = 'http://127.0.0.1:5984/pos/';
		$result = json_decode(curl($url,$_POST,array('is_content_type_allowed'=>true,'contentType'=>'application/json')),true);
		if(array_key_exists('ok', $result)){
			echo '{"error":false,"bill_no":"'.$currentBillNo.'"}';			
		}

		return;
	}
	if(array_key_exists('request_type', $_POST) && $_POST['request_type'] == 'todays_bill'){
		$url = 'http://127.0.0.1:5984/pos/_design/billing/_view/bill_by_date?key="'.date('Y-m-d').'"';
		$bills = json_decode(curl($url),true);
		//($bills);
		echo json_encode($bills['rows']);
		return;
	}
	//

	$catList = array();
	if(array_key_exists('store', $_GET) && is_numeric($_GET['store']) && $_GET['store'] >0){
		$url = 'http://127.0.0.1:5984/pos/_design/store/_view/store_list?key="'.$_GET['store'].'"';
		require_once 'httpapi.php';
		$storeDataList = json_decode(curl($url),true);
		$result = current($storeDataList['rows']);
		//print_r($result['value']);
		$catList = array();
		//var_dump($catList);
		$productList = array();
		//print_r($result['value']['menu_items']);
		foreach($result['value']['menu_items'] as $key => $Items){
			if(!empty($Items['category_id'])){
				$catList[$Items['category_id']] = $Items['category']; 
				$productList[$Items['category_id']][] = $Items;
			}
 		}

 		ksort($catList);
 	$catList[99] = 'AKESH';
 		 		$catList[] = 'AEWEKESH';
 		 		$catList[] = 'AEWEKESHSDFF';/**/

 		ksort($productList);
// 		print_r($productList);
 		$currectCat = array_keys($catList);
  		$firstCat = $currectCat[0];
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<base href="http://localhost/pos/" />
<title>Chai Point POS</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="shortcut icon"
	href="http://localhost/pos/images/icon.png" />
<link rel="stylesheet" href="css/bootstrap.css" type="text/css"
	charset="utf-8">
<link rel="stylesheet" href="css/posajax.css" type="text/css"
	charset="utf-8">

<link rel="stylesheet" href="css/non-responsive.css" type="text/css">
<link rel="stylesheet" href="css/print.css" type="text/css"
	media="print">
<script src="js/jquery.min.js"></script>


<script src="js/purl.js"></script>
<script src="js/moment.js"></script>
<style>
.btn-product {
	background: #EEE;
	border: 1px solid #EEE;
	border-bottom: 0;
}

.btn-con .btn-default {
	height: 43px;
}
</style>
<?php
	if(count($catList)>0){
		echo '
			<script>
				var catList = \''.json_encode($catList).'\';
				var productList = \''.json_encode($productList).'\';
				var productArray = $.parseJSON(productList);
				var selectedCat = '.$firstCat.';
				var store = '.$_GET['store'].';
			</script>
		';
	}

?>
<script>
	$(document).ready(function(){


		var now = moment().format("dddd, MMMM Do, YYYY, h:mm:ss A");
        $('#cur-time').text(now);
       

		var $billingItems = new Object();
		var $totalBillItems = 0;
		var $totalBillCost = 0.0;


		$("#submit-sale").click(function(){

			if(!$('#paid-amount').val()){
				bootbox.alert("Before Saving Bill Make Payment");
				return false;
			}
			if(parseInt($('#paid-amount').val()) < $totalBillCost){
				bootbox.alert("Please Make Full Payment");
				return false;
			}
			var billDetails = new Object();
			billDetails.total_qty = $totalBillItems;
			billDetails.total_amount = $totalBillCost;
			billDetails.payment_type = $("#paid_by").val();
			billDetails.customer = $("#billing_customer").val();
			billDetails.store = store;

			billDetails.items = new Object();
			
			//console.log(JSON.stringify($billingItems));
			var i = 0;
			$.each($billingItems,function(index,val){
				billDetails.items[i] = new Object();
				billDetails.items[i].p_id = index;
				billDetails.items[i].qty = val.qty;
				billDetails.items[i].name = val.name;
				billDetails.items[i].price = val.price;
				billDetails.items[i].total_amount = val.total_amount;
				billDetails.request_type = 'save_bill';
				i++;
			});
//			console.log(JSON.stringify(billDetails));
			$.ajax({
				type: 'POST',
				url: "dashboard.php",
		  		data : billDetails,
			}).done(function(response) {
				console.log(response);
				$('#payModal').modal('hide');
				result = $.parseJSON(response);
				bootbox.alert('Bill Successfully Saved <a class="label label-primary print-bill-today" href="billprint.php?bill_no='+result.bill_no+'" target="_blank">Print</a>');
			});
		});

		/*todays_sale*/
		$('#todays_sale').click(function(){
			$.ajax({
				type: 'POST',
				url: "dashboard.php",
		  		data : {request_type:'todays_bill'},
			}).done(function(response) {
				var result = $.parseJSON(response);
				var totalBills = result.length;
				if(totalBills>0){
					var trs = "";
					$.each(result,function(index,details){
						console.log(index+"=>"+JSON.stringify(details));
						trs += '<tr><td>'+details.value.customer+'</td><td>'+
								details.value.total_qty+'</td><td>'+
								details.value.total_amount+'</td><td><a class="label label-primary print-bill-today" href="billprint.php?bill_no='+details.value.bill_no+'" target="_blank">Print</a></td><td><a class="label label-warning print-bill-today" href="billprint.php?bill_no='+details.value.bill_no+'" target="_blank">Edit</a></td><td><a class="label label-danger print-bill-today" href="billprint.php?bill_no='+details.value.bill_no+'" target="_blank">Delete</a></td></tr>';
					});
					$("#today-sale-table tbody").html(trs);
				} 
			});
		});
		$("#today-sale-table tbody").on('click','.print-bill-today',function(){
	//		window.location = 'billprint.php?bill_no='+$(this).data('href');
		});
		

		$("#payment").click(function(){
			$("#fcount").text($totalBillItems);
			$("#twt").text($totalBillCost);
			if($totalBillItems == 0){
				bootbox.alert('Please add product to sale first');
			}else{
				$('#payModal').modal();
			}
		});

		$("#payModal").on('click','.showCModal',function(){
			$('#payModal').modal('hide');
			$('#customerModal').modal('show');
			return false;
		});

		$('#paid-amount').keyboard({
				restrictInput:true,
				preventPaste:true,
				autoAccept:true,
				alwaysOpen:false,
				openOn:'click',
				layout:'costom',
				display:{
					'a':'\u2714:Accept (Shift-Enter)',
					'accept':'Accept:Accept (Shift-Enter)',
					'b':'\u2190:Backspace',
					'bksp':'Bksp:Backspace',
					'c':'\u2716:Cancel (Esc)',
					'cancel':'Cancel:Cancel (Esc)',
					'clear':'C:Clear'
				},
				position:{
					of:null,
					my:'center top',
					at:'center top',
					at2:'center bottom'
				},
				usePreview:false,
				customLayout:{
					'default':['1 2 3 {clear}','4 5 6 .','7 8 9 0','{accept} {cancel}']
				},
				beforeClose:function(e,keyboard,el,accepted){
					if(accepted){
						var paid=parseFloat(el.value);
						if(paid < $totalBillCost){
							bootbox.alert('Paid amount is less than payable amount');
							$("#balance").text('')
							return false;
						}else{
							$("#balance").append( paid- $totalBillCost );
						}
					}
				}
			});


/*		$('input[id^="paid-amount"]').keydown(function(e){
			alert("HELLO");
			paid=$(this).val();
			if(e.keyCode==13){
				if(paid<total){
					bootbox.alert('Paid amount is less than payable amount');
					return false
				}
				$("#balance").empty();
				var balance=paid-twt;
				balance=parseFloat(balance).toFixed(2);
				$("#balance").append(balance);
				e.preventDefault();
				return false;
			}
		});/**/



		$("#cancel").click(function(){
			$billingItems = new Object();
			$totalBillItems = 0;
			$totalBillCost = 0.0;
			$("#count").text(0);		
			$("#total").text(0);
			$("#total-payable").text(0);	
			$("#saletbl tbody").html("");	
		});
		$(".category-selection").click(function(){
			if(!$(this).hasClass("btn-primary")){
				$(".category-selection").removeClass('btn-primary');
				$(this).addClass('btn-primary');
				//console.log(productArray[$(this).data('category')]);
				$buttonList = "";
				selectedCat = $(this).data('category');
				$.each(productArray[selectedCat],function(key,value){
					$buttonList += '<button type="button" class="btn btn-prni hov category-product" value="'+value.mysql_id+'" category-product-sequence="'+key+'">'+value.name+'</button>';
				});
				$("#proajax").html($buttonList);
			}
		});

		$("#proajax").on("click",".category-product",function(){
				var selectedSequence = $(this).attr('category-product-sequence');
				var productData = productArray[selectedCat][selectedSequence];
				if($billingItems[productData.mysql_id]){
					$billingItems[productData.mysql_id].qty = parseInt($billingItems[productData.mysql_id].qty) + 1;
					//$billingItems[productData.mysql_id].price =  parseFloat(productData.price);
					$billingItems[productData.mysql_id].total_amount = parseFloat($billingItems[productData.mysql_id].total_amount) + parseFloat(productData.price);
					var bIO = $('tr[billing-product="'+productData.mysql_id+'"]');					
					bIO.find(".qty").text($billingItems[productData.mysql_id].qty);
					bIO.find(".price").text($billingItems[productData.mysql_id].total_amount.toFixed(2));
					
					$totalBillItems +=  1;


					$totalBillCost = parseFloat($totalBillCost) + parseFloat(productData.price);
					console.log("Inside Existing = " + $totalBillCost);

				}else{
					$billingItems[productData.mysql_id] = new Object();
					$billingItems[productData.mysql_id].qty = 1;
					$billingItems[productData.mysql_id].price = parseFloat(productData.price);
					$billingItems[productData.mysql_id].name = productData.name;
					$billingItems[productData.mysql_id].total_amount = productData.price;

					$totalBillItems 	+= 	parseInt($billingItems[productData.mysql_id].qty);
					$totalBillCost 		= 	parseFloat($totalBillCost) + parseFloat(productData.price);

					$("#saletbl tbody").append('<tr billing-product="'+productData.mysql_id+'"><td><span class="glyphicon glyphicon-remove-sign"></span></td><td class="btn-warning">'+productData.name+'&nbsp;@&nbsp;'+productData.price+'</td><td><span class="qty">'+(1)+'</span></td><td><span class="price text-right">'+parseFloat(productData.price).toFixed(2)+'</span></td></tr>');
					//				console.log($billingItems[productData.mysql_id]);
					//				console.log($billingItems);
						console.log(productData.name);
						console.log(productData['name']);
				}
				$("#count").text($totalBillItems);		
				$totalBillCost = $totalBillCost.toFixed(2);
				$("#total").text($totalBillCost);
				$("#total-payable").text($totalBillCost);		
				console.log("OUtside = " + $totalBillCost);

		});	
		$('.btn-category').bxSlider({minSlides:5,maxSlides:5,slideWidth:600,slideMargin:0,ticker:false,infiniteLoop:false,hideControlOnEnd:true,mode:'horizontal'});

	});
</script>
</head>
<body>
	<div id="wrap">
		<div class="navbar navbar-static-top navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse"
						data-target=".navbar-inverse-collapse">
						<span class="icon-bar"></span> <span class="icon-bar"></span> <span
							class="icon-bar"></span>
					</button>
					<a class="navbar-brand"> Chai Point POS </a>
				</div>
				<ul class="nav navbar-nav">
					<li class="dropdown"><a class="dropdown-toggle tip"
						data-toggle="dropdown" href="#" data-placement="right"
						title="Language"><img
							src=""
							style="margin-top: -1px" align="middle"> </a>
						<ul class="dropdown-menu" style="min-width: 60px;" role="menu"
							aria-labelledby="dLabel">
							<li><a
								href=""><img
									src=""
									class="language-img"> &nbsp;&nbsp; English </a></li>
						</ul>
					</li>
					<li><a href="sales.php"
						class="tip" data-placement="right" title="Sales"><i
							class="glyphicon glyphicon-list"></i> </a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a
						href="http://localhost/pos/index.php?module=auth&amp;view=logout"
						class="tip" data-placement="left" title="Hi, User! Logout"><i
							class="glyphicon glyphicon-log-out"></i> </a></li>
				</ul>
				<a
					class="btn btn-success btn-sm pull-right external"
					style="padding: 5px 8px; margin: 10px 0 5px 5px;"
					data-toggle="modal" data-target="#saleModal" id="todays_sale"> Today's Sale </a> <a
					data-toggle="modal"
					data-target="#opModal"
					class="btn btn-info btn-sm pull-right external" id="ob"
					style="padding: 5px 8px; margin: 10px 5px 5px 5px;"> Opened Bills </a>
				
				<ul class="nav navbar-nav navbar-right">
					<li><a class="hov"><span id="cur-time"></span> </a></li>
				</ul>
			</div>
		</div>
		<div class="container">
			<div id="wrapper">
				<div id="content">
					<div class="c1">
						<div class="pos">
							<div class="alert alert-dismissable alert-success">
								<button type="button" class="close" data-dismiss="alert">×</button>
								<p>Logged In Successfully</p>
							</div>
							<div id="pos">
								<form action="http://localhost/pos/index.php?module=pos"
									method="post" accept-charset="utf-8">
									<div style="display: none">
										<input type="hidden" name="csrf_pos"
											value="d18c90a451393f634f543c90f9a24b6d" />
									</div>
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
															<td width="25%">Total</td>
															<td class="text_right" colspan="2"><span id="total">0</span>
															</td>
														</tr>
														<tr class="success">
															<td width="25%">Discount <a href="#" id="add_discount"
																style="color: #FFF; font-size: 0.80em"><i
																	class="glyphicon glyphicon-pencil"></i> </a>
															</td>
															<td><span id="ds_con">0</span></td>
															<td width="25%">Tax <a href="#" id="add_tax"
																style="color: #FFF; font-size: 0.80em"><i
																	class="glyphicon glyphicon-pencil"></i> </a>
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
												style="width: 90px;">Cancel</button>
											<!--<button type="button" class="btn btn-warning" id="print" onClick="window.print();return false;">
                  Print                  </button>-->
											<button type="button" class="btn btn-info" id="hold"
												style="width: 90px;">Hold</button>
											<button type="button" class="btn btn-success" id="payment"
												style="margin-right: 0; width: 180px;">Payment</button>
										</div>

										<input type="hidden" name="customer" id="customer" value="19" />
										<input type="hidden" name="inv_tax" id="tax_val" value="0" />
										<input type="hidden" name="inv_discount" id="discount_val"
											value="0" /> <input type="hidden" name="rpaidby" id="rpaidby"
											value="cash" style="display: none;" /> <input type="hidden"
											name="count" id="total_item" value="" /> <input type="hidden"
											name="delete_id" id="is_delete" value="" /> <input
											type="hidden" name="hold_ref" id="hold_ref" value="" /> <input
											type="hidden" name="paid_val" id="paid_val" value="" /> <input
											type="hidden" name="cc_no_val" id="cc_no_val" value="" /> <input
											type="hidden" name="cc_holder_val" id="cc_holder_val"
											value="" /> <input type="hidden" name="cheque_no_val"
											id="cheque_no_val" value="" /> <span id="hidesuspend"></span>
										<input type="submit" id="submit" value="Submit Sale"
											style="display: none;" />
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
											<div id="proajax" style="overflow:scroll;max-height:400px;">
													<?php
														foreach($productList[$firstCat] as $pKey => $pValue){
															echo '<button type="button" class="btn btn-prni hov category-product" value="'.$pValue['mysql_id'].'" category-product-sequence="'.$pKey.'">'.$pValue['name'].'</button>';
														}

													?>

												<div style="clear: both;"></div>
											</div>
											<div class="btn-con">
												<button id="previous" type="button" class="btn btn-default"
													style='z-index: 10002;'>
													<i  style="margin-bottom:100%;"class="glyphicon glyphicon-chevron-left"></i>
												</button>
												<button id="next" type="button" class="btn btn-default"
													style='z-index: 10003;'>
													<i class="glyphicon glyphicon-chevron-right"></i>
												</button>
											</div>
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
	</div>
	<div id="footer">
		<div class="container">
			<p class="credit">
				Copyright &copy; 2014 POS <a href="http://tecdiary.net/support/sma-guide/"
					target="_blank" class="tip" title="Help"><i
					class="icon-question-sign"></i> </a>
			</p>
		</div>
	</div>
	<div id="loading" style="display: none;">
		<div class="blackbg"></div>
		<div class="loader">
			<img src="http://localhost/pos/images/loader.gif" alt="" />
		</div>
	</div>
	<div class="modal fade" id="saleModal" tabindex="-1" role="dialog"
		aria-labelledby="saleModalLabel" aria-hidden="true">

		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
					<h4 class="modal-title" id="today-sale-header"><?php echo "Sale Of : ".date("M,d Y");?></h4>
				</div>
				<div class="modal-body">
					<table class="table table-striped" style="margin-bottom: 0;" id="today-sale-table">
						<tbody>
						</tbody>

					</table>
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>

	</div>
	<div class="modal fade" id="opModal" tabindex="-1" role="dialog"
		aria-labelledby="opModalLabel" aria-hidden="true"></div>

	<div class="modal fade" id="payModal" tabindex="-1" role="dialog"
		aria-labelledby="payModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
					<h4 class="modal-title" id="payModalLabel">Payment</h4>
				</div>
				<div class="modal-body">
					<table class="table table-striped" style="margin-bottom: 0;">
						<tbody>
							<tr>
								<td width="50%">Customer <!--<a href="#"
									class="btn btn-primary btn-xs showCModal"><i
										class="glyphicon glyphicon-plus-sign"></i> Add Customer </a>-->
								</td>
								<td width="50%">
									<span class="inv_cus_con"> 
										<select class="form-control pcustomer" id="billing_customer">
											<option value="wic">Walk-in Client</option>
										</select>
									</span>
								</td>
							</tr>
							<tr>
								<td>Total Payable Amount :</td>
								<td><span
									style="background: #FFFF99; padding: 5px 10px; text-weight: bold; color: #000;"><span
										id="twt"></span> </span></td>
							</tr>
							<tr>
								<td>Total Purchased Items :</td>
								<td><span
									style="background: #FFFF99; text-weight: bold; padding: 5px 10px; color: #000;"><span
										id="fcount"></span> </span></td>
							</tr>
							</tr>

							<td>Paid by :</td>
							<td><select name="paid_by" id="paid_by" class="form-control">
									<option value="cash">Cash</option>
							</select></td>
							</tr>
							<tr class="pcash">
								<td>Paid :</td>
								<td><input type="text" id="paid-amount" class="form-control"/></td>
							</tr>
							<tr class="pcash">
								<td>Return Change :</td>
								<td><span
									style="background: #FFFF99; padding: 5px 10px; text-weight: bold; color: #000;"
									id="balance"></span></td>
							</tr>
							<tr class="pcc" style="display: none;">
								<td>Credit Card No :</td>
								<td><input type="text" id="pcc" class="form-control"
									style="padding: 2px !important; height: auto !important;" /></td>
							</tr>
							<tr class="pcc" style="display: none;">
								<td>Credit Card Holder :</td>
								<td><input type="text" id="pcc_holder" class="form-control"
									style="padding: 2px !important; height: auto !important;" /></td>
							</tr>
							<tr class="pcheque" style="display: none;">
								<td>Cheque No :</td>
								<td><input type="text" id="cheque_no" class="form-control"
									style="padding: 2px !important; height: auto !important;" /></td>
							</tr>
						</tbody>

					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						Close</button>
					<button class="btn btn-success" id="submit-sale">Submit</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="proModal" tabindex="-1" role="dialog"
		aria-labelledby="proModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
					<h4 class="modal-title" id="proModalLabel">Payment</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="rwNo" value="">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="oPrice" class="control-label"> Current Price </label>
								<input type="text" class="form-control input-sm" id="oPrice"
									disabled="disabled">
							</div>
							<div class="form-group">
								<label for="nPrice" class="control-label"> New Price </label> <input
									type="text" class="form-control input-sm kbp-input" id="nPrice"
									onClick="this.select();" placeholder="New Price">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="oQuantity" class="control-label"> Current Quantity </label>
								<input type="text" class="form-control input-sm" id="oQuantity"
									disabled="disabled">
							</div>
							<div class="form-group">
								<label for="nQuantity" class="control-label"> New Quantity </label>
								<input type="text" class="form-control input-sm kbq-input"
									id="nQuantity" onClick="this.select();"
									placeholder="Current Quantity">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						Close</button>
					<button class="btn btn-success" id="update-row">Update</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="customerModal" tabindex="-1" role="dialog"
		aria-labelledby="proModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
					<h4 class="modal-title" id="proModalLabel">Add Customer</h4>
				</div>
				<div class="modal-body">
					<div id="customerError"></div>
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<label class="control-label" for="code"> Name </label> <input
									type="text" name="name" value="" class="form-control input-sm"
									id="cname" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group">
								<label class="control-label" for="cemail"> Email Address </label>
								<input type="text" name="email" value=""
									class="form-control input-sm" id="cemail" />
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								<label class="control-label" for="phone"> Phone </label> <input
									type="text" name="phone" value="" class="form-control input-sm"
									id="cphone" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group">
								<label class="control-label" for="cf1"> Custom Field 1 </label>
								<input type="text" name="cf1" value=""
									class="form-control input-sm" id="cf1" />
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								<label class="control-label" for="cf2"> Custom Field 2 </label>
								<input type="text" name="cf2" value=""
									class="form-control input-sm" id="cf2" />
							</div>
						</div>
					</div>
					<input type="hidden" id="show_m" value="">
				</div>
				<div class="modal-footer" style="margin-top: 0;">
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						Close</button>
					<button class="btn btn-success" id="add-customer">Add Customer</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="susModal" tabindex="-1" role="dialog"
		aria-labelledby="susModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
					<h4 class="modal-title" id="susModalLabel">Save to Opened Bills</h4>
				</div>
				<div class="modal-body">
					<table class="table table-striped" style="margin-bottom: 0;">
						<tbody>
							<tr>
								<td width="50%">Customer <a href="#"
									class="btn btn-primary btn-xs showCModal"><i
										class="glyphicon glyphicon-plus-sign"></i> Add Customer </a>
								</td>
								<td width="50%"><span class="inv_cus_con"> <select
										class="form-control pcustomer"
										style="padding: 2px !important; height: auto !important;">
											<option value="3">Walk-in Client</option>
									</select>
								</span></td>
							</tr>

							<tr class="pcash">
								<td>Hold Bill Ref :</td>
								<td><input type="text" name="hold_v" value=""
									class="form-control input-sm" id="hold_ref_v" /></td>
						
						</tbody>

					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						Close</button>
					<button class="btn btn-success" id="submit-hold">Submit</button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript" src="js/jquery.bxslider.min.js"></script>
	<script type="text/javascript" src="js/jquery.keyboard.min.js"></script>
	<script type="text/javascript" src="js/bootbox.js"></script>
	<script type="text/javascript">
var KB = 1;
var DTIME = 1;
var count = 1;
var total = 0;
var an = 1;
var rt = 1;
var ids = new Array();
var p_page = 0;
var page = 0;
var cat_id = 60;
var sproduct_name;
var slast;
var total_cp = 0;

//$('#opModal').bind().on('click','a',function(){var pg=$.url($(this).attr("href")).param("per_page");
</script>
</body>
</html>
