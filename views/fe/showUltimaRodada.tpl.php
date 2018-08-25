<?php

$categoria = $data;

if (!$categoria) $categoria = 'misto';

$rodadanome = 'ultima_rodada';

$jogos = new jogos();
$idsJogos = $jogos->get_ids($rodadanome);

$rodada = new rodada(2);
echo "<center><h1>{$rodada->nome}</h1></center>";
echo "<center><h4>{$rodada->data}</h4></center>";

$linhas = '';
foreach ($idsJogos as $id){
    $jogo = new jogo();
    $jogo->get($id,$rodadanome);
    $quadra = new quadra($jogo->quadra);
    $linha = array();
    $linha[] = $quadra->nome;
    if ($jogo->desafio==1) $desafio = ' (desafio)'; else $desafio = '';
    $linha[] = $jogo->ranking . $desafio;
    $desafiante = new jogador($jogo->desafiante);
    $linha[] = ($jogo->vencedor==$desafiante->id) ? "({$desafiante->posicao})  <b>$desafiante->nome_completo</b>" : "({$desafiante->posicao})  $desafiante->nome_completo";
    $desafiado = new jogador($jogo->desafiado);
    $linha[] = ($jogo->vencedor==$desafiado->id) ? "({$desafiado->posicao})  <b>$desafiado->nome_completo</b>" : "({$desafiado->posicao})  $desafiado->nome_completo";
    if ($jogo->ocorrido){
        $linha[100] = $jogo->desafiante_sets . "x" . $jogo->desafiado_sets;
        $linha[100].= ($jogo->parciais) ?  " [{$jogo->parciais}]" : '';
    }
    else {
        $linha[100] = "-";
        if ($jogo->woduplo==1) $linha[100] = "WO Duplo";
        if ($jogo->cancelado==1) $linha[100] = "Cancelado";
    }
    $linhas.= "<tr><td>" . implode("</td><td>",$linha) . "</td></tr>\n";
}
$cabecalho = array('QUADRA/HORÁRIO','CATEGORIA','DESAFIANTE','DESAFIADO','PLACAR');
$cabecalho = "<tr><th>" . implode("</th><th>",$cabecalho) . "</th></tr>\n";
$tabela = "<table class='table table-striped table-hover'>\n $cabecalho $linhas </table>";
echo $tabela;