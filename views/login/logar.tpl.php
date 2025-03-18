<?php

global $url, $dev;

if (($_SESSION['acesso_autorizado']) and ($_SESSION['admin']!=1)){
    $url .= "ranking.php?module=admin3&action=showRanking&id=misto";
    header("Refresh: 0; URL=$url");
}
else {
    echo "<p>" . $data . "</p>";
    header("Refresh: 3; URL=$url");
}
