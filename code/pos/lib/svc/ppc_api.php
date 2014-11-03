<?php
	require_once 'SVClient/SVProperties.php';
	require_once 'SVClient/GCWebPos.php';
	require_once 'SVClient/SVServerData.php';
	
class PpcAPI extends App_config {
		public function initialize(){
				$svp = new SVProperties();
				/* $svp->setServerURL("http://cards.qwikcilver.com/eGMS.HTTPProcessor2/TransactionPOST.aspx");
				$svp->setForwardingEntityId('com.chaipoint');
				$svp->setForwardingEntityPassword('c0m.ch@ip0int');
				$svp->setTerminalId('CP:54.249.247.15');
				$svp->setUsername("chaipointwebuser");
				$svp->setPassword('user@w3bp0s4ch@ip0int'); */
				
				$svp->setServerURL("http://qc3.qwikcilver.com/eGMS.HTTPProcessor2/TransactionPOST.aspx");
				$svp->setForwardingEntityId('cpwebposuser');
				$svp->setForwardingEntityPassword('cpwebposuser');
				$svp->setTerminalId('webpos-cp-dev-1');
				$svp->setUsername("cponline");
				$svp->setPassword('welcome');

				$svp = GCWebPos::initLibrary($svp); 
				return $svp;
		}


function redeem($details, $request_type){ 
	global $CARD_RESPONSE_ARRAY;
	$return = array();
	$return['error'] = false;
	if(!is_array($details) || count($details) == 0){
		$return['error'] = true;
		$return['msg'] = "Incorrect Parameter Supplied";
		return $return;
	}

	$getConfigDetails = $this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getView(PPC_DETAIL_DESIGN_DOCUMENT_VIEW_INITIALIZE_DETAIL)->setParam(array('include_docs'=>'true','key'=>'"'.date('Y-m-d').'"'))->execute();
	
	if(array_key_exists('rows', $getConfigDetails) && count($getConfigDetails['rows'])>0){ 
		$config = $getConfigDetails['rows'][0]['doc']['details']['params'];
		$svp = new SVProperties();
		$svp->params = $config ;
		$configDetails = $svp;
		
	}else{
		$configDetails = $this->initialize();
		if( $configDetails->getErrorCode() != SVStatus::SUCCESS){
			$return['error'] = true;
			$return['msg'] = $configDetails->getErrorMessage();
			return $return;
		}else{
			$data = array();
			$data['cd_doc_type'] = LAST_INITIALIZE_DOC_TYPE ;
			$data['time'] = $this->getCDTime();
			$data['details'] = $configDetails;
			$r = $this->cDB->saveDocument()->execute($data);
			
		} 
	}
	
	list($first, $second) = explode('=', $details['card_number']);
	$card_no = str_replace(';', '', $first);
	$cardNumber = $card_no;
	$cardPin = '';
	$notes = 'ChaiPoint Order Transcation On '.Date("d/m/Y H:i:s");
	$trackData = $details['card_number'];
	$invoiceNumber = '1';
	$amount = $details['amount'];
	$approvalCode = '';
	$billAmount = $details['amount'];
	$microTime = microtime();
	$microTimeArr = explode(" ", $microTime);
	$transactionId = substr($microTimeArr[1],5).round($microTimeArr[0] * 1000) ;
	$txn_type = '';
	
	if($request_type==PPC_REDEEM){ 
		$svRequest = GCWebPos::redeem($configDetails, $cardNumber, $cardPin, $transactionId, $invoiceNumber, $amount, $trackData, $notes, $billAmount);
		
	}elseif($request_type==LOAD_PPC_CARD) { 
		$svRequest = GCWebPos::load($configDetails, $cardNumber, $transactionId, $amount, $invoiceNumber, $cardPin, $trackData, $notes, '');
		$txn_type = LOAD;
	
	}elseif($request_type==ACTIVATE_PPC_CARD){  
		$first_name = $details['first_name'];
		$last_name = $details['last_name'];
		$mobile = $details['mobile_no'];
		$svRequest = GCWebPos::activate($configDetails, $cardNumber, $invoiceNumber, $transactionId, $amount, $first_name, $last_name, $mobile, $trackData, '', '');
		$txn_type = ACTIVATE;
	
	}elseif($request_type==BALANCE_CHECK_PPC_CARD){
		$svRequest = GCWebPos::balanceEnquiry($configDetails, $cardNumber, $cardPin, $transactionId, $trackData, $notes);
		$txn_type = BALANCE_CHECK;
	
	}elseif($request_type==ISSUE_PPC_CARD){
		$cardProgramGroupName = $details['card_group_name'];
		$corporateName = $details['corporate_name'];
		$employeeId = $details['empolye_id'];
		$address1 = $details['address'];
		$first_name = $details['first_name'];
		$last_name = $details['last_name'];
		$mobile = $details['mobile_no'];
		$email = $details['email'];
		$gender = $dob = $maritalStatus = $salutation = $address2 = $area = $city = $state = $country = $pinCode = $phoneAlternate = $anniversary = $expiry = '';

		$svRequest = GCWebPos::createAndIssue($configDetails, $cardProgramGroupName, $invoiceNumber, 
            		$transactionId, $amount, $corporateName, $employeeId, $gender, $dob, 
            		$maritalStatus, $salutation, $first_name, $last_name, $address1, $address2, $area, 
            		$city, $state, $country, $pinCode, $mobile, $phoneAlternate, $email, $anniversary, 
            		$notes, $expiry);
	}

	$svResponse = $svRequest->execute();
	//print_r($svResponse);
	$responseArray = $CARD_RESPONSE_ARRAY;
	
	if($svResponse->errorCode != 0){
		$responseArray['success'] = 'False';
		$responseArray['message'] = $svResponse->errorMessage;
		$responseArray['balance'] = ($svResponse->params['ResponseMessage']=="Balance is insufficient." ? '0' : '');
		$return['data'] = $responseArray; 
		
	}else{
		$responseArray['success'] = 'True';
		$responseArray['message'] = $svResponse->params['ResponseMessage'];
		$responseArray['balance'] = ($svResponse->params['ResponseMessage']=="Balance is insufficient." ? '0' : $svResponse->params['Amount']);
		$responseArray['card_number'] = $cardNumber;
		$responseArray['txn_no'] = $svResponse->params['TransactionId'];
		$responseArray['approval_code'] = $svResponse->params['ApprovalCode'];
		$responseArray['txn_type'] = $txn_type;
		$return['data'] = $responseArray;
	}
	
	return $return;
}

function cancel($details, $request_type){ 
	global $CARD_RESPONSE_ARRAY;
	$return = array();
	$return['error'] = false;
	if(!is_array($details) || count($details) == 0){
		$return['error'] = true;
		$return['msg'] = "Incorrect Parameter Supplied";
		return $return;
	}

	$getConfigDetails = $this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getView(PPC_DETAIL_DESIGN_DOCUMENT_VIEW_INITIALIZE_DETAIL)->setParam(array('include_docs'=>'true','key'=>'"'.date('Y-m-d').'"'))->execute();
	
	if(array_key_exists('rows', $getConfigDetails) && count($getConfigDetails['rows'])>0){ 
		$config = $getConfigDetails['rows'][0]['doc']['details']['params'];
		$batchNo = $getConfigDetails['rows'][0]['doc']['details']['params']['CurrentBatchNumber'];
		$svp = new SVProperties();
		$svp->params = $config ;
		$configDetails = $svp;
	}
	
	$cardNumber = $details['card_number'];
	$cardPin = '';
	$notes = 'ChaiPoint Order Transcation On '.Date("d/m/Y H:i:s");
	$trackData = $details['card_number'];
	$invoiceNumber = '1';
	$amount = $details['amount'];
	$approvalCode = $details['approval_code'];
	$txnCode = $details['txn_no'];
	$billAmount = $details['amount'];
	$microTime = microtime();
	$microTimeArr = explode(" ", $microTime);
	$transactionId = substr($microTimeArr[1],5).round($microTimeArr[0] * 1000) ;
	
	if($request_type==CANCEL_REDEEM){
		$svRequest = GCWebPos::cancelRedeem($configDetails, $cardNumber, '1', $txnCode, $batchNo,  $amount, $transactionId, $cardPin, $approvalCode, '', $notes);
	}else if($request_type==CANCEL_LOAD){ 
		$svRequest = GCWebPos::cancelLoad($configDetails, $cardNumber, $amount, '1', $txnCode, $batchNo, $transactionId, $cardPin, $approvalCode, '', $notes);
	}
	$svResponse = $svRequest->execute();
	$responseArray = $CARD_RESPONSE_ARRAY;
	
	if($svResponse->errorCode != 0){
		$responseArray['success'] = 'False';
		$responseArray['message'] = $svResponse->errorMessage;
		$responseArray['balance'] = ($svResponse->params['ResponseMessage']=="Balance is insufficient." ? '0' : '');
		$return['data'] = $responseArray; 
		
	}else{
		$responseArray['success'] = 'True';
		$responseArray['message'] = $svResponse->params['ResponseMessage'];
		$responseArray['balance'] = ($svResponse->params['ResponseMessage']=="Balance is insufficient." ? '0' : $svResponse->params['Amount']);
		$responseArray['card_number'] = $cardNumber;
		$responseArray['txn_no'] = $svResponse->params['TransactionId'];
		$responseArray['approval_code'] = $svResponse->params['ApprovalCode'];
		$return['data'] = $responseArray;
	}
	
	return $return;
}

}
?>
