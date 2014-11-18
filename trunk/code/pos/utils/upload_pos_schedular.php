<?php 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://localhost:2020/pos/index.php?dispatch=utils.billing');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$json = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($json);
	return $data;