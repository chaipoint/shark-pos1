function toggleRepBT (){
	if(is_rep_running){
		$("#billing_sync_bt").addClass('hidden')
		$("#billing_stop_sync_bt").removeClass('hidden')
	}else{
		$("#billing_stop_sync_bt").addClass('hidden')
		$("#billing_sync_bt").removeClass('hidden')		
	}
}
$(document).ready( function(){
	toggleRepBT ();
/* DatePicker Class Function */
    $('.datepicker').datepicker({
		maxDate:0,
		dateFormat:'yy-mm-dd'
	}).datepicker('setDate',new Date());

    $('#ui-datepicker-div').css('display','none');
    
/* Function To Add Prety Expense */
	$('#add_expense').click(function(event){
		event.preventDefault();
		$('#addExpenseModal').modal();
	});

/* Function To View Prety Expense */
	$('#view_expense').click(function(event){
		event.preventDefault();
		$('#viewExpenseModal').modal();
	});
		
	$('.close-model').click(function(){
		$("div.ui-keyboard").hide();
	});


/* Function To Save Petty Expense */
	$('#add-expense-form').on('submit', function(event){
		event.preventDefault();
        $('#add-expense-form').bootstrapValidator();
		if($('input[name="expense_done_by_id"]').val()==undefined){
			bootbox.alert('Please Select Done By');
			return false;
		}else if($('input[name="expense_approved_by_id"]').val()==undefined){
            bootbox.alert('Please Select Approved By');
			return false;
		}
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

/* Number Validation For Amount */
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
	$('#todays-sale').click(function(event){
			event.preventDefault();
			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=billing.getSaleBills",
		  		data : {request_type:'todays_bill'},
			}).done(function(response) {
					var result = $.parseJSON(response);
					if(result.error){
						bootbox.alert(result.message);
					}else{
						var totalBills = result.data.summary.length;
						//console.log(result.data.summary.length);

	//					if(totalBills>0){
							var trs = "";
							var trh = "";
							var tfs = "";
							
							var sumPaymenyTypes = new Object();
							var sumTotal = 0;
							$.each(result.data.summary,function(index,details){
								//console.log(index+"=>"+JSON.stringify(details));
								trs += '<tr><td>'+index+'</td>';
								var total = 0;
										
								$.each(result.data.payment_type, function(subIndex, subDetails){
									trs += '<td class="text-right">'+(details[subIndex] ?  details[subIndex] : 0)+'</td>';
									total += (details[subIndex] ?  details[subIndex] : 0);
									if(subIndex in sumPaymenyTypes){
										sumPaymenyTypes[subIndex] += (details[subIndex] ?  details[subIndex] : 0);
									}else{
										sumPaymenyTypes[subIndex] = (details[subIndex] ?  details[subIndex] : 0);
									}
								});
                            	trs += '<td class="text-right">'+total+'</td></tr>';
                            	sumTotal += total;
                            });
                            console.log(sumPaymenyTypes);
							trh += '<tr><th></th>';
							$.each(result.data.payment_type,function(index,details){
								trh += '<th>'+index+'</th>';
								tfs += '<th class="text-right">'+(sumPaymenyTypes[index] ? sumPaymenyTypes[index] : 0)+'</th>';
							});
							trh += '<th class="text-right">Total</th></tr>';
							$("#today-sale-table thead").html(trh);
							$("#today-sale-table tbody").html(trs);
							$("#today-sale-table tfoot").html('<tr><th>Total</th>'+tfs+'<th class="text-right">'+sumTotal+'</th></tr>');

	//				}
				} 
			});
		});
});