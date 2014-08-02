$(document).ready(function(){
	//---START--- Initial Configurations on Load Of Page

	var $billingItems = new Object();
	var $totalBillItems = 0;
	var $totalBillCost = 0.0;
	var $taxAmount = 0.0;
	var $totalTaxAmount = 0.0;
	var $totalBillCostAfterTaxAD = 0.0;
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

	//Add Time in Header or Navigation Panel 
	var now = moment().format("dddd, MMMM Do, YYYY, h:mm:ss A");
    $('#cur-time').text(now);

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
					$buttonList += '<button type="button" class="btn btn-prni hov category-product" value="'+value.mysql_id+'" category-product-sequence="'+key+'">'+value.name+'</button>';
				});
				$("#proajax").html($buttonList);
			}
		});
		//---END--- Category SElection Ends

		//---START--- Event For Product Selection
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


					$taxAmount = (productData.price * (productData.tax.rate));
					
					$totalTaxAmount = parseFloat($totalTaxAmount) + $taxAmount; 
					$totalBillCostAfterTaxAD = parseFloat($totalBillCostAfterTaxAD) + $taxAmount + parseFloat(productData.price);


					//console.log("Inside Existing = " + $totalBillCost);
				}else{
					$billingItems[productData.mysql_id] = new Object();
					$billingItems[productData.mysql_id].qty = 1;
					$billingItems[productData.mysql_id].price = parseFloat(productData.price);
					$billingItems[productData.mysql_id].name = productData.name;
					$billingItems[productData.mysql_id].total_amount = productData.price;
					$billingItems[productData.mysql_id].tax = productData.tax.rate;

//					$taxTrace[productData.mysql_id] = new Object();
//					$taxTrace

					$taxAmount = (productData.price * (productData.tax.rate));
					$totalTaxAmount = parseFloat($totalTaxAmount) + $taxAmount; 
					$totalBillCostAfterTaxAD = parseFloat($totalBillCostAfterTaxAD) + $taxAmount + parseFloat(productData.price);

					$totalBillItems 	+= 	parseInt($billingItems[productData.mysql_id].qty);
					$totalBillCost 		= 	parseFloat($totalBillCost) + parseFloat(productData.price);

					$("#saletbl tbody").append('<tr billing-product="'+productData.mysql_id+'"><td><span class="glyphicon glyphicon-remove-sign"></span></td><td class="btn-warning">'+productData.name+'&nbsp;@&nbsp;'+productData.price+'</td><td><span class="qty">'+(1)+'</span></td><td><span class="price text-right">'+parseFloat(productData.price).toFixed(2)+'</span></td></tr>');
					//				console.log($billingItems[productData.mysql_id]);
					//				console.log($billingItems);
					//	console.log(productData.name);
					//	console.log(productData['name']);
				}
				$("#count").text($totalBillItems);		
				$totalBillCost = $totalBillCost.toFixed(2);
				$("#total").text($totalBillCost);

				$totalBillCostAfterTaxAD = $totalBillCostAfterTaxAD.toFixed(2)
				$("#total-payable").text($totalBillCostAfterTaxAD);	
				$totalTaxAmount = $totalTaxAmount.toFixed(4);
				$("#ts_con").text($totalTaxAmount);	
				//console.log("OUtside = " + $totalBillCost);
		});	
		//---END--- Event For Product Selection
		//onCancel Of Bill
		$("#cancel").click(function(){
			$billingItems = new Object();
			$totalBillItems = 0;
			$totalBillCost = 0.0;
			$("#count").text(0);		
			$("#total").text(0);
			$("#total-payable").text(0);	
			$("#saletbl tbody").html("");	
		});
		//---START--- Payment Event After Products selection  or Without Product Selection
		$("#payment").click(function(){
			$("#fcount").text($totalBillItems);
			$("#twt").text($totalBillCost);
			if($totalBillItems == 0){
				bootbox.alert('Please add product to sale first');
			}else{
				$('#payModal').modal();
			}
		});
		//---END--- Payment Event After Products selection  or Without Product Selection

		//---START--- SUbmit Payment Bill
		$("#submit-sale").click(function(){
			if(!$('#paid-amount').val()){
				bootbox.alert("Please Enter Payment Amount");
				return false;
			}
			if(parseInt($('#paid-amount').val()) < $totalBillCost){
				bootbox.alert("Paid Amount is Less");
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
			//	console.log(JSON.stringify(billDetails));
			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=billing.save",
		  		data : billDetails,
			}).done(function(response) {
				console.log(response);
				result = $.parseJSON(response);
				if(result.error){
					bootbox.alert('OOPS! Some Error Please Contect Admin');
				}else{
					$('#payModal').modal('hide');
					bootbox.alert('Bill Successfully Saved <a class="label label-primary print-bill-today" href="billprint.php?bill_no='+result.data.bill_no+'" target="_blank">Print</a>');
				}
			});
		});
		//---END--- SUbmit Payment Bill

		//---START--- Todays Sale Request
		$('#todays_sale').click(function(){
			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=billing.getSaleBills",
		  		data : {request_type:'todays_bill'},
			}).done(function(response) {
					var result = $.parseJSON(response);
					if(result.error){
						bootbox.alert(result.message);
					}else{
						var totalBills = result.data.length;
						if(totalBills>0){
							var trs = "";
							$.each(result.data,function(index,details){
								//console.log(index+"=>"+JSON.stringify(details));
								trs += '<tr><td>'+details.value.customer+'</td><td>'+
										details.value.total_qty+'</td><td>'+
										details.value.total_amount+'</td><td><a class="label label-primary print-bill-today" href="billprint.php?bill_no='+details.value.bill_no+'" target="_blank">Print</a></td><td><a class="label label-warning print-bill-today" href="billprint.php?bill_no='+details.value.bill_no+'" target="_blank">Edit</a></td><td><a class="label label-danger print-bill-today" href="billprint.php?bill_no='+details.value.bill_no+'" target="_blank">Delete</a></td></tr>';
							});
							$("#today-sale-table tbody").html(trs);
					}
				} 
			});
		});
		//---END--- Todays Sale Request

		//---START-TAX POPUP --//
		$("#add_tax").click(function(){

			console.log($billingItems);
			$viewData = '<table class="table table-striped table-condensed table-hover protable" width="100%" border="0" cellspacing="0" cellpadding="0">'+
							'<thead>'+
								'<tr>'+
									'<th>Menu Item</th><th>Tax Rate</th><th>Qty</th><th>Price</th><th>Tax Amount</th>'+
								'</tr>'+
							'</thead>'+
						'<tbody>';
			var QtyTotal = 0;
			var totalTaxAmountCheck = 0.0;
			$.each($billingItems, function(index,data){
				QtyTotal += data.qty;
				taxAM = (data.qty * data.tax * data.price).toFixed(4);
				totalTaxAmountCheck += parseFloat(taxAM); 
				$viewData += '<tr>'+
								'<td>'+data.name+'</td>'+
								'<td>'+
									'<input type="text" class="form-control input-sm" value="'+data.tax+'"/>'+
								'</td>'+
								'<td>'+data.qty+'</td>'+
								'<td>'+data.price+'</td>'+
								'<td>'+taxAM+'</td>'+
							'</tr>';
			});
			$viewData += '</tbody><tfoot><th colspan="2">Total</th><th>'+QtyTotal+'</th><th></th><th>'+totalTaxAmountCheck.toFixed(2)+'</th></tfoot><table>';

			bootbox.dialog({
				message:$viewData,
				title:"Menu Tax Rate",
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
							$("#balance").text( paid - $totalBillCost );
						}
					}
				}
			});
});