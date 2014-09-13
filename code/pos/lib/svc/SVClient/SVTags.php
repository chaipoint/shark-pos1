﻿<?php /* This file encoded by Raizlabs PHP Obfuscator http://www.raizlabs.com/software */ ?>
<?php        class SVTags {             const CARD_NUMBER                          = "CardNumber";      const CARD_PROGRAM_GROUP_NAME              = "CardProgramGroupName";      const CARD_PIN                             = "CardPIN";      const ADDON_CARD_NUMBER                    = "AddonCardNumber";      const TRANSACTION_TYPE_ID                  = "TransactionTypeID";      const AMOUNT                               = "Amount";      const DATE_AT_CLIENT                       = "DateAtClient";      const TRANSACTION_ID                       = "TransactionId";      const DATE_AT_SERVER                       = "DateAtServer";      const EXPIRY                               = "Expiry";      const POS_ENTRY_MODE                       = "POSEntryMode";      const POS_TYPE_ID                          = "POSTypeId";      const ACQUIRER_ID                          = "AcquirerId";      const TRACK_DATA                           = "TrackData";      const ADDON_CARD_TRACK_DATA                = "AddonCardTrackData";      const INVOICE_NUMBER                       = "InvoiceNumber";      const APPROVAL_CODE                        = "ApprovalCode";      const RESPONSE_CODE                        = "ResponseCode";      const TERMINAL_ID                          = "TerminalId";      const ORGANIZATION_NAME                    = "OrgName";      const MERCHANT_NAME                        = "MerchantName";      const MERCHANT_OUTLET_NAME                 = "MerchantOutletName";      const RESPONSE_MESSAGE                     = "ResponseMessage";      const OUTLET_ADDRESS_1                     = "OutletAddress1";      const OUTLET_ADDRESS_2                     = "OutletAddress2";      const OUTLET_CITY                          = "OutletCity";      const OUTLET_STATE                         = "OutletState";      const OUTLET_PIN_CODE                      = "OutletPinCode";      const OUTLET_TELEPHONE                     = "OutletTelephone";      const USER_ID                              = "UserId";      const PASSWORD                             = "Password";      const CURRENT_BATCH_NUMBER                 = "CurrentBatchNumber";      const ACTIVATION_COUNT                     = "ActivationCount";      const ACTIVATION_AMOUNT                    = "ActivationAmount";      const REDEEM_COUNT                         = "RedemptionCount";      const REDEEM_AMOUNT                        = "RedemptionAmount";      const RELOAD_COUNT                         = "ReloadCount";      const RELOAD_AMOUNT                        = "ReloadAmount";      const NEW_BATCH_NUMBER                     = "NewBatchNumber";      const INVOICE_NUMBER_MANDATORY             = "InvoiceNumberMandatory";      const MESSAGE_AUTHENTICATION_CODE          = "MessageAuthenticationCode";      const MASK_CARD_NUMBER                     = "MaskCardNumber";      const PRINT_MERCHANT_COPY                  = "PrintMerchantCopy";      const CORPORATE_NAME                       = "CorporateName";      const CARD_TYPE                            = "CardType";      const RECEIPT_FOOTER_LINE_1                = "ReceiptFooterLine1";      const RECEIPT_FOOTER_LINE_2                = "ReceiptFooterLine2";      const RECEIPT_FOOTER_LINE_3                = "ReceiptFooterLine3";      const RECEIPT_FOOTER_LINE_4                = "ReceiptFooterLine4";      const ORIGINAL_INVOICE_NUMBER              = "OriginalInvoiceNumber";      const ORIGINAL_TRANSACTION_ID              = "OriginalTransactionId";      const ORIGINAL_BATCH_NUMBER                = "OriginalBatchNumber";      const ORIGINAL_AMOUNT                      = "Amount";      const ORIGINAL_APPROVAL_CODE               = "OriginalApprovalCode";      const CANCEL_LOAD_COUNT                    = "CancelLoadCount";      const CANCEL_LOAD_AMOUNT                   = "CancelLoadAmount";      const CANCEL_REDEEM_COUNT                  = "CancelRedeemCount";      const CANCEL_REDEEM_AMOUNT                 = "CancelRedeemAmount";      const POS_NAME                             = "POSName";      const INTEGER_AMOUNTS                      = "IntegerAmounts";      const NUMERIC_USER_PASSWORD                = "NumericUserPwd";      const TERMINAL_APP_VERSION                 = "TermAppVersion";      const CANCEL_ACTIVATION_COUNT              = "CancelActivationCount";      const CANCEL_ACTIVATION_AMOUNT             = "CancelActivationAmount";      const FIRST_NAME                           = "Firstname";      const LAST_NAME                            = "Lastname";      const PHONE_NUMBER                         = "Phone";      const TRANSFER_CARD_NUMBER                 = "TransferCardNumber";      const TRANSFER_CARD_BALANCE                = "TransferCardBalance";      const TRANSFER_CARD_EXPIRY                 = "TransferCardExpiry";      const FORWARDING_ENTITY_ID                 = "ForwardingEntityId";      const FORWARDING_ENTITY_PASSWORD           = "ForwardingEntityPwd";      const DEACTIVATION_COUNT                   = "DeactivationCount";      const DEACTIVATION_AMOUNT                  = "DeactivationAmount";      const NOTES                                = "Notes";      const ADJUSTMENT_AMOUNT                    = "AdjustmentAmount";      const CURRENCY_CONVERSION_RATE             = "CurrencyConversionRate";      const CARD_CURRENCY_SYMBOL                 = "CardCurrencySymbol";      const CURRENCY_CONVERTED_AMOUNT            = "CurrencyConvertedAmount";      const EMPLOYEE_ID                          = "EmployeeId";      const CARD_HOLDER_NAME                     = "CardHolderName";      const PREV_BALANCE                         = "PreviousBalance";      const SALUTATION                           = "Salutation";      const ADDRESS1                             = "Address1";      const ADDRESS2                             = "Address2";      const AREA                                 = "Area";      const CITY                                 = "City";      const STATE                                = "State";      const COUNTRY                              = "Country";      const PIN_CODE                             = "PinCode";      const PHONE_ALTERNATE                      = "PhoneAlternate";      const EMAIL                                = "Email";      const DOB                                  = "DOB";      const ANNIVERSARY                          = "Anniversary";      const GENDER                               = "Gender";      const MARITAL_STATUS                       = "MaritalStatus";      const XACTION_AMOUNT                       = "XactionAmount";      const CARD_PROGRAM_ID                      = "CardProgramID";      const ENROLLED_STORE                       = "EnrolledStore";      const ENROLLED_SINCE                       = "EnrolledSince";      const CARD_STATUS                          = "CardStatus";      const OUTSTANDING_BALANCE                  = "OutstandingBalance";      const CARD_EXPIRY_DATE                     = "CardExpiryDate";      const CUSTOMER_VALIDATION_FOR_REDEMPTION   = "CustomerValidationForRedemption";      const MEETS_MINIMUM_REDEMPTION_CRITERIA    = "MeetsMinimumRedemptionCriteria";      const READY_FOR_REDEMPTION                 = "ReadyForRedemption";      const IS_PROXY                             = "IsProxy";      const IS_OFFLINE                           = "IsOffline";      const ACTUAL_MERCHANT_OUTLET_NAME          = "ActualMerchantOutletName";      const ADDITIONAL_TXN_REF_1                 = "AdditionalTxnRef1";      const CARD_BALANCE                         = "Amount";      const SETTLEMENT_DATE                      = "SettlementDate";      const EMBOSSING_RECORD                     = "EmbossingFileRecord";      const IIN                                  = "IIN";      const CULTURE_NAME                         = "CultureName";      const CURRENCY_SYMBOL                      = "CurrencySymbol";      const CURRENCY_POSITION                    = "CurrencyPosition";      const CURRENCY_DECIMAL_DIGITS              = "CurrencyDecimalDigits";      const DISPLAY_UNIT_FOR_POINTS              = "DisplayUnitForPoints";      const FROM_CARD_NUMBER                     = "FromCardNumber";      const FROM_CARD_PIN                        = "FromCardPin";      const TO_CARD_NUMBER                       = "ToCardNumber";      const BILL_AMOUNT                          = "BillAmount";      const PROMOTIONAL_VALUE                    = "PromotionalValue";      const TRANSACTION_AMOUNT_CONVERTED_VALUE   = "XactionAmountConvertedValue";      const SV_TYPE                              = "SVType";      const SV_CONVERTED_AMOUNT                  = "SVConvertedAmount";      const EARNED_VALUE                         = "EarnedValue";      const RECENT_TRANSACTIONS                  = "RecentTransactions";      const CUMULATIVE_AMOUNT_SPENT              = "CumulativeAmountSpent";      const ORIGINAL_CARD_NUMBER                 = "OriginalCardNumber";      const ORIGINAL_CARD_PIN                    = "OriginalCardPin";  }  ?>
