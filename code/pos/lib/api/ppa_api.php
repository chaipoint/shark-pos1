<?php 
		function ppa_api($details, $data, $request_type, $invoiceNumber){
			global $CARD_RESPONSE_ARRAY, $config;
            $responseArray = $CARD_RESPONSE_ARRAY;
			$return = array('error'=>false,'message'=>'','data'=>array());
			if(empty($_SESSION['user']['store']['id'])){
				$result = $this->getSessionData();
				if($result['error']){
					$return['error'] = true;
					$return['message'] = $result['message'];
					return json_encode($return);
				}
			}
    		$username = $details['username'];
    		$password = $details['password'];
    		$authorization = 'Basic '.base64_encode("$username:$password");
    		$tuCurl = curl_init();
            $txn_type = '';

            if($request_type==REWARD_REDEMPTION || $request_type==REWARD_CHECK){
                $code = $data['code'];
                $markused = $data['markused'];
                $url = $details['url']."redeem/$code/?format=json&markused=$markused";
                curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER , 0);
                curl_setopt($tuCurl, CURLOPT_HEADER, 0);
                curl_setopt($tuCurl, CURLOPT_URL, "$url");
                curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ".$authorization));
                $tuData = curl_exec($tuCurl); 
                curl_close($tuCurl); 
                $result = json_decode($tuData,true);
                
                $responseArray['success'] = $result['success'];
                $responseArray['message'] = $result['message'];
                $responseArray['product_id'] = $result['redemption_value'];
                $responseArray['invoice_number'] = $invoiceNumber;
                $responseArray['txn_type'] = BALANCE_CHECK;
                $return['data'] = $responseArray;
                return $return;

            }else{ 
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
    			
					$url = $details['url']."card/balanceupdate/?format=json&card_number=$card_number&amount=$amount";
    			    curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER , 0);
    			    curl_setopt($tuCurl, CURLOPT_HEADER, 0);
    			    curl_setopt($tuCurl, CURLOPT_URL, "$url");
    			    curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
    			    curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ".$authorization));
    			    $tuData = curl_exec($tuCurl); 
					curl_close($tuCurl); 
    			    $result = json_decode($tuData,true);
					$responseArray['success'] = (!empty($result) ? $result['success'] : 'False');
                    $responseArray['message'] = (!empty($result) ? $result['message'] : 'Server Error! Please Contact Admin');
                    $responseArray['balance'] = $result['balance'];
                    $responseArray['card_number'] = $result['card_number'];
                    $responseArray['txn_no'] = $result['approval_code'];
                    $responseArray['approval_code'] = $result['approval_code'];
                    $responseArray['invoice_number'] = $invoiceNumber;
                    $responseArray['txn_type'] = $txn_type;
					$responseArray['card_type'] = 'PPA';
					$responseArray['store_message'] = $_SESSION['user']['store']['store_message'];
					$responseArray['store_name'] = $_SESSION['user']['store']['name'];
					$responseArray['staff_id'] = $_SESSION['user']['mysql_id'];
					$responseArray['location_name'] = $_SESSION['user']['location']['name'];
					$mode = ($config['billing_mode']=='local' ? 'L' : 'C');
					$responseArray['bill'] = $_SESSION['user']['store']['code']."".$_SESSION['user']['counter']."".str_pad($invoiceNumber, 4 , '0', STR_PAD_LEFT)."".$mode;
					
                    $return['data'] = $responseArray;
                } 
    		return $return;
            }  
    	}















/*if(!curl_errno($tuCurl)){ 
        		$info = curl_getinfo($tuCurl); 
        		echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']; 
    			} else { 
        		echo 'Curl error: ' . curl_error($tuCurl); 
    			} */

?>