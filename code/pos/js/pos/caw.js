var keyboard = new Array();
$(document).ready(function(){
/* Customer Selection Block Start */
	$('.customer-selection').click(function (){
		$('.customer-selection').css('background-color','');
		$(this).css('background-color','black');
		var customer_id = $(this).attr('id');
		var customer_name = $(this).text() ;
		var customer_type = $(this).data('type') ;
			if(customer_type==1 || customer_type==2){ 
				window.location = 'index.php?dispatch=billing&cawOrder='+customer_id
				/*$('#customer_name').val(customer_name);
				$('#customer_id').val(customer_id);
				$.ajax({
					type: 'POST',
					url: "index.php?dispatch=caw.schedule",
					data : {'customer_name':customer_name, 'customer_id':customer_id},
				}).done(function(response) { 
					//alert(response);
					var $result = $.parseJSON(response);
					if($result.error){
						$('#schedule_table tbody').html('');
						$('#schedule_div').removeClass('hide');
						$('.panel-heading').html('<span><strong class="panel-title" style="color:red;">'+$result.message+'</strong>&nbsp&nbsp<input type="button" id="caw_sync" class="btn btn-success" data-store_id='+$result.store_id+' value="Get Latest CAW Data"/></span>');
					}else{
						$('#schedule_table tbody').html('');
						$('#schedule_div').removeClass('hide');
						$('.panel-heading').html('<h4 class="panel-title" style="color:black;">C@W Schedule For '+$result.customer_name+'</h4>')
						$('#schedule_table tbody').append($result.data);
					}
						
				});*/
			}
	});
/* Customer Selection Block End */

	/*$("#schedule_table").on('click','.generate-bill',function(){
		var thisRow = $(this).closest("tr");
		var currentRow = parseInt(thisRow.attr('rowid'));
		
		if($('#challan_'+currentRow).val()!=''){ 
			var challan_no = $('#challan_'+currentRow).val();
			var date = new Date();
			var cawOrder = date.getMilliseconds();
			var orderDetails = $('#button_'+currentRow).data('order_details');
			orderDetails.challan_no = challan_no;
			if(window.localStorage){
				localStorage.setItem(cawOrder, JSON.stringify(orderDetails));
				window.location = 'index.php?dispatch=billing&cawOrder='+cawOrder
			}
		} 
	});*/
	
	keyboard.push($('.challan').cKeyboard());

});
