$(document).ready(function(){

			$("#error_message").hide(); //Hide Error Message Block on Load of Content
	
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




//KEYBORD TO ENTER PAYMENT
			$('#username').keyboard({
				restrictInput:true,
				preventPaste:true,
				autoAccept:false,
				alwaysOpen:false,
				layout:'costom',
				customLayout:{
					'default':['M T F 0 1 2 3 4 5 6 7 8 9 {Bksp}','{accept} {cancel}']
				},
			});
			$('#password').keyboard({
				restrictInput:true,
				preventPaste:true,
				autoAccept:false,
				alwaysOpen:false
//				layout:'costom',
/*				customLayout:{
					"default" : ["` 1 2 3 4 5 6 7 8 9 0 - = {bksp}","{tab} a b c d e f g h i j [ ] \\","k l m n o p q r s ; ' {enter}","{shift} t u v w x y z , . / {shift}","{accept} {space} {cancel}"],shift:["~ ! @ # $ % ^ & * ( ) _ + {bksp}","{tab} A B C D E F G H I J { } |",'K L M N O P Q R S : " {enter}', "{shift} T U V W X Y Z < > ? {shift}","{accept} {space} {cancel}"]
					//'default':['0 1 2 3 4 5 6 7 8 9','a b c d e f g h i j k l m','n o p q r s t u v w x y z','{shift} {Bksp} {accept} {cancel}']
				},/*/
			});




});