﻿<?php /* This file encoded by Raizlabs PHP Obfuscator http://www.raizlabs.com/software */ ?>
<?php        class SVStatus {      const SUCCESS = 0;      const HTTP_OK = 200;      const TOTALS_MATCH_BATCH_CLOSED = 0;      const TOTALS_MISMATCH_BATCH_CLOSED = 99;     const UNKNOWN_ERROR = 801;   const INVALID_PARAM = 802;   const NOT_INITIALIZED_ERROR = 803;   const SERVICE_UNAVAILABLE_ERROR = 804;   const OPERATION_FAILED_ERROR = 805;   const CONNECTION_TIMEOUT_ERROR = 806;   const REQUEST_SEND_ERROR = 807;   const UNSUPPORTED_TYPE_ERROR = 808;     const INVALID_VALUE = -1;           private static $R759D9F21D715F63E6FD59F1373406F22 = array(    SVStatus::UNKNOWN_ERROR => "Encountered Error(UNKNOWN) while performing this operation",    SVStatus::INVALID_PARAM => "Invalid Parameter",    SVStatus::NOT_INITIALIZED_ERROR => "SVClient Library is Not Initialized",    SVStatus::SERVICE_UNAVAILABLE_ERROR => "Service Unavailable",    SVStatus::OPERATION_FAILED_ERROR => "Operation Failed. Did not receive any response",    SVStatus::CONNECTION_TIMEOUT_ERROR => "The Connection for this operation timed out",    SVStatus::REQUEST_SEND_ERROR => "Unable to send the request data to the Server",    SVStatus::UNSUPPORTED_TYPE_ERROR => "This API currently does not support the requested interface type"          );           public static function getMessage($RE2A6348A524DA39F3A55BC3C1C4497F5) {          return SVStatus::$R759D9F21D715F63E6FD59F1373406F22[$RE2A6348A524DA39F3A55BC3C1C4497F5];      }          public static function getHttpMessage($RC5BF58FE5CEF9C7E5CCB3F0606FA6866)   {          $RBC0B25587C784C1297B2623E4F9D5F03 = "HTTP Error [";          $RBC0B25587C784C1297B2623E4F9D5F03 .= $RC5BF58FE5CEF9C7E5CCB3F0606FA6866->getStatus();          $RBC0B25587C784C1297B2623E4F9D5F03 .= "] : ";          $RBC0B25587C784C1297B2623E4F9D5F03 .= $RC5BF58FE5CEF9C7E5CCB3F0606FA6866->getReasonPhrase();    return $RBC0B25587C784C1297B2623E4F9D5F03;   }  }  ?>
