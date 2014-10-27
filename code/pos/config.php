<?php
/*
	Config File Created By Rakesh Kaswan, rakeshkaswan8356@gmail.com 25/09/2014
	$config is array to hold global configuration for whole application
	@operating_mode is used in accessing URLs of production and test servers
		values:- test|production
*/
global $config;
$config['operating_mode'] = 'test';

/*
	Operation Mode test Local Details
*/
$config['test_cd_local_protocol'] = 'http';
$config['test_cd_local_url'] = '127.0.0.1';
$config['test_cd_local_port'] = '5984';
$config['test_cd_local_db'] = 'sharkpos';
$config['test_cd_local_username'] = 'pos';
$config['test_cd_local_password'] = 'pos';
/*
	Operation Mode test HO Details
*/
$config['test_cd_remote_protocol'] = 'http';
$config['test_cd_remote_url'] = '54.249.247.15';
$config['test_cd_remote_port'] = '5984';
$config['test_cd_remote_db'] = 'sharkho';
$config['test_cd_remote_username'] = 'pos';
$config['test_cd_remote_password'] = 'pos';
/*
	Operation Mode Production Local Details
*/
$config['production_cd_local_protocol'] = 'http';
$config['production_cd_local_url'] = '127.0.0.1';
$config['production_cd_local_port'] = '5984';
$config['production_cd_local_db'] = 'sharkpos';
$config['production_cd_local_username'] = 'pos';
$config['production_cd_local_password'] = 'pos';
/*
	Operation Mode Production HO Details
*/
$config['production_cd_remote_protocol'] = 'http';
$config['production_cd_remote_url'] = '54.178.189.25';
$config['production_cd_remote_port'] = '5984';
$config['production_cd_remote_db'] = 'sharkho';
$config['production_cd_remote_username'] = 'pos';
$config['production_cd_remote_password'] = 'pos';