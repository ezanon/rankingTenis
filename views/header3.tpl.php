<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // Evita cache em navegadores
header("Cache-Control: post-check=0, pre-check=0", false); // Compatibilidade com navegadores antigos
header("Pragma: no-cache"); // Diretiva para HTTP 1.0
?>

<html>
<head>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<link rel="stylesheet" href="estilos.css">
</head> 

<html>
    
 <?php
 require_once('config.php'); 
 global $dev;
 ?>
    
<div id="menuSuperior" class="container-fluid bg-warning">

<nav class="navbar navbar-expand-lg navbar-light bg-warning">
  <a class="navbar-brand text-dark" href="#">Ranking ADMIN<?php if ($dev) echo ' (DEV)'; ?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
      
    <ul class="navbar-nav mr-auto">
        
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Classificação
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="?module=admin3&action=showRanking&id=misto">Misto</a>
          <a class="dropdown-item" href="?module=admin3&action=showRanking&id=feminino">Feminino</a>
        </div>
      </li>
        
      <li class="nav-item">
        <a class="nav-link" href="?module=admin3&action=prepararNovaRodada">Nova Rodada</a>
      </li>
      
    </ul>
      
    <form class="form-inline my-2 my-lg-0">
      <!--<input class="form-control mr-sm-2" type="search" placeholder="Busque um jogador" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>-->
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link disabled text-info" href="index.php?module=fe&action=meuRanking">Meu Ranking</a>
        </li>
        <?php
        if (($_SESSION['acesso_autorizado']) and ($_SESSION['jogador']['admin']==1)){?>
            <li class="nav-item">
              <a class="nav-link disabled text-info" href="ranking.php">Sair</a>
            </li>
        <?php } ?>
      </ul>
    </form>
      
  </div>
</nav>    
    
</div>    
    
<div id="main" class="container-fluid">  

