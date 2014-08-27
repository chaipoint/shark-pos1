<?php 

function call_api($data,$status){
	/* SMS Gateway Setting */
    $SMSURL = 'http://secure.boancomm.net/boansms/boansmsinterface.aspx';
    $postData = array(
                'Mobileno' => urlencode($data['To']),
                'Smsmsg' => $data['Body'],
                'Uname' => urlencode('chaipoint'),
                'Pwd' => urlencode('chaipoint14sms'),
                'PID' => urlencode('392')
                );
    
    // Send Using Curl While We Can Send using Ajax Also
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $SMSURL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData
                ));

            $output = curl_exec($ch);
            curl_close($ch);
            //echo $output;

    $url = "http://boancomm.net/";
	$username = "chaipoint";
	$password = "chaipoint14sms";
	$project_id = '392';
}
