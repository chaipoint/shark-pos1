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
    width: 34px;
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
    width: 34px;
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
    
    if(navigator.onLine){
        $(function(){
            window.setInterval(function(){ 
			var host = 'http://'+"<?php echo $_SERVER['HTTP_HOST']; ?>"; 
                $.ajax({
                    type: 'POST',
                    url: host+'/pos/index.php?dispatch=orders.getCocOrder',
                    data: {request_type:'getCOCOrder'},
                    timeout:6000
                }).done(function(response){ 
                    console.log(response);
                    var $res = $.parseJSON(response);
                    if($res.data['newOrder']){
                        beep(20000,3);
                        $('#notification').show();
                    }else{
                        $('#notification').hide();
                    }
                }).error(function(x, t, m){
                    console.log(t);
                    return false;
                })
            },30000);
        });
    }	

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
<span style="display:none" id="internetYes"><img src="../images/ok.png" class="con"></span>
<span style="display:none" id="internetNo"><img src="../images/not_ok.png" class="con"></span>
<span style="display:none" id="notification"><img src="../images/noti.ico" class="con1"></span>


	