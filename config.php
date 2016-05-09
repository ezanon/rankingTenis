<?php

$config = array(
	'driver' => 'mysql',
	'host' => 'localhost',
	'database' => 'rankingtenis',
	'user' => 'rankingtenis',
	'pass' => 'DxVhDKPPNRcyHVVd',
	'pass_admin' => 'ed05c6f3ba85a201a478267fa6ef9e2a' 
		// 'bontenis!@#'
);

$config_dev = array(
	'driver' => 'mysql',
	'host' => 'localhost',
	'database' => 'rankingtenis',
	'user' => 'rankingtenis',
	'pass' => 'DxVhDKPPNRcyHVVd',
	'pass_admin' => 'ed05c6f3ba85a201a478267fa6ef9e2a' 
		// 'bontenis!@#'
);

/*$dev = false;
if ($dev){
	$config = $config_dev;
	echo "<p>### AMBIENTE DESENVOLVIMENTO ###</p>";
}
else {
	error_reporting(0);
}*/


$url = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'];

?>