<?php

global $url;

if (!$_SESSION['acesso_autorizado']) {
	echo "<p>" . $data . "</p>";
	header("Refresh: 3; URL=$url"); 	
}
else {
	echo $data;
	if ($_SESSION['jogador']['jogador']==1)
		$url .= "?module=jogador&action=listar";
	else if ($_SESSION['jogador']['admin']==1)
		$url .= "?module=admin&action=listagem";
	header("Refresh: 2; URL=$url");
}


?>