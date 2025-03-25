<?php

class jogador3 {

    public $banco;

    function __construct() {
        $this->banco = banco::instanciar();
        return true;
    }

    public function exibirJogadorComBadges($id, $fav, $cat = '', $mostrarDisponibilidade = true, $layout = 1) {
        // Buscar dados do jogador
        $sql = "SELECT id, nome_completo, posicao_misto, posicao_feminino, 
                   ranking_misto, ranking_feminino, categoria_misto, categoria_feminino,
                   barragem_misto, barragem_feminino, disponibilidade, 
                   rcsa_misto, jat_misto, rcsa_feminino, jat_feminino, 
                   priorizar, fav_misto, fav_feminino, ultimojogo_feminino
            FROM jogador WHERE id = :id";
        $dados = $this->banco->consultar($sql, ["id" => $id])[0];

        // Definir sufixo (misto ou feminino)
        $sufixo = ($cat == 'WTA') ? "feminino" : "misto";

        // Definir posição correta
        $posicao = $dados["posicao_$sufixo"];
        $rcsa = $dados["rcsa_$sufixo"];
        $jat = $dados["jat_$sufixo"];
        $priorizar = $dados["priorizar"];
        $disponibilidade = explode(",", $dados["disponibilidade"]);
        $categoria = $dados["categoria_$sufixo"];
        $barragem = $dados["barragem_$sufixo"];
        $rankingMisto = $dados["ranking_misto"];
        $rankingFeminino = $dados["ranking_feminino"];
        $ultimo = $dados["ultimojogo_feminino"];

        // Construir badges
        $badgePriorizar = ($priorizar == 1) ? "<span class='badge bg-primary text-white'>P</span> " : "";
        $badgeBola = ($fav == $id) ? "<span class='badge'>🥎</span> " : "";
        $badgeRcsa = "<span class='badge bg-warning text-dark'>RCSA:$rcsa</span> ";
        $badgeJat = "<span class='badge bg-info text-white'>JAT:$jat</span> ";
        $badgePosicao = "<span class='badge bg-success text-white'>#$posicao</span> ";
        $badgeCategoria = "<span class='badge bg-secondary text-white'>$categoria</span> ";
        $badgeBarragem = "<span class='badge bg-light text-black'>B$barragem</span>";
        $badgeFeminino = "";
        if ($rankingFeminino and $rankingMisto) {
            $r = $ultimo ? 'Femin' : 'Misto';
            $badgeFeminino = "<span class='badge bg-secondary text-white'>last:$r</span>";
        }

        // Criar badges de disponibilidade (verde = disponível, vermelho = indisponível)
        $badgesDisponibilidade = "";
        if ($mostrarDisponibilidade) {
            for ($i = 1; $i <= 5; $i++) {
                $cor = in_array($i, $disponibilidade) ? "bg-success text-white" : "bg-danger text-white";
                $badgesDisponibilidade .= "<span class='badge $cor me-1'>$i</span>";
            }
        }

        // Organizar a ordem dos badges de acordo com o layout
        if ($layout == 1) {
            $badges = "$badgeRcsa$badgeJat";
        } else {
            $badges = "$badgeJat$badgeRcsa";
        }

        $id = "({$dados['id']})"; $id='';
        if ($layout == 1) {
            return "$badgePriorizar $badgeBola $badgePosicao {$dados['nome_completo']}$id $badgeFeminino<br>$badgeBarragem$badgeCategoria $badges $badgesDisponibilidade";
        } else {
            return "$badgeFeminino $id{$dados['nome_completo']} $badgePosicao $badgeBola $badgePriorizar<br>$badgesDisponibilidade $badges $badgeCategoria$badgeBarragem";
        }
    }
}
