﻿<?php /* This file encoded by Raizlabs PHP Obfuscator http://www.raizlabs.com/software */ ?>
<?php          require_once 'SVUtils.php';    class SVQcsData {           protected $RC2D2567438B1F39DD71F78195B5F3DED = array();        public final function getValue($RF413F06AEBBCEF5E1C8B1019DEE6FE6B) {          $R7D0596C36891967F3BB9D994B4A97C19 = null;          if (array_key_exists($RF413F06AEBBCEF5E1C8B1019DEE6FE6B, $this->params)) {              $R7D0596C36891967F3BB9D994B4A97C19 = $this->params[$RF413F06AEBBCEF5E1C8B1019DEE6FE6B];          }          return $R7D0596C36891967F3BB9D994B4A97C19;      }        public final function setValue($RF413F06AEBBCEF5E1C8B1019DEE6FE6B, $R7D0596C36891967F3BB9D994B4A97C19) {          $this->params[$RF413F06AEBBCEF5E1C8B1019DEE6FE6B] = $R7D0596C36891967F3BB9D994B4A97C19;      }        public final function getDate($RF413F06AEBBCEF5E1C8B1019DEE6FE6B, $REB66C46D3229C1C94FB923CAA907A5B5) {          $R7D0596C36891967F3BB9D994B4A97C19 = $this->getValue($RF413F06AEBBCEF5E1C8B1019DEE6FE6B);          return SVUtils::getDate($R7D0596C36891967F3BB9D994B4A97C19, $REB66C46D3229C1C94FB923CAA907A5B5);      }        protected function printParams($R1130829E6C4C8DBF13408502FB280443) {          echo('<p>'.$R1130829E6C4C8DBF13408502FB280443.'<BR><BR>');          foreach($this->params as $RF413F06AEBBCEF5E1C8B1019DEE6FE6B => $R7D0596C36891967F3BB9D994B4A97C19) {              echo($RF413F06AEBBCEF5E1C8B1019DEE6FE6B.' = '.$R7D0596C36891967F3BB9D994B4A97C19.'<BR>');          }      }  }  ?>
