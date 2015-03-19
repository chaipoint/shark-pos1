function sales_register(data){
	window.location = 'index.php?dispatch=sales_register';
}
function data_sync(data){ 
		$("#login_holder_home").modal('hide');
		$("#sync-modal").modal({backdrop: 'static'});
		$('.alert',$("#sync-modal")).remove();
}
var url = null;
$(document).ready( function(){
    url = $.url();

/* DatePicker Class Function */
    $('.datepicker').datepicker({
		maxDate:0,
		dateFormat:'yy-mm-dd'
	}).datepicker('setDate',new Date());

    $('#ui-datepicker-div').css('display','none');
    var dateParam = (url.param('sales_reg_search'));
	var dateParam1 = (url.param('sales_reg_search1'));
	var date = new Date();
	var date1 = new Date();
	if(dateParam){
		var parmArray = dateParam.split('-');
		var parmArray1 = dateParam1.split('-');
		date = new Date(parmArray[2],getMonth(parmArray[1]),parmArray[0]);
		date1 = new Date(parmArray1[2],getMonth(parmArray1[1]),parmArray1[0]);
	}
$('#sales_reg_search').datepicker('update', new Date(date.getFullYear(), date.getMonth(), date.getDate()));
$('#sales_reg_search1').datepicker('update', new Date(date1.getFullYear(), date1.getMonth(), date1.getDate()));

$("#search_button").click(function(){
	if((url.param('dispatch').split('.'))[0] == 'sales_register'){
		var date1 = $('#sales_reg_search').datepicker('getDate');
		var date2 = $('#sales_reg_search1').datepicker('getDate');
		if (date1 > date2) {
			bootbox.alert('End date should be greater than Start');
			return false;
		}
		$("#search_form").submit();
	}else{
		searchShiftData();
	}
});
/* Function To Add Prety Expense */
	$('#add_expense').click(function(event){
		event.preventDefault();
		$('#addExpenseModal').modal();
		$('#expense_purpose, #expense_amount').val('');
		$('#item, #expense_purpose, #expense_amount').cKeyboard();
		setTimeout(function(){
			$('#item').focus();				
		},500);

	});

/* Function To View Prety Expense */
	$('#view_expense').click(function(event){
		event.preventDefault();
		$('#viewExpenseModal').modal();
	});
		
	$('.close-model').click(function(){
		$("div.ui-keyboard").hide();
	});

/* Function To Paid Bill */
	$('.panel-body').on('click','.pay_bill',function(){
        $billno = $(this).data('href');
		bootbox.confirm("Do You Want To Paid This Bill?", function(result) {
		if(result==true){
			$.ajax({
						type: 'POST',
						url: "index.php?dispatch=billing.save",
			  			data : {request_type:'update_bill', doc:$billno, bill_status_id: 80,bill_status_name:'Paid'},
					}).done(function(response) {
						response = $.parseJSON(response);
						if(response.error){
						bootbox.alert(response.message);
						}else{
						bootbox.alert(response.message,function(){
							//window.location = "?dispatch=sales_register";	
							window.location.reload(true);
						});
					}
				});
            }
        });
	});

/* Function To Cancel Card Load Transaction */
	$('.cancel-transaction').on('click', function(){
        var txn_no = $(this).closest('tr').data('txn_no');
        var approval_code = $(this).closest('tr').data('approval_code');
        var amount = $(this).closest('tr').data('amount');
        var invoice_number = $(this).closest('tr').data('invoice_number');
        var card = $(this).closest('tr').data('card_no');
        var doc_id = $(this).closest('tr').data('doc_id');
        //alert(card);
		bootbox.confirm("Do You Want To Cancel This Transaction?", function(result) {
		if(result==true){
			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=billing.cancelLoad",
			  	data : {request_type:'cancel_load', 'doc_id':doc_id, 'txn_no':txn_no, 'approval_code':approval_code, 'invoice_number':invoice_number, 'amount':amount, 'card_number':card},
				}).done(function(response) {
					response = $.parseJSON($.trim(response));
					if(response.error){
						bootbox.alert(response.message);
					}else{
						bootbox.alert(response.message,function(){
							window.location.reload(true);													
						});
					}
				});
            }
        });
	});


/* Function To Save Petty Expense */
	$('#add-expense-form').on('submit', function(event){
		event.preventDefault();
        $('#add-expense-form').bootstrapValidator();
		
	    var data = $('form#add-expense-form').serializeArray();
		console.log(data);
		$.ajax({
				type: 'POST',
				url: "index.php?dispatch=sales_register.save",
		  		data : data ,
			 }).done(function(response) {
				console.log(response);
				result = $.parseJSON(response);
				if(result.error){
					bootbox.alert(result.message);
				}else{
					$('#addExpenseModal').modal('hide');
					//$('form#add-expense-form')[0].reset();
					bootbox.alert('Expense Successfully Saved');
					window.location.reload(true);
				}
			});

	});

/* Number Validation For Amount */
	$("#expense_amount").on('keypress',function (e){ 
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
    });	

/* Function For glyphicon Icon Up And Down*/
	$('.col').click(function(){
		if($(this).find('i').attr('class')=='glyphicon glyphicon-chevron-up pull-right'){
			$(this).find('i').removeClass('glyphicon glyphicon-chevron-up pull-right');
			$(this).find('i').addClass('glyphicon glyphicon-chevron-down pull-right');
		}else if($(this).find('i').attr('class')=='glyphicon glyphicon-chevron-down pull-right'){
			$(this).find('i').removeClass('glyphicon glyphicon-chevron-down pull-right');
			$(this).find('i').addClass('glyphicon glyphicon-chevron-up pull-right');
		}
	});

/* Todays Sale Function */
	$('#todays_sale').click(function(event){ 
			event.preventDefault();
			var date1 = $('#sales_reg_search').val();
			var date2 = $('#sales_reg_search1').val();
			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=sales_register.getTodaysSale",
		  		data : {request_type:'todays_bill', 'date1':date1, 'date2':date2},
			}).done(function(response) {
					var result = $.parseJSON(response);
					if(result.error){
						bootbox.alert(result.message);
					}else{
						var totalBills = result.data.summary.length;
							var trs = "";
							var trh = "";
							var tfs = "";
							
							var sumPaymenyTypes = new Object();
							var sumTotal = 0;
							$.each(result.data.summary,function(index,details){
								trs += '<tr><td>'+index+'</td>';
								var total = 0;
										
								$.each(result.data.payment_type, function(subIndex, subDetails){
									trs += '<td class="text-right">'+(details[subIndex] ?  details[subIndex].toFixed(2) : 0)+'</td>';
									total += (details[subIndex] ?  details[subIndex] : 0);
									if(subIndex in sumPaymenyTypes){
										sumPaymenyTypes[subIndex] += (details[subIndex] ?  details[subIndex] : 0);
									}else{
										sumPaymenyTypes[subIndex] = (details[subIndex] ?  details[subIndex] : 0);
									}
								});
                            	trs += '<td class="text-right">'+total.toFixed(2)+'</td></tr>';
                            	sumTotal += total;
                            });
                            console.log(sumPaymenyTypes);
							trh += '<tr><th></th>';
							$.each(result.data.payment_type,function(index,details){
								trh += '<th>'+index.toUpperCase()+'</th>';
								tfs += '<th class="text-right">'+(sumPaymenyTypes[index] ? sumPaymenyTypes[index].toFixed(2) : 0)+'</th>';
							});
							trh += '<th class="text-right">Total</th></tr>';
							$("#today-sale-table thead").html(trh);
							$("#today-sale-table tbody").html(trs);
							$("#today-sale-table tfoot").html('<tr><th>Total</th>'+tfs+'<th class="text-right">'+sumTotal.toFixed(2)+'</th></tr>');
				} 
			});
		});
});
function getMonth(monthName){
	var monthNumber = 0;
	switch(monthName){
		case 'January':
			monthNumber = 0;
			break;
		case 'February':
			monthNumber = 1;
			break;
		case 'March':
			monthNumber = 2;
			break;
		case 'April':
			monthNumber = 3;
			break;
		case 'May':
			monthNumber = 4;
			break;
		case 'June':
			monthNumber = 5;
			break;
		case 'July':
			monthNumber = 6;
			break;
		case 'August':
			monthNumber = 7;
			break;
		case 'September':
			monthNumber = 8;
			break;
		case 'October':
			monthNumber = 9;
			break;
		case 'November':
			monthNumber = 10;
			break;
		case 'December':
			monthNumber = 11;
			break;
	}
	return monthNumber;
}