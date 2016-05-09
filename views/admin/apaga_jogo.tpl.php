<?php

echo $data;
global $url;
$url .= "?module=admin&action=nova_rodada";
header("Refresh: 3; URL=$url");

?>