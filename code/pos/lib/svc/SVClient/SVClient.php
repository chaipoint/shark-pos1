﻿<?php /* This file encoded by Raizlabs PHP Obfuscator http://www.raizlabs.com/software */ ?>
<?php    require_once 'HTTP/Request2.php';  require_once 'SVProperties.php';  require_once 'SVParamDownload.php';      class SVClient {        public static function initLibrary($RA8C93126CF7345E5E077A4614741E050, $R65DFACB39960C22313740A131148FB81 = null)   {    return SVClient::getParamsFromServer($RA8C93126CF7345E5E077A4614741E050, $R65DFACB39960C22313740A131148FB81);   }          private static function getParamsFromServer($RA8C93126CF7345E5E077A4614741E050, $R65DFACB39960C22313740A131148FB81 = null)   {    if ($RA8C93126CF7345E5E077A4614741E050 == null)          {              $R80A6475C281165B67857D6EAFE633F34 = new SVProperties();              $R80A6475C281165B67857D6EAFE633F34->setErrorCode(SVStatus::INVALID_PARAM);              $R80A6475C281165B67857D6EAFE633F34->setErrorMessage(SVStatus::getMessage( SVStatus::INVALID_PARAM));              return $R80A6475C281165B67857D6EAFE633F34;            }             $R79EE178E43B2F2F1776DA1505EAA45B7 = null; $RED2B7A9821D29A47BDBB6136CA1F99A3 = null;          if($R65DFACB39960C22313740A131148FB81 != null)           $RED2B7A9821D29A47BDBB6136CA1F99A3 = $R65DFACB39960C22313740A131148FB81;          else           $RED2B7A9821D29A47BDBB6136CA1F99A3 = $RA8C93126CF7345E5E077A4614741E050->getSvType();          switch($RED2B7A9821D29A47BDBB6136CA1F99A3)    {     case SVType::WEBPOS_GIFTCARD:      $R79EE178E43B2F2F1776DA1505EAA45B7 = SVClient::getWebposGiftcardParamDownload($RA8C93126CF7345E5E077A4614741E050);      break;     case SVType::WEBPOS_LOYALTY:      $R79EE178E43B2F2F1776DA1505EAA45B7 = SVClient::getWebposLoyaltyParamDownload($RA8C93126CF7345E5E077A4614741E050);      break;     default:      return SVStatus::UNSUPPORTED_TYPE_ERROR;         }       $RAA9603BC76668260C895E0E3E96AEBCC = $R79EE178E43B2F2F1776DA1505EAA45B7->execute();    $R3E76EA730200D02D78F383403F3C809D = $RAA9603BC76668260C895E0E3E96AEBCC->getErrorCode();    if ($R3E76EA730200D02D78F383403F3C809D != SVStatus::SUCCESS)    {              $R80A6475C281165B67857D6EAFE633F34 = new SVProperties();              $R80A6475C281165B67857D6EAFE633F34->setErrorCode($R3E76EA730200D02D78F383403F3C809D);              $R80A6475C281165B67857D6EAFE633F34->setErrorMessage($RAA9603BC76668260C895E0E3E96AEBCC->getErrorMessage());              return $R80A6475C281165B67857D6EAFE633F34;    }             $REE8037EF503FD1D7D35EC0C0C3C280B3 = new SVProperties($RA8C93126CF7345E5E077A4614741E050);    $REE8037EF503FD1D7D35EC0C0C3C280B3->setTransactionId($RAA9603BC76668260C895E0E3E96AEBCC->getTransactionId());    $REE8037EF503FD1D7D35EC0C0C3C280B3->setPosEntryMode($RAA9603BC76668260C895E0E3E96AEBCC->getPosEntryMode());    $REE8037EF503FD1D7D35EC0C0C3C280B3->setPosTypeId($RAA9603BC76668260C895E0E3E96AEBCC->getPosTypeId());    $REE8037EF503FD1D7D35EC0C0C3C280B3->setAcquirerId($RAA9603BC76668260C895E0E3E96AEBCC->getAcquirerId());    $REE8037EF503FD1D7D35EC0C0C3C280B3->setOrganizationName($RAA9603BC76668260C895E0E3E96AEBCC->getOrganizationName());    $REE8037EF503FD1D7D35EC0C0C3C280B3->setMerchantName($RAA9603BC76668260C895E0E3E96AEBCC->getMerchantName());    $REE8037EF503FD1D7D35EC0C0C3C280B3->setMerchantOutletName($RAA9603BC76668260C895E0E3E96AEBCC->getMerchantOutletName());    $REE8037EF503FD1D7D35EC0C0C3C280B3->setPosName($RAA9603BC76668260C895E0E3E96AEBCC->getPosName());    $REE8037EF503FD1D7D35EC0C0C3C280B3->setCurrentBatchNumber($RAA9603BC76668260C895E0E3E96AEBCC->getCurrentBatchNumber());          $REE8037EF503FD1D7D35EC0C0C3C280B3->setErrorCode($RAA9603BC76668260C895E0E3E96AEBCC->getErrorCode());          $REE8037EF503FD1D7D35EC0C0C3C280B3->setErrorMessage($RAA9603BC76668260C895E0E3E96AEBCC->getErrorMessage());       return $REE8037EF503FD1D7D35EC0C0C3C280B3;   }          private static function getWebposGiftcardParamDownload($RA8C93126CF7345E5E077A4614741E050)   {    $R79EE178E43B2F2F1776DA1505EAA45B7 = new SVParamDownload($RA8C93126CF7345E5E077A4614741E050);    $R79EE178E43B2F2F1776DA1505EAA45B7->setForwardingEntityId($RA8C93126CF7345E5E077A4614741E050->getForwardingEntityId());    $R79EE178E43B2F2F1776DA1505EAA45B7->setForwardingEntityPassword($RA8C93126CF7345E5E077A4614741E050->getForwardingEntityPassword());    $R79EE178E43B2F2F1776DA1505EAA45B7->setTerminalId($RA8C93126CF7345E5E077A4614741E050->getTerminalId());    $R79EE178E43B2F2F1776DA1505EAA45B7->setUsername($RA8C93126CF7345E5E077A4614741E050->getUsername());    $R79EE178E43B2F2F1776DA1505EAA45B7->setPassword($RA8C93126CF7345E5E077A4614741E050->getPassword());    $R79EE178E43B2F2F1776DA1505EAA45B7->setTransactionId(1);    return $R79EE178E43B2F2F1776DA1505EAA45B7;   }          private static function getWebposLoyaltyParamDownload($RA8C93126CF7345E5E077A4614741E050)   {    $R79EE178E43B2F2F1776DA1505EAA45B7 = new SVParamDownload($RA8C93126CF7345E5E077A4614741E050);    $R79EE178E43B2F2F1776DA1505EAA45B7->setForwardingEntityId($RA8C93126CF7345E5E077A4614741E050->getForwardingEntityId());    $R79EE178E43B2F2F1776DA1505EAA45B7->setForwardingEntityPassword($RA8C93126CF7345E5E077A4614741E050->getForwardingEntityPassword());    $R79EE178E43B2F2F1776DA1505EAA45B7->setTerminalId($RA8C93126CF7345E5E077A4614741E050->getTerminalId());    $R79EE178E43B2F2F1776DA1505EAA45B7->setUsername($RA8C93126CF7345E5E077A4614741E050->getUsername());    $R79EE178E43B2F2F1776DA1505EAA45B7->setPassword($RA8C93126CF7345E5E077A4614741E050->getPassword());    $R79EE178E43B2F2F1776DA1505EAA45B7->setTransactionId(1);    return $R79EE178E43B2F2F1776DA1505EAA45B7;   }  }  ?>
