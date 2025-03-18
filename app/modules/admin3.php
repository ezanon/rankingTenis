<?php

class admin3 {
	
	public $banco;
	public $confirma_todos_possiveis = false;
	
	function __construct(){
            $this->banco = banco::instanciar();
            return NULL;
	}
        
        function showRanking(){
            $ranking = $_GET['id'];
            return $this->gerarTabelaRanking($ranking);
        }
        
private function gerarTabelaRanking($sufixo) {
    // Monta a consulta SQL
    $sql = "SELECT id, posicao_$sufixo, nome_completo, telefone_celular, pontuacao_$sufixo, 
                   vitorias_$sufixo, jogos_$sufixo, rcsa_$sufixo, jat_$sufixo, 
                   tft_$sufixo, fav_$sufixo, wo_$sufixo, categoria_$sufixo, barragem_$sufixo
            FROM jogador 
            WHERE ranking_$sufixo = 1
            ORDER BY 
                FIELD(categoria_$sufixo, 'USP SLAM', 'USP MASTERS', 'USP 500', 'USP 250'),
                posicao_$sufixo ASC";

    $jogadores = $this->banco->consultar($sql);

    if (empty($jogadores)) {
        return "<p class='text-center'>Nenhum jogador encontrado.</p>";
    }

    $mostrarTelefone = isset($_SESSION['acesso_autorizado']) && $_SESSION['acesso_autorizado'] == 1;

    // Inicia a tabela
    $tabela = "<table class='table table-striped table-bordered text-center'>";
    $tabela .= "<thead class='thead-dark'><tr>
                    <th>Posição</th>
                    <th>Nome</th>";

    if ($mostrarTelefone) {
        $tabela .= "<th>Telefone</th>";
    }

    $tabela .= "<th>Pontos</th>
                <th>Vitórias/Jogos</th>
                <th>RCSA</th>
                <th>JAT</th>
                <th>TFT</th>
                <th>FAV</th>
                <th>WO</th>
            </tr></thead><tbody>";

    $categoriaAtual = null;
    $contador = 0;

    foreach ($jogadores as $jogador) {
        $contador++;

        // Se a categoria mudar, adiciona um separador
        if ($categoriaAtual !== $jogador["categoria_$sufixo"]) {
            $categoriaAtual = $jogador["categoria_$sufixo"];
            $tabela .= "<tr class='table-primary text-white'>
                            <td colspan='10'><strong>$categoriaAtual</strong></td>
                        </tr>";
        }

        // Badge de posição
        $posicaoBadge = "<span class='badge bg-success text-white'>{$jogador["posicao_$sufixo"]}</span>";

        // Badge de categoria
        if ($sufixo == "misto") {
            $categoriaBadge = "<span class='badge bg-danger text-white'>{$jogador["categoria_$sufixo"]}</span>";
        } else {
            $categoriaBadge = "<span class='badge bg-primary text-white'>WTA</span>";
        }

        // Badge de barragem
        $barragemBadge = "<span class='badge bg-dark text-white'>{$jogador["barragem_$sufixo"]}</span>";

        // Adiciona telefone se a sessão permitir
        $telefoneColuna = "";
        if ($mostrarTelefone) {
            $telefone = !empty($jogador['telefone_celular']) ? $jogador['telefone_celular'] : "<span class='text-muted'>Sem número</span>";
            $telefoneColuna = "<td>$telefone</td>";
        }

        // Badges para FAV
        $fav = $jogador["fav_$sufixo"];
        if ($fav == 0) {
            $favBadge = "<span class='badge bg-success'>$fav</span>"; // Verde 🟢
        } elseif ($fav == 1) {
            $favBadge = "<span class='badge bg-warning text-dark'>$fav</span>"; // Amarelo 🟡
        } else {
            $favBadge = "<span class='badge bg-danger'>$fav</span>"; // Vermelho 🔴
        }

        // Badge para WO
        $wo = $jogador["wo_$sufixo"];
        $woBadge = $wo > 0 ? "<span class='badge bg-danger'>$wo</span>" : "<span class='badge bg-success'>$wo</span>";

        // Monta a linha da tabela
        $tabela .= "<tr>
                        <td>$posicaoBadge</td>
                        <td>$barragemBadge $categoriaBadge {$jogador['nome_completo']}</td>
                        $telefoneColuna
                        <td>{$jogador["pontuacao_$sufixo"]}</td>
                        <td>{$jogador["vitorias_$sufixo"]}/{$jogador["jogos_$sufixo"]}</td>
                        <td>{$jogador["rcsa_$sufixo"]}</td>
                        <td>{$jogador["jat_$sufixo"]}</td>
                        <td>{$jogador["tft_$sufixo"]}</td>
                        <td>$favBadge</td>
                        <td>$woBadge</td>
                    </tr>";
    }

    $tabela .= "</tbody></table>";

    return $tabela;
}

public function prepararNovaRodada() {
    $tabelas = ""; 
    $rankings = ["misto", "feminino"];

    foreach ($rankings as $sufixo) {
        $sql = "SELECT id, nome_completo, pontuacao_$sufixo, disponibilidade, 
                       rcsa_$sufixo, jat_$sufixo, categoria_$sufixo, barragem_$sufixo, posicao_$sufixo,
                       ranking_feminino, ultimojogo_feminino
                FROM jogador 
                WHERE ranking_$sufixo = 1
                ORDER BY 
                    FIELD(categoria_$sufixo, 'USP SLAM', 'USP MASTERS', 'USP 500', 'USP 250'),
                    rcsa_$sufixo ASC, 
                    jat_$sufixo ASC, 
                    posicao_$sufixo ASC";

        $jogadores = $this->banco->consultar($sql);

        if (empty($jogadores)) {
            $tabelas .= "<p class='text-center'>Nenhum jogador encontrado no ranking $sufixo.</p>";
            continue;
        }

        $titulo = ($sufixo == "misto") ? "Ranking Misto" : "Ranking Feminino";
        $tabelas .= "<h3 class='text-center mt-4'>$titulo</h3>";

        $tabelas .= "<table class='table table-striped table-bordered text-center'>";
        $tabelas .= "<thead class='thead-dark'><tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Pontos</th>
                        <th>Disponibilidade</th>
                        <th>RCSA</th>
                        <th>JAT</th>
                    </tr></thead><tbody>";

        $categoriaAtual = null;
        $contador = 0;

        foreach ($jogadores as $jogador) {
            $contador++;

            // Se a categoria mudar, adiciona um separador
            if ($categoriaAtual !== $jogador["categoria_$sufixo"]) {
                $categoriaAtual = $jogador["categoria_$sufixo"];
                $tabelas .= "<tr class='table-primary text-white'>
                                <td colspan='6'><strong>$categoriaAtual</strong></td>
                             </tr>";
            }

            // Badge de posição
            $posicaoBadge = "<span class='badge bg-success text-white'>{$jogador["posicao_$sufixo"]}</span>";

            // Badge de categoria (USP SLAM, MASTERS, 500, 250)
            if ($sufixo == "misto") {
                $categoriaBadge = "<span class='badge bg-danger text-white'>{$jogador["categoria_$sufixo"]}</span>";
            } else {
                $categoriaBadge = "<span class='badge bg-primary text-white'>WTA</span>";
            }

            // Badge de barragem
            $barragemBadge = "<span class='badge bg-dark text-white'>{$jogador["barragem_$sufixo"]}</span>";

            // Badge de último jogo (para mulheres)
            $ultimoJogoBadge = "";
            if ($sufixo == "feminino" || $jogador["ranking_feminino"] == 1) {
                $ultimoJogoBadge = $jogador["ultimojogo_feminino"] == 1 
                    ? "<span class='badge bg-info text-white'>last: femin</span>" 
                    : "<span class='badge bg-secondary text-white'>last: misto</span>";
            }

            // Disponibilidade (1 a 5 em verde ou vermelho)
            $disponiveis = explode(",", $jogador["disponibilidade"]);
            $disponibilidadeBadges = "";
            for ($i = 1; $i <= 5; $i++) {
                $cor = in_array($i, $disponiveis) ? "bg-success text-white" : "bg-danger text-white";
                $disponibilidadeBadges .= "<span class='badge $cor me-1'>$i</span>";
            }

            // Monta a linha da tabela
            $tabelas .= "<tr>
                            <td>$contador</td>
                            <td>$posicaoBadge $barragemBadge $categoriaBadge {$jogador['nome_completo']} $ultimoJogoBadge</td>
                            <td>{$jogador["pontuacao_$sufixo"]}</td>
                            <td>$disponibilidadeBadges</td>
                            <td>{$jogador["rcsa_$sufixo"]}</td>
                            <td>{$jogador["jat_$sufixo"]}</td>
                        </tr>";
        }

        $tabelas .= "</tbody></table>";
    }

    return $tabelas;
}

public function atualizarCategoriasEBarragens() {
    $rankings = ["misto", "feminino"];

    foreach ($rankings as $sufixo) {
        // Busca os jogadores ordenados pela posição
        $sql = "SELECT id, posicao_$sufixo FROM jogador WHERE ranking_$sufixo = 1 ORDER BY posicao_$sufixo";
        $jogadores = $this->banco->consultar($sql);

        if (empty($jogadores)) continue;

        $updates = [];
        $categorias = ["USP SLAM", "USP MASTERS", "USP 500", "USP 250"];
        $limites = [40, 80, 120, PHP_INT_MAX]; // Máximo de jogadores por categoria

        $categoriaIndex = 0;
        $contadorCategoria = 0;

        foreach ($jogadores as $jogador) {
            $id = $jogador['id'];
            $posicao = $jogador["posicao_$sufixo"];

            // Se a categoria atingiu o limite, passa para a próxima
            if ($posicao > $limites[$categoriaIndex]) {
                $categoriaIndex++;
                $contadorCategoria = 0;
            }

            $categoria = $categorias[$categoriaIndex];
            $contadorCategoria++;

            // Define a barragem: 1 para os primeiros 20 da categoria, 2 para os seguintes
            $barragem = ($contadorCategoria <= 20) ? 1 : 2;

            // Adiciona a query para esse jogador
            $updates[] = "UPDATE jogador 
                          SET categoria_$sufixo = '$categoria', barragem_$sufixo = $barragem 
                          WHERE id = $id";
        }

        // Executa todas as queries
        foreach ($updates as $query) {
            $this->banco->executar($query);
        }
    }

    return "Categorias e Barragens atualizadas com sucesso!";
}
      
}