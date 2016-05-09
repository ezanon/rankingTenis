<?php

echo $data;
global $url;
$url .= "?module=admin&action=rodada_atual";
header("Refresh: 3; URL=$url");
?>