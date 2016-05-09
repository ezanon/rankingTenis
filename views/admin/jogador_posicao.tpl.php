<?php 

global $url;
$url .= "?module=admin&action=editar_jogador&id=" . $_GET['id'];
header("Refresh: 0; URL=$url");

?>
