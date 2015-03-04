<style>
@-webkit-keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
@-moz-keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
@-o-keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
.con {
    -webkit-animation: blink 1s;
    -webkit-animation-iteration-count: infinite;
    -moz-animation: blink 1s;
    -moz-animation-iteration-count: infinite;
    -o-animation: blink 1s;
    -o-animation-iteration-count: infinite;
    height: 40px;
    margin-left: 5px;
    margin-top: 1px;
}

.con1 {
    -webkit-animation: blink 1s;
    -webkit-animation-iteration-count: infinite;
    -moz-animation: blink 1s;
    -moz-animation-iteration-count: infinite;
    -o-animation: blink 1s;
    -o-animation-iteration-count: infinite;
    height: 40px;
    margin-left: -6px;
    margin-top: 1px;
}

.inactive {
	height: 40px;
	margin-left: -6px;
    margin-top: 1px;
}

</style>
<script src="../js/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $(function(){
		window.setInterval(function(){
		 	var result = checkInternet();
			if(result==true){ 
				$('#internetYes').show();
				$('#internetNo').hide();
			}else{
				$('#internetYes').hide();
				$('#internetNo').show();
			}
		},1000);
	});
    
    
        $(function(){
            window.setInterval(function(){ 
			var host = 'http://'+"<?php echo $_SERVER['HTTP_HOST']; ?>";
			if(navigator.onLine){
                $.ajax({
                    type: 'POST',
                    url: host+'/pos/index.php?dispatch=orders.getNewOrder',
					//url: 'http://54.178.189.25/cpos/api/coc/coc_api.php',
                    data: {'action':'getCocOrder'},
                    timeout:6000
                }).done(function(response){  
                    console.log(response);
					//alert(response);return false;
                    var $res = $.parseJSON(response);
                    if($res.data['cocOrder']){
                        beep(20000,3);
                        $('#cocYes').show();
						$('#cocNo').hide();
                    }else {
						$('#cocNo').show();
						$('#cocYes').hide();
					}
					
					if($res.data['oloOrder']){
						beep(20000,3);
                        $('#oloYes').show();
						$('#oloNo').hide();
					}else{ //alert('sdf');
						$('#oloNo').show();
						$('#oloYes').hide();
					}
                }).error(function(x, t, m){
                    console.log(t);
                    return false;
                })
			}
            },30000);
        });
		
		
});

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
</script>
<div style="">
	<span style="display:none;display:none;float:left;" id="internetYes"><img src="../images/internet_active.png" class="con"></span>	
	<span style="display:none;float:left;" id="internetNo"><img src="../images/internet_inactive.png" class="inactive"></span>
	<span style="display:none;float:left;margin:0px 54px;" id="oloYes"><img src="../images/active_olo.png" class="con1"></span>
	<span style="display:none;float:left;margin:0px 54px;" id="oloNo"><img src="../images/inactive_olo.png" class="inactive"></span>	
	<span style="display:none;float:left;" id="cocYes"><img src="../images/active_coc.png" class="con1"></span>
	<span style="display:none;float:left;" id="cocNo"><img src="../images/inactive_coc.png" class="inactive"></span>
</div>


	