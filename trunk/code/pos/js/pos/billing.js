var $billingItems = new Object();
var $totalBillItems = 0;
var $totalTaxAmount = 0.0;
var $totalVatTaxAmount = 0.0;
var $totalServiceTaxAmount = 0.0;
var $totalBillCostAfterTaxAD = 0.0;
var $totalAmountWOT = 0.0;
var $totalAmountWT = 0.0;
var $totalBillQty = 0;
var $intDiscount = 0;
var serviceTax = 0;
var $totalDiscountAmount = 0.0;
var order = 0;
var loadedBill = null;
var cawBill = null;
var modifyBill = false;
var popupKeyboard = null;

$(document).ready(function(){  
	var url = $.url();
	$(this).attr("title", "Shark |ChaiPoint POS| Billing");
	
	$('.datepicker').datepicker({
		startDate: '-3d',
    	endDate: '+1d',
		dateFormat: 'yyyy-mm-dd'
	}).datepicker('setDate',new Date());
	console.log(new Date());

	/*
	*	PLEASE Don't Change This code Block Without Prior Permission
	*/
	if(Object.keys($billingItems).length > 0){ 

		/*Load Bill IF provided with doc_id*/
		var button = (bill_status_id != 80) ? '<button id="paid_button" class="btn btn-primary">Paid</button>' : '' ;
		$('#botbuttons').append(button);
		$('#payment').prop('disabled',true).addClass('hide');
		for (var keys in $billingItems) {
			$intDiscount = $billingItems[keys].discount;
			break;
		};
		modifyBill = true;
		generateSalesTable();
		$(".del_row").removeClass('del_row');
		$('.bill_qty_input').prop('readonly',true).removeClass('bill_qty_input');
		$('.category-selection').removeClass('category-selection');
		$('.category-product').removeClass('category-product');
		$('#add_discount').attr('id','');
		$("#discount_input_box").val($intDiscount).prop('disabled',true);
		$("#discount-close").attr('id','');


	}
	
	/* Function To Add Discount */
	$('#apply_discount').click(function(){ 
		discount('add');
	});
	
	/* Function To Remove Discount */
	$('#remove_discount').click(function(){
		discount('remove');
	});
	
	var url = $.url(window.location);
	order = url.param('order');
	if(order && order > 0 && ! isNaN(order)){ 
		/*
		*	PLEASE Don't Change This code Block Without Prior Permission
		*/
		loadedBill = new Object();
		var orderData = $.parseJSON(localStorage.getItem(order));
		loadedBill = orderData;
		//alert(JSON.stringify(loadedBill));
		if(orderData){
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
				if(data['id'] in productArray){
					var productData = productArray[productNewList[data['id']].cat] [productNewList[data['id']].seq];
					productData['price'] = data['cost'];
					//productData['service_tax'] = 0.0249;
					selectedCat = productData['category']['id'];
					$('#proajax button[value="'+data['id']+'"]').addClass('active-btn');
					generateSalesTable(data['id'], parseInt(data['qty']), productData);
				}
			});

			$(".del_row").removeClass('del_row');
			$('.bill_qty_input').prop('readonly',true).removeClass('bill_qty_input');
			$('.category-selection').removeClass('category-selection');
			$('.category-product').removeClass('category-product');
			//alert(orderData['coupon_code']);
			if(orderData['coupon_code']!=''){ 
				$('#discount_input_box').val(orderData['coupon_code']);
				$('#apply_discount').trigger('click');
			}
		}
	}
	
	
	var url = $.url(window.location);
	cawOrder = url.param('cawOrder');
	if(cawOrder && cawOrder > 0 && ! isNaN(cawOrder)){ 
		
		$.ajax({
			type: 'POST',
			url: "index.php?dispatch=billing.getCawProduct",
			data : {request_type:'getCawProduct', 'customer_id':cawOrder},
		}).done(function(response) {   
			console.log(response);
			var $result = $.parseJSON(response);
			if($result.error){ 
				bootbox.alert($result.message, function(){
					window.location.href = '?dispatch=caw.index';
				});
				//return false;
				
			}
			cawBill = new Object();
			cawBill = $result['data'];
		});
	}

	$('#reward_redemption').click(function(event){
		event.preventDefault();
		$('#redemption_code').removeClass('hide').focus();
	});

	
	$('#redemption_code').on('change', function(){
		var code = $(this).val();
		var markused = 'false';
		var request_type = 'reward_check';
		$("#ajaxfadediv").addClass('ajaxfadeclass');
		$.ajax({
			type: 'POST',
			url: "index.php?dispatch=billing.loadCard",
			data: {'request_type':request_type, 'code':code, 'markused':markused}
		}).done(function(response) { 
			console.log(response);
			$("#ajaxfadediv").removeClass('ajaxfadeclass');  
			$result = $.parseJSON($.trim(response));
			if($result.error){
				$('#redemption_code').val('').addClass('hide');
				bootbox.alert($result.message);
			}else if($result.data['success']=='False'){
				$('#redemption_code').val('').addClass('hide');
				bootbox.alert($result.data['message']);
			}else if($result.data['success']=='True'){ 
				if($result.data['product_id']=='105,345'){
					var product_id = 105;
				}else{
					var product_id = $result.data['product_id'];
				}
				var product = ertArray[product_id];
				if(product!=undefined){
					var productNewList = new Object();
					$.each(productArray, function(index, data){
						var category = index;
						$.each(data, function(pIndex, pData){
							productNewList[pData.mysql_id]  = new Object();
							productNewList[pData.mysql_id].cat = category;
							productNewList[pData.mysql_id].seq = pIndex;
						});
					});
				
					var productData = productArray[productNewList[product].cat] [productNewList[product].seq];
					$('#discount_input_box').val('').prop('disabled',true);
					$intDiscount =  100;
					$('#reward_redemption_code').val(code);
					$('#card_invoice_no').val($result.data['invoice_number']);
					$('#claim_reward').removeClass('hide');
					$('#payment').addClass('hide');
					generateSalesTable(product, '1', productData);
					$('.bill_qty_input').prop('readonly', true);
					$('.del_row').addClass('hide');
					$('.category-product').prop('disabled', true);
					$('#proajax button[value="'+$result.data['product_id']+'"]').addClass('active-btn').prop('disabled', false);
					$('.category-selection').prop('disabled', true);
				}else{
					bootbox.alert('Product Not Found in List');
				}
			} else{
				bootbox.alert('Server Error! Please Contact Admin');
			}
					
		});

	});

	$('#claim_reward').click(function(){
		var code = $('#reward_redemption_code').val();
		var markused = 'true';
		var request_type = 'reward_redemption';
		$.ajax({
			type: 'POST',
			url: "index.php?dispatch=billing.loadCard",
			data: {'request_type':request_type, 'code':code, 'markused':markused}
		}).done(function(response) { 
			console.log(response);

			$result = $.parseJSON($.trim(response));
			if($result.error){
				$('#redemption_code').val('').addClass('hide');
				bootbox.alert($result.message);
			}else if($result.data['success']=='False'){
				$('#redemption_code').val('').addClass('hide');
				bootbox.alert($result.data['message']);
			}else if($result.data['success']=='True'){ 
				$('#paid_by').val('ppa');
				$("#phone_number").val('');
				$("#delivery_channel").val(74);
				$("#delivery_channel_name").val(config_data.delivery_channel[74]);
				$("#booking_channel").val(53);
				$("#booking_channel_name").val(config_data.channel[53]);
				$("#is_cod").val('N');
				$("#is_prepaid").val('Y');
				$("#is_credit").val('N');
				$("#bill_status_id").val(80);
				$("#bill_status").val(config_data.bill_status[80]);
				var amountoBePaid = Math.ceil($totalAmountWT.toFixed(0));
				if(amountoBePaid%50 != 0){
					var divder = Math.floor(amountoBePaid/50);
					amountoBePaid = 50 * (divder + 1);
				}
				$("#paid-amount").val(amountoBePaid);
				$('#card_type').val('ppa');
				$('#card_company').val('urbanPiper');
				$('#card_txn_type').val($result.data['txn_type']);
				$('#card_invoice_no').val($result.data['invoice_number']);
				$('#submit-sale').trigger('click');
			}else{
				bootbox.alert('Server Error! Please Contact Admin');
			} 
					
		});
		
	});
	
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
				$buttonList = "";
				selectedCat = $(this).data('category');
				if(selectedCat == 100){
					$("#proajax").html('');
					bootbox.dialog({
						message:'<div class="form-group"><input type="text" name="customer_name" id="customer_name" class="autocomplete form-control" action="index.php?dispatch=customer.retail_customer" strict="true" target="customer_id"/></div>',
						title:"Select Customer",
						buttons:{
							main:{
								label:"Go",
								className:"btn-success btn-sm",
								callback:function(){
									var customer_name = $('input[name="customer_id"]');
									var customer_id = $.trim(customer_name.val()); 
									if(customer_id == '' ){
										customer_name.closest('.form-group').addClass('has-error');
										return false;
									}
									$.ajax({
										type: 'POST',
										url: "index.php?dispatch=billing.getCawProduct",
										data : {request_type:'getCawProduct', 'customer_id':customer_id},
									}).done(function(response) {  
										console.log(response);
										var $result = $.parseJSON(response);
										if($result.error){ 
											bootbox.alert($result.message);
											return false;
										}
										productArray = $.extend(true, productArray ,$.parseJSON($result.data));
										console.log(productArray);
										$.each(productArray[selectedCat],function(key,value){
											$buttonList += '<button type="button" class="btn btn-success btn-lg btn3d btn25 category-product '+(value.mysql_id in $billingItems ? 'active-btn' : '')+'" value="'+value.mysql_id+'" category-product-sequence="'+key+'">'+value.name+'</button>';
										});
										$("#proajax").html($buttonList);
									});
								}
							},
							danger:{
								label:"Cancel",
								className:"btn-danger btn-sm"
							},	

						}
					});
					
				}else{
					$.each(productArray[selectedCat],function(key,value){
						$buttonList += '<button type="button" class="btn btn-success btn-lg btn3d btn25 category-product '+(value.mysql_id in $billingItems ? 'active-btn' : '')+'" value="'+value.mysql_id+'" category-product-sequence="'+key+'">'+value.name+'</button>';
					});
					$("#proajax").html($buttonList);
				}
				
			}
		});
		//---END--- Category SElection Ends


		$("#saletbl").on("click",".del_row",function(){ 
			var tableTR = $(this).closest('tr');
			var pID = tableTR.attr('billing-product');
			$('.category-product').each(function(){
				if($(this).prop('value')==pID){
					$(this).removeClass('active-btn');
					return false;
				}
			});
			delete $billingItems[pID];
			generateSalesTable();
			if($('#discount_input_box').val()){
				$('#apply_discount').trigger('click');
			}
		});

		$("#saletbl").on("change",".bill_qty_input",function(event){ 
			var newQty = parseInt($(this).val());
			newQty = isNaN(newQty) ? 0 : newQty;
			var pID = $(this).closest('tr').attr('billing-product');
			generateSalesTable(pID, newQty);
			if($('#discount_input_box').val()){
				$('#apply_discount').trigger('click');
			}
			event.preventDefault();
		});

		//---END--- Event For Product Selection
		
		// ON Cancel Of Bill
		$("#cancel").click(function(){
			//alert($(this).text());
			if(modifyBill){
				bootbox.dialog({
					message:'<div class="form-group"><textarea name="cancel_reason_bill" id="cancel_reason_bill" class="form-control"></textarea></div>',
					title:"Bill Cancel Reason",
					buttons:{
						main:{
							label:"Yes, Cancel",
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
										  		data : {request_type:'update_bill', doc:doc, cancel_reason:reason, bill_status_id: 79,bill_status_name:config_data.bill_status[79], due_amount:due_amount},
											}).done(function(response) {
												response = $.parseJSON($.trim(response));
												if(response.error){
													bootbox.alert(response.message);
												}else{
													bootbox.alert(response.message,function(){
														//window.location = "?dispatch="+url.param('referer');
														window.location = "?dispatch=home.report";	
													});
												}
											});
										}
									}
								}
							},
						danger:{
							label:"No, Don't Cancel",
							className:"btn-danger btn-sm",
							callback:function(){
								//window.location = '?dispatch='+url.param('referer');
								window.location = "?dispatch=home.report";
							}
						},	
					}
				});
				$('#cancel_reason_bill').cKeyboard();
				setTimeout(function(){$('#cancel_reason_bill').focus();},600);
			}else{
			resetBill(true);
			}
		});

		$("#content").on('click','#paid_button',function(){
			$.ajax({
					type: 'POST',
					url: "index.php?dispatch=billing.save",
			  		data : {request_type:'update_bill', doc:doc, bill_status_id: 80,bill_status_name:config_data.bill_status[80]},
				}).done(function(response) {
					response = $.parseJSON(response);
					if(response.error){
						bootbox.alert(response.message);
					}else{
						bootbox.alert(response.message,function(){
							window.location = "?dispatch=sales_register";													
						});
					}
				});
		});

		//---START--- Payment Event After Products selection  or Without Product Selection
		$(".payment-type-bt").click(function(){ 
			var type = $(this).data('value');
			$("#paid_by").val(type);
			$('.payment-type-bt').removeClass('btn-success').addClass('btn-primary');
			$(this).removeClass('btn-primary').addClass('btn-success');
			if(type == 'ppc' || type == 'ppa'){
				$('#submit-sale').prop('disabled', true);
				$('#error_div').addClass('hide');
				$('#load_amount_div').css('display','none');
				$(".ppc").show();
				$("#ppc").val('');
				$('#balance').closest('tr').hide();
				bootbox.alert(type=='ppc' ? 'Please Swipe The Card' : 'Please scan the Bar Code', function() { 
					setTimeout(function(){
						$("#ppc").focus();
					},600);	
				});
			}else{
				$('#submit-sale').prop('disabled', false);
				$('#balance').closest('tr').show();
				$(".ppc").hide();
				$('#ac_balance').closest('tr').hide();
				$('#ppc').val('');

			}
		});
		
		/* START -- Payment Using PPC NO  */
        $('#ppc').on('change', function() {
			$(this).prop('type' , 'password');
        	var card_no = $.trim($(this).val());
           	var payment_type = $('#paid_by').val();
           	var total_amount = $('#twt').text();
           	$('#error_message').text();
          		if(card_no!='') { 
              		if(navigator.onLine === true) {
                		$("span#loading_image").removeClass('hide');
                		$('#submit-sale').attr('disabled',true);
                		$.ajax({
                  			type: 'POST',
                  			url:  (payment_type=='ppc') ? 'index.php?dispatch=billing.ppcBill' : 'index.php?dispatch=billing.ppaBill',
                  			data:  {'card_number':card_no,'amount':total_amount},
							timeout:18000
               			}).done(function(response){  
               	  			console.log(response);
               	  			$("span#loading_image").addClass('hide');
               	  			
               	  			var IS_JSON = true;
								try{
									var result = $.parseJSON($.trim(response));
								}
								catch(err){
									IS_JSON = false;
								}  
               	 			
							if (IS_JSON) {
								if(result.error){
				 					$('.ppc_balance').hide();
									$('#ppc').val('');
									bootbox.alert(result.message);
								}else if(result.data['success']=='False'){
									$('.ppc_balance').hide();
									if(result.data['balance']=='' || result.data['balance']==null){
										bootbox.alert(result.data['message']);
										$('#ppc').val('');
									}else{
										$('#load_amount_div').css('display','none');
										$('#error_message').text('Balance is Insufficient. Do You Want To Load It?');
								    	$('#error_div').removeClass('hide');
									}
								}else if(result.data['success']=='True'){
									$('#submit-sale').attr('disabled',false);
									$('.ppc_balance').show();
									$('#ac_balance').text(result.data['balance']);
									$('#card_number').val(result.data['card_number']);
									$('#card_type').val(payment_type);
									$('#card_company').val(payment_type == 'ppa' ? 'urbanPiper' : 'qwikcilver');
									$('#card_redeem_amount').val(total_amount);
									$('#card_txn_no').val(result.data['txn_no']);
									$('#card_txn_type').val(result.data['txn_type']);
									$('#card_approval_code').val(result.data['approval_code']);
									$('#card_balance').val(result.data['balance']);
									$('#is_prepaid').val('Y');
									$('#card_invoice_no').val(result.data['invoice_number']);
									$('#submit-sale').trigger('click');
								}else{
									bootbox.alert('Server Error! Please Contact Admin');
								}
							}else{
								$('.ppc_balance').hide();
								$('#ppc').val('');
								bootbox.alert('Server Error! Please Contact Admin');
							}
              			}).error(function(x, t, m){
								if(t==='timeout'){
									bootbox.alert('Enable to Process.Please Try Another Method');
								}
						});
             		}else{
               			bootbox.alert("Sorry,No Internet Available");
               			return false;
              		}
          		}else{
          	   		bootbox.alert("Please Enter Valid Card No");;
			   		return false;
				} 
        	});

		
		/* function to load card amount  */
		$('.load').on('click', function(event){
			event.preventDefault();
			if($(this).data('value')=='no'){
			$('#error_div').addClass('hide');
			}else if($(this).data('value')=='yes'){
				$('#error_div').addClass('hide');
				$('#load_amount_div').css('display','block');
			}else{
				var card_no = $.trim($('#ppc').val());
           		var payment_type = $('#paid_by').val();
           		var total_amount = $('#load_amount').val();
           		var request_type = (payment_type=='ppa') ? 'load_ppa_card' : 'load_ppc_card';
           		$("span#loading_image").removeClass('hide');
           		$.ajax({
					type: 'POST',
					url: "index.php?dispatch=billing.loadCard",
			  		data : {'request_type':request_type,'amount':total_amount,'card_number':card_no},
				}).done(function(response) { 
					console.log(response);
					var IS_JSON = true;
					try
						{
							var $result = $.parseJSON($.trim(response));
						}
					catch(err)
						{
							IS_JSON = false;
						}  
					//$result = $.parseJSON($.trim(response));
					if(IS_JSON){
					if($result.error){
				 		$('.ppc_balance').hide();
						bootbox.alert($result.message);
					}else if($result.data['success']=='False'){
						$('.ppc_balance').hide();
						$('#load_amount_div').css('display','none');
						bootbox.alert($result.data['message']);
					}else if($result.data['success']=='True'){ 
						//printBill(response);
						$('#load_amount_div').css('display','none');
						$('#ppc').trigger('change');
					} 
					} else{
						$('.ppc_balance').hide();
						$('#load_amount_div').css('display','none');
						bootbox.alert('Server Error! Please Contact Admin');
					}
					
				});
			}
		});

		$("#payment").click(function(){ 
			$("#twt").text(Math.ceil($totalAmountWT.toFixed(0)));
			if($totalBillQty == 0){
				bootbox.alert('Please add product to sale first');
			}else{
				$("#balance").text(0);
				$('#payModal').modal();
				$("#phone_number").val('');
				$("#delivery_channel").val(74);
				$("#delivery_channel_name").val(config_data.delivery_channel[74]);
				$("#booking_channel").val(53);
				$("#booking_channel_name").val(config_data.channel[53]);
				$("#is_cod").val('N');
				$("#is_prepaid").val('N');
				$("#is_credit").val('N');
				$("#bill_status_id").val(80);
				$("#bill_status").val(config_data.bill_status[80]);
				$("#customer_type").prop('disabled',false);
				$("#customer_type option:first").prop('selected',true);
				$("#customer_type").trigger('change');
				$("#customer_type").prop('disabled',false);

				var amountoBePaid = Math.ceil($totalAmountWT.toFixed(0));
				if(amountoBePaid%50 != 0){
					var divder = Math.floor(amountoBePaid/50);
					amountoBePaid = 50 * (divder + 1);
				}
				$("#paid-amount").val(amountoBePaid);
				$("#balance").text(amountoBePaid - ($totalAmountWT.toFixed(0)));
				if(loadedBill){ 
					$("#customer_type").append('<option  value="coc">COC</option>');
					$('.payment-type-bt[data-value="'+loadedBill.payment_method+'"]').trigger('click');
					$("#paid-amount").val(Math.ceil($totalAmountWT.toFixed(2)));
					$("#balance").text(0);
					$("#delivery_channel").val(75);
					$("#delivery_channel_name").val(config_data.delivery_channel[75]);
					$("#booking_channel").val(loadedBill.channel_id);
					$("#booking_channel_name").val(loadedBill.channel_name);
					$("#billing_customer").val(loadedBill.name).css('display','none');
					$('span#customer').removeClass('hide').text(loadedBill.name);
					$("#phone_number").val(loadedBill.phone).css('display','none');
					$('span#phone').removeClass('hide').text(loadedBill.phone);
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
					$("#bill_status").val(config_data.bill_status[77]);
					$("#bill_status_id").val(77);
					$("#customer_type").val('coc');
					$("#customer_type").prop('disabled',true);
			}else if(cawBill){ 
					$("#delivery_channel").val(75);
					$("#delivery_channel_name").val(config_data.delivery_channel[75]);
					$("#booking_channel").val(231);
					$("#booking_channel_name").val(config_data.channel[231]);
					$("#customer_type").append('<option value="caw">CAW</option>');
					$("#customer_type").val('caw');
					$("#customer_type").prop('disabled',true);
					//$("#paid-amount").prop('disabled',true);
					$("#customer_name").val(cawBill.mysql_id);
					$("#customer_name").prop('disabled',true);
					$("#is_cod").val('N');
					$("#is_prepaid").val('N');
					$("#is_credit").val('Y');
					$('.payment-type-bt[data-value="caw"]').trigger('click');
					$('.payment-type-bt').attr('disabled',true);
					$('.payment-type-bt[data-value="caw"]').attr('disabled',false);
					$('#billing_customer').addClass('hide');
					$('#customer_name').removeClass('hide');
					$('#phone_number').val(cawBill.phone);
					$('#billing_customer_address').val(cawBill.billing_address);
					$('#billing_customer_company_name').val(cawBill.name);
					$("span#address").closest('tr').removeClass('hide');
					$("span#address").text(cawBill.billing_address);
					$("span#contact_person").closest('tr').removeClass('hide');
					$("span#contact_person").text(cawBill.name);
					$("#challan_no").closest('tr').removeClass('hide');
					$("#onsite_time").closest('tr').removeClass('hide');
			}
			popupKeyboard = $('#paid-amount, #phone_number, #billing_customer').cKeyboard();

			setTimeout(function(){
				$("#paid-amount").focus();
			},600);

			}

		});
		$("#customer_type").change(function(){
			var cusID = $('input[name="customer_id"]');
			if(cusID){
				cusID.remove();
			}
			$('#customer_name').val('');
			switch($(this).val()){
				case 'walk_in':
					$("#is_cod").val('N');
					$("#is_prepaid").val('N');
					$("#is_credit").val('N');
					$('#phone_number').val('');
					$('#billing_customer_address').val('');
					$('#billing_customer_company_name').val('');
					$("span#address").closest('tr').addClass('hide');
					$("span#contact_person").closest('tr').addClass('hide');
					$('.payment-type-bt[data-value="cash"]').trigger('click');
					$('.payment-type-bt').attr('disabled',false);
					$('#customer_name').addClass('hide');
					$('#billing_customer').removeClass('hide');
					break;
				case 'coc':
					$("#is_cod").val('Y');
					$("#is_prepaid").val('N');
					$("#is_credit").val('N');
					$('.payment-type-bt[data-value="cash"]').trigger('click');
					$('.payment-type-bt').attr('disabled',false);
					$('#customer_name').addClass('hide');
					$('#billing_customer').removeClass('hide');
					break;
				case 'caw':
					$("#is_cod").val('N');
					$("#is_prepaid").val('N');
					$("#is_credit").val('Y');
					$('.payment-type-bt[data-value="caw"]').trigger('click');
					$('.payment-type-bt').attr('disabled',true);
					$('.payment-type-bt[data-value="caw"]').attr('disabled',false);
					$('#billing_customer').addClass('hide');
					$('#customer_name').removeClass('hide');
				break;
			}
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
			if($('#phone_number').val()!='' && !$('#phone_number').val().match(phoneno)){
				bootbox.alert('Please Enter Valid Phone No', function() { 
				setTimeout('setFocus("phone_number")',100);
			});
        		return false;
			}
			if(!$('#paid-amount').val()){
				bootbox.alert("Please Enter Payment Amount");
				return false;
			}
			if(parseInt($('#paid-amount').val()) < Math.ceil($totalAmountWT.toFixed(0))){
				console.log(Math.ceil($totalAmountWT));
				bootbox.alert("Paid Amount is Less");
				return false;
			}
			if(!$("#paid_by").val()){
				bootbox.alert("Please Select Paid By");
				return false;				
			}
			
			if($('#customer_type').val()=='caw' && $('#customer_name').val()==''){
				bootbox.alert('Please Select Customer Name');
				return false;	
			}

			if(cawBill){
				if(!$('#challan_no').val()){
					bootbox.alert('Please Enter Challan No');
				    return false;
				}else if(!$('#onsite_time').val()){
					bootbox.alert('Please Select Date');
				    return false;
				}

			}
			
			var billDetails = new Object();
			billDetails.items = new Object();
			billDetails.items = $billingItems;

			billDetails.total_qty = $totalBillQty;
			billDetails.sub_total = $totalAmountWOT.toFixed(2);
			billDetails.total_vat_tax = ($totalVatTaxAmount).toFixed(2);
			billDetails.total_service_tax = ($totalServiceTaxAmount).toFixed(2);
			billDetails.total_tax = ($totalTaxAmount).toFixed(2);
			billDetails.discount = $intDiscount;
			billDetails.total_discount = ($totalDiscountAmount).toFixed(2);
			billDetails.total_amount = ($totalAmountWT).toFixed(2);
			billDetails.round_off = (($totalAmountWT).toFixed(0) - $totalAmountWT).toFixed(2);
			billDetails.due_amount = parseFloat(billDetails.total_amount) + parseFloat(billDetails.round_off);
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
			console.log($('#customer_name'));
			console.log($('#customer_name').hasClass('hide'));
			if(!$('#customer_name').hasClass('hide')){
				billDetails.customer.name = $("#customer_name option:selected").text();				
				billDetails.customer.id = $("#customer_name").val();				
			}
			billDetails.customer.type = $("#customer_type").val();
			billDetails.customer.city = $("#billing_customer_city").val();
			billDetails.customer.locality = $("#billing_customer_locality").val();
			billDetails.customer.sub_locality = $("#billing_customer_sub_locality").val();
			billDetails.customer.land_mark = $("#billing_customer_landmark").val();
			billDetails.customer.phone_no = $("#phone_number").val();
			billDetails.customer.company_name = $("#billing_customer_company_name").val();
			billDetails.customer.address = $("#billing_customer_address").val();
			if(billDetails.payment_type=='caw'){
				billDetails.customer.challan_no = $('#challan_no').val();
				billDetails.customer.onsite_time = $('#onsite_time').val();
			}
			billDetails.card = new Object();
			billDetails.card.no = $('#card_number').val();
			billDetails.card.type = $('#card_type').val();
			billDetails.card.company = $('#card_company').val();
			billDetails.card.redeem_amount = $('#card_redeem_amount').val();
			billDetails.card.txn_no = $('#card_txn_no').val();
			billDetails.card.txn_type = $('#card_txn_type').val();
			billDetails.card.approval_code = $('#card_approval_code').val();
			billDetails.card.balance = $('#card_balance').val();
			billDetails.card.invoice_number = $('#card_invoice_no').val();
			
			if(!$('#reward_redemption_code').val()){
				billDetails.card.reward_redemption = 'no';
				billDetails.card.redemption_code = '';
			}else{
				billDetails.card.reward_redemption = 'yes';
				billDetails.card.redemption_code = $('#reward_redemption_code').val();
			}
			billDetails.reprint = 1;
			billDetails.request_type = 'save_bill';
			billDetails.utility_check = printUtility;
			billDetails.coupon_code = $('#coupon_code').val();
			console.log(billDetails);
			$('#submit-sale').attr('disabled',true);
			$("#ajaxfadediv").addClass('ajaxfadeclass');
			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=billing.save",
		  		data : billDetails,
			}).done(function(response) {
				console.log(response);
				$("#ajaxfadediv").removeClass('ajaxfadeclass');
				result = $.parseJSON($.trim(response));
				$('#submit-sale').attr('disabled',false);
				//var check = result.error;
				if(result.error){
					bootbox.alert(result.message);
				}else{
					if(!exeMode){
						printBill(response, false);
					}
					$('#payModal').modal('hide');
					if(result.message!=''){
						bootbox.alert(result.message, function(){
							window.location.reload(true);
						}).find(".btn-primary").removeClass("btn-primary").addClass("btn-danger");
					}else if(exeMode){
						if(!printUtility){bootbox.alert('Print Utility Not Exists').find(".btn-primary").removeClass("btn-primary").addClass("btn-danger");}
						window.location=(billDetails.payment_type=='caw') ? 'index.php?dispatch=caw.index' : 'index.php?dispatch=billing.index';
					}
					
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
									'<th>Product Name</th><th>Menu Price</th><th>Price After Discount</th><th>Actual Base Price</th><th>Qty</th><th>Tax %</th><th>Tax</th><th>Service Tax%</th><th>Service Tax</th><th>Sub Total</th><th>Discount</th><th>Net Amount</th>'+
								'</tr>'+
							'</thead>'+
						'<tbody>';
						var qty = 0;
						var netAmount = 0.0;
						var taxAmount = 0.0;
						var serviceTaxAmount = 0.0;
						var discountAmount = 0.0;
						var subTotalSum = 0.0;
						var priceAfterDiscount = 0.0;
			$.each($billingItems, function(index,data){
				$viewData += '<tr class="text-right">'+
								'<td class="text-left">'+data.name+'</td>'+
								'<td>'+(parseFloat(data.price)).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.discount_price)).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.ABP)).toFixed(2)+'</td>'+
								'<td>'+data.qty+'</td>'+
								'<td>'+(((data.tax) ? data.tax : 0) * 100).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.taxAmount)).toFixed(2)+'</td>'+
								'<td>'+(((data.serviceTax) ? data.serviceTax : 0) * 100).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.serviceTaxAmount)).toFixed(2)+'</td>'+
								'<td>'+((parseFloat(data.subTotal))).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.discountAmount)).toFixed(2)+'</td>'+
								'<td>'+(parseFloat(data.netAmount)).toFixed(2)+'</td>'+
							'</tr>';
							qty += parseInt(data.qty);
							netAmount += data.netAmount;
							taxAmount += (data.taxAmount);
							serviceTaxAmount += (data.serviceTaxAmount);
							discountAmount += (data.discountAmount);
							subTotalSum += ((parseFloat(data.subTotal)));
							priceAfterDiscount += (parseFloat(data.priceAD));
							
										//'+(priceAfterDiscount).toFixed(2)+'

			});
			$viewData += '</tbody>'+
						'<tfoot><tr class="active"><th>Total</th><th></th><th></th><th class="text-right"></th><th class="text-right">'+qty+'</th><th class="text-right"></th><th class="text-right">'+(taxAmount).toFixed(2)+'</th><th class="text-right"></th><th class="text-right">'+(serviceTaxAmount).toFixed(2)+'</th><th class="text-right">'+subTotalSum.toFixed(2)+'</th><th class="text-right">'+(discountAmount).toFixed(2)+'</th><th class="text-right">'+(netAmount).toFixed(2)+'</th></tr></tfoot>'
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
	//---END--- Functions Work After Page Load via Events
	popupKeyboard = $('#discount_input_box').cKeyboard();
	popupKeyboard = $('#challan_no').cKeyboard();
	$("#discount-close").click(function(){ 
//		$("#discount-popover").toggle();
		$intDiscount = 0;
		$("#discount_input_box").val('');
		generateSalesTable();
	});

			//---START--- Event For Product Selection
		$("#proajax").on("click",".category-product",function(){ 
			    $(this).addClass('active-btn');
				var selectedSequence = $(this).attr('category-product-sequence');
				var productData = productArray[selectedCat][selectedSequence];
				var productID = productData.mysql_id;
				var newQty = (productID in $billingItems) ? ($billingItems[productID].qty + 1) : 1;
				generateSalesTable(productID, newQty, productData);
				if($('#discount_input_box').val()){
					discount('add', true);
				}
		});	


});
function generateSalesTable(productId, qty, productData){ 
	//alert(JSON.stringify(productData));
	var addAtLast = false;
	if(productData){
		addAtLast = true;
	}
	var applyDiscount = false;
	if(productData=='apply_discount'){ 
		applyDiscount = true;
	}
	resetBill(false);
	
	var productID = (productId) ? productId : 0;
	var  newqty = 0;
	if(productID && productID > 0){
		var  newqty = isNaN(parseInt(qty)) ? 0 : qty;
		if(!(productID in $billingItems)){
			$billingItems[productID] = new Object();
			$billingItems[productID].category_id = selectedCat;
			$billingItems[productID].category_name = catArray[selectedCat];
			$billingItems[productID].recipe_id = productData.recipe_id;
			$billingItems[productID].id = productID;
			$billingItems[productID].name = productData.name;
			$billingItems[productID].price = isNaN(productData.price * 1) ? 0 : productData.price;
			productData.service_tax = (productData.service_tax) ? productData.service_tax : 0;
			
			if($intDiscount!=0){ 
				$billingItems[productID].discount_price = decimalAdjust(productData.price - (productData.price  * $intDiscount/100 ) , -2);
			}else{
				$billingItems[productID].discount_price = isNaN(productData.price * 1) ? 0 : productData.price;
			}
			
			$billingItems[productID].ABP = decimalAdjust((productData.tax.rate) ? ($billingItems[productID].discount_price/((parseFloat(productData.tax.rate) * 100) + (parseFloat(productData.service_tax) * 100) + 100)) * 100 : ($billingItems[productID].discount_price/((parseFloat(productData.service_tax) * 100) + 100)) * 100 ,-2);
			
			$billingItems[productID].taxAbleAmount = $billingItems[productID].ABP;
			$billingItems[productID].qty = newqty;
			$billingItems[productID].tax = productData.tax.rate;
			$billingItems[productID].serviceTax = parseFloat(productData.service_tax);
			$billingItems[productID].discount = $intDiscount;

		}else{
			$billingItems[productID].qty = newqty;
		}
	} else if(applyDiscount){
		for(var index in $billingItems){
			$billingItems[index].discount_price = decimalAdjust($billingItems[index].price - ($billingItems[index].price  * $billingItems[index].discount/100 ) , -2);
			$billingItems[index].ABP = decimalAdjust(($billingItems[index].tax) ? ($billingItems[index].discount_price/((parseFloat($billingItems[index].tax) * 100) + (parseFloat($billingItems[index].serviceTax)* 100) + 100)) * 100 : ($billingItems[index].discount_price/((parseFloat($billingItems[index].serviceTax)* 100) + 100)) * 100 ,-2);
			$billingItems[index].taxAbleAmount = $billingItems[index].ABP;
		}
	 }

	var tableRows = '';
	//alert(JSON.stringify($billingItems));
	for(var index in $billingItems){ 
			//$billingItems[index].discount = $intDiscount;
			$billingItems[index].subTotal = $billingItems[index].qty * $billingItems[index].ABP;
			$billingItems[index].discountAmount = $billingItems[index].price * $billingItems[index].qty * $billingItems[index].discount/100;
			$billingItems[index].priceAD = $billingItems[index].discount_price ;
			$billingItems[index].taxAmount = $billingItems[index].ABP * $billingItems[index].tax * $billingItems[index].qty;
			$billingItems[index].serviceTaxAmount = $billingItems[index].ABP * $billingItems[index].serviceTax * $billingItems[index].qty;
			$billingItems[index].netAmount = $billingItems[index].subTotal + $billingItems[index].taxAmount + $billingItems[index].serviceTaxAmount;
		if(productID != index){
			tableRows +='<tr billing-product="'+index+'">'+
				'<td style="width:9%"><span class="glyphicon glyphicon-remove-sign del_row" style="font-size:1.4em"></span></td>'+
				'<td style="width:53%" class="btn-warning">'+$billingItems[index].name+'&nbsp;@&nbsp;'+$billingItems[index].price+'</td>'+
				'<td style="width:12%"><span class="bill_item_qty"><input type="text" class="keyboard nkb-input bill_qty_input" value="'+$billingItems[index].qty+'"/></span></td>'+
				'<td style="width:26%" class="text-right"><span class="bill_item_price text-right">'+($billingItems[index].qty * $billingItems[index].taxAbleAmount).toFixed(2)+'</span></td>'+
				'</tr>';
		}
		$totalBillQty += parseInt($billingItems[index].qty);
		$totalAmountWOT += ($billingItems[index].subTotal);
		$totalAmountWT += $billingItems[index].netAmount;
		$totalTaxAmount += $billingItems[index].taxAmount + $billingItems[index].serviceTaxAmount;
		$totalVatTaxAmount += $billingItems[index].taxAmount;
		$totalServiceTaxAmount += $billingItems[index].serviceTaxAmount;
		$totalDiscountAmount += ($billingItems[index].discountAmount );

	}
	//alert(JSON.stringify($billingItems));
	if(productID>0){
			tableRows +='<tr billing-product="'+productID+'">'+
				'<td style="width:9%"><span class="glyphicon glyphicon-remove-sign del_row" style="font-size:1.4em"></span></td>'+
				'<td style="width:53%" class="btn-warning">'+$billingItems[productID].name+'&nbsp;@&nbsp;'+$billingItems[productID].price+'</td>'+
				'<td style="width:12%"><span class="bill_item_qty"><input type="text" class="keyboard nkb-input bill_qty_input" value="'+$billingItems[productID].qty+'"/></span></td>'+
				'<td style="width:26%" class="text-right"><span class="bill_item_price text-right">'+($billingItems[productID].qty * $billingItems[productID].taxAbleAmount).toFixed(2)+'</span></td>'+
				'</tr>';
	}
	$('#saletbl tbody').html(tableRows);
	$('.bill_qty_input').cKeyboard();
	$("#count").text($totalBillQty);	
	$("#total").text($totalAmountWOT.toFixed(2));
	$("#ts_con").text($totalTaxAmount.toFixed(2));
	$("#total-payable").text($totalAmountWT.toFixed(0));	
	$("#ds_con").text(($totalDiscountAmount).toFixed(2));
	
}
function resetBill(refresh){ 
	if(refresh){ 
		$billingItems = new Object();
		delete $billingItems;
		$('#discount_input_box').val('').prop('disabled',false);
		$intDiscount = 0;	
		$('button',proajax).removeClass('active-btn');
		$('.category-selection[data-category="1"]').trigger('click');
		$('#redemption_code').val('').addClass('hide');
		$('#claim_reward').addClass('hide');
		$('#payment').removeClass('hide');
		$('.category-product').prop('disabled', false);
		$('.category-selection').prop('disabled', false);
		$('#apply_discount').removeClass('hide');
		$('#remove_discount').addClass('hide');
	}
	$totalBillItems = 0;
	$totalBillQty = 0;
	$totalAmountWOT = 0.0;
	$totalAmountWT = 0.0;
	$totalTaxAmount = 0.0;
	$totalVatTaxAmount = 0.0;
	$totalServiceTaxAmount = 0.0;
	$totalDiscountAmount = 0.0;
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

function getCawProduct(customer_id){
 alert(customer_id);
}

function discount(action, flag){
	var discount_code = $('#discount_input_box').val();
	var business_type = 227;
	var channel_type = 230;
	var bill_amount = $('#total-payable').text();
	var url = $.url(window.location);
	var coc = url.param('order');
	var caw = url.param('cawOrder');
	if(coc){
		business_type = 228;
		var orderData = $.parseJSON(localStorage.getItem(order));
		channel_type = orderData['channel_id'];
	}else if(caw){
		business_type = 229;
		channel_type = 231;
	}
		
	if(discount_code && action){
		$("#ajaxfadediv").addClass('ajaxfadeclass');
		$.ajax({
			type: 'POST',
			url: "index.php?dispatch=billing.getCoupanCode",
			data : {'coupan_code':discount_code, 'business_type':business_type, 'channel_type':channel_type, 'bill_amount':bill_amount},
			timeout:10000
		}).done(function(response) { 
				console.log($billingItems);
				$("#ajaxfadediv").removeClass('ajaxfadeclass');
				$result = $.parseJSON(response);
				if($result.error && !flag){
					bootbox.alert($result.message);
				}else if(!$result.error && $result.data['is_product']=='N' && $result.data['discount_type']=='P' && action=='add'){
					for(var index in $billingItems){
						$billingItems[index].discount = $result.data['discount_value'];
					}
				}else if(!$result.error && $result.data['is_product']=='N' && $result.data['discount_type']=='P' && action=='remove'){
					for(var index in $billingItems){
						$billingItems[index].discount = 0;
					}
				}else if(!$result.error && $result.data['is_product']=='N' && $result.data['discount_type']=='V' && action=='add'){
					bootbox.alert('Sorry, Coupon Can Not be Applied'); 
					return false;
					/*for(var index in $billingItems){
						$billingItems[index].discount = $result.data['discount_value'];
					}*/
				}else if(!$result.error && $result.data['is_product']=='N' && $result.data['discount_type']=='V' && action=='remove'){
					for(var index in $billingItems){
						$billingItems[index].discount = 0;
					}
				}else if(!$result.error && $result.data['is_product']=='Y'){
						$data = $result.data['discount_data'];
						for(var index in $data){
							
							if($data[index].product_id in $billingItems && $data[index].product_qty <= $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'F' && action=='add'){
								product_id = $data[index].free_productid;
								var reminder = parseInt($billingItems[$data[index].product_id].qty/$data[index].product_qty);
								var addobj = {};
								addobj[product_id] = {"category_id":0, "category_name":'', "recipe_id":"", "id":$data[index].free_productid, "name":$data[index].free_productname, "price":0, "discount_price":0, "ABP":0, "taxAbleAmount":"", "qty":$data[index].free_productqty * reminder, "tax":"0", "serviceTax":"0", "discount":"100"};
								$.extend($billingItems, addobj);
								
							}else if($data[index].product_id in $billingItems && $data[index].product_qty <= $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'F' && action=='remove'){
								product_id = $data[index].free_productid;
								delete $billingItems[product_id];
								
							}else if($data[index].product_id in $billingItems && $data[index].product_qty > $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'F' && action=='add'){
								product_id = $data[index].free_productid;
								delete $billingItems[product_id];
								
							}else if($data[index].product_id in $billingItems && $data[index].product_qty <= $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'P' && action=='add'){
								var reminder = parseInt($billingItems[$data[index].product_id].qty/$data[index].product_qty);
								var discountAmount = $billingItems[$data[index].product_id].price * reminder * $data[index].product_discount/100;
								//alert(reminder); alert(discountAmount);
								$billingItems[$data[index].product_id].discount = $data[index].product_discount;
							
							}else if($data[index].product_id in $billingItems && $data[index].product_qty > $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'P' && action=='add'){
								$billingItems[$data[index].product_id].discount = 0;
								
							}else if($data[index].product_id in $billingItems && $data[index].product_qty <= $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'P' && action=='remove'){
								$billingItems[$data[index].product_id].discount = 0;
							
							}else if($data[index].product_id in $billingItems && $data[index].product_qty <= $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'V' && action=='add'){
								$billingItems[$data[index].product_id].price = $billingItems[$data[index].product_id].price - $data[index].product_discount;
							
							}else if($data[index].product_id in $billingItems && $data[index].product_qty > $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'V' && action=='add'){
								$billingItems[$data[index].product_id].price = parseInt($billingItems[$data[index].product_id].price) + parseInt($data[index].product_discount);
							
							}else if($data[index].product_id in $billingItems && $data[index].product_qty <= $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'V' && action=='remove'){
								$billingItems[$data[index].product_id].price = parseInt($billingItems[$data[index].product_id].price) + parseInt($data[index].product_discount);
							
							}else if($data[index].product_id in $billingItems && $data[index].product_qty <= $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'C' && action=='add'){
								if(!$billingItems[$data[index].product_id].original_price){
									$billingItems[$data[index].product_id].original_price = $billingItems[$data[index].product_id].price;
								} 
								$billingItems[$data[index].product_id].price = $data[index].product_discount;
								//alert(JSON.stringify($billingItems));
							
							}else if($data[index].product_id in $billingItems && $data[index].product_qty > $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'C' && action=='add'){
								$billingItems[$data[index].product_id].price = ($billingItems[$data[index].product_id].original_price ? $billingItems[$data[index].product_id].original_price : $billingItems[$data[index].product_id].price);
								
							
							}else if($data[index].product_id in $billingItems && $data[index].product_qty <= $billingItems[$data[index].product_id].qty && $data[index].product_discount_type == 'C' && action=='remove'){
								$billingItems[$data[index].product_id].price = $billingItems[$data[index].product_id].original_price;
								
							}
							
						}
					}
					generateSalesTable('','','apply_discount');
					if(action=='add'){
						$('#coupon_code').val(discount_code);
						$('#apply_discount').addClass('hide');
						$('#remove_discount').removeClass('hide');
					}else if(action=='remove'){
						$('#apply_discount').removeClass('hide');
						$('#remove_discount').addClass('hide');
						$('#coupon_code').val('');
						$('#discount_input_box').val('');
						
					}
				});
			//$('#discount_input_box').val('');
		}
	}