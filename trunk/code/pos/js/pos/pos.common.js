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
									trs += '<td class="text-center">'+(details[subIndex] ?  details[subIndex] : 0)+'</td>';
									total += (details[subIndex] ?  details[subIndex] : 0);
									if(subIndex in sumPaymenyTypes){
										sumPaymenyTypes[subIndex] += (details[subIndex] ?  details[subIndex] : 0);
									}else{
										sumPaymenyTypes[subIndex] = (details[subIndex] ?  details[subIndex] : 0);
									}
								});
                            	trs += '<td class="text-center">'+total+'</td></tr>';
                            	sumTotal += total;
                            });
                            console.log(sumPaymenyTypes);
							trh += '<tr><th></th>';
							$.each(result.data.payment_type,function(index,details){
								trh += '<th>'+index+'</th>';
								tfs += '<th class="text-center">'+(sumPaymenyTypes[index] ? sumPaymenyTypes[index] : 0)+'</th>';
							});
							trh += '<th class="text-center">Total</th></tr>';
							$("#today-sale-table thead").html(trh);
							$("#today-sale-table tbody").html(trs);
							$("#today-sale-table tfoot").html('<tr class="success"><th>Total</th>'+tfs+'<th class="text-center">'+sumTotal+'</th></tr>');

	//				}
				} 
			});
		});
		//---END--- Todays Sale Request

		$(document).ajaxSuccess(function() {
 		  $('.btn').attr('disabled', false);
		});

		$(document).ajaxError(function() {
 		  bootbox.alert('Error In Ajax Request!Please Contact Admin');
		});

		$(document).ajaxSend(function() { 
 		  $('.btn').attr('disabled', true);
		});
});

var createDataTable = function (path) { 
    var media_path = path;
    var oTable=null;
		if(oTable!=null){
			oTable.destroy();
		}
		
		oTable = $('#fileData').DataTable({
			"dom": 'T<"H"lfr>t<"F"ip>',
			"tableTools": {
					"sSwfPath": media_path+"/swf/copy_csv_xls_pdf.swf",		
					"aButtons": ["csv",{
					"sExtends": "pdf",
					"sPdfOrientation": "landscape",
					"sPdfMessage": "Your custom message would go here."
					},"print"	]
			},						
			paging: true,
			"jQueryUI": true,
			"bSort": true,
		});
}

$(document).on('keydown.autocomplete', ".autocomplete", autocomplete);
function autocomplete(){ 
	var action,name,strict;
	action = $(this).attr('action');
	target = $(this).attr('target');
	strict = $(this).attr('strict');
	if($(this).attr('ac-target')){
		target = $(this).attr('ac-target');
	}
	var params = $(this).attr('acparams');
	var elem = $('input[name='+target+']');
	if(elem.length==0){
		elem = $("<input type='hidden' name='"+target+"'>");
		$(this).after(elem);	
	}
	$(this).autocomplete({
		source: function( request, response ) {		
			var data = {
					maxRows: 10,
					token: request.term,
					action : action
				};
			if(params){
				p1 = $(params);
				p1.each(function(){
					data[$(this).attr('name')]=$(this).val();
				});				
			}			
			xhr = 	$.ajax({
				url: "index.php?dispatch=orders.getStaff",
				data: data,
				success: function( data, status ) {
					if(data){
						response( $.map( eval(data), function( item ) {
							return {
								label: item.label, value: item.value, id:item.id, desc :item.name
							}
						}));
					}
					xhr=null;
				}
		});
		},
		open : function(){elem.val('');},
		minLength: 3,
		autoFocus: true,
		selectFirst: true,
		select : function(event, ui){
			elem.val(ui.item.id);
			$('#'+$(this).attr('target')).val(ui.item.id);
			$('#'+$(this).attr('display')).val(ui.item.desc );
		},
		delay: 0,
		change : function(event, ui){
			if(ui.item){
				elem.val(ui.item.id);
				$('#'+$(this).attr('target')).val(ui.item.id);
			}else{ 
				if(strict != "false" && strict != false){
					$(this).val("");					
				}
				elem.val("");
				$('#'+$(this).attr('display')).val('');
			}
		},
		close: function(a,b){
			if(elem.val()==""){
				if(strict != "false" && strict != false){
					$('#'+$(this).attr('display')).val('');
					$(this).val("");
				}
			}
			
		}
	});	 
}

