﻿<?php /* This file encoded by Raizlabs PHP Obfuscator http://www.raizlabs.com/software */ ?>
<?php    require_once 'SVProperties.php';      class SVServerData {      public static function save($RA8C93126CF7345E5E077A4614741E050, $R1FCB858E12F43C96B8466B76542888EA) {           $RCBDDB47F1C2BFDBB49390A50C7E29292 = fopen($R1FCB858E12F43C96B8466B76542888EA, 'w') or die("can't open file");           $R7D0596C36891967F3BB9D994B4A97C19 = serialize($RA8C93126CF7345E5E077A4614741E050);           fwrite($RCBDDB47F1C2BFDBB49390A50C7E29292, $R7D0596C36891967F3BB9D994B4A97C19);           fclose($RCBDDB47F1C2BFDBB49390A50C7E29292);      }        public static function load($R1FCB858E12F43C96B8466B76542888EA) {                    $RCB14AD459444B18496BE02AADCA01BA8 = implode("", @file($R1FCB858E12F43C96B8466B76542888EA));          $RA8C93126CF7345E5E077A4614741E050 = unserialize($RCB14AD459444B18496BE02AADCA01BA8);          return $RA8C93126CF7345E5E077A4614741E050;      }  }  ?>
