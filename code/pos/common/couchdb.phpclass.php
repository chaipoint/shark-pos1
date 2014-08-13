<?php

/*$cd = new couchPHP();
echo "<pre>";
print_r($cd->fn);
echo "</pre>";/**/

class CouchPHP{
	private $url;
	private $port;
	private $userName;
	private $password;
	private $db;
	private $genUrl;
	private $isPost = false;
	private $postData = array();
	private $allowContentType = false;
	private $remote;
	function __construct(){
		$this->port = "5984";
		$this->url = 'http://127.0.0.1:'.$this->port."/";
		$this->db = 'vente_pos_db';
		$this->userName = '';
		$this->password = '';
		$this->remote = 'http://pos:pos@54.249.247.15:5984/vente_ho_db';
	}
	public function getUrl(){
		return $this->url;
	}
	public function getRemote(){
		return $this->remote;
	}
	public function getDB(){
		return $this->db;
	}
	public function version(){
		return $this->curl($this->url);
	}

	public function getAllDbs(){
		return $this->curl($this->url."_all_dbs");		
	}
	public function saveDocument($bulk = false){
		$this->allowContentType = true;
		$this->genUrl = $this->url.$this->db;
		if($bulk){
			$this->genUrl .= "/_bulk_docs";
		}
		return $this;
	}

	public function getDbDetails(){
		$db = trim($this->db);
		if(empty($db)){
			return '{"error":true,"reason":"no database provided"}';
		}
		return $this->curl($this->url.$db);		
	}

	public function getDocs($docId=""){
		$db = trim($this->db);
		if(empty($db)){
			return '{"error":true,"reason":"no database provided"}';
		}
		$url = $this->url.$db;
		$doc_id  = trim($docId);
		if(!empty($doc_id)){
			return $this->curl($this->url.$db."/".$doc_id);
		}
		return $this->curl($this->url.$db."/_all_docs");		
	}

	public function getDesign($design){
		$this->genUrl = $this->url.$this->db."/_design/".$design;
		return $this;
	}
	public function getView($view,$key=0){
		$this->genUrl .= "/_view/".$view;
		if(is_numeric($key) && $key > 0){
			$this->genUrl .= '?key="'.$key.'"';
		}
		return $this;
	}
	public function getList($list,$map){
		$this->genUrl .= "/_list/".$list."/".$map;
		return $this;
	}
	public function replicate(){
		$this->genUrl = $this->url.'_replicate';
		return $this;
	}
	public function getUpdate($update,$docId){
		$this->isPost = true;
		$this->postData = array();
		$this->genUrl .= "/_update/".$update."/".$docId;
		return $this;
	}
	public function execute($data = array()){
		if(is_array($data) && count($data)>0){
			$this->isPost = true;
			$this->postData = $data;
			$this->allowContentType = true;
		}
		return $this->curl($this->genUrl);
	}
	public function executeRemote($extra = ''){
		$url = $this->remote.'/'.$extra;
		$result = $this->curl($url);
		
		return $result;
	}
	public function setParam($paramList){
		$this->genUrl .= '?'.http_build_query($paramList);
		return $this;

	}
	public function getLastUrl(){
		return $this->genUrl;
	}

	private function curl($url){
		$ch = curl_init();
       	curl_setopt($ch, CURLOPT_URL,$url);
       	if($this->isPost){
       		$this->isPost = false;
       		curl_setopt($ch, CURLOPT_POST, TRUE); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, stripslashes(json_encode($this->postData)));
            //echo json_encode($this->postData);
       		$this->postData = array();
       	}
       	if($this->allowContentType){
	       	$this->allowContentType = false;
	       	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json"));
	     }
        curl_setopt($ch, CURLOPT_NOBODY, FALSE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
       	$result = curl_exec($ch); 
       	curl_close($ch);
       	return json_decode($result,true);
	}
}