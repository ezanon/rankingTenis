<?php

echo $data;
global $url;
$url .= "?module=admin&action=nova_rodada#" . $_POST['ranking'];
header("Refresh: 3; URL=$url");

?>