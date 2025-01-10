<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // Evita cache em navegadores
header("Cache-Control: post-check=0, pre-check=0", false); // Compatibilidade com navegadores antigos
header("Pragma: no-cache"); // Diretiva para HTTP 1.0
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="views/css/estilo.css">
<title>Ranking Tenis CEPEUSP</title>
</head>
<body>
    <div id=all><p><a href="index.php">Ir para portal</a></p>
<h1>Ranking Tenis CEPEUSP <?php require_once('config.php'); global $dev; if ($dev) echo '(DEV)'; ?></h1>
