<?php

global $url, $dev;

$logando = new login();
echo $logando->logar();

if (!$_SESSION['acesso_autorizado']) {
	echo "<p>" . $data . "</p>";
//	header("Refresh: 3; URL=$url"); 	
}
else {
	echo $data;
	if ($_SESSION['jogador']['jogador']==1)
		$url .= "?module=fe&action=meuRankingOpcoes";
	else if ($_SESSION['jogador']['admin']==1)
		$url .= "?module=fe&action=meuRankingOpcoes";
//	header("Refresh: 2; URL=$url");
}

if ($dev){
    echo '<pre>SESSAO';
    echo print_r($_SESSION);
    echo '</pre>';   
}
?>

<a href=?module=fe&action=meuRankingOpcoes>Agora vai</a>