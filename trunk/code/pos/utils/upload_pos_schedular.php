<?php
	class utils extends App_config{
		function __construct(){
			parent::__construct();
			$this->log =  Logger::getLogger("CP-POS|Window Schedular");
		}
		
		$this->billing();
		function billing(){
			$this->log->trace('Upload POS Data USing Window Schedular');
			$return = array('error'=>false,'message'=>'');
			$source = $this->cDB->getUrl().$this->cDB->getDB();
			$result = $this->cDB->replicate()->execute(array('source'=>$source, 'target'=>$this->cDB->getRemote(), 'filter'=>'doc_replication/bill_replication', "continuous" => true));
			$this->log->trace('Start Uploading POS Data'.json_encode($result));
			if(array_key_exists('ok', is_null($result) ? array() : $result)){
				$return['message'] = 'Process Start SuccessFully';
			}else{
				$return = array('error'=>true,'message'=>'OOPS! Some Problem Contact Admin');
			}
			return $return; 
		}
	}