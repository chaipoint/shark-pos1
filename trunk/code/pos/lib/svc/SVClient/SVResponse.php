﻿<?php /* This file encoded by Raizlabs PHP Obfuscator http://www.raizlabs.com/software */ ?>
<?php    require_once(dirname(dirname(__FILE__)).'/HTTP/Request2/Response.php');  require_once('SVQcsData.php');  require_once('SVTransactionInfo.php');        class SVResponse extends SvQcsData {                        private $R3A575FCD2DDEBFEE1A86E4F2131045B6 = SVStatus::SUCCESS;      private $RE0CB6407394BA2F0F0CF09EDD8BD630A = '';             public function __construct($RD2232E0A2452DEF6DFBECE9564F170D4, $R157A6826A8BF1F36EBBE3DEC02351744 = null, $RC5BF58FE5CEF9C7E5CCB3F0606FA6866 = null) {          $this->errorCode = $RD2232E0A2452DEF6DFBECE9564F170D4;          $this->errorMessage = $R157A6826A8BF1F36EBBE3DEC02351744;          if($RC5BF58FE5CEF9C7E5CCB3F0606FA6866 != null) {              $this->parseHttpResponse($RC5BF58FE5CEF9C7E5CCB3F0606FA6866);                                 }      }        private function parseHttpResponse($RC5BF58FE5CEF9C7E5CCB3F0606FA6866) {                   $RE9AAA1BCBBEE7ADB82820C4198D9BFE3 = $RC5BF58FE5CEF9C7E5CCB3F0606FA6866->getStatus();          if($RE9AAA1BCBBEE7ADB82820C4198D9BFE3 != SVStatus::HTTP_OK)          {              $this->errorCode = SVStatus::SERVICE_UNAVAILABLE_ERROR;              $this->errorMessage = SVStatus::getHttpMessage($RC5BF58FE5CEF9C7E5CCB3F0606FA6866);              return;          }                   $R4DE86CA7A871DCFC0AE70F81ED3E4209 = $RC5BF58FE5CEF9C7E5CCB3F0606FA6866->getBody();          if( ($R4DE86CA7A871DCFC0AE70F81ED3E4209 == null) || (strlen($R4DE86CA7A871DCFC0AE70F81ED3E4209) <= 0) ) {              $this->errorCode = SVStatus::OPERATION_FAILED_ERROR;              $this->errorMessage = SVStatus::getMessage($this->errorCode);              return;          }                   try{              parse_str($R4DE86CA7A871DCFC0AE70F81ED3E4209, $this->params);                           foreach($this->params as $RF413F06AEBBCEF5E1C8B1019DEE6FE6B => $R7D0596C36891967F3BB9D994B4A97C19) {                  $R49C28E565E4A351EFD3EB2017457422C = substr($RF413F06AEBBCEF5E1C8B1019DEE6FE6B, 0, 1);                  if(strcasecmp($R49C28E565E4A351EFD3EB2017457422C, "0") == 0) {                      unset($this->params[$RF413F06AEBBCEF5E1C8B1019DEE6FE6B]);                      break;                  }              }          }catch (Exception $R0D54236DA20594EC13FC81B209733931){              echo 'Response Parsing exception:'.$R0D54236DA20594EC13FC81B209733931->getMessage();          }                     $this->errorCode = $this->getValue(SVTags::RESPONSE_CODE);          $this->errorMessage = $this->getValue(SVTags::RESPONSE_MESSAGE);      }                public function getErrorCode() {          return $this->errorCode;      }                 public function getErrorMessage() {          return $this->errorMessage;      }              public function getCardNumber()      {          return $this->getValue(SVTags::CARD_NUMBER);      }                     public function getCardPin()      {          return $this->getValue(SVTags::CARD_PIN);      }                 public function getCurrentBatchNumber()      {          return $this->getValue(SVTags::CURRENT_BATCH_NUMBER);      }                   public function getTransactionId()      {          return $this->getValue(SVTags::TRANSACTION_ID);      }                   public function getTerminalId()      {          return $this->getValue(SVTags::TERMINAL_ID);      }                   public function getTrackData()      {          return $this->getValue(SVTags::TRACK_DATA);      }                   public function getPhoneNumber()      {          return $this->getValue(SVTags::PHONE_NUMBER);      }                       public function getApprovalCode()      {          return $this->getValue(SVTags::APPROVAL_CODE);      }                       public function getTransactionTypeId()      {          return $this->getValue(SVTags::TRANSACTION_TYPE_ID);      }                     public function getFirstName()      {          return $this->getValue(SVTags::FIRST_NAME);      }           public function getLastName()      {          return $this->getValue(SVTags::LAST_NAME);      }                   public function getCardExpiry()      {          return $this->getDate(SVTags::EXPIRY, SVUtils::QC_EXPIRY_DATE_FORMAT);               }                   public function getDateAtServer()      {          return $this->getDate(SVTags::DATE_AT_SERVER, SVUtils::QC_SERVER_DATE_FORMAT);      }                   public function getCardBalance()      {          return $this->getValue(SVTags::CARD_BALANCE);      }                   public function getAmount()      {          return $this->getValue(SVTags::AMOUNT);      }                   public function getCardType()      {          return $this->getValue(SVTags::CARD_TYPE);      }                   public function getCorporateName()      {          return $this->getValue(SVTags::CORPORATE_NAME);      }                   public function getCardProgramGroupName()      {          return $this->getValue(SVTags::CARD_PROGRAM_GROUP_NAME);      }                   public function getInvoiceNumber()      {          return $this->getValue(SVTags::INVOICE_NUMBER);      }                   public function getTransferCardNumber()      {          return $this->getValue(SVTags::TRANSFER_CARD_NUMBER);      }                   public function getTransferCardExpiry()      {          return $this->getValue(SVTags::TRANSFER_CARD_EXPIRY);      }                   public function getTransferCardBalance()      {          return $this->getValue(SVTags::TRANSFER_CARD_BALANCE);      }                   public function getEmbossingRecord()      {          return $this->getValue(SVTags::EMBOSSING_RECORD);      }                   public function getSettlementDate()      {          return $this->getValue(SVTags::SETTLEMENT_DATE, SVUtils.QC_SERVER_DATE_FORMAT);      }                   public function getActivationAmount()      {          return $this->getValue(SVTags::ACTIVATION_AMOUNT);      }                   public function getActivationCount()      {          return $this->getValue(SVTags::ACTIVATION_COUNT);      }                   public function getRedemptionCount()      {          return $this->getValue(SVTags::REDEEM_COUNT);      }                   public function getRedemptionAmount()      {          return $this->getValue(SVTags::REDEEM_AMOUNT);      }                   public function getCancelActivationAmount()      {          return $this->getValue(SVTags::CANCEL_ACTIVATION_AMOUNT);      }                   public function getCancelActivationCount()      {          return $this->getValue(SVTags::CANCEL_ACTIVATION_COUNT);      }                   public function getReloadAmount()      {          return $this->getValue(SVTags::RELOAD_AMOUNT);      }                   public function getReloadCount()      {          return $this->getValue(SVTags::RELOAD_COUNT);      }                   public function getCancelLoadAmount()      {          return $this->getValue(SVTags::CANCEL_LOAD_AMOUNT);      }                   public function getCancelLoadCount()      {          return $this->getValue(SVTags::CANCEL_LOAD_COUNT);      }                   public function getRedeemAmount()      {          return $this->getValue(SVTags::REDEEM_AMOUNT);      }                   public function getRedeemCount()      {          return $this->getValue(SVTags::REDEEM_COUNT);      }                   public function getCancelRedeemAmount()      {          return $this->getValue(SVTags::CANCEL_REDEEM_AMOUNT);      }                   public function getCancelRedeemCount()      {          return $this->getValue(SVTags::CANCEL_REDEEM_COUNT);      }                   public function getNewBatchNumber()      {          return $this->getValue(SVTags::NEW_BATCH_NUMBER);      }                 public function getPreviousBalance()      {          return $this->getValue(SVTags::PREV_BALANCE);      }                 public function getPromotionalValue()      {          return $this->getValue(SVTags::PROMOTIONAL_VALUE);      }                 public function getTransactionAmountConvertedValue()      {          return $this->getValue(SVTags::TRANSACTION_AMOUNT_CONVERTED_VALUE);      }                  public function getCardCurrencySymbol()      {          return $this->getValue(SVTags::CARD_CURRENCY_SYMBOL);      }                  public function getCurrencyConversionRate()      {          return $this->getValue(SVTags::CURRENCY_CONVERSION_RATE);      }                  public function getCurrencyConvertedAmount()      {          return $this->getValue(SVTags::CURRENCY_CONVERTED_AMOUNT);      }                 public function getCardHolderName()      {          return $this->getValue(SVTags::CARD_HOLDER_NAME);      }                 public function getEmployeeId()      {          return $this->getValue(SVTags::EMPLOYEE_ID);      }                   public function getSvType()      {          return $this->getValue(SVTags::SV_TYPE);      }                       public function getSvConvertedAmount()      {          return $this->getValue(SVTags::SV_CONVERTED_AMOUNT);      }                       public function getTransactionAmount()      {          return $this->getValue(SVTags::XACTION_AMOUNT);      }                       public function getEarnedValue()      {          return $this->getValue(SVTags::EARNED_VALUE);      }                       public function getSalutation()      {          return $this->getValue(SVTags::SALUTATION);      }                       public function getAddress1()      {          return $this->getValue(SVTags::ADDRESS1);      }                       public function getAddress2()      {          return $this->getValue(SVTags::ADDRESS2);      }                       public function getArea()      {          return $this->getValue(SVTags::AREA);      }                       public function getCity()      {          return $this->getValue(SVTags::CITY);      }                       public function getState()      {          return $this->getValue(SVTags::STATE);      }                       public function getCountry()      {          return $this->getValue(SVTags::COUNTRY);      }                       public function getPinCode()      {          return $this->getValue(SVTags::PIN_CODE);      }                       public function getAlternatePhoneNumber()      {          return $this->getValue(SVTags::PHONE_ALTERNATE);      }                       public function getEmail()      {          return $this->getValue(SVTags::EMAIL);      }                       public function getDOB()      {          return $this->getValue(SVTags::DOB);      }                       public function getAnniversary()      {          return $this->getValue(SVTags::ANNIVERSARY);      }                       public function getGender()      {          return $this->getValue(SVTags::GENDER);      }                       public function getMaritalStatus()      {          return $this->getValue(SVTags::MARITAL_STATUS);      }                       public function getEnrolledStore()      {          return $this->getValue(SVTags::ENROLLED_STORE);      }                       public function getEnrolledSince()      {          return $this->getValue(SVTags::ENROLLED_SINCE);      }                       public function getCardStatus()      {          return $this->getValue(SVTags::CARD_STATUS);      }                       public function getOutstandingBalance()      {          return $this->getValue(SVTags::OUTSTANDING_BALANCE);      }                       public function getCardExpiryDate()      {          return $this->getValue(SVTags::CARD_EXPIRY_DATE);      }                       public function getCustomerValidationForRedemption()      {          return $this->getValue(SVTags::CUSTOMER_VALIDATION_FOR_REDEMPTION);      }                       public function getMeetsMinimumRedemptionCriteria()      {          return $this->getValue(SVTags::MEETS_MINIMUM_REDEMPTION_CRITERIA);      }                       public function getReadyForRedemption()      {          return $this->getValue(SVTags::READY_FOR_REDEMPTION);      }                       public function getCumulativeAmountSpent()      {          return $this->getValue(SVTags::CUMULATIVE_AMOUNT_SPENT);      }                       public function getRecentTransactions()      {          return SVTransactionInfo::getTransactions($this->getValue(SVTags::RECENT_TRANSACTIONS));      }                 public function getPosName() { return $this->getValue(SVTags::POS_NAME); }      public function getPosTypeId() { return $this->getValue(SVTags::POS_TYPE_ID); }      public function getPosEntryMode() { return $this->getValue(SVTags::POS_ENTRY_MODE); }      public function getMerchantOutletName() { return $this->getValue(SVTags::MERCHANT_OUTLET_NAME); }      public function getMerchantName() { return $this->getValue(SVTags::MERCHANT_NAME); }      public function getOrganizationName() { return $this->getValue(SVTags::ORGANIZATION_NAME); }      public function getAcquirerId() { return $this->getValue(SVTags::ACQUIRER_ID); }                                      public final function getParams() {          return $this->params;      }    }  ?>
