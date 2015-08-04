//window.addEventListener("offline", function(e) {alert("offline");})
//window.addEventListener("online", function(e) {alert("online");})
//navigator.onLine
$(document).ready(function(){
			$('#username,#password').cKeyboard();
			$("#error_message").hide();

			$(document).on('submit',"#loginform, .store_shift",function(event){ 
				event.preventDefault(); 	//Privents Form to Submit
				var form = $(this);
				var formID = form.attr('id');
				//alert(formID);
				var errorHolder = 'error_message_shift';
				
				if( formID == 'loginform'){
					var errorHolder = 'error_message';
				}
				$("#"+errorHolder).hide();
				var msg = "";
				var formData = form.serializeObject();
				//alert(JSON.stringify(formData));
				if( formID == 'loginform'){
					
					/*$.ajax({
				  		type: 'POST',
				  		url: "http://cp-os.com/cpos/api/coc/coc_api.php",
				  		data : {'action':"getCurrentDate"},
				  		timeout:5000
					}).done(function(response) {
						console.log(response);
						var $result = $.parseJSON(response);
						var serverDate = $result.data;
						var systemDate = new Date();
						var month = systemDate.getMonth() + 1;
						month = (month < 10 ? '0' : '') + month;
						var day = systemDate.getDate();
						var year = systemDate.getFullYear();
						var systemDate = year+"-"+month+"-"+day;
						if(new Date(serverDate) > new Date(systemDate) || new Date(serverDate) < new Date(systemDate)){
							bootbox.alert('Incorrect Date');
							var valid = false;
						} alert(valid)
					});*/ 
					//var curret = new Date();
					//var server = new Date(formData.current_time);
					//var diffMs = (server.getTime() - curret.getTime()); 
					//var diffMins = Math.round(diffMs / 60000); // minutes
					var systemDate = new Date();
						var month = systemDate.getMonth() + 1;
						month = (month < 10 ? '0' : '') + month;
						var day = systemDate.getDate();
						day = (day < 10 ? '0' : '') + day;
						var year = systemDate.getFullYear();
						var systemDate = year+"-"+month+"-"+day;//alert(systemDate);
						var serverDate = formData.current_time;//alert(serverDate); return false;
					if(new Date(serverDate) > new Date(systemDate) || new Date(serverDate) < new Date(systemDate)){
						bootbox.alert('System And Server Date Is Mismatch', function(){
							window.location.reload(true);
						});
						return false;
					}
					
				}
				if(formData.username1!=undefined){ 
					formData.username = formData.username1;
				}
				if(formData.username.trim() == ""){msg += "<li>Provide Username</li>";}
				if(formData.password.trim() == ""){msg += "<li>Provide Password</li>"}
					
				switch(formID){
						case 'store_day_start_form':
							formData.mode = 'day_start';
							if((formData.petty_cash).trim() == ""){ msg += "<li>Provide Petty Cash</li>";}
							break;
						case 'store_shift_start_form':
							formData.mode = 'shift_start';
							if((formData.counter_no).trim() == ""){msg += "<li>Provide Counter No</li>";}
							break;						
						case 'store_shift_end_form':
							formData.mode = 'shift_end';
							if((formData.petty_cash_end).trim() == ""){msg += "<li>Provide Petty Cash</li>";}
							//if((formData.box_cash).trim() == ""){msg += "<li>Provide Box Cash</li>";}
							if((formData.cash_denomination).trim() == ""){msg += "<li>Provide Cash Denomination</li>";}
							//if(formData.box_cash!=formData.cash_denomination){msg += "<li>Cash Denomination Should be equal to Box Cash</li>";}
							break;						
						case 'store_day_end_form':
							formData.mode = 'day_end';
							if((formData.box_cash_end).trim() == ""){msg += "<li>Provide Box Cash</li>";}
							break;
				}					
				if(msg){ 
					$("#"+errorHolder).show();$("#"+errorHolder+" ul").html(msg);
				}else{
					$.ajax({
				  		type: 'POST',
				  		url: "index.php?dispatch=login.validate",
				  		data : formData

					}).done(function(response) {
						var $res =  $.parseJSON(response); //Parse result of response
						if($res.error){ //If Their Exists any problem in login then show errors
							$("#"+errorHolder).show();$("#"+errorHolder+" ul").html($res.message);
						}else{
							$('input[name="username"]',form).val('');
							$('input[name="password"]',form).val('');
							var fun = window[formData.validateFor];
							fun($res.data);
						}
					})
				}
			});
});
function login(data){
	window.location = data.redirect;
}