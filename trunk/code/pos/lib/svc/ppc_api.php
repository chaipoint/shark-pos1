<?php
	require_once 'SVClient/SVProperties.php';
	require_once 'SVClient/GCWebPos.php';
	require_once 'SVClient/SVServerData.php';
	
class PpcAPI {
		public static function initialize(){
				$svp = new SVProperties();
				$svp->setServerURL("http://cards.qwikcilver.com/eGMS.HTTPProcessor2/TransactionPOST.aspx");
				$svp->setForwardingEntityId('com.chaipoint');
				$svp->setForwardingEntityPassword('c0m.ch@ip0int');
				$svp->setTerminalId('CP:54.249.247.15');
				$svp->setUsername("chaipointwebuser");
				$svp->setPassword('user@w3bp0s4ch@ip0int');
				
				/*$svp->setServerURL("http://qc3.qwikcilver.com/eGMS.HTTPProcessor2/TransactionPOST.aspx");
				$svp->setForwardingEntityId('cpwebposuser');
				$svp->setForwardingEntityPassword('cpwebposuser');
				$svp->setTerminalId('webpos-cp-dev-1');
				$svp->setUsername("cponline");
				$svp->setPassword('welcome');/**/
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
	$cardNo = $details['ppc_no'];
	$pin = '';//$details['ppc_no'];
	$notes='ChaiPoint Order Transcation On '.Date("d/m/Y H:i:s");
	$trackData = '';//$details['data'];
	$microTime = microtime();
	$microTimeArr = explode(" ", $microTime);
	$txnId = substr($microTimeArr[1],5).round($microTimeArr[0] * 1000) ;
	
	$svRequest = GCWebPos::balanceEnquiry($configDetails,$cardNo,$pin,$txnId,$trackData,$notes);
	$svResponse = $svRequest->execute();
	if($svResponse->errorCode != 0){
		$return['error'] = true;
		$return['msg'] = $svResponse->errorMessage;
		return $return;
	}
	$return['balance'] = $svResponse->params['Amount'];
	return $return;
}
function redeem($details){
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
	$cardNo = $details['ppc_no'];
	$pin = '';//$details['ppc_no'];
	$notes='ChaiPoint Order Transcation On '.Date("d/m/Y H:i:s");
	$trackData = '';//$details['data'];
	$invoice = $details['order_id'];
	$amt = $details['amount'];
	$billAmount = $details['amount'];
	$microTime = microtime();
	$microTimeArr = explode(" ", $microTime);
	$txnId = substr($microTimeArr[1],5).round($microTimeArr[0] * 1000) ;

	$svRequest = GCWebPos::redeem($configDetails, $cardNo, $pin, $txnId, $invoice, $amt, $trackData,$notes,$billAmount);
	$svResponse = $svRequest->execute();
	if($svResponse->errorCode != 0){
		$return['error'] = true;
		$return['msg'] = $svResponse->errorMessage;
		return $return;
	}
	return $return;
	/*
	 * 	$svRequest = GCWebPos::balanceEnquiry($serverProps,$cardNo,$pin,$txnId,$trackData,$notes);
	$svResponse = $svRequest->execute();
	* */
}
?>
