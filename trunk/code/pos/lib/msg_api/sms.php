<?php

function call_api($data,$status){
   
   $couch = new CouchPHP();
   $configList = $couch->getDesign('config')->getView('config_list')->setParam(array('include_docs'=>'true'))->execute();
   
   $username  = reset($configList['rows'][0]['doc']['sms_username']);
   $password  = reset($configList['rows'][0]['doc']['sms_password']);
   $projectId = reset($configList['rows'][0]['doc']['sms_project_id']);
   $url       = reset($configList['rows'][0]['doc']['sms_url']);

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
            //echo $output;

    $url = "http://boancomm.net/";
	$username = "chaipoint";
	$password = "chaipoint14sms";
	$project_id = '392';
}
