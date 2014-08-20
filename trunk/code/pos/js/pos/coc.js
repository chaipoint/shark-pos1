$(document).ready(function(){
	$('.bt-update-status').click(function(){
		var data = new Object();
		data['order'] = $(this).closest('tr').data('order-id');
		data['new_status'] = $(this).data('new_status');
		data['current_status'] = $(this).data('current_status');
		console.log(data);
		$.ajax({
			type: 'POST',
			url: "index.php?dispatch=orders.updateOrderStatus",
			data : data
		}).done(function(response) {
		});

	});
});