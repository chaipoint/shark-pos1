$(document).ready(function(){
	$("#pos_sync").click(function(){
		$("#sync-modal").modal('show');
		$('.alert',$("#sync-modal")).remove();
	});
	$(".sync-bt").click(function(){
		var msgHolder = $(this).closest('.modal-body');
		$("#loading_image").removeClass('hide');
		//return false;
		$.ajax({
			url: "index.php?dispatch=utils.sync&mode="+$(this).attr('id'),
		}).done(function(response) {
			result = $.parseJSON(response);
			var msg = "";
			var reload = false;
			if(result.error){
				msg = '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>'+result.message+'</div>';
			}else{
				msg = '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>Process started successfully</div>';
				reload = true;
			}
			$("#loading_image").addClass('hide');
			$(".alert",msgHolder).remove();
			msgHolder.prepend(msg);
			if(reload){
				window.location.reload(true);
			}
			console.log(response);
		});
	});
});