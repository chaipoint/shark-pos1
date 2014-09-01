var $billingItems = new Object();
var $totalBillItems = 0;
var $totalTaxAmount = 0.0;
var $totalBillCostAfterTaxAD = 0.0;
var $totalAmountWOT = 0.0;
var $totalAmountWT = 0.0;
var $totalBillQty = 0;
var $intDiscount = 0;
var $totalDiscountAmount = 0.0;
var order = 0;
var loadedBill = null;
var modifyBill = false;
$(document).ready(function(){
	/*
	*	PLEASE Don't Change This code Block Without Prior Permission
	*/
	if(Object.keys($billingItems).length > 0){
		/*Load Bill IF provided with doc_id*/
		$('#botbuttons').append('<button class="btn btn-primary">Print</button>');
		$('#payment').prop('disabled',true).addClass('hide');

		modifyBill = true;
		generateSalesTable();
		$(".del_row").removeClass('del_row');
		$('.bill_qty_input').prop('readonly',true).removeClass('bill_qty_input');
		$('.category-selection').removeClass('category-selection');
		$('.category-product').removeClass('category-product');
		$('#add_discount').attr('id','');

	}

	var url = $.url(window.location);
	order = url.param('order');
	if(order && order > 0 && ! isNaN(order)){
		/*
		*	PLEASE Don't Change This code Block Without Prior Permission
		*/
		loadedBill = new Object();
		var orderData = $.parseJSON(localStorage.getItem(order));
		loadedBill = orderData;
		//console.log(loadedBill);
		if(orderData){
			localStorage.removeItem(order);			
			var productNewList = new Object();
			$.each(productArray, function(index, data){
				var category = index;
				$.each(data, function(pIndex, pData){
					productNewList[pData.mysql_id]  = new Object();
					productNewList[pData.mysql_id].cat = category;
					productNewList[pData.mysql_id].seq = pIndex;
				});
			});


			$.each(orderData.products, function (index, data){
				var productData = productArray[productNewList[data['id']].cat] [productNewList[data['id']].seq];
				$('#proajax button[category-product-sequence="'+productNewList[data['id']].seq+'"]').addClass('active-btn');
				generateSalesTable(data['id'], parseInt(data['qty']), productData);
			});
		}
	}
	//console.log();
	//---START--- Initial Configurations on Load Of Page


	//---START--- Categories Silder Starts
	/*On Load of DOM Manages Categores Silder*/
		$('.btn-category').bxSlider({
			minSlides:5,
			maxSlides:5,
			slideWidth:600,
			slideMargin:0,
			ticker:false,
			infiniteLoop:false,
			hideControlOnEnd:true,
			mode:'horizontal'
		});
	//---END---Categories Silder Ends

	

	//---END--- Initial Configurations on Load Of Page

	//---START--- Functions Work After Page Load via Events
		//---START--- Works on Change of Category i.e Change in Products according to Category	
		$(".category-selection").click(function(){
			if(!$(this).hasClass("btn-primary")){
				$(".category-selection").removeClass('btn-primary');
				$(this).addClass('btn-primary');
				//console.log(productArray[$(this).data('category')]);
				$buttonList = "";
				selectedCat = $(this).data('category');
				$.each(productArray[selectedCat],function(key,value){
					$buttonList += '<button type="button" class="btn btn-success btn-lg btn3d btn25 category-product '+(value.mysql_id in $billingItems ? 'active-btn' : '')+'" value="'+value.mysql_id+'" category-product-sequence="'+key+'">'+value.name+'</button>';
				});
				$("#proajax").html($buttonList);
			}
		});
		//---END--- Category SElection Ends


		$("#saletbl").on("click",".del_row",function(){
			var tableTR = $(this).closest('tr');
			var pID = tableTR.attr('billing-product');
			delete $billingItems[pID];
			generateSalesTable();
		});

		$("#saletbl")
		.on("change",".bill_qty_input",function(event){
			var newQty = parseInt($(this).val());
			newQty = isNaN(newQty) ? 0 : newQty;
			var pID = $(this).closest('tr').attr('billing-product');

			if(newQty != 0){
				generateSalesTable(pID, newQty);
			}
			event.preventDefault();
		});

		//---END--- Event For Product Selection
		//onCancel Of Bill
		$("#cancel").click(function(){
			bootbox.dialog({
				message:'<div class="form-group"><textarea placeholder="reason" name="cancel_reason_bill" id="cancel_reason_bill" class="form-control"></textarea></div>',
				title:"Bill Cancel Reason",
				buttons:{
					main:{
						label:"Cancel Bill",
						className:"btn-success btn-sm",
						callback:function(){
								var textArea = $('#cancel_reason_bill').attr('type','text');
								var reason = $.trim(textArea.val()); 
								if( reason == '' ){
									textArea.closest('.form-group').addClass('has-error');
									return false;
								}			
								if(modifyBill){
									if(doc){
										$.ajax({
											type: 'POST',
											url: "index.php?dispatch=billing.save",
									  		data : {request_type:'update_bill', doc:doc, cancel_reason:reason},
										}).done(function(response) {
											if(response.error){
												bootbox.alert(response.message);
											}else{
												bootbox.alert('Bill Cancelled Successfully',function(){
													window.location = "?dispatch=sales_register";													
												});
											}
										});
									}
								}
							}
						},
					danger:{
						label:"Cancel",
						className:"btn-danger btn-sm"
					},	
				}
			});
			resetBill(true);
		});
		//---START--- Payment Event After Products selection  or Without Product Selection
		$(".payment-type-bt").click(function(){
			var type = $(this).data('value');
			$("#paid_by").val(type);
			$('.payment-type-bt').removeClass('btn-success').addClass('btn-primary');
			$(this).removeClass('btn-primary').addClass('btn-success');

			if(type == 'ppc'){ 
				$(".ppc").show();
				$('#balance').closest('tr').hide();
				bootbox.alert('Please Swipe The Card', function() { 
				setTimeout('setFocus("ppc")',200);	
				});
			}else{
				$('#balance').closest('tr').show();
				$(".ppc").hide();
				$('#ac_balance').closest('tr').hide();
				$('#ppc').val('');

			}
		});
		/* START -- Payment Using PPC NO  */
        
        $('#ppc').on('keyup', function() {
           var ppc_no = $(this).val();

          if(ppc_no!='' && ppc_no.length==16) {
              if(navigator.onLine === true) {
                $("span#loading_image").removeClass('hide');
                $.ajax({
                  type: 'POST',
                  url:  'index.php?dispatch=billing.getBalanceInq',
                  data:  {'ppc_no':ppc_no},
               }).done(function(response){
               	  console.log(response);
               	  $("span#loading_image").addClass('hide');
               	  //alert(response);
               	  result = $.parseJSON(response.trim());
               	 if(result.error){
				 	$('.ppc_balance').hide();
					bootbox.alert(result.message);
				}else{
					$('.ppc_balance').show();
					$('#ac_balance').text(result.data['balance']);
				}
              });
              }else {
               bootbox.alert("Sorry,No Internet Available");
               return false;
               }
          }else {
          	   bootbox.alert("Please Enter Valid Card No");;
			   return false;
			} 
        });

		/* END -- Payment Using PPC NO  */

		$("#payment").click(function(){
			//$("#fcount").text($totalBillQty);
			$("#twt").text(Math.ceil($totalAmountWT.toFixed(2)));
			if($totalBillQty == 0){
				bootbox.alert('Please add product to sale first');
			}else{
				//$("div.ui-keyboard").remove();
				$("#balance").text(0);
				$('#payModal').modal();
				$("#paid_by").val('');
				$(".payment-type-bt").removeClass('btn-success').addClass('btn-primary');
				$("#paid-amount").prop('autofocus',true);
				$("#is_cod").val('N');

				$("#delivery_channel").val(63);
				$("#delivery_channel_name").val(config_data.delivery_channel[63]);
				$("#booking_channel").val(53);
				$("#booking_channel_name").val(config_data.channel[53]);
				$("#is_cod").val('N');
				$("#is_prepaid").val('Y');
				$("#is_credit").val('N');
				$("#bill_status_id").val(68);
				$("#bill_status").val(config_data.bill_status[68]);
				if(loadedBill){ 
					$('.payment-type-bt[data-value="'+loadedBill.payment_method+'"]').trigger('click');
					$("#paid-amount").val(Math.ceil($totalAmountWT.toFixed(2)));
					$("#delivery_channel").val(62);
					$("#delivery_channel_name").val(config_data.delivery_channel[62]);
					$("#booking_channel").val(loadedBill.channel_id);
					$("#booking_channel_name").val(loadedBill.channel_name);

					$("#billing_customer").val(loadedBill.name).prop('readonly',true);
					$("#phone_number").val(loadedBill.phone).prop('readonly',true);
					$("#billing_customer_city").val(loadedBill.city);
					$("span#city").closest('tr').removeClass('hide');
					$("span#city").text(loadedBill.city);
					$("#billing_customer_locality").val(loadedBill.locality);
					$("span#locality").closest('tr').removeClass('hide');
					$("span#locality").text(loadedBill.locality);
					$("#billing_customer_sub_locality").val(loadedBill.sublocality);
					$("span#sublocality").closest('tr').removeClass('hide');
                    $("span#sublocality").text(loadedBill.sublocality);
					$("#billing_customer_landmark").val(loadedBill.landmark);
					$("#billing_customer_company_name").val(loadedBill.company);
					$("span#company").closest('tr').removeClass('hide');
					$("span#company").text(loadedBill.company);
					$("span#landmark").closest('tr').removeClass('hide');
					$("span#landmark").text(loadedBill.landmark);					
					$("#is_cod").val('Y');
					$("#is_prepaid").val('N');
					$("#is_credit").val('N');
					$("#bill_status").val(config_data.bill_status[68]);
					$("#bill_status_id").val(68);
			}

			}
		});
		$(".close-model").click(function(){
			$("div.ui-keyboard").hide();
		});
		//---END--- Payment Event After Products selection  or Without Product Selection

		//---START--- SUbmit Payment Bill
		$("#submit-sale").click(function(event){
			event.preventDefault();

			$("#phone_number").on('keypress',function (e){ 
     			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        		return false;
    		}
    		});	
			var phoneno = /^\d{10}$/;
			if(!$('#phone_number').val().match(phoneno)){
				bootbox.alert('Please Enter Valid Phone No', function() { 
				setTimeout('setFocus("phone_number")',100);
			});
        		return false;
			}
			if(!$('#paid-amount').val()){
				bootbox.alert("Please Enter Payment Amount");
				return false;
			}
			if(parseInt($('#paid-amount').val()) < Math.ceil($totalAmountWT)){
				console.log(Math.ceil($totalAmountWT));
				bootbox.alert("Paid Amount is Less");
				return false;
			}
			if(!$("#paid_by").val()){
				bootbox.alert("Please Select Paid By");
				return false;				
			}
			if($("#paid_by").val() == 'ppc' ){
             bootbox.alert('Please Select Valid Payment Method');
             return false;
			}
			var billDetails = new Object();
			billDetails.items = new Object();
			billDetails.items = $billingItems;

			billDetails.total_qty = $totalBillQty;
			billDetails.sub_total = $totalAmountWOT.toFixed(2);
			billDetails.total_tax = ($totalTaxAmount).toFixed(2);
			billDetails.discount = $intDiscount;
			billDetails.total_discount = $totalDiscountAmount;
			billDetails.total_amount = $totalAmountWT;
			billDetails.round_off = Math.ceil(billDetails.total_amount) - billDetails.total_amount;
			billDetails.due_amount = billDetails.total_amount + billDetails.round_off;
			billDetails.paid_amount = $('#paid-amount').val();
			

			billDetails.payment_type = $("#paid_by").val();

			billDetails.is_cod = $("#is_cod").val();
			billDetails.is_prepaid = $("#is_prepaid").val();
			
			billDetails.is_credit = $("#is_credit").val();
			billDetails.bill_status = $("#bill_status").val();
			billDetails.bill_status_id = $("#bill_status_id").val();

			billDetails.booking_channel_id = $("#booking_channel").val();
			billDetails.booking_channel_name = $("#booking_channel_name").val();
			billDetails.delivery_channel_id = $("#delivery_channel").val();
			billDetails.delivery_channel_name = $("#delivery_channel_name").val();
			billDetails.order_no = (loadedBill && loadedBill.order_id) ? loadedBill.order_id : 0;

			billDetails.customer = new Object();
			billDetails.customer.name = $("#billing_customer").val();
			billDetails.customer.city = $("#billing_customer_city").val();
			billDetails.customer.locality = $("#billing_customer_locality").val();
			billDetails.customer.sub_locality = $("#billing_customer_sub_locality").val();
			billDetails.customer.land_mark = $("#billing_customer_landmark").val();
			billDetails.customer.phone_no = $("#phone_number").val();
			billDetails.customer.company_name = $("#billing_customer_company_name").val();

			billDetails.card = new Object();
			billDetails.card.no = '';
			billDetails.card.type = '';
			billDetails.card.company = '';
			billDetails.card.redeem_amount = '';
			billDetails.card.txn_no = '';
			billDetails.card.balance = '';
			billDetails.reprint = 1;


			billDetails.request_type = 'save_bill';


			$("div.ui-keyboard").hide();

			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=billing.save",
		  		data : billDetails,
			}).done(function(response) {
				console.log(response);
				result = $.parseJSON(response);
				if(result.error){
					bootbox.alert(result.message);
				}else{
					$('#payModal').modal('hide');
					bootbox.alert('Bill Successfully Saved');
					//<a class="label label-primary print-bill-today" href="billprint.php?bill_no='+result.data.bill_no+'" target="_blank">Print</a>
					resetBill(true);	
					$intDiscount = 0;
				}
			});
		});
		//---END--- SUbmit Payment Bill

		

		//---START-TAX POPUP --//
		$("#add_tax").click(function(){

			console.log($billingItems);
			$viewData = '<table class="table table-striped table-condensed table-hover protable small" width="100%" border="0" cellspacing="0" cellpadding="0">'+
							'<thead>'+
								'<tr class="active">'+
									'<th>Product Name</th><th>Menu Price</th><th>Price Before Tax</th><th>Discount Amount</th><th>Taxable Amount</th><th>Qty</th><th>SubTotal</th><th>Tax %</th><th>Tax</th><th>Net Amount</th>'+
								'</tr>'+
							'</thead>'+
						'<tbody>';
						var qty = 0;
						var netAmount = 0.0;
						var taxAmount = 0.0;
						var discountAmount = 0.0;
						var subTotalSum = 0.0;
			$.each($billingItems, function(index,data){
				$viewData += '<tr class="text-right">'+
								'<td class="text-left">'+data.name+'</td>'+
								'<td>'+(parseFloat(data.price)).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.priceBT)).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.discountAmount)).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.taxAbleAmount)).toFixed(2)+'</td>'+
								'<td>'+data.qty+'</td>'+
								'<td>'+(parseFloat(data.taxAbleAmount) * data.qty).toFixed(2)+'</td>'+
								'<td>'+(((data.tax) ? data.tax : 0) * 100).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.taxAmount) * data.qty).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.netAmount)).toFixed(2)+'</td>'+
							'</tr>';
							qty += data.qty;
							netAmount += data.netAmount;
							taxAmount += (data.taxAmount  * data.qty);
							discountAmount += (data.discountAmount * data.qty);
							subTotalSum += data.taxAbleAmount * data.qty;
			});
			$viewData += '</tbody>'+
						'<tfoot><tr class="active"><th>Total</th><th></th><th></th><th class="text-right">'+(discountAmount).toFixed(2)+'</th><th></th><th class="text-right">'+qty+'</th><th class="text-right">'+subTotalSum.toFixed(2)+'</th><th></th><th class="text-right">'+(taxAmount).toFixed(2)+'</th><th class="text-right">'+(netAmount).toFixed(2)+'</th></tr></tfoot>'
						+'</table>';

			bootbox.dialog({
				message:$viewData,
				title:"Product Pricing Details",
				className: "bootbox-dialog-modal",
				buttons:{
					main:{
						label:"Close",
						className:"btn-primary btn-sm",
					}
			}

			});
		});
/*			$("#add_tax").click(function(){
				var tval=$('#tax_val').val(); 
				bootbox.dialog({
					message:"<input type='text' class='form-control input-sm' id='get_ts' onClick='this.select();' value='"+tval+"'></input>",
					title:"Tax Rate (5 or 5%)",
					buttons:{
						main:{
							label:"Update",
							className:"btn-primary btn-sm",
							callback:function(){
								var ts=$('#get_ts').val();
								if(ts.length!=0){
									$('#tax_val').val(ts);
									if(ts.indexOf("%")!==-1){
										var pts=ts.split("%");
										if(!isNaN(pts[0])){
											var tax=(total*parseFloat(pts[0]))/100;
											var g_total=(total+tax)-parseFloat($('#ds_con').text());
											grand_total=parseFloat(g_total).toFixed(2);
											$("#ts_con").text(tax.toFixed(2));
											$("#total-payable").text(grand_total)
										}else{
											$('#get_ts').val('0');
											$('#tax_val').val('0');
											var g_total=(total)-parseFloat($('#ds_con').text());
											grand_total=parseFloat(g_total).toFixed(2);
											$("#ts_con").text('0');
											$("#total-payable").text(grand_total)
										}
									}else{
										if(!isNaN(ts)&&ts!=0){
											var g_total=(total+parseFloat(ts))-parseFloat($('#ds_con').text());
											grand_total=parseFloat(g_total).toFixed(2);
											$("#ts_con").text(parseFloat(ts).toFixed(2));
											$("#total-payable").text(grand_total)
										}else{
											$('#get_ts').val('0');
											$('#tax_val').val('0');
											var g_total=(total)-parseFloat($('#ds_con').text());
											grand_total=parseFloat(g_total).toFixed(2);
											$("#ts_con").text('0');
											$("#total-payable").text(grand_total)
										}
									}
								}
							}
						}
					}
				});
				return false
			});/**/
		//---END TAX POPUP--//

	//---END--- Functions Work After Page Load via Events

	//KEYBORD TO ENTER PAYMENT
			$('#paid-amount').keyboard({
				restrictInput:true,
				preventPaste:true,
				autoAccept:false,
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
				usePreview:true,
				customLayout:{
					'default':['1 2 3 {clear}','4 5 6 .','7 8 9 0','{accept} {cancel}']
				},
				beforeClose:function(e,keyboard,el,accepted){
					if(accepted){
						var paid=parseFloat(el.value);
						if(paid < Math.ceil($totalAmountWT)){
							console.log(Math.ceil($totalAmountWT));
							bootbox.alert('Paid amount is less than payable amount');
							$("#balance").text('')
							return false;
						}else{
							var balance = paid - Math.ceil($totalAmountWT);
							$("#balance").text( isNaN(balance) ? 0 : balance );
						}
					}
				}
			});



	$("#add_discount").click(function(){
		var dval=$('#discount_val').val(); 
		bootbox.dialog({
			message:"<input type='text' class='form-control input-sm' id='get_ds' onClick='this.select();' value='"+(($intDiscount == 0) ? '' : $intDiscount)+"'></input>",
			title:"Discount (%)",
			buttons:{
				main:{
					label:"Update",
					className:"btn-primary btn-sm",
					callback:function(){
						if(parseInt($('#get_ds').val())>100){

						}else{
							var dis = parseInt($('#get_ds').val());
							$intDiscount = isNaN(dis) ? $intDiscount : dis;
							generateSalesTable();
						}
						$("div.ui-keyboard").hide();
					}
				}
			}
		});

$('#get_ds').keyboard({
		layout:'custom',
		customLayout:{
					'default':['0 1 2 3 4','5 6 7 8 9','{clear} {bksp} {accept} {cancel}']
				},
		beforeClose:function(e,keyboard,el,accepted){
			if(accepted){

			//	console.log($('input',".ui-keyboard").val());
			//	console.log(keyboard.$el[0].value)				
			}
/*			console.log(e);
			console.log(keyboard);
			console.log(el);
			console.log(accepted);/**/
		}
	});


		return false
	});
			//---START--- Event For Product Selection
		$("#proajax").on("click",".category-product",function(){
			$(this).addClass('active-btn');
				var selectedSequence = $(this).attr('category-product-sequence');
				var productData = productArray[selectedCat][selectedSequence];
				var productID = productData.mysql_id;
				var newQty = (productID in $billingItems) ? ($billingItems[productID].qty + 1) : 1;
				generateSalesTable(productID, newQty, productData);
		});	


});
function generateSalesTable(productId, qty, productData){
	resetBill(false);
	var productID = productId;
	if(productId && productId > 0){
		var  newqty = isNaN(parseInt(qty)) ? 0 : qty;
		if(! (productID in $billingItems)){
			$billingItems[productID] = new Object();
			$billingItems[productID].name = productData.name;
			$billingItems[productID].category_id = selectedCat;
			$billingItems[productID].category_name = catArray[selectedCat];
			$billingItems[productID].id = productID;
			$billingItems[productID].qty = newqty;
			$billingItems[productID].price = productData.price;
			$billingItems[productID].tax = productData.tax.rate;
			$billingItems[productID].priceBT = (productData.tax.rate) ? (productData.price / ( 1 + parseFloat(productData.tax.rate) )) : productData.price;
			$billingItems[productID].discount = $intDiscount;
			$billingItems[productID].discountAmount = $billingItems[productID].priceBT * $billingItems[productID].discount/100;
			$billingItems[productID].taxAbleAmount = $billingItems[productID].priceBT - $billingItems[productID].discountAmount;
			$billingItems[productID].taxAmount = $billingItems[productID].taxAbleAmount * $billingItems[productID].tax;
			$billingItems[productID].totalAmount = $billingItems[productID].taxAbleAmount + $billingItems[productID].taxAmount;
			$billingItems[productID].netAmount = $billingItems[productID].qty * $billingItems[productID].totalAmount;
		}else{
			$billingItems[productID].qty = newqty;
			$billingItems[productID].netAmount = $billingItems[productID].qty * $billingItems[productID].totalAmount;
		}
	}
	

	var tableRows = '';
	for(var index in $billingItems){
		$billingItems[index].discount = $intDiscount;
		$billingItems[index].discountAmount = $billingItems[index].priceBT * $billingItems[index].discount/100;
		$billingItems[index].taxAbleAmount = $billingItems[index].priceBT - $billingItems[index].discountAmount;
		$billingItems[index].taxAmount = $billingItems[index].taxAbleAmount * $billingItems[index].tax;
		$billingItems[index].totalAmount = $billingItems[index].taxAbleAmount + $billingItems[index].taxAmount;
		$billingItems[index].netAmount = $billingItems[index].qty * $billingItems[index].totalAmount;
		tableRows +='<tr billing-product="'+index+'">'+
			'<td style="width:9%"><span class="glyphicon glyphicon-remove-sign del_row"></span></td>'+
			'<td style="width:53%" class="btn-warning">'+$billingItems[index].name+'&nbsp;@&nbsp;'+$billingItems[index].price+'</td>'+
			'<td style="width:12%"><span class="bill_item_qty"><input type="text" class="keyboard nkb-input bill_qty_input" value="'+$billingItems[index].qty+'"/></span></td>'+
			'<td style="width:26%" class="text-right"><span class="bill_item_price text-right">'+($billingItems[index].qty * $billingItems[index].taxAbleAmount).toFixed(2)+'</span></td>'+
			'</tr>';

		$totalBillQty += parseInt($billingItems[index].qty);
		$totalAmountWOT += ($billingItems[index].qty * $billingItems[index].taxAbleAmount);
		$totalAmountWT += $billingItems[index].netAmount;

		$totalTaxAmount += ( $billingItems[index].qty * $billingItems[index].taxAmount );
		$totalDiscountAmount += ( $billingItems[index].qty * $billingItems[index].discountAmount );

	}
	$('#saletbl tbody').html(tableRows);
	$('.bill_qty_input').keyboard({
		layout:'custom',
		customLayout:{
					'default':['0 1 2 3 4','5 6 7 8 9','{clear} {bksp} {accept} {cancel}']
				},
		beforeClose:function(e,keyboard,el,accepted){
			if(accepted){

			//	console.log($('input',".ui-keyboard").val());
			//	console.log(keyboard.$el[0].value)				
			}
/*			console.log(e);
			console.log(keyboard);
			console.log(el);
			console.log(accepted);/**/
		}
	});

	$("#count").text($totalBillQty);	
	$("#total").text($totalAmountWOT.toFixed(2));
	$("#ts_con").text($totalTaxAmount.toFixed(2));
	$("#total-payable").text($totalAmountWT.toFixed(2));	
	$("#ds_con").text(($totalDiscountAmount).toFixed(2));
}
function resetBill(refresh){
	if(refresh){
		$billingItems = new Object();
		delete $billingItems;
		$('button',proajax).removeClass('active-btn');
	}
	$totalBillItems = 0;
	$totalBillQty = 0;
	$totalAmountWOT = 0.0;
	$totalAmountWT = 0.0;
	$totalTaxAmount = 0.0;
	$totalDiscountAmount = 0.0;
	//$intDiscount = 0;
	$("#count").text($totalBillQty);		
	$("#total").text($totalAmountWOT);
	$("#total-payable").text($totalAmountWT);	
	$("#ts_con").text($totalTaxAmount);
	$("#saletbl tbody").html("");	
	$("#ds_con").text($totalDiscountAmount);
	$("#paid-amount").val('');
}

var setFocus = function(id) { 
  $('input[id='+id+']').focus();
}