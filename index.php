<html>
<head>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<link rel="stylesheet" href="estilos.css">
</head> 

<html>
    
<div id="menuSuperior" class="container-fluid bg-dark">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand text-warning" href="#">Ranking Tênis CEPEUSP</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="?module=fe&action=showRodadaAtual">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Regulamento</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Misto
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="?module=fe&action=showRanking&id=misto">Ranking</a>
          <a class="dropdown-item" href="?module=fe&action=showRodadaAtual&id=misto">Rodada Atual</a>
          <a class="dropdown-item" href="?module=fe&action=showUltimaRodada&id=misto">Última Rodada</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Feminino
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="?module=fe&action=showRanking&id=feminino">Ranking</a>
          <a class="dropdown-item" href="?module=fe&action=showRodadaAtual&id=feminino">Rodada Atual</a>
          <a class="dropdown-item" href="?module=fe&action=showUltimaRodada&id=feminino">Última Rodada</a>
        </div>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <!--<input class="form-control mr-sm-2" type="search" placeholder="Busque um jogador" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>-->
      <ul class="navbar-nav mr-auto"><li class="nav-item">
        <a class="nav-link disabled text-warning" href="ranking.php">Login</a>
      </li></ul>
    </form>
  </div>
</nav>    
    
</div>    
    
<div class="container-fluid">

<?php

error_reporting(E_ALL); ini_set('display_errors', 1);
require('bootstrap.php');
new controller2(); 

?>    
    
</div>
    
<!-- RODAPE -->
<footer class="footer bg-dark">
    <div class="container">
        <span class="text-muted"><center>© 2012-<?php echo date('Y'); ?> :: Erickson Zanon :: ezanon@gmail.com</center></span>
    </div>
</footer>
<!-- RODAPE -->
    
</html>


