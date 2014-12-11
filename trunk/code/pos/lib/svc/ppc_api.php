<?php
	require_once 'SVClient/SVProperties.php';
	require_once 'SVClient/GCWebPos.php';
	require_once 'SVClient/SVServerData.php';
	
class PpcAPI extends App_config {
		private $configData;
		public function initialize(){
				$configResult = $this->getConfig($this->cDB, array('ppc_api'));
			    $this->configData = (count($configResult['data']) > 0) ? $configResult['data'] : array();
			    $configData = $this->configData['ppc_api'];
				
				$svp = new SVProperties();
				$svp->setServerURL($configData['url']);
				$svp->setForwardingEntityId($configData['entity_id']);
				$svp->setForwardingEntityPassword($configData['entity_password']);
				$svp->setTerminalId($configData['terminal_id']);
				$svp->setUsername($configData['username']);
				$svp->setPassword($configData['password']);

				$svp = GCWebPos::initLibrary($svp); 
				return $svp;
		}

/*Function To Perform Redeem, Load, Activate, Card Issue, Balance Check Operation  */
function ppcOperation($details, $request_type){ 
	global $CARD_RESPONSE_ARRAY;
	$responseArray = $CARD_RESPONSE_ARRAY;
	$return = array('error'=>false, 'message'=>'', 'data'=>array());
	
	if(!is_array($details) || count($details) == 0){
		$return['error'] = true;
		$return['message'] = "Incorrect Parameter Supplied";
		return $return;
	}

	//$res = $this->timeOutCancellation(); print_r($res);die();

	$getConfigDetails = $this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getView(PPC_DETAIL_DESIGN_DOCUMENT_VIEW_INITIALIZE_DETAIL)->setParam(array('include_docs'=>'true','key'=>'"'.date('Y-m-d').'"'))->execute();
	if(array_key_exists('rows', $getConfigDetails) && count($getConfigDetails['rows'])>0){ 
		$config = $getConfigDetails['rows'][0]['doc']['details']['params'];
		$batchNo = $getConfigDetails['rows'][0]['doc']['details']['params']['CurrentBatchNumber'];
		$svp = new SVProperties();
		$svp->params = $config ;
		$configDetails = $svp;
		
	}else{
		$configDetails = $this->initialize();
		if( $configDetails->getErrorCode() != SVStatus::SUCCESS){
			$return['error'] = true;
			$return['message'] = $configDetails->getErrorMessage();
			return $return;
		}else{
			$data = array();
			$data['cd_doc_type'] = LAST_INITIALIZE_DOC_TYPE ;
			$data['time'] = $this->getCDTime();
			$data['details'] = $configDetails;
			$r = $this->cDB->saveDocument()->execute($data);
			
		} 
	}

	$trackData = $details['card_number'];
	if(strpos($trackData,'=') == false){
		$return['error'] = true;
		$return['message'] = "Incorrect Card Number";
		return $return;
	}
	list($first, $second) = explode('=', $trackData); // To Get Card Number
	$cardNumber = str_replace(';', '', $first);
	$cardPin = '';
	$notes = 'SHARK POS Transaction On '.Date("d/m/Y H:i:s");
	$invoiceNumber = '';
	if($request_type!=BALANCE_CHECK_PPC_CARD){
		$invoiceNumber = $this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getUpdate(PPC_DETAIL_DESIGN_DOCUMENT_UPDATE_GET_BILL_NO,'generateppcBill')->setParam(array('date'=>$this->getCDate()))->execute();;
	}
	$amount = $details['amount'];
	$approvalCode = '';
	$billAmount = $details['amount'];
	$microTime = microtime();
	$microTimeArr = explode(" ", $microTime);
	$transactionId = substr($microTimeArr[1],5).round($microTimeArr[0] * 1000) ;
	$txn_type = '';
	
	if($request_type==PPC_REDEEM){ 
		$svRequest = GCWebPos::redeem($configDetails, $cardNumber, $cardPin, $transactionId, $invoiceNumber, $amount, $trackData, $notes, $billAmount);
		$txn_type = REDEEM;
	
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
	
	if($request_type!=BALANCE_CHECK_PPC_CARD){
		$this->createLastBillDoc($cardNumber, $amount, $invoiceNumber, $transactionId, $txn_type);	
	}

	if($svResponse->errorCode != 0){
		$responseArray['success'] = 'False';
		$responseArray['message'] = $svResponse->errorMessage;
		$responseArray['balance'] = ($svResponse->params['ResponseMessage']=="Balance is insufficient." ? '0' : '');
		$return['data'] = $responseArray;
		if($request_type!=BALANCE_CHECK_PPC_CARD){
			$this->updateLastBillDoc($svResponse->params['TransactionId'], '', $cardNumber, $amount, $invoiceNumber, $txn_type);	
		}
	}else{
		$responseArray['success'] = 'True';
		$responseArray['message'] = $svResponse->params['ResponseMessage'];
		$responseArray['balance'] = ($svResponse->params['ResponseMessage']=="Balance is insufficient." ? '0' : $svResponse->params['Amount']);
		$responseArray['card_number'] = $cardNumber;
		$responseArray['txn_no'] = $svResponse->params['TransactionId'];
		$responseArray['approval_code'] = $svResponse->params['ApprovalCode'];
		$responseArray['txn_type'] = $txn_type;
		$responseArray['invoice_number'] = $invoiceNumber;
		$return['data'] = $responseArray;
		if($request_type!=BALANCE_CHECK_PPC_CARD){
			$this->updateLastBillDoc($svResponse->params['TransactionId'], $svResponse->params['ApprovalCode'], $cardNumber, $amount, $invoiceNumber, $txn_type);	
		}

	}
	
	return $return;
}

/*Function To Create Last PPC BIll Doc*/
function createLastBillDoc($cardNumber, $amount, $invoiceNumber, $transactionId, $txn_type){
	$bill_data = array();
	if(!empty($_SESSION['user']['ppc_bill'])){
		$bill_data['_id'] = $_SESSION['user']['ppc_bill']['_id'];
	}
	$bill_data['status'] = 'False';
	$bill_data['cd_doc_type'] = LAST_PPC__BILL_DOC_TYPE;
	$bill_data['card_no'] = $cardNumber;
	$bill_data['amount'] = $amount;
	$bill_data['invoice_number'] = $invoiceNumber;
	$bill_data['txn_type'] = $txn_type;
	$bill_data['txn_no'] = $transactionId;
	$bill_data['time'] = date('Y-m-d H:i:s');
	$res = $this->cDB->saveDocument()->execute($bill_data);
	
	if(array_key_exists(OK, $res)){
		$_SESSION['user']['ppc_bill']['_id'] = $res['id'];
		$_SESSION['user']['ppc_bill']['_rev'] = $res['rev'];
	}

}

/*Function To Update Last PPC BIll Doc IF Response Is come From QuickSliver*/
function updateLastBillDoc($transactionId, $approval_code, $cardNumber, $amount, $invoiceNumber, $txn_type){
	$bill_data = array();
	if(!empty($_SESSION['user']['ppc_bill'])){
		$bill_data['_id'] = $_SESSION['user']['ppc_bill']['_id'];
		$bill_data['_rev'] = $_SESSION['user']['ppc_bill']['_rev'];
	}
	$bill_data['status'] = 'True';
	$bill_data['cd_doc_type'] = LAST_PPC__BILL_DOC_TYPE;
	$bill_data['card_no'] = $cardNumber;
	$bill_data['amount'] = $amount;
	$bill_data['invoice_number'] = $invoiceNumber;
	$bill_data['txn_type'] = $txn_type;
	$bill_data['txn_no'] = $transactionId;
	$bill_data['approval_code'] = $approval_code;
	$bill_data['time'] = date('Y-m-d H:i:s');
	
	$res = $this->cDB->saveDocument()->execute($bill_data);
	if(array_key_exists(OK, $res)){
		$_SESSION['user']['ppc_bill']['_id'] = $res['id'];
		$_SESSION['user']['ppc_bill']['_rev'] = $res['rev'];
	}
	
}

/*Function To Cancel Last PPC transaction*/
function timeOutCancellation(){
	$return = array('error'=>false, 'message'=>'', 'data'=>array('success'=>'True'));
	
	$resultLastBill = $this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getView(PPC_DETAIL_DESIGN_DOCUMENT_VIEW_LAST_BILL)->setParam(array("descending"=>"true","limit"=>"1","include_docs"=>"true"))->execute();
	if(array_key_exists('rows', $resultLastBill) && count($resultLastBill['rows'])>0){
		$data = $resultLastBill['rows'][0]['doc'];
		if($data['status']!='True'){
			$data['card_number'] = $data['card_no'];
			if($data['txn_type'] == REDEEM){
				$request_type = CANCEL_REDEEM;
			}else if($data['txn_type'] == LOAD){
				$request_type = CANCEL_LOAD;
			}
			$res = $this->cancel($data, $request_type);
			return $res;
		}
	}
	return $return;
}

/*Function To Perform ppc redeem cancel, ppc load cancel, ppc activate cancel*/
function cancel($details, $request_type){ 

	global $CARD_RESPONSE_ARRAY;
	$responseArray = $CARD_RESPONSE_ARRAY;
	$return = array('error'=>false, 'message'=>'');
	
	if(!is_array($details) || count($details) == 0){
		$return['error'] = true;
		$return['message'] = "Incorrect Parameter Supplied";
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
	$notes = 'SHARK POS Transaction On '.Date("d/m/Y H:i:s").'.'.(!empty($details['cancel_reason']) ? $details['cancel_reason'] : '');
	$trackData = $details['card_number'];
	$invoiceNumber = $details['invoice_number'];
	$amount = $details['amount'];
	$approvalCode = (!empty($details['approval_code']) ? $details['approval_code'] : '');
	$txnCode = $details['txn_no'];
	$billAmount = $details['amount'];
	$microTime = microtime();
	$microTimeArr = explode(" ", $microTime);
	$transactionId = substr($microTimeArr[1],5).round($microTimeArr[0] * 1000) ;
	
	if($request_type==CANCEL_REDEEM){
		$svRequest = GCWebPos::cancelRedeem($configDetails, $cardNumber, $invoiceNumber, $txnCode, $batchNo,  $amount, $transactionId, $cardPin, $approvalCode, '', $notes);
	
	}else if($request_type==CANCEL_LOAD){ 
		$svRequest = GCWebPos::cancelLoad($configDetails, $cardNumber, $amount, $invoiceNumber, $txnCode, $batchNo, $transactionId, $cardPin, $approvalCode, '', $notes);
	}
	
	$svResponse = $svRequest->execute();
	
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

/*Function To REISSUE PPC CARD*/
function reIssue($details, $request_type){ 
	global $CARD_RESPONSE_ARRAY;
	$responseArray = $CARD_RESPONSE_ARRAY;
	$return = array('error'=>false, 'message'=>'');
	
	if(!is_array($details) || count($details) == 0){
		$return['error'] = true;
		$return['message'] = "Incorrect Parameter Supplied";
		return $return;
	}

	$getConfigDetails = $this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getView(PPC_DETAIL_DESIGN_DOCUMENT_VIEW_INITIALIZE_DETAIL)->setParam(array('include_docs'=>'true','key'=>'"'.date('Y-m-d').'"'))->execute();
	if(array_key_exists('rows', $getConfigDetails) && count($getConfigDetails['rows'])>0){ 
		$config = $getConfigDetails['rows'][0]['doc']['details']['params'];
		$batchNo = $getConfigDetails['rows'][0]['doc']['details']['params']['CurrentBatchNumber'];
		$svp = new SVProperties();
		$svp->params = $config ;
		$configDetails = $svp;
		
	}else{
		$configDetails = $this->initialize();
		if( $configDetails->getErrorCode() != SVStatus::SUCCESS){
			$return['error'] = true;
			$return['message'] = $configDetails->getErrorMessage();
			return $return;
		}else{
			
			$data = array();
			$data['cd_doc_type'] = LAST_INITIALIZE_DOC_TYPE ;
			$data['time'] = $this->getCDTime();
			$data['details'] = $configDetails;
			$r = $this->cDB->saveDocument()->execute($data);
			
		} 
	}
	
	$trackData = $details['card_number'];
	list($first, $second) = explode('=', $trackData); // to get card number
	$cardNumber = str_replace(';', '', $first);
	$cardPin = '';
	$notes = 'SHARK POS Transaction On '.Date("d/m/Y H:i:s");
	$invoiceNumber = '';
	$microTime = microtime();
	$microTimeArr = explode(" ", $microTime);
	$transactionId = substr($microTimeArr[1],5).round($microTimeArr[0] * 1000) ;
	
	if($request_type==GET_CUSTOMER_INFO){
		$originalCardNo = $details['original_card_no'];
		$firstName = $details['first_name'];
		$lastName = $details['last_name'];
		$phoneNumber = $details['mobile_no'];
		$svRequest = GCWebPos::getCustomerInfoFromCardNumber($configDetails, $originalCardNo, $firstName, $lastName, $phoneNumber);
		$txn_type = GET_CUSTOMER_INFO;
		$svResponse = $svRequest->execute();

		if($svResponse->errorCode != 0){
			$responseArray['success'] = 'False';
			$responseArray['message'] = $svResponse->errorMessage;
			$responseArray['balance'] = ($svResponse->params['ResponseMessage']=="Balance is insufficient." ? '0' : '');
			$responseArray['txn_type'] = $txn_type;
			$return['data'] = $responseArray; 
		}else {
			$microTime = microtime();
			$microTimeArr = explode(" ", $microTime);
			$transactionId = substr($microTimeArr[1],5).round($microTimeArr[0] * 1000) ;
			$originalCardNo = $svResponse->params['CardNumber'];
			$invoiceNumber = $this->cDB->getDesign(PPC_DETAIL_DESIGN_DOCUMENT)->getUpdate(PPC_DETAIL_DESIGN_DOCUMENT_UPDATE_GET_BILL_NO,'generateppcBill')->setParam(array('date'=>$this->getCDate()))->execute() ;
			$svRequest = GCWebPos::reissue($configDetails, $cardNumber, $originalCardNo, $transactionId, $cardPin, $trackData, $invoiceNumber, $notes);
			//$svRequest = GCWebPos::deactivate($configDetails, $cardNumber, $transactionId, $approvalCode, $notes);
			$txn_type = REISSUE_PPC_CARD;
			$svResponse = $svRequest->execute();
			if($svResponse->errorCode != 0){
				$responseArray['success'] = 'False';
				$responseArray['message'] = $svResponse->errorMessage;
				$responseArray['balance'] = ($svResponse->params['ResponseMessage']=="Balance is insufficient." ? '0' : '');
				$responseArray['txn_type'] = $txn_type;
				$return['data'] = $responseArray; 
			}else{
				$responseArray['success'] = 'True';
				$responseArray['message'] = $svResponse->params['ResponseMessage'];
				$responseArray['balance'] = ($svResponse->params['ResponseMessage']=="Balance is insufficient." ? '0' : $svResponse->params['OutstandingBalance']);
				$responseArray['card_number'] = $cardNumber;
				$responseArray['txn_no'] = $svResponse->params['TransactionId'];
				$responseArray['txn_type'] = $txn_type;
				$return['data'] = $responseArray;
			}

		}
	}
	return $return;
}
}
?>
