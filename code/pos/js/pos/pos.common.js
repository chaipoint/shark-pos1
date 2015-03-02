/*
Please Don't Make any Changes in this File Prior permissson to
	Rakesh Kaswan
	rakeshkaswan8356@gmail.com
	9992749952
*/
/*Reload as Toggle between Network offline and online*/


//setTimeout(,2000);
var url = $.url();
var module = JSON.stringify(url.data.attr.query);

/*if(module!='"dispatch=billing.index"'){
	window.addEventListener("offline", function(e) { window.location.reload(true);})
	window.addEventListener("online", function(e) { window.location.reload(true);})
}*/

$(document).ready(function(){
	if(! navigator.onLine){
		$('#billing_sync').addClass('btn-danger').attr('id','');
		$('#caw_sync').addClass('btn-danger').attr('id','');
		$('#coc').addClass('btn-danger').attr('id','');
		$('#olo').addClass('btn-danger').attr('id','');
	}
	$(document).on('click','#data_sync',function(){
		if(! navigator.onLine){
			alert("No Internet Connection Available");
			return  false;
		}
		data_sync();
		//sales register have handleResponse
	});
	
	//is_shift_running = (is_shift_running==undefined) ? false : is_shift_running;
	
	$('#start_billing').on('click', function(){
		var url = $(this).data('menu');
		//alert(url);
		if(url=='Walk-in'){
			window.location.href = 'index.php?dispatch=billing.index';
		}else if(url =='COC'){
			window.location.href = 'index.php?dispatch=orders.index';
		}else if(url =='OLO'){
			window.location.href = 'index.php?dispatch=olo.index';
		}else if(url =='Pre-Order'){
			window.location.href = 'index.php?dispatch=preorder.index';
		}
	});

	$('#dashboard').click(function(){
		window.location.href = 'index.php?dispatch=dashboard.index';
	});
	$('#walk-in').click(function(){
		window.location.href = 'index.php?dispatch=billing.index';
	});
	$('#reprint_operation').click(function(){
		window.location.href = 'index.php?dispatch=home.report';
	});
	$('#ppc_operation').click(function(){
		window.location.href = 'index.php?dispatch=home.ppc';
	});
	$('#ppa_operation').click(function(){
		window.location.href = 'index.php?dispatch=home.ppa';
	});
	$('#ppa_operation').click(function(){
		window.location.href = 'index.php?dispatch=home.ppa';
	});
	$('#coc').click(function(){
		window.location.href = 'index.php?dispatch=orders.index';
	});
	$('#olo').click(function(){
		window.location.href = 'index.php?dispatch=orders.olo';
	});
	$('#caw').click(function(){
		window.location.href = 'index.php?dispatch=caw.index';
	});
	
	
	$('#shift_data').click(function(){
		$('.alert-danger').addClass('hide');
		$('#dashboard_div').addClass('hide');
		$('#reconcilation_div').removeClass('hide');
		$('#report_div').addClass('hide');
	});
	$('#report_data').click(function(){
		$('.alert-danger').addClass('hide');
		$('#dashboard_div').addClass('hide');
		$('#reconcilation_div').addClass('hide');
		$('#report_div').removeClass('hide');
	});
	
	if($('#shift_end').hasClass('alert-danger')){
		$('#change_store').show();
		$('#dashboard').hide();
		
	}else{
		$('#change_store').hide();
		$('#dashboard').show();
	}
	
	
	$(function(){
		window.setInterval(function(){
		 var result = checkInternet();
		 if(result==true){
			$('.payment-type-bt[data-value="ppc"]').attr('disabled',false);
			$('.payment-type-bt[data-value="ppa"]').attr('disabled',false);
		 }else{
			$('.payment-type-bt[data-value="ppc"]').attr('disabled',true);
			$('.payment-type-bt[data-value="ppa"]').attr('disabled',true);
		 }
		},1000);
	});


	/*For Login Concept*/
	$(".require_valid_user").click(function(){
		var id = $(this).attr('id');
		var param = (url.param('dispatch')).split('.');
		if(is_login_allowed && param[0] != 'sales_register'){
			$('#login_holder_home .alert').hide();
			$('#login_holder_home').modal('show');
			$('input[name="validateFor"]','#login_holder_home form').val(id);
			$('#username').getkeyboard().destroy();
			$('#password').getkeyboard().destroy();
			$('#username, #password').cKeyboard();
		}else{
			var func = window[id];
			func();
		}
	});


	$(".sync-bt").click(function(){ 
		var msgHolder = $(this).closest('.modal-body');
		$("#loading_image").removeClass('hide');
		$("#ajaxfadediv").addClass('ajaxfadeclass');
		$.ajax({
			url: "index.php?dispatch=utils.sync&mode="+$(this).attr('id'),
			timeout:120000,
		}).done(function(response) {
			$("#ajaxfadediv").removeClass('ajaxfadeclass');
			result = $.parseJSON(response);
			var msg = "";
			var reload = false;
			if(result.error){
				msg = '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>'+result.message+'</div>';
			}else{
				msg = '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>'+result.message+'</div>';
				reload = true;
			}
			$("#loading_image").addClass('hide');
			$(".alert",msgHolder).remove();
			msgHolder.prepend(msg);
			if(reload){
				window.location.reload(true);
			}
			console.log(response);
		}).error(function(x, t, m){
			$("#ajaxfadediv").removeClass('ajaxfadeclass');
			if(t==='timeout'){
				console.log('timeout');
				bootbox.alert('Sorry Timeout Occured! Please Conatact Admin', function(){
					$("#sync-modal").modal('hide');
				});
			}

		});
	});
	
	
	$("#billing_sync").click(function(){
		//alert('hello'); //return false;
		$("#loading_image").removeClass('hide');
		$("#ajaxfadediv").addClass('ajaxfadeclass');
		$.ajax({
			url: "index.php?dispatch=utils.sync&mode=billing_sync_bt",
			timeout:120000,
		}).done(function(response) {
			//alert(response);
			$("#ajaxfadediv").removeClass('ajaxfadeclass');
			result = $.parseJSON(response);
			var reload = false;
			if(result.error){
				bootbox.alert(result.message);
			}else{
				reload = true;
			}
			$("#loading_image").addClass('hide');
			if(reload){
				window.location.reload(true);
			}
			console.log(response);
		}).error(function(x, t, m){
			$("#ajaxfadediv").removeClass('ajaxfadeclass');
			if(t==='timeout'){
				console.log('timeout');
				bootbox.alert('Sorry Timeout Occured! Please Conatact Admin', function(){
					//$("#sync-modal").modal('hide');
				});
			}

		});
	});
	
	$(document).on('click', '#caw_sync',function(){ 
		var store = $(this).data('store_id');
		$("#ajaxfadediv").addClass('ajaxfadeclass');
		$.ajax({
			url: "download/download.php?param=updateSingleStore-"+store,
			timeout:120000,
		}).done(function(response) {
		//alert(response); 
			$("#ajaxfadediv").removeClass('ajaxfadeclass');
			result = $.parseJSON(response);
			var reload = false;
			if(result.error){
				bootbox.alert(result.message);
			}else{
				reload = true;
			}
			if(reload){
				window.location.reload(true);
			}
			
		}).error(function(x, t, m){
			$("#ajaxfadediv").removeClass('ajaxfadeclass');
			if(t==='timeout'){
				console.log('timeout');
				bootbox.alert('Sorry Timeout Occured! Please Conatact Admin', function(){
					//$("#sync-modal").modal('hide');
				});
			}

		});
	});



/* Logout ConfirmBox */
		$('#logout').on('click', function(event){
			event.preventDefault();
			bootbox.confirm("Are you sure to Logout?", function(result) {
            if(result==true){
            	window.location.href='index.php?dispatch=login.out';
            }else{
            	window.location.reload(true);            	
            }
		});

		});		

		/*$(document).ajaxSuccess(function() {
 		  	if(module!='"dispatch=billing.index"'){ 
 		  		$('.btn').attr('disabled', false);
 		  	}
 		});

		$(document).ajaxError(function() {
 		  	bootbox.alert('Unable To Process.Please Try Another Method');
			$("span#loading_image").addClass('hide');
		});

		$(document).ajaxSend(function() { 
			if(module!='"dispatch=billing.index"'){
 		  		$('.btn').attr('disabled', true);
 		  	}
		});*/
});

var createDataTable = function (path,table,footerRow,filterRow) { //alert(filterRow); 
    var media_path = path;
    var iDisplay = 5;
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
		if(filterRow!=undefined){
			$("#"+filterRow+" th").each( function ( i ) {				
					if(i==8 || i==9 || i==10 || i==11 || i==12 || i==13 || i==14)
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
				url: action,
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

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

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
					complete : function(response){
						if($.isFunction(reqOptions.callback)){
							var result = $.parseJSON(response.responseText);
							if(result.error){
								bootbox.alert(result.message);
							}else{
								reqOptions.callback(result);
							}

						}
					}	
			});
	}	
	return xhr;

};
function error(response, status){
	console.log('Response From AJAX Call');
	console.log(response);
	console.log('Status From AJAX Call');
	console.log(status);
}
/*Function Block Ends For AJAX Related Calls*/

$.fn.cKeyboard = function(){
	var element = ($(this).selector).split(',');
	var returnEle = {};
	$.each(element, function(index,value){
		value = $.trim(value);
		var options = new Object();
			options.tabNavigation = true;
			options.enterNavigation = true;

			options.restrictInput = true;
			options.preventPaste = true;
			options.initialFocus = true;
			options.autoAccept =false;
			options.usePreview = false;

			//options.appendLocally=true;
			options.visible = function(event, keyboard, input){
					keyboard.$preview[0].select();
					keyboard.lastCaret.start = 0;
					keyboard.lastCaret.end = (keyboard.$preview[0].value).length;
			}

			switch(value){
				case '#username':
				case 'input[name="username1"]':
					options.layout = 'caustom';
					options.customLayout = {
							'default':['M T F 0 1 2 3 4 5 6 7 8 9 {Bksp}','{accept} {cancel}']
						};
					break;
				case '#discount_input_box':
					options.layout = 'caustom';
					options.usePreview = true;
					options.customLayout = {
							'default':['1 2 3 {clear}','4 5 6 .','7 8 9 0','{accept} {cancel}']
						};
					options.beforeClose = function(e,keyboard,el,accepted){
						if(accepted){
							var dis = parseFloat(keyboard.$preview[0].value);
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
							if(paid < Math.ceil($totalAmountWT.toFixed(0))){
								bootbox.alert('Paid amount is less than payable amount',function(){
									popupKeyboard['#paid-amount'].reveal();
								});
								$("#balance").text('')
								return false;
							}else{
								var balance = paid - Math.ceil($totalAmountWT.toFixed(0));
								$("#balance").text( isNaN(balance) ? 0 : balance );
							}
						}
					};			
					break;
				case '#counter_no':
					options.layout = 'caustom';
					options.customLayout = {
							'default':['1 2 3 4','{clear} {bksp} {accept} {cancel}']
						};					
					break;
				case '#phone_number':
				case 'input[name="mobile_no"]':
				case 'input[name="amount"]':
				case 'input[name="original_card_no"]':
				case '#petty_cash':
				case '.bill_qty_input':
				case '.cash-qty':
				case '.sodex':
				case '.tr':
				case '.total-ticket':
				case '#ppc' :
				case '#expense_amount' :
				case '#petty_cash_end':
				case '#box_cash':
				case '#opening_box_cash':
				case '#box_cash_end':
				case '#inward_amount':
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
function db_error(message){ alert
	if(message){
		var msg = message;
	}else{
		var msg = 'OOPS! Some Problem11 Please Contact Admin.';
	}
	bootbox.dialog({message:'<div class="text-center text-danger">'+msg+'</div>'});
}
function decimalAdjust(value, exp) {
		var type = 'round';
		// If the exp is undefined or zero...
		if (typeof exp === 'undefined' || +exp === 0) {
			return Math[type](value);
		}
		value = +value;
		exp = +exp;
		// If the value is not a number or the exp is not an integer...
		if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
			return NaN;
		}
		// Shift
		value = value.toString().split('e');
		value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
		// Shift back
		value = value.toString().split('e');
		return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
}
function toggleRepBT (){
	if(is_rep_running){
		$("#billing_sync_bt").addClass('hidden')
		$("#billing_stop_sync_bt").removeClass('hidden')
	}else{
		$("#billing_stop_sync_bt").addClass('hidden')
		$("#billing_sync_bt").removeClass('hidden')		
	}
}

function checkInternet(){
	if(navigator.onLine === true) {
		return true;
	}else{
		return false;
	}
}

  var beep = (function () {
    var ctx = new(window.audioContext || window.webkitAudioContext);
    return function (duration, type, finishedCallback) {

        duration = +duration;

        // Only 0-4 are valid types.
        type = (type % 5) || 0;

        if (typeof finishedCallback != "function") {
            finishedCallback = function () {};
        }

        var osc = ctx.createOscillator();

        osc.type = type;

        osc.connect(ctx.destination);
        osc.noteOn(0);

        setTimeout(function () {
            osc.noteOff(0);
            finishedCallback();
        }, duration);

    };
})();

function printBill(responce_array, p){
//alert(p);
responce_array = $.trim(responce_array);
var responce_array1=$.parseJSON(responce_array);
bill_array=responce_array1.data;
var flag=0;
if(bill_array.card_number){
flag=1;
}
//alert(flag);
var company="Mountain Trail Foods Pvt. Ltd."; 
var companyAddress="#10\/1 2nd floor PT Street Basvangudi";
var companyRegion="Bangalore-560004";
var companyPhone="";
var companyTin="TIN:1234567890123456";
var companySTN="STN:1234567890123456";
var breakLine="--------------------------------------------------------------------------<br />";

// -------------------------------------------------------store Detail
var store_detail="";
//if(flag==1){
//store_detail+="<p >CHAIPOINT</p>";
//store_detail+="<p >Mobifly - Delhi</p>";
//store_detail+=breakLine;
//}else{
store_detail+="<p >CHAIPOINT</p>";
store_detail+="<p >"+bill_array.store_name+" - "+bill_array.location_name+"</p>";
store_detail+=breakLine;
//}


//-------------------------------------------------------------Invoice Detail
//alert(JSON.stringify(bill_array));
var currentTime = new Date();
var month = currentTime.getMonth() + 1;
var day = currentTime.getDate();
var year = currentTime.getFullYear();
var dte=month + "-" + day + "-" + year;
var tme = currentTime.getHours() + ":"+ currentTime.getMinutes() + ":" + currentTime.getSeconds();
var invoice_detail="";
//if(flag==0){
invoice_detail+="<table align='center' style='width:100%;text-align:left;'><tr ><td >Invoice No: "+((bill_array.bill === 'undefined')?"":bill_array.bill)+"</td><td style='text-align:right;' >Partner:"+((bill_array.staff_id === 'undefined')?"":bill_array.staff_id)+"</td></tr>";
invoice_detail+="<tr ><td>Date:"+dte+"</td><td style='text-align:right;'>Time:"+tme+"</td></tr>";
invoice_detail+="</table>";
invoice_detail+=breakLine;
//}else{
//invoice_detail+="<table align='center' style='width:100%;text-align:left;'><tr ><td >Invoice No: "+((bill_array.bill === 'undefined')?"":bill_array.bill)+"</td><td style='text-align:right;' >Partner:"+'301'+"</td></tr>";
//invoice_detail+="<tr ><td>Date:"+dte+"</td><td style='text-align:right;'>Time:"+tme+"</td></tr>";
//invoice_detail+="</table>";
//invoice_detail+=breakLine;
//}
//----------------------------------------------------------------------------------Thank company
var companyDetail1="";
companyDetail1+="THANK YOU. VISIT AGAIN<br/>"
companyDetail1+=breakLine;
companyDetail1+="Registered Office <br/>";
companyDetail1+="<table align='center' style='width:100%;text-align:left;'>";
companyDetail1+="<tr ><td style='text-align:left;'>"+company+"</td></tr>";
companyDetail1+="<tr ><td style='text-align:left;'>"+companyAddress+"</td></tr>";
companyDetail1+="<tr ><td style='text-align:left;'>"+companyRegion+"</td></tr>";
companyDetail1+="<tr ><td style='text-align:left;'>"+companyPhone+"</td></tr>";
companyDetail1+="<tr ><td style='text-align:left;'>"+companyTin+"</td></tr>";
companyDetail1+="<tr ><td style='text-align:left;'>"+companySTN+"</td></tr>";
companyDetail1+="</table>";
companyDetail1+=breakLine;
if((bill_array.store_message).length>0){
companyDetail1+=bill_array.store_message+"<br/>";
companyDetail1+=breakLine;
}
//------------------------------------------------------------------------------Card Detail Reciept
var card_detail="";
if(flag==1){
card_detail+="<p style='text-align:center;line-height:8px;margin-top:0px;'>Transaction successful.</p>";
card_detail+=breakLine;
card_detail+="<table align='center' style='width:100%;text-align:left;'>";
card_detail+="<tr ><td style='text-align:center;'>"+(bill_array.card_type).toUpperCase()+" "+((bill_array.txn_type).substr(0,1).toUpperCase()+(bill_array.txn_type).substr(1).toLowerCase())+"</td></tr>";
card_detail+="<tr ><td style='text-align:left;'  >Card No: "+bill_array.card_number+"</td></tr>";
card_detail+="<tr ><td style='text-align:left;'  >Txn No: "+bill_array.txn_no+"</td></tr>";
card_detail+="<tr ><td style='text-align:left;'  >Balance: "+bill_array.balance+"(Reward points will be added)</td></tr>";
card_detail+="</table>";
card_detail+=breakLine;
}else if((bill_array.card.type).toUpperCase()=='PPC' || (bill_array.card.type).toUpperCase()=='PPA' ){
card_detail+="<table align='center' style='width:100%;text-align:left;'>";
card_detail+="<tr ><td style='text-align:left;'>Card Type: "+(bill_array.card.type).toUpperCase()+"</td><td style='text-align:right;' >Txn Type:"+((bill_array.card.txn_type).substr(0,1).toUpperCase()+(bill_array.card.txn_type).substr(1).toLowerCase())+"</td></tr>";
card_detail+="<tr ><td style='text-align:left;' colspan='2' >Card No: "+bill_array.card.no+"</td></tr>";
card_detail+="<tr ><td style='text-align:left;' colspan='2' >Txn No: "+bill_array.card.txn_no+"</td></tr>";
card_detail+="<tr ><td style='text-align:left;' colspan='2' >"+((bill_array.card.txn_type).substr(0,1).toUpperCase()+(bill_array.card.txn_type).substr(1).toLowerCase())+" Amount: "+bill_array.card.redeem_amount+"</td></tr>";
card_detail+="<tr ><td style='text-align:left;' colspan='2' >Balance: "+bill_array.card.balance+"</td></tr>";
card_detail+="</table>";
card_detail+=breakLine;
}
//---------------------------------------------------------------------Item detail
var item_detail="";
//if(((bill_array.card.type).toUpperCase()=='PPC' || (bill_array.card.type).toUpperCase()=='PPA' )){
if(flag==0){
item_detail+="<table align='center' style='width:100%;text-align:left; boredr-botton:1px solid #000;' ><tr><th style='width:50%;'>ITEM</th><th style='text-align:right;width:20%;'>QTY</th><th style='text-align:right;width:30%;'>AMOUNT</th></tr>";
 item_data=bill_array.items;
 item_detail+="</table>"+breakLine+"<table align='center' style='width:100%;text-align:left;' >";
 for (var tmp in item_data) {
  item_detail+="<tr><td style='width:50%;'>"+item_data[tmp].name+"</td><td style='text-align:right;width:20%;'>"+item_data[tmp].qty+"</td><td style='text-align:right;width:30%;'>"+parseFloat(item_data[tmp].subTotal).toFixed(2)+"</td></tr>";
  }
item_detail+="</table>";
item_detail+=breakLine;
}
//alert(item_detail);
//--------------------------------------------------------------------------------Bill details
bill_array.discountAmount=0;
var bill_details="";
if(flag==0){
bill_details+="<table align='center' style='width:100%;text-align:left;'>";
bill_details+="<tr ><td style='text-align:right;'>Sub Total: "+(parseFloat(bill_array.sub_total)).toFixed(2)+"</td></tr>";
if(parseFloat(bill_array.total_discount)>0){
bill_details+="<tr ><td style='text-align:right;'>Discount(-): "+parseFloat(bill_array.total_discount).toFixed(2)+"</td></tr>";
}
bill_details+="<tr ><td style='text-align:right;'>Net: "+parseFloat(parseFloat(bill_array.sub_total)-parseFloat(bill_array.total_discount)).toFixed(2)+"</td></tr>";
bill_details+="<tr ><td style='text-align:right;'>VAT(+): "+parseFloat(bill_array.total_tax).toFixed(2)+"</td></tr>";
if(parseFloat(bill_array.round_off)>0){
bill_details+="<tr ><td style='text-align:right;'>Rounding Off: "+parseFloat(bill_array.round_off).toFixed(2)+"</td></tr>";
}
bill_details+="<tr ><td style='text-align:right;'>Total: "+parseFloat(bill_array.due_amount).toFixed(2)+"</td></tr>";
bill_details+="</table>";
bill_details+=breakLine;
}
//  -----------------------------------------------------------------------Customer Detail
var customer_detail="";
if(flag==0){
if(bill_array.customer.type=="coc"){
var order_type="COC";
if(bill_array.booking_channel_name=="OLO"){
order_type="OLO";
}
customer_detail+=" CHAI ON CALL ORDER("+order_type+")<br/>";
customer_detail+="<table align='center' style='width:100%;text-align:left;'>";
customer_detail+="<tr ><td style='text-align:left;'>Order No: "+bill_array.order_no+" / "+(bill_array.time.created).slice(-8)+"</td></tr>";
customer_detail+="<tr ><td style='text-align:left;'>Packed At: "+tme+"</td></tr>";
customer_detail+="<tr ><td style='text-align:left;'>Company Name: "+bill_array.customer.company_name+"</td></tr>";
customer_detail+="<tr ><td style='text-align:left;'>Customer Name: "+bill_array.customer.name+"</td></tr>";
customer_detail+="<tr ><td style='text-align:left;'>Phone: "+bill_array.customer.phone_no+"</td></tr>";
customer_detail+="<tr ><td style='text-align:left;'>City: "+bill_array.customer.city+"</td></tr>";
customer_detail+="<tr ><td style='text-align:left;'>Locality: "+bill_array.customer.locality+"</td></tr>";
customer_detail+="<tr ><td style='text-align:left;'>Landmark: "+bill_array.customer.land_mark+"</td></tr>";
customer_detail+="</table>";
customer_detail+=breakLine;
}}
//------------------------------------------------------------------------------CAW Detail
var caw_detail="";
if(flag==0){
if(bill_array.payment_type=="caw"){
caw_detail+=" CHAI AT WORK <br />";
caw_detail+="<table align='center' style='width:100%;text-align:left;'>";
caw_detail+="<tr ><td style='text-align:left;'>Company Name: "+bill_array.customer.company_name+"</td></tr>";
caw_detail+="<tr ><td style='text-align:left;'>Phone: "+bill_array.customer.phone_no+"</td></tr>";
caw_detail+="<tr ><td style='text-align:left;'>Address: "+bill_array.customer.address+"</td></tr>";
caw_detail+="<tr ><td style='text-align:left;'>DC Challan: "+bill_array.customer.challan_no+"</td></tr>";
caw_detail+="</table>";
caw_detail+=breakLine;
}}
//------------------------------------------------------------------------------Duplicate Reciept
var reprint1="";
if(p==true){
reprint1+="<p style='text-align:center;' >DUPLICATE COPY</p>";
}

var billingDetail="<div style='width:240px;text-align:center;border:0px solid #333;font-size:10px;line-height:10px;' >";
billingDetail+=store_detail;
billingDetail+=invoice_detail;

if(flag==0){
billingDetail+=item_detail;
billingDetail+=bill_details;
}
billingDetail+=card_detail;
billingDetail+=customer_detail;
billingDetail+=caw_detail;
billingDetail+=companyDetail1;
billingDetail+=reprint1;
billingDetail+="</div>";

//alert(JSON.stringify(bill_array.customer));
//alert(bill_array.customer.name);
//alert(billingDetail);
$("#printBill").html(billingDetail); 
      var divElements = $("#printBill").html();
            var oldPage = document.body.innerHTML;
            document.body.innerHTML = 
              "<html><head><title></title><style type='text/css' media='print'>	p {line-height:10px;}</style></head><body>" + 
              divElements + "</body></html>";
				window.print();	
			document.body.innerHTML = oldPage;
			window.location.href = window.location;
            
			
			//return true;
}

