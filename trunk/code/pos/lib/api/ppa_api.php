<?php 
		function ppa_api($details,$data,$request_type){
            global $CARD_RESPONSE_ARRAY;
			$return = array('error'=>false,'message'=>'','data'=>array());
    		$username = $details["username"];
    		$password = $details['password'];
    		$authorization = 'Basic '.base64_encode("$username:$password");
    		$tuCurl = curl_init();
            $txn_type = '';
    		if(empty($data['card_number'])){
        		$return['error'] = true;
        		$return['message'] = 'Please Provide Card No';

    		}else if(empty($data['amount'])){
         		$return['error'] = true;
         		$return['message'] = 'Please Provide Amount';
    		}else{
    			$card_number = $data['card_number'];
                if($request_type==PPA_REDEEM){
                  $amount = '-'.$data['amount'];
                }elseif($request_type==LOAD_PPA_CARD){
                  $amount = $data['amount']; 
                  $txn_type = LOAD; 
                }
    			
    			$url = $details['url']."?format=json&card_number=$card_number&amount=$amount";
    			curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER , 0);
    			curl_setopt($tuCurl, CURLOPT_HEADER, 0);
    			curl_setopt($tuCurl, CURLOPT_URL, "$url");
    			curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
    			curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ".$authorization));
    			$tuData = curl_exec($tuCurl); 
    			curl_close($tuCurl); 
    			$return['error'] = false;
                $responseArray = $CARD_RESPONSE_ARRAY;
                $result = json_decode($tuData,true);
               // print_r($result);
                $responseArray['success'] = $result['success'];
                $responseArray['message'] = $result['message'];
                $responseArray['balance'] = $result['balance'];
                $responseArray['card_number'] = $result['card_number'];
                $responseArray['txn_no'] = $result['approval_code'];
                $responseArray['approval_code'] = $result['approval_code'];
                $responseArray['txn_type'] = $txn_type;
                $return['data'] = $responseArray;
            } 
    		return $return;  
    	}















/*if(!curl_errno($tuCurl)){ 
        		$info = curl_getinfo($tuCurl); 
        		echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']; 
    			} else { 
        		echo 'Curl error: ' . curl_error($tuCurl); 
    			} */

?>