﻿<?php /* This file encoded by Raizlabs PHP Obfuscator http://www.raizlabs.com/software */ ?>
<?php    require_once('SVRequest.php');  require_once('SVGiftCardCodes.php');      class SVBatchClose extends SVRequest{         function __construct($RA8C93126CF7345E5E077A4614741E050, $R65DFACB39960C22313740A131148FB81 = null, $RD9E28F8B534DDE2A4441ABC4AC7C66CC = null)   {          parent::__construct($RA8C93126CF7345E5E077A4614741E050, $R65DFACB39960C22313740A131148FB81, $RD9E28F8B534DDE2A4441ABC4AC7C66CC);   }        protected  function  getTransactionTypeId()   {    $R4C37C63CAC11C7E95ECC26F23AEFD7FD = 0;    switch($this->requestType)    {     case SVType::WEBPOS_GIFTCARD:     case SVType::WEBPOS_LOYALTY:      $R4C37C63CAC11C7E95ECC26F23AEFD7FD = SVGiftCardCodes::BATCH_CLOSE;      break;    }      return $R4C37C63CAC11C7E95ECC26F23AEFD7FD;   }       }  ?>
