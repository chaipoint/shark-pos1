<?php /* This file encoded by Raizlabs PHP Obfuscator http://www.raizlabs.com/software */ ?>
<?php
/*
 * QwikCilver Copyright 2000-2009, QwikCilver Solutions Private Limited. All rights reserved.
*
*/

require_once 'SVUtils.php';
require_once 'SVType.php';
require_once 'SVClient.php';
require_once 'SVIssue.php';
require_once 'SVCreateAndIssue.php';
require_once 'SVCancelIssue.php';
require_once 'SVDeActivate.php';
require_once 'SVLoad.php';
require_once 'SVCancelLoad.php';
require_once 'SVRedeem.php';
require_once 'SVCancelRedeem.php';
require_once 'SVBalanceEnquiry.php';
require_once 'SVGetCustomerInfo.php';
require_once 'SVViewLastTransaction.php';
require_once 'SVGetCorporateInfo.php';
require_once 'SVBatchClose.php';
require_once 'SVBarCodeReader.php';

/**
 * This Class provides the Gift Card related API for a QwikCilver WebPos Client to interface with QwikCilver Server.
 * <BR>
 * This Class provides the same functionality as the existing GCWebPos class with the less overhead of multiple parameters 
 * being passed for every factory method. This is achieved by sending the constructed Array of required parameters.  
 * The keys for the Array elements can come from {@link  SVTags  SVTags} class.
 * <p>
 * This class provides: <BR>
 * - a method to initialize this library <BR>
 * - a set of factory methods to get/create an SVRequest instance pertaining to the operation
 * that needs to be performed <BR>
 * <p>
 * <B>NOTE:</B> The caller must first initialize this library by calling
 * {@link  LCWebPosArrays::initLibrary()  initLibrary()} method
 * before any operation can be performed by this library. This method needs to be called after starting
 * the web server and needs to be called only once.
 * <p>
 * After this library has been initialized the caller calls the appropriate factory method to create
 * an instance of {@link SVRequest SVRequest} that will be used to perform the desired operation.
 * <p>
 * Once an {@link SVRequest SVRequest} instance has been created/obtained the caller can
 * set any optional attributes, as desired, by calling the
 * <B>SVRequest.setValue(String, Object)</B> method <BR>
 * <p>
 * Once the desired attributes have been set the caller must call
 * {@link  SVRequest::execute()  SVRequest.execute()} method to perform the operation. <BR>
 * {@link  SVRequest::execute()  SVRequest.execute()} method will return
 * {@link  SVResponse  SVResponse} object.
 * <p>
 * To check if the operation was performed successfully by QwikCilver Server
 * call {@link  SVResponse::getErrorCode()  SVResponse.getErrorCode()} method.
 * <p>
 * If the return  value of {@link  SVResponse::getErrorCode()  SVResponse.getErrorCode()} method
 * is NOT EQUAL TO {@link  SVStatus::SUCCESS  SVStatus.SUCCESS} then the caller should call
 * {@link  SVResponse::getErrorMessage()  SVResponse.getErrorMessage()} to get a detailed error
 * message on why the operation failed. <BR>
 * <p>
 * If the return  value of {@link  SVResponse::getErrorCode()  SVResponse.getErrorCode()} method
 * is EQUAL TO {@link  SVStatus::SUCCESS  SVStatus.SUCCESS} then the caller should call
 * the appropriate getters in {@link  SVResponse  SVResponse} object to get the
 * data returned by QwikCilver Server after successful execution of this operation.
 *
 */

class LCWebPosArrays {
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * This method is used to initialize the SVClient Library.
	 * This method needs to be called once before using this library.
	 * <p>
	 * The required/mandatory attributes for initializing this library are pre-assigned
	 * by QwikCilver and provided to the Caller.
	 * <p>
	 * The list of mandatory and optional
	 * attributes are: <BR>
	 * 1) serverURL (Mandatory) - Server URL provided by QwikCilver<BR>
	 * 2) forwardingEntityId (Mandatory) - Forwarding Entity Id provided by QwikCilver<BR>
	 * 3) forwardingEntityPassword (Mandatory) - Forwarding Entity Password provided by QwikCilver<BR>
	 * 4) terminalId (Mandatory) - TerminalId is provided by QwikCilver<BR>
	 * 5) username (Mandatory) - username provided by QwikCilver<BR>
	 * 6) password (Mandatory) - password provided by QwikCilver<BR>
	 * 7) connectionTimeout (Optional) - default value is 17 seconds (17000 milliseconds). Actual value is provided by QwikCilver <BR>
	 * 8) transactionTimeout (Optional) - default value is 17 seconds (17000 milliseconds). Actual value is provided by QwikCilver <BR>
	 * <p>
	 * To initialize this library the caller needs to create an instance of
	 * {@link SVProperties  SVProperties} and set the Mandatory attributes and optional
	 * attributes, as needed, and call this method by passing the {@link SVProperties  SVProperties}
	 * instance as a parameter to this method.
	 *
	 * @param
	 * SVProperties - SVProperties instance that contains the attributes for initializing
	 * this library
	 *
	 * @return
	 * SVProperties SVProperties which contains QwikCilver server specific data
	 * To check if the initLibrary() operation was performed successfully by QwikCilver Server
	 * call {@link  SVProperties::getErrorCode()  SVProperties.getErrorCode()} method
	 * and check its return value.
	 * If the return  value of {@link  SVProperties::getErrorCode()  SVProperties.getErrorCode()} method
	 * is NOT EQUAL TO {@link  SVStatus::SUCCESS()  SVStatus.SUCCESS} then the caller should call
	 * {@link  SVProperties::getErrorMessage()  SVProperties.getErrorMessage()} to get a detailed error
	 * message on why the operation failed. <BR>
	 * If the return value is EQUAL TO {@link  SVStatus::SUCCESS  SVStatus::SUCCESS} then save this object
	 *  as it needs to be passed to subsequent calls to perform card related operations.
	 * <BR>
	 *
	 * @throws Exception  If input parameter is null or if any of the Mandatory
	 * attributes are null or empty.
	 */
	public static function initLibrary($webPosProps){
	
		if($webPosProps== null){
			throw new Exception("Input is null");
		}
		if( (SVUtils::isNullOrEmpty($webPosProps->getServerURL())) ||
				(SVUtils::isNullOrEmpty($webPosProps->getForwardingEntityId())) ||
				(SVUtils::isNullOrEmpty($webPosProps->getForwardingEntityPassword())) ||
				(SVUtils::isNullOrEmpty($webPosProps->getTerminalId())) ||
				(SVUtils::isNullOrEmpty($webPosProps->getUsername())) ||
				(SVUtils::isNullOrEmpty($webPosProps->getPassword()))
		)
		{
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		//$webPosProps->setSvType(SVType::WEBPOS_LOYALTY);
		return SVClient::initLibrary($webPosProps, SVType::WEBPOS_LOYALTY);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>Create and Issue</B> operation.
	 *
	 * @param
     * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
     * 
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object. 
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos#createAndIssue  LCWebPos.createAndIssue()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>Create And Issue</B> operation
	 *
	 * @throws Exception If any input parameter that is Mandatory is null or empty
	 */
	public static function createAndIssue($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
		
		$mandatoryParameters = array(SVTags::CARD_PROGRAM_GROUP_NAME, SVTags::AMOUNT, 
				SVTags::TRANSACTION_ID );
		
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
		
		return new SVCreateAndIssue($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>Create and Issue Or Load</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::createAndIssueOrLoad  LCWebPos.createAndIssueOrLoad()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>Create And Issue Or Load</B> operation
	 *
	 * @throws Exception If any input parameter that is Mandatory is null or empty
	 */
	public static function createAndIssueOrLoad($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::CARD_PROGRAM_GROUP_NAME, SVTags::AMOUNT, 
				SVTags::FIRST_NAME, SVTags::LAST_NAME, SVTags::PHONE, SVTags::TRANSACTION_ID );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
		
		$txnMapDataCustSearch = array();
		$txnMapDataCustSearch[SVTags::FIRST_NAME]=$txnMapData[SVTags::FIRST_NAME];
		$txnMapDataCustSearch[SVTags::LAST_NAME]=$txnMapData[SVTags::LAST_NAME];
		$txnMapDataCustSearch[SVTags::PHONE]=$txnMapData[SVTags::PHONE];
		$txnMapDataCustSearch[SVTags::TRANSACTION_ID]=$txnMapData[SVTags::TRANSACTION_ID];
		
		$svRequest = LCWebPosArrays::getCustomerInfo($svProps, $txnMapDataCustSearch);
		$svResponse = $svRequest->execute();
		
		if( $svResponse->getErrorCode() != SVStatus::SUCCESS)
		{
			return new SVCreateAndIssue($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
		}
		else
		{
			$txnMapDataLoad = array();
			$txnMapDataLoad[SVTags::CARD_NUMBER]=$svResponse->getCardNumber();
			$txnMapDataLoad[SVTags::AMOUNT]=$txnMapData[SVTags::AMOUNT];
			$txnMapDataLoad[SVTags::TRANSACTION_ID]=$txnMapData[SVTags::TRANSACTION_ID];
			$txnMapDataLoad[SVTags::INVOICE_NUMBER]=$txnMapData[SVTags::INVOICE_NUMBER];
			$txnMapDataLoad[SVTags::APPROVAL_CODE]=$txnMapData[SVTags::APPROVAL_CODE];
			$txnMapDataLoad[SVTags::NOTES]=$txnMapData[SVTags::NOTES];
			
			return new SVLoad($svProps, SVType::WEBPOS_LOYALTY, $txnMapDataLoad);
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>Activate</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 * 
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::issue  LCWebPos.issue()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>Activate</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function issue($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
		
		$mandatoryParameters = array(SVTags::CARD_NUMBER, SVTags::AMOUNT, SVTags::INVOICE_NUMBER, 
				SVTags::TRANSACTION_ID );
		
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
		
		return new SVIssue($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>BalanceEnquiry</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::balanceEnquiry  LCWebPos.balanceEnquiry()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>BalanceEnquiry</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function balanceEnquiry($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::CARD_NUMBER, SVTags::TRANSACTION_ID );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		return new SVBalanceEnquiry($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>BatchClose</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::batchClose  LCWebPos.batchClose()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>BatchClose</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function batchClose($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::ACTIVATION_COUNT,
					SVTags::ACTIVATION_AMOUNT, SVTags::REDEEM_COUNT,
					SVTags::REDEEM_AMOUNT, SVTags::RELOAD_COUNT,
					SVTags::RELOAD_AMOUNT, SVTags::CANCEL_LOAD_COUNT,
					SVTags::CANCEL_LOAD_AMOUNT, SVTags::CANCEL_REDEEM_COUNT,
					SVTags::CANCEL_REDEEM_AMOUNT, SVTags::CANCEL_ACTIVATION_COUNT,
					SVTags::CANCEL_ACTIVATION_AMOUNT );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		return new SVBatchClose($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>CancelActivate</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::cancelIssue  LCWebPos.cancelIssue()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>CancelActivate</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function cancelIssue($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::CARD_NUMBER, SVTags::ORIGINAL_AMOUNT, 
				SVTags::ORIGINAL_BATCH_NUMBER, SVTags::ORIGINAL_TRANSACTION_ID, SVTags::TRANSACTION_ID );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		return new SVCancelIssue($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>CancelLoad</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::cancelLoad  LCWebPos.cancelLoad()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>CancelLoad</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function cancelLoad($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::CARD_NUMBER, SVTags::ORIGINAL_AMOUNT, 
				SVTags::ORIGINAL_BATCH_NUMBER, SVTags::ORIGINAL_TRANSACTION_ID, SVTags::TRANSACTION_ID );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		return new SVCancelLoad($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>CancelRedeem</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::cancelRedeem  LCWebPos.cancelRedeem()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>CancelRedeem</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function cancelRedeem($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::CARD_NUMBER, SVTags::ORIGINAL_AMOUNT,
				SVTags::ORIGINAL_BATCH_NUMBER, SVTags::ORIGINAL_TRANSACTION_ID, SVTags::TRANSACTION_ID );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		return new SVCancelRedeem($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>Deactivate</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data in HashMap form.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::deactivate  LCWebPos.deactivate()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>Deactivate</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function deactivate($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::CARD_NUMBER, SVTags::TRANSACTION_ID );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		return new SVDeActivate($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>GetCorporateInfo</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::getCorporateInfo  LCWebPos.getCorporateInfo()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>getCorporateInfo</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function getCorporateInfo($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$anyoneParameters = array(SVTags::SEARCH_CORPORATE_NAME, SVTags::SEARCH_CORPORATE_PHONE, 
				SVTags::EXTERNAL_CORPORATE_ID );
	
		if(!SVUtils::anyoneParameterPresent($anyoneParameters, $txnMapData)) {
			throw new Exception("At least one of the Optional attributes MUST have a valid value");
		}
	
		return new SVGetCorporateInfo($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>GetCustomerInfo</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::getCustomerInfo  LCWebPos.getCustomerInfo()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>GetCustomerInfo</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function getCustomerInfo($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$anyoneParameters = array(SVTags::CARD_NUMBER, SVTags::EXTERNAL_CARD_NUMBER, 
				SVTags::CARD_PROGRAM_GROUP_NAME, SVTags::FIRST_NAME, SVTags::LAST_NAME, 
				SVTags::PHONE_NUMBER, SVTags::SALUTATION, SVTags::EMAIL, SVTags::DOB, 
				SVTags::CARD_PROGRAM_ID, SVTags::NOTES );
	
		if(!SVUtils::anyoneParameterPresent($anyoneParameters, $txnMapData)) {
			throw new Exception("At least one of the Optional attributes MUST have a valid value");
		}
	
		return new SVGetCustomerInfo($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>Load</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::load  LCWebPos.load()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>Load</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function load($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::CARD_NUMBER, SVTags::AMOUNT, 
				SVTags::TRANSACTION_ID );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		return new SVLoad($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>Redeem</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::redeem  LCWebPos.redeem()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>Redeem</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function redeem($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::CARD_NUMBER, SVTags::AMOUNT, SVTags::TRANSACTION_ID );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		return new  SVRedeem($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>SetCustomerInfo</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::setCustomerInfo  LCWebPos.setCustomerInfo()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>SetCustomerInfo</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function setCustomerInfo($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::CARD_NUMBER, SVTags::FIRST_NAME,
				SVTags::LAST_NAME, SVTags::PHONE_NUMBER, SVTags::TRANSACTION_ID );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		return new  SVSetCustomerInfo($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this overloaded factory method to create an instance of {@link  SVRequest  SVRequest}
	 * that can be used to perform <B>ViewLastTransaction</B> operation.
	 *
	 * @param
	 * SVProperties  [Mandatory] Data returned by QwikCilver Server in the initLibrary call
	 *
	 * @param
	 * txnMapData  [Mandatory] Transaction data as an Array object.
	 * For more addditional Mandatory and Optional sub parameters within the array can be found in {@link  LCWebPos::viewLastTransaction  LCWebPos.viewLastTransaction()}
	 *
	 * @return
	 * SVRequest - SVRequest instance that should be used to perform <B>ViewLastTransaction</B> operation
	 *
	 * @throws Exception  If any input parameter that is Mandatory is null or empty
	 */
	public static function viewLastTransaction($svProps, $txnMapData)
	{
		if(sizeof($txnMapData) <= 0 || $svProps == null) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		$mandatoryParameters = array(SVTags::CARD_NUMBER, SVTags::TRANSACTION_ID );
	
		if(!SVUtils::manadatoryParametersPresent($mandatoryParameters, $txnMapData)) {
			throw new Exception("One or More of the Mandatory attributes does not have a valid value");
		}
	
		return new SVViewLastTransaction($svProps, SVType::WEBPOS_LOYALTY, $txnMapData);
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this factory method to get the card number from 31 digit barcode.
	 * @param
	 * barcode [Mandatory] 31 digit barcode.
	 * @return
	 * String - 16 digit card number.
	 * @throws
	 * InvalidParameterException - If input parameter that is Mandatory is null or empty
	 * or length of barcode is not 31 digits.
	 */
	public static function getCardNumberFrom31DigitBarCode($barcode)
	{
		if(SVUtils::isNullOrEmpty($barcode) || strlen($barcode) != SVBardCodeReader::BAR_CODE_LENGTH_V1)
			throw new InvalidArgumentException(SVStatus::getMessage(SVStatus::INVALID_PARAM));
		$svRequest = new SVBardCodeReader();
		return $svRequest->getCardNumber($barcode, "BAR_CODE_LENGTH_V1");
	}
	
	////////////////////////////////////////////////////////////////////////////
	/**
	 * Use this factory method to get the card number from 26 digit barcode.
	 * @param
	 * barcode [Mandatory] 26 digit barcode.
	 * @return
	 * String - 16 digit card number.
	 * @throws
	 * InvalidParameterException - If input parameter that is Mandatory is null or empty
	 * or length of barcode is not 26 digits.
	 */
	public static function getCardNumberFrom26DigitBarCode($barcode)
	{
		if(SVUtils::isNullOrEmpty($barcode) || (strlen($barcode) != SVBardCodeReader::BAR_CODE_LENGTH_V2))
			throw new InvalidArgumentException(SVStatus::getMessage(SVStatus::INVALID_PARAM));
		$svRequest = new SVBardCodeReader();
		return $svRequest->getCardNumber($barcode, "BAR_CODE_LENGTH_V2");
	}
}