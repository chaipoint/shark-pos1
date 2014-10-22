<?php
	require_once 'SVClient/SVProperties.php';
	require_once 'SVClient/GCWebPos.php';
	require_once 'SVClient/SVServerData.php';
	
class PpcAPI {
		public static function initialize(){
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
}
function balanceINQ($details){
	$return = array();
	$return['error'] = false;
	if(!is_array($details) || count($details) == 0){
		$return['error'] = true;
		$return['msg'] = "Incorrect Parameter Supplied";
		return $return;
	}
	
	$configDetails = PpcAPI::initialize();
	if( $configDetails->getErrorCode() != SVStatus::SUCCESS){
		$return['error'] = true;
		$return['msg'] = $configDetails->getErrorMessage();
		return $return;
	}
	$cardNo = $details['card_number'];
	$pin = '';
	$notes = 'ChaiPoint Order Transcation On '.Date("d/m/Y H:i:s");
	$trackData = '';
	$microTime = microtime();
	$microTimeArr = explode(" ", $microTime);
	$txnId = substr($microTimeArr[1],5).round($microTimeArr[0] * 1000) ;
	
	$svRequest = GCWebPos::balanceEnquiry($configDetails, $cardNo, $pin, $txnId, $trackData, $notes);
	$svResponse = $svRequest->execute();
	if($svResponse->errorCode != 0){
		$return['error'] = true;
		$return['msg'] = $svResponse->errorMessage;
		return $return;
	}
	$return['balance'] = $svResponse->params['Amount'];
	return $return;
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

	$configDetails = PpcAPI::initialize();
	if( $configDetails->getErrorCode() != SVStatus::SUCCESS){
		$return['error'] = true;
		$return['msg'] = $configDetails->getErrorMessage();
		return $return;
	}
	
	//list($first, $second) = explode('=', $details['card_number']);
	//$card_no = str_replace(';', '', $first);
	$cardNumber = $details['card_number'];//$card_no;//
	$cardPin = '134784';
	$notes = 'ChaiPoint Order Transcation On '.Date("d/m/Y H:i:s");
	$trackData = $details['card_number'];
	$invoiceNumber = '1';
	$amount = $details['amount'];
	$approvalCode = '120';
	$billAmount = $details['amount'];
	$microTime = microtime();
	$microTimeArr = explode(" ", $microTime);
	$transactionId = substr($microTimeArr[1],5).round($microTimeArr[0] * 1000) ;
	if($request_type==PPC_REDEEM){
		$svRequest = GCWebPos::redeem($configDetails, $cardNumber, $cardPin, $transactionId, $invoiceNumber, $amount, $trackData, $notes, $billAmount);
	}elseif($request_type==PPC_LOAD) {
		$svRequest = GCWebPos::load($configDetails, $cardNumber, $transactionId, $amount, $invoiceNumber, $cardPin, $trackData, $notes, '');
	}
	//$svRequest = GCWebPos::activate($configDetails, $cardNumber, $invoiceNumber, $transactionId, $amount, 'alam', 'tanveer', '8882355910', $trackData, '', '');
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
		$return['data'] = $responseArray;
	}
	
	return $return;
}
?>
