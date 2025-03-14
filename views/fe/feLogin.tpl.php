<?php

global $url, $dev;

$logando = new login();
$str = $logando->logar();

if (!$_SESSION['acesso_autorizado']) {
	echo "<p>" . $str . "</p>";
        session_destroy();
//	header("Refresh: 3; URL=$url"); 	
}
else {
	echo $str;
	$url .= "?module=fe&action=meuRankingOpcoes";
	header("Refresh: 1; URL=$url");
}

//if ($dev){
//    echo '<p><pre>SESSAO<br>*<br>';
//    echo var_dump($_SESSION);
//    echo '*</pre></p>';   
//}



