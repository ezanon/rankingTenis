<?php

global $url;
$url .= "?module=admin&action=listagem#" . $data;
header("Refresh: 0; URL=$url");

?>