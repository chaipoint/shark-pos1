$(document).ready(function(){

			$("#error_message").hide(); //Hide Error Message Block on Load of Content
	
			$("#loginform").submit(function(event){ //alert('hi');

				var username = $(this).find("#username").val();
				var password = $(this).find("#password").val();
				//alert(username);
				var msg = "";
				var tempdata={};
				tempdata['username'] = username;
				tempdata['password']  = password;
				
				if(username.trim() == ""){msg += "<li>Provide Username</li>";}
				if(password.trim() == ""){msg += "<li>Provide Password</li>"}
				if(msg){
					$("#error_message").show();
					$("#error_message ul").html(msg);
				}else{
					$("#error_message").hide();
					$("#error_message ul").html("");
                    var d={};
					$.ajax({
				  		type: 'POST',
				  		url: "http://cp-os.com/cpos/api/mobilePOSLogin.php",
				  		data : {'data':JSON.stringify(tempdata),'action':"get_store_list"},
				  		dataTypr:'JSON'

					}).done(function(response) {
						var $res =  $.parseJSON(response); //Parse result of response
						console.log($res);
					if($res.status=='Sucess'){
							window.location='dashboard.php';
						}
						else{
                        $("#error_message").show();
                        msg = "<li style='font-size:12px;'>Wrong Username or Password</li>";
					    $("#error_message ul").html(msg);
						}
						
					});
				}
				event.preventDefault(); 	//Privents Formto Submit
			});
			
			
});