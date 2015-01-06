<?php 
global $config;
/* CPOS DATABASE CONFIGRATION */
//$config['cpos_host'] = '54.249.247.15'; --test
$config['cpos_host'] = '127.0.0.1';
$config['cpos_username'] = 'root';
//$config['cpos_password'] = 'root'; --test
$config['cpos_password'] = 'mtf@9081';
$config['cpos_db'] = 'cabbeein_cpos';

/* HO DATABASE CONFIGRATION */
$config['ho_port'] = '5984';
//$config['ho_url'] = 'http://pos:pos@127.0.0.1:5984/';	--test
$config['ho_url'] = 'http://pos:pos@127.0.0.1:5984/';
$config['ho_db'] = 'sharkho';
