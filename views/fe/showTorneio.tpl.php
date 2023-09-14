<?php 
global $torneio_planilha;
global $torneio_ano;
?>
<div id="torneio" class="container">

    <div class="card">
      <div class="card-header">
        Torneio <?php echo $torneio_ano;?>
      </div>
      <div class="card-body">
        <h5 class="card-title">Caso esteja com dificuldades para visualizar as chaves na sequência, clique no link abaixo para acessar a agenda e chaves do torneio no Google Planilhas.</h5>
        <p class="card-text">
            <a target="_blank" href='<?php echo $torneio_planilha;?>' class="btn btn-primary">Clique aqui!</a>
        </p>
      </div>
    </div>
    
</div>

<hr>

<div id="torneio" class="container-fluid">

    <style type="text/css">
    #iframe-holder {
        overflow:hidden;
        position: relative;
        top:0px;
        bottom:0px;
        left:0;
        right:0;
     }
    #iframe-holder iframe {
        width:100%;
        height:100%;
     }
    </style>

    <div id="iframe-holder">
        <iframe src='<?php echo $torneio_planilha;?>' frameborder="0"  name="main" scrolling="no"></iframe>
    </div>

</div>