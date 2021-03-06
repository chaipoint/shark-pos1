$(document).ready(function(){
	$(this).attr("title", "Shark |ChaiPoint POS| COC");
	$(".generate-bill").click(function(){
		var order = $(this).data('order-id');
		var orderDetails = $('tr[data-order-id="'+order+'"]').data('order-details');
		if(window.localStorage){
			localStorage.setItem(order, JSON.stringify(orderDetails));
			window.location = 'index.php?dispatch=billing&order='+order 
//			console.log(localStorage.getItem(order).toString());
		}
	});

	$(".products_list_toggle").click(function(){
		//alert($(this).text());
		if($(this).text()=='Show Detail'){
		//$(".toggle-table").addClass('hide');
        $(".toggle-table#"+$(this).data('target')).removeClass('hide');
        $(this).text('Hide Detail');
      } else if($(this).text()=='Hide Detail'){
       // $(".toggle-table").removeClass('hide');
        $(".toggle-table#"+$(this).data('target')).addClass('hide');
        $(this).text('Show Detail');
     }
	});

	
	$('.bt-update-status').click(function(){
		var ajax = true;
		var data = new Object();
		var ele = this;
		data['order'] = $(this).closest('tr').data('order-id');
		data['new_status'] = $(this).data('new_status');
		data['current_status'] = $(this).data('current_status');
		data['customer_name'] = ($(this).closest('tr').data('order-details')).name;
		data['customer_phone'] = ($(this).closest('tr').data('order-details')).phone;
		data['net_amount'] = ($(this).closest('tr').data('order-details')).net_amount;
		data['store_name'] = ($(this).closest('tr').data('order-details')).store_name;
 
           if(data['new_status'] == 'Cancelled'){
			var ajax = false;

			bootbox.dialog({
			message:'<div class="form-group"><select name="cancel_reason" id="cancel_reason" class="form-control" onchange="MyFunction($(this).val())"><option value="">Select Reason</option><option value="Food not available">Food not available</option><option value="Store closed">Store closed</option><option value="Out of delivery area">Out of delivery area</option><option value="Customer not reachable">Customer not reachable</option><option value="Cancelled due to delayed delivery">Cancelled due to delayed delivery</option><option value="Duplicate entries">Duplicate entries</option><option value="Address not found">Address not found</option><option value="other">Others � pl specify</option></select><textarea name="cancel_reason1" id="cancel_reason1" class="form-control hide" style="margin-top:15px"></textarea></div>',
			title:"Order Cancel Reason",
			buttons:{
				main:{
					label:"Save",
					className:"btn-success btn-sm",
					callback:function(){
							var textArea = $('#cancel_reason');
							var reason = $.trim(textArea.val());
							if(reason=='other'){
								reason = $('#cancel_reason1').val();
							}
							if( reason == '' ){
								textArea.closest('.form-group').addClass('has-error');
								return false;
							}
							data['reason'] = reason;
							changeStatus(data);
						}
					},
				danger:{
					label:"Cancel",
					className:"btn-danger btn-sm"
				},	

				}
			});
		}else if(data['new_status'] == 'Delivered'){
			var ajax = false;

			bootbox.dialog({
			message:'<div class="form-group"><input type="text" name="staff_name" id="staff_name" class="autocomplete form-control" action="index.php?dispatch=staff.getDeliveryBoy" strict="true" target="staff_id"/></div>',
			title:"Delivery Boy",
			buttons:{
				main:{
					label:"Save",
					className:"btn-success btn-sm",
					callback:function(){
							var staff_name = $('input[name="staff_id"]');
							var staff_id = $.trim(staff_name.val()); 
							if( staff_id == '' ){
								staff_name.closest('.form-group').addClass('has-error');
								return false;
							}
							data['staff_id'] = staff_id;
							changeStatus(data);
						}
					},
				danger:{
					label:"Cancel",
					className:"btn-danger btn-sm"
				},	

				}
			});
        
		}
		if(ajax){
			changeStatus(data);
		}

	});
	
	
});

function changeStatus(data){ 
			$("#ajaxfadediv").addClass('ajaxfadeclass');
			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=orders.updateOrderStatus",
				data : data
			}).done(function(response) {
				$("#ajaxfadediv").removeClass('ajaxfadeclass');
				var result = $.parseJSON($.trim(response));
				if(result.error){
					bootbox.alert(result.message);
					if(data in result && status in result.data){
						$('tr[data-order-id="'+data.order+'"]','#order-holder').remove();//ele.closest('tr').remove();
					}
				}else{
					bootbox.alert("Order "+data.new_status+" Successfully");
					$('tr[data-order-id="'+data.order+'"]','#order-holder').remove();//ele.closest('tr').remove();
					$("span#"+data.current_status).text(parseInt($("span#"+data.current_status).text())-1);
					$("span#"+data.new_status).text(parseInt($("span#"+data.new_status).text())+1);
					$('#tot-amt').text(parseFloat($('#tot-amt').text().replace(',', '')) - parseFloat((data.net_amount).replace(',', '')));
				}
			});	
}

function MyFunction(val){
	if(val=='other'){
		$('#cancel_reason1').removeClass('hide');
	}else{
		$('#cancel_reason1').addClass('hide');
	}

}
