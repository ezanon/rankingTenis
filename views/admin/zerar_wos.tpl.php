<?php

echo "WOs zerados. O número de WO agora é zero para todos os jogadores.";

global $url;
$url .= "?module=admin&action=listagem";
header("Refresh: 4; URL=$url");

?>