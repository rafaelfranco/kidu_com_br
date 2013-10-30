<?php 
	#start session
	session_start();	


	#import configs
	include 'config/envinroments.php';
	include 'config/webservices.php';

	#define SimplePhp Path
	if($_SERVER['HTTP_HOST'] == DEVEVOPMENT_URL) {
		define('SIMPLEPHP_PATH', '/Library/Server/Web/Data/Sites/kidu_com_br/SimplePHP/');
		define('APP_PATH', '/Library/Server/Web/Data/Sites/kidu_com_br/app/');
		define('ELGG_WS','http://elgg.local/services/api/rest/json/');
		define('ELGG_WS_API_KEY','e5692e5a207837560efcc24de3ec7b466bb56e7e');
	}

	if($_SERVER['HTTP_HOST'] == PRODUCTION_URL) {
		#error_reporting(0);
		define('SIMPLEPHP_PATH', '/var/www/kidu_com_br/SimplePHP/');
		define('APP_PATH', '/var/www/kidu_com_br/app/');
		define('ELGG_WS','http://engine.kidu.com.br/services/api/rest/json/');
		define('ELGG_WS_API_KEY','e5692e5a207837560efcc24de3ec7b466bb56e7e');
	}
	

	define('LANGUAGE', 'pt-br');

	#import libraries
	#include SIMPLEPHP_PATH.'app/code/libs/MDB2.php';

	#init db connections
	#include 'config/db.php';
	
	#test if parameter is an user 
	#$sql = ("SELECT count(id) as qtd from usuario where url = 'rafafranco' ");
	#$qtd = $mdb2->loadModule('Extended')->getAll($sql, null, array(), '', MDB2_FETCHMODE_ASSOC);
	
	#include SimplePhp
	require SIMPLEPHP_PATH.'SimplePHP.php';
	

?>