<?php 

echo 'Registro deletado.';

global $url;
$url .= "?module=admin&action=listagem";
header("Refresh: 2; URL=$url");

?>
