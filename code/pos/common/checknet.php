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
    
   // beep();
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
					var $res = $.parseJSON(response);
                    if($res.data['cocOrder']){
                        $('#cocYes').show();
						$('#cocNo').hide();
						beep();
                    }else {
						$('#cocNo').show();
						$('#cocYes').hide();
					}
					
					if($res.data['oloOrder']){
						$('#oloYes').show();
						$('#oloNo').hide();
						beep();
					}else{ 
						$('#oloNo').show();
						$('#oloYes').hide();
					}
                }).error(function(x, t, m){
                    console.log(t);
                    return false;
                })
			}
            },10000); // 30 second
        });
		
		$(function(){
            window.setInterval(function(){ 
			var host = 'http://'+"<?php echo $_SERVER['HTTP_HOST']; ?>";
			if(navigator.onLine){
                $.ajax({
                    type: 'POST',
					url: host+'/pos/download/download.php?param=checkRepDoc',
					timeout:6000
                }).done(function(response){  
                    console.log(response);
					//alert(response);return false;
                    //var $res = $.parseJSON(response);
                }).error(function(x, t, m){
                    console.log(t);
                    return false;
                })
			}
            },3600000); // 1 hour
        });
		
		
});

	function checkInternet(){ 
		if(navigator.onLine === true) {
			return true;
		}else{
			return false;
		}
	}

    //if you have another AudioContext class use that one, as some browsers have a limit
var audioCtx = new (window.AudioContext || window.AudioContext || window.audioContext);

//All arguments are optional:

//duration of the tone in milliseconds. Default is 500
//frequency of the tone in hertz. default is 440
//volume of the tone. Default is 1, off is 0.
//type of tone. Possible values are sine, square, sawtooth, triangle, and custom. Default is sine.
//callback to use on end of tone
function beep(duration, frequency, volume, type, callback) {
    var oscillator = audioCtx.createOscillator();
    var gainNode = audioCtx.createGain();

    oscillator.connect(gainNode);
    gainNode.connect(audioCtx.destination);

    if (volume){gainNode.gain.value = volume;};
    if (frequency){oscillator.frequency.value = frequency;}
    if (type){oscillator.type = type;}
    if (callback){oscillator.onended = callback;}

    oscillator.start();
    setTimeout(function(){oscillator.stop()}, (duration ? duration : 10000));
};
</script>
<div style="">
	<span style="display:none;display:none;float:left;" id="internetYes"><img src="../images/internet_active.png" class="con"></span>	
	<span style="display:none;float:left;" id="internetNo"><img src="../images/internet_inactive.png" class="inactive"></span>
	<span style="display:none;float:left;margin:0px 54px;" id="oloYes"><img src="../images/active_olo.png" class="con1"></span>
	<span style="display:none;float:left;margin:0px 54px;" id="oloNo"><img src="../images/inactive_olo.png" class="inactive"></span>	
	<span style="display:none;float:left;" id="cocYes"><img src="../images/active_coc.png" class="con1"></span>
	<span style="display:none;float:left;" id="cocNo"><img src="../images/inactive_coc.png" class="inactive"></span>
</div>


	