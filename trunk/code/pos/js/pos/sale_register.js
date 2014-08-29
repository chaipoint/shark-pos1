$(document).ready( function(){

/* DatePicker Class Function */
    $('.datepicker').datepicker({
		maxDate:0,
		dateFormat:'yy-mm-dd'
	}).datepicker('setDate',new Date());

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
	$('#submit-expense').on('click', function(event){
		event.preventDefault();
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
				}
			});
	});

/* Number Validation For Amount */
	$("#expense_amount").on('keypress',function (e){ 
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
    });	

});