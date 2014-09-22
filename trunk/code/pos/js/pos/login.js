//window.addEventListener("offline", function(e) {alert("offline");})
//window.addEventListener("online", function(e) {alert("online");})
//navigator.onLine
$(document).ready(function(){
			$('#username,#password').cKeyboard();
			$("#error_message").hide();
			$("#error_message_modal").hide(); //Hide Error Message Block on Load of Content
			//console.log($("#username","#loginform"));
			$("#loginform, .store_shift").submit(function(event){
				$("#error_message").hide();
				$("#error_message_modal").hide();
				var form = $(this);
				var url = $.url();
				var param = url.param('dispatch') ? url.param('dispatch').split('.') : '.';

				var username = $('input[name="identity"]',form).val();
				var password = $('input[name="password"]',form).val();


				var msg = "";
				if(username.trim() == ""){msg += "<li>Provide Username</li>";}
				if(password.trim() == ""){msg += "<li>Provide Password</li>"}
					var dataObj = new Object();
					dataObj.username = username;
					dataObj.password = password;
					switch(param[0]){
						case 'home':
							dataObj.validateFor = 'shift';
							break;
						case 'sales_register':
							dataObj.validateFor = 'sales_register';
							break;
						default:
						dataObj.validateFor = 'user';
					}
					switch($(this).attr('id')){
						case 'store_day_start_form':
							dataObj.petty_cash = $(this).find("#petty_cash").val();	
							dataObj.mode = 'day_start';
							if((dataObj.petty_cash).trim() == ""){msg += "<li>Provide Petty Cash</li>";}
							break;
						case 'store_shift_start_form':
							dataObj.counter_no = $('input#counter_no',this).val();
							dataObj.mode = 'shift_start';
							if((dataObj.counter_no).trim() == ""){msg += "<li>Provide Counter No</li>";}
							break;						
						case 'store_shift_end_form':
							dataObj.petty_cash = $('input#petty_cash_end',this).val();
							dataObj.box_cash = $('input#box_cash',this).val();
							dataObj.mode = 'shift_end';
							if((dataObj.petty_cash).trim() == ""){msg += "<li>Provide Petty Cash</li>";}
							if((dataObj.box_cash).trim() == ""){msg += "<li>Provide Box Cash</li>";}
							break;						
						case 'store_day_end_form':
							dataObj.box_cash = $('input#box_cash_end',this).val();
							dataObj.mode = 'day_end';
							if((dataObj.box_cash).trim() == ""){msg += "<li>Provide Box Cash</li>";}
							break;
						default:
						if(dataObj.validateFor == 'shift'){
							dataObj.validateFor = 'cash_reconciliation';
						}
					}					

				if(msg){
					if(dataObj.validateFor == 'cash_reconciliation'){
						$("#error_message_modal").show();$("#error_message_modal ul").html(msg);
					}else{
						$("#error_message").show();$("#error_message ul").html(msg);
					}
				}else{
					$("#error_message").hide();
					$("#error_message ul").html("");
					$.ajax({
				  		type: 'POST',
				  		url: "index.php?dispatch=login.validate",
				  		data : dataObj

					}).done(function(response) {
						var $res =  $.parseJSON(response); //Parse result of response
						if($res.error){ //If Their Exists any problem in login then show errors
							msg = $res.message; 
							if(dataObj.validateFor == 'cash_reconciliation'){
								$("#error_message_modal").show();$("#error_message_modal ul").html(msg);
							}else{
								$("#error_message").show();$("#error_message ul").html(msg);
							}
						}else{
							if(dataObj.validateFor == 'user'){
								window.location = $res.data.redirect; //Transfer to Next Page After setting session and login
							}else if (dataObj.validateFor == 'shift') {
								staffHandleResponse($res)
							}else if (dataObj.validateFor == 'cash_reconciliation') {
								CashRHandleResponse($res)
							}else{
								handleResponse($res);								
							}
							$('input[name="identity"]',form).val('');
							$('input[name="password"]',form).val('');
						}
					});
				}
				event.preventDefault(); 	//Privents Formto Submit
			});
});