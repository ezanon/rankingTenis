<?php

$version = '2.1.0';

require_once('config.php');

//$url = 'https://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'];
$url = $url . $_SERVER['PHP_SELF'];

session_name("RANKINGTENISCEPEUSP");
session_start();


function appload($classe) {
	$dir = dirname(__FILE__);
	$arquivo = "$dir/app/$classe.php";
	
	if(file_exists($arquivo)) {
		require_once($arquivo);
		return true;
	}
}

function modload($classe) {
	$dir = dirname(__FILE__);
	$arquivo = "$dir/app/modules/$classe.php";
	
	if(file_exists($arquivo)) {
		require_once($arquivo);
		return true;
	}
}

function libload($classe) {
	$dir = dirname(__FILE__);
	$arquivo = "$dir/lib/$classe.php";
	
	if(file_exists($arquivo)) {
		require_once($arquivo);
		return true;
	}
}

spl_autoload_register('appload');
spl_autoload_register('modload');
spl_autoload_register('libload');

?>