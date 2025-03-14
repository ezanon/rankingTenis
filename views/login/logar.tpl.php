<?php

global $url, $dev;

if (($_SESSION['acesso_autorizado']) and ($_SESSION['admin']!=1)){
    $url .= "?module=admin&action=listagem";
    header("Refresh: 0; URL=$url");
}
else {
    echo "<p>" . $data . "</p>";
    header("Refresh: 3; URL=$url");
}
