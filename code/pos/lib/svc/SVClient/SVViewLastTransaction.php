﻿<?php /* This file encoded by Raizlabs PHP Obfuscator http://www.raizlabs.com/software */ ?>
<?php    require_once('SVRequest.php');  require_once('SVGiftCardCodes.php');  require_once('SVLoyaltyCodes.php');      class SVViewLastTransaction extends SVRequest{         function __construct($R65DFACB39960C22313740A131148FB81)      {          parent::__construct($R65DFACB39960C22313740A131148FB81);      }        protected  function  getTransactionTypeId()      {          $R4C37C63CAC11C7E95ECC26F23AEFD7FD = 0;          switch($this->requestType)          {              case SVType::WEBPOS_GIFTCARD:                  $R4C37C63CAC11C7E95ECC26F23AEFD7FD = SVGiftCardCodes::VIEW_LAST_TRANSACTION;                  break;              case SVType::WEBPOS_LOYALTY:                  $R4C37C63CAC11C7E95ECC26F23AEFD7FD = SVLoyaltyCodes::VIEW_LAST_TRANSACTION;                  break;          }            return $R4C37C63CAC11C7E95ECC26F23AEFD7FD;      }       }  ?>
