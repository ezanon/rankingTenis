<?php

echo "Cores Redefinidas.";

global $url;
$url .= "?module=admin&action=listagem";
header("Refresh: 4; URL=$url");

?>