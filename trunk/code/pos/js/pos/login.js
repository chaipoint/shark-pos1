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
				var errorHolder = 'error_message_shift';
				if( formID == 'loginform'){
					var errorHolder = 'error_message';
				}
				$("#"+errorHolder).hide();
				var msg = "";
				var formData = form.serializeObject();
				alert(JSON.stringify(formData));//return false;
				//var diffDays = Math.round(diffMs / 86400000); // minutes
				if( formID == 'loginform'){
					var curret = new Date();
					var server = new Date(formData.current_time);
					var diffMs = (server.getTime() - curret.getTime()); 
					var diffMins = Math.round(diffMs / 60000); // minutes
					if(diffMins > 5){
						bootbox.alert('System And Server Time Is Mismatch');
						return false;
					}
				}
				if(formData.username1){
					formData.username = formData.username1;
				}
				if(formData.username.trim() == ""){msg += "<li>Provide Username</li>";}
				if(formData.password.trim() == ""){msg += "<li>Provide Password</li>"}

				switch(formID){
						case 'store_day_start_form':
							formData.mode = 'day_start';
							if((formData.petty_cash).trim() == ""){msg += "<li>Provide Petty Cash</li>";}
							break;
						case 'store_shift_start_form':
							formData.mode = 'shift_start';
							if((formData.counter_no).trim() == ""){msg += "<li>Provide Counter No</li>";}
							break;						
						case 'store_shift_end_form':
							formData.mode = 'shift_end';
							if((formData.petty_cash_end).trim() == ""){msg += "<li>Provide Petty Cash</li>";}
							if((formData.box_cash).trim() == ""){msg += "<li>Provide Box Cash</li>";}
							if((formData.cash_denomination).trim() == ""){msg += "<li>Provide Cash Denomination</li>";}
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