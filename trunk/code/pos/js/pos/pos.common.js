/*
Please Don't Make any Changes in this File Prior permissson to
	Rakesh Kaswan
	rakeshkaswan8356@gmail.com
	9992749952
*/
/*Reload as Toggle between Network offline and online*/
window.addEventListener("offline", function(e) { window.location.reload(true);})
window.addEventListener("online", function(e) { window.location.reload(true);})
//setTimeout(,2000);
$(document).ready(function(){
	console.log(navigator.onLine);
	if(! navigator.onLine){
		$('#pos_sync').addClass('btn-danger').attr('id','');

	}
	$(document).on('click','#pos_sync',function(){
		if(! navigator.onLine){
			alert("No Internet Connection Available");
			return  false;
		}
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



/* Logout ConfirmBox */
		$('#logout').on('click', function(event){
			event.preventDefault();
			bootbox.confirm("Are you sure to Logout?", function(result) {
            if(result==true){
            	window.location='index.php?dispatch=login.out';
            }
		});

		});		



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

var createDataTable = function (path,table,footerRow) {  
    var media_path = path;
    var iDisplay = 25;
    var oTable=null;
		if(oTable!=null){
			oTable.destroy();
		}
		
		oTable = $('#'+table).DataTable({
			/*"dom": 'T<"H"lfr>t<"F"ip>',
			"tableTools": {
					"sSwfPath": media_path+"swf/copy_csv_xls_pdf.swf",		
					"aButtons": ["csv",{
					"sExtends": "pdf",
					"sPdfOrientation": "landscape",
					"sPdfMessage": "Your custom message would go here."
					},"print"	]
			},*/

			   "footerCallback": function ( row, data, start, end, display ) { 
			   	if(footerRow!=undefined){
			   		var footerArray = [];
			   		footerArray = footerRow;
			   		var counter = footerArray.length;
           			var api = this.api(), data;
 					
 				// Remove the formatting to get integer data for summation
            		var intVal = function ( i ) {
                	return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            	};
 
		            // Total over all pages
		            for (var i = 0; i < counter; i++) { 
		            data = api.column( footerArray[i] ).data();
            		total = data.length ?
                	data.reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                	}) : 0;
                	
 
		            // Total over this page
		            
        		    data = api.column( footerArray[i], { page: 'current'} ).data();
            		pageTotal = data.length ?
                	data.reduce( function (a, b) {
                        return (intVal(a) + intVal(b)).toFixed(2);
                	}) : 0;
                	
 
		            // Update footer
		            
        		    $( api.column( footerArray[i] ).footer() ).html(
                	''+pageTotal +' ('+ total +' Total)'
            	);
        		};
        }},						
			paging: true,
			"jQueryUI": true,
			"iDisplayLength" : iDisplay,
			"bSort": true,
		});
		$("#filter_row th").each( function ( i ) {				
					if(i==7 || i==8 || i==9 || i==10 || i==11 || i==12 || i==13)
					{
					var select = $('<select style="width:90%;"><option value=""></option></select>')
						.appendTo( $(this).empty() )
						.on( 'change', function () {
							oTable.column( i )
								.search($(this).val())
								.draw();
						} );
			
					oTable.column( i ).data().unique().sort().each( function ( d, j ) {
						select.append( '<option value="'+d+'">'+d+'</option>' )
					} );
					}
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
				url: "index.php?dispatch=staff.getStaff",
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

/*Function Block Start For AJAX Related Calls*/

var cAjax = function(reqOptions){
	var xhr = null;
	if(reqOptions){
		xhr =  $.ajax({
					type : (reqOptions.method) ? reqOptions.method : 'GET',
					url : reqOptions.url,
					data : reqOptions.data, 
					success : reqOptions.success,
					error : error,
					complete : function(){
					}	
			});
	}	
	return xhr;

};
function error(response, status){
	consol.log('Response From AJAX Call');
	consol.log(response);
	consol.log('Status From AJAX Call');
	consol.log(status);
}
/*Function Block Ends For AJAX Related Calls*/

$.fn.cKeyboard = function(){
	var element = ($(this).selector).split(',');
	var returnEle = {};
	$.each(element, function(index,value){
		value = $.trim(value);
		var options = new Object();

			options.restrictInput = true;
			options.preventPaste = true;
			options.autoAccept = false;
			options.lockInput = true;
			options.visible = function(event, keyboard, input){
					keyboard.$preview[0].select();
					keyboard.lastCaret.start = 0;
					keyboard.lastCaret.end = (keyboard.$preview[0].value).length;
					//console.log((keyboard.$preview[0].value).length);
			}

			switch(value){
				case '#username':
					options.layout = 'caustom';
					options.customLayout = {
							'default':['M T F 0 1 2 3 4 5 6 7 8 9 {Bksp}','{accept} {cancel}']
						};
					break;
				case '#discount_input_box':
					options.layout = 'caustom';
					options.customLayout = {
							'default':['0 1 2 3 4','5 6 7 8 9','{clear} {bksp} {accept} {cancel}']
						};
					options.beforeClose = function(e,keyboard,el,accepted){
						if(accepted){
							var dis = parseInt(keyboard.$preview[0].value);
							if(!isNaN(dis) && dis<=100){
								$intDiscount =  dis;
								generateSalesTable();
							}else{
								setTimeout(function(){
									$("#discount_input_box").val('');
									popupKeyboard['#discount_input_box'].focusOn();
								},500);
							}
						}else{
							$("#discount-close").trigger('click');
						}
					};
					break;
				case '#paid-amount':
					options.layout = 'caustom';
					options.customLayout = {
							'default':['1 2 3 {clear}','4 5 6 .','7 8 9 0','{accept} {cancel}']
						};	
					options.beforeClose = function(e,keyboard,el,accepted){
						if(accepted){
							var paid = keyboard.$preview[0].value;
							paid = isNaN(paid) ? 0 : paid;
							if(paid < Math.ceil($totalAmountWT)){
								bootbox.alert('Paid amount is less than payable amount',function(){
									popupKeyboard['#paid-amount'].reveal();
								});
								$("#balance").text('')
								return false;
							}else{
								var balance = paid - Math.ceil($totalAmountWT);
								$("#balance").text( isNaN(balance) ? 0 : balance );
							}
						}
					};			
					break;
				case '#phone_number':
					options.layout = 'caustom';
					options.customLayout = {
							'default':['0 1 2 3 4','5 6 7 8 9','{clear} {bksp} {accept} {cancel}']
						};	
					break;
				case '.bill_qty_input':
					options.layout = 'caustom';
					options.customLayout = {
							'default':['0 1 2 3 4','5 6 7 8 9','{clear} {bksp} {accept} {cancel}']
						};					
					break;
				case '#ppc' :
					options.layout = 'caustom';
					options.customLayout = {
							'default':['0 1 2 3 4','5 6 7 8 9','{clear} {bksp} {accept} {cancel}']
						};					
					break;
				case '#expense_amount' :
					options.layout = 'caustom';
					options.customLayout = {
							'default':['0 1 2 3 4','5 6 7 8 9','{clear} {bksp} {accept} {cancel}']
						};					
					break;

			}
		$(value).keyboard(options);
		returnEle[value] = $(value).getkeyboard();
	});
	return returnEle;
}
function db_error(){
	bootbox.dialog({message:'<div class="text-center text-danger">OOPS! Some Problem Please Contact Admin.</div>'});
}
