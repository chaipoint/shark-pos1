//window.addEventListener("offline", function(e) {alert("offline");})
//window.addEventListener("online", function(e) {alert("online");})
//navigator.onLine
$(document).ready(function(){
			$('#username,#password').cKeyboard();
			$("#error_message").hide(); //Hide Error Message Block on Load of Content
			//console.log($("#username","#loginform"));
			$("#loginform").submit(function(event){
				var form = $(this);
				var url = $.url();
				var param = url.param('dispatch') ? url.param('dispatch').split('.') : '.';

				
				var username = $(this).find("#username").val();
				var password = $(this).find("#password").val();
				var msg = "";
				if(username.trim() == ""){msg += "<li>Provide Username</li>";}
				if(password.trim() == ""){msg += "<li>Provide Password</li>"}
				if(msg){
					$("#error_message").show();
					$("#error_message ul").html(msg);
				}else{
					$("#error_message").hide();
					$("#error_message ul").html("");

					var dataObj = new Object();
					dataObj.username = username;
					dataObj.password = password;
					dataObj.validateFor = (param[0] == 'staff') ? 'shift' : 'user';
					if(param[0] == 'staff'){
						dataObj.petty_cash = $(this).find("#petty_cash").val();	
						dataObj.mode = $('#shift_nav li.active a').attr('id');						
					}
					$.ajax({
				  		type: 'POST',
				  		url: "index.php?dispatch=login.validate",
				  		data : dataObj

					}).done(function(response) {
						var $res =  $.parseJSON(response); //Parse result of response
						console.log(response);
						if($res.error){ //If Their Exists any problem in login then show errors
							msg = $res.message; 
							$("#error_message").show();$("#error_message ul").html(msg);
						}else{
							if(param[0] == 'staff'){
								handleResponse($res);
								//form.hide();
								//var sForm = $('form:last',form.parent());
								//$('form:last',form.parent()).removeClass('hide').end().find('input[type="text"]').focus();
								//sForm.show();
							}else{
								window.location = $res.data.redirect; //Transfer to Next Page After setting session and login																
							}
						}
					});
				}
				event.preventDefault(); 	//Privents Formto Submit
			});
});