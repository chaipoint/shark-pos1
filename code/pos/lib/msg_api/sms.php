<?php

function call_api($data,$status,$details){
   
  // print_r($details['data']['sms_api']);
  $url = $details['data']['sms_api']['url'];//"http://secure.boancomm.net/boansms/boansmsinterface.aspx";
  $username = $details['data']['sms_api']['username'];//"chaipoint";
  $password = $details['data']['sms_api']['password'];//"chaipoint14sms";
  $projectId = $details['data']['sms_api']['project_id'];//'392';
 //echo $url.$username.$password.$projectId;
    /* SMS Gateway Setting */
    $SMSURL = $url;
    $postData = array(
                'Mobileno' => urlencode($data['To']),
                'Smsmsg' => $data['Body'],
                'Uname' => urlencode($username),
                'Pwd' => urlencode($password),
                'PID' => urlencode($projectId)
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
           // echo $output;

  $url = $details['data']['sms_api']['url'];//"http://secure.boancomm.net/boansms/boansmsinterface.aspx";
	$username = $details['data']['sms_api']['username'];//"chaipoint";
	$password = $details['data']['sms_api']['password'];//"chaipoint14sms";
	$project_id = $details['data']['sms_api']['project_id'];//'392';
}
