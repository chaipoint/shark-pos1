$(document).ready(function(){
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
		$(".toggle-table").addClass('hide');

		$(".toggle-table#"+$(this).data('target')).removeClass('hide');
	});

	$('.bt-update-status').click(function(){
		var ajax = true;
		var data = new Object();
		var ele = this;
		data['order'] = $(this).closest('tr').data('order-id');
		data['new_status'] = $(this).data('new_status');
		data['current_status'] = $(this).data('current_status');

		if(data['new_status'] == 'Cancelled'){
			var ajax = false;

			bootbox.dialog({
			message:'<div class="form-group"><textarea placeholder="reason" name="cancel_reason" id="cancel_reason" class="form-control"></textarea></div>',
			title:"Order Cancel Reason",
			buttons:{
				main:{
					label:"Change Status",
					className:"btn-success btn-sm",
					callback:function(){
							var textArea = $('#cancel_reason');
							var reason = $.trim(textArea.val()); 
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
		}
		if(ajax){
			changeStatus(data);
		}

	});
});

function changeStatus(data){
			$.ajax({
				type: 'POST',
				url: "index.php?dispatch=orders.updateOrderStatus",
				data : data
			}).done(function(response) {
				var result = $.parseJSON($.trim(response));
				if(result.error){
					bootbox.alert(result.message);
					if(data in result && status in result.data){
						$('tr[data-order-id="'+data.order+'"]','#order-holder').remove();//ele.closest('tr').remove();
					}
				}else{
					bootbox.alert("Status Changed Successfully");
					$('tr[data-order-id="'+data.order+'"]','#order-holder').remove();//ele.closest('tr').remove();
				}
			});	
}