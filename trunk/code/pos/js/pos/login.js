//window.addEventListener("offline", function(e) {alert("offline");})
//window.addEventListener("online", function(e) {alert("online");})
//navigator.onLine
$(document).ready(function(){
			$('#username,#password').cKeyboard();
			$("#error_message").hide(); //Hide Error Message Block on Load of Content
			//console.log($("#username","#loginform"));
			$("#loginform").submit(function(event){

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

					$.ajax({
				  		type: 'POST',
				  		url: "index.php?dispatch=login.validate",
				  		data : {username:username,password:password}

					}).done(function(response) {
						var $res =  $.parseJSON(response); //Parse result of response
						console.log(response);
						if($res.error){ //If Their Exists any problem in login then show errors
							msg = $res.message; 
							$("#error_message").show();$("#error_message ul").html(msg);
						}else{
							window.location = $res.data.redirect; //Transfer to Next Page After setting session and login
						}
					});
				}
				event.preventDefault(); 	//Privents Formto Submit
			});
});