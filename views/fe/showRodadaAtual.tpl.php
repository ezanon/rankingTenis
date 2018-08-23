<?php

$categoria = $data;

$jogos = new jogos();
$idsJogos = $jogos->get_ids('rodada_atual');

echo '<center><h1>Rodada Atual</h1></center>';

$linhas = '';
foreach ($idsJogos as $id){
    $jogo = new jogo();
    $jogo->get($id,'rodada_atual');
    $quadra = new quadra($jogo->quadra);
    $linha = array();
    $linha[] = $quadra->nome;
    if ($jogo->desafio==1) $desafio = ' (desafio)'; else $desafio = '';
    $linha[] = $jogo->ranking . $desafio;
    $desafiante = new jogador($jogo->desafiante);
    $linha[] = "({$desafiante->posicao}) $desafiante->nome_completo";
    $desafiado = new jogador($jogo->desafiado);
    $linha[] = "({$desafiado->posicao})  $desafiado->nome_completo";
    if ($jogo->ocorrido) $linha[] = $jogo->desafiante_sets . "x" . $jogo->desafiado_sets . " : " . $jogo->parciais;
    else $linha[] = '-';
    $linhas.= "<tr><td>" . implode("</td><td>",$linha) . "</td></tr>\n";
}
$cabecalho = array('QUADRA/HORÁRIO','CATEGORIA','DESAFIANTE','DESAFIADO','PLACAR');
$cabecalho = "<tr><th>" . implode("</th><th>",$cabecalho) . "</th></tr>\n";
$tabela = "<table class='table table-striped'>\n $cabecalho $linhas </table>";
echo $tabela;
