$(document).ready(function(){
	/* Store Selection Block Start */
	
	$('.store-selection').click(function (){
		var store_id = $(this).attr('id');
		var store_name = $(this).text() ;
		var store_code = $(this).data('code') ;
		var bill_type = $(this).data('bill_type') ;
		var store_message = $(this).data('store_msg') ;
		//alert(store_message);//return false;
		//alert(store_code);return false;
		bootbox.confirm("Do You Want To Select  <b style='font-size:16px'>"+store_name+"?</b>", function(result) {
			if(result==true){
				$('#store_id').val(store_id);
				$('#store_name').val(store_name);
				$('#store_code').val(store_code);
				$('#bill_type').val(bill_type);
				$('#store_message').val(store_message);
				$("#ajaxfadediv").addClass('ajaxfadeclass');
				$.ajax({
					url: "download/download.php?param=updateSingleStore-"+store_id,
					timeout:10000,
					}).done(function(response) {
						$("#ajaxfadediv").removeClass('ajaxfadeclass');
						$('#store_selection').submit();
					}).error(function(x, t, m){
						$('#store_selection').submit();
					});
		//alert(response); 
			
            }
		}); 
	});
		
			
	/* Store Selection Block End */

});
