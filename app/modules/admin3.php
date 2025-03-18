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
                           tft_$sufixo, fav_$sufixo, wo_$sufixo 
                    FROM jogador 
                    WHERE categoria_$sufixo = 1 
                    ORDER BY posicao_$sufixo";

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

            $corClasses = ["table-light", "table-warning"]; // Alternância de cores a cada 20 linhas
            $contador = 0;

            foreach ($jogadores as $jogador) {
                $contador++;

                // Alterna a cor a cada 20 jogadores se for ranking misto
                $classeLinha = ($sufixo == "misto" && floor(($contador - 1) / 20) % 2 == 1) ? $corClasses[1] : $corClasses[0];

                // Define a categoria com base na posição
                $posicao = $jogador["posicao_$sufixo"];
                if ($sufixo == "feminino") {
                    $categoriaBadge = "<span class='badge badge-primary'>WTA</span>";
                } elseif ($posicao <= 40) {
                    $categoriaBadge = "<span class='badge badge-danger'>USP SLAM</span>";
                } elseif ($posicao <= 80) {
                    $categoriaBadge = "<span class='badge badge-warning'>USP MASTERS</span>";
                } elseif ($posicao <= 120) {
                    $categoriaBadge = "<span class='badge badge-success'>USP 500</span>";
                } else {
                    $categoriaBadge = "<span class='badge badge-info'>USP 250</span>";
                }

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
                $tabela .= "<tr class='$classeLinha'>
                                <td>{$jogador["posicao_$sufixo"]}</td>
                                <td>$categoriaBadge {$jogador['nome_completo']}</td>
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
    $tabelas = ""; // Armazena as tabelas geradas

    // Definir os rankings a serem processados (Misto e Feminino)
    $rankings = ["misto", "feminino"];

    foreach ($rankings as $sufixo) {
        // Consulta os jogadores de cada ranking
        $sql = "SELECT id, posicao_$sufixo, nome_completo, pontuacao_$sufixo, 
                       disponibilidade, rcsa_$sufixo, jat_$sufixo, 
                       categoria_misto, categoria_feminino, ultimojogo_feminino
                FROM jogador 
                WHERE categoria_$sufixo = 1
                ORDER BY posicao_$sufixo"; // Agora já ordena pela posição original

        $jogadores = $this->banco->consultar($sql);

        if (empty($jogadores)) {
            $tabelas .= "<p class='text-center'>Nenhum jogador encontrado no ranking $sufixo.</p>";
            continue;
        }

        // Define título da tabela
        $titulo = ($sufixo == "misto") ? "Ranking Misto" : "Ranking Feminino";
        $tabelas .= "<h3 class='text-center mt-4'>$titulo</h3>";

        // Agrupar os jogadores por barragem com base na posição original
        $barragens = [];
        foreach ($jogadores as $jogador) {
            $posicao = $jogador["posicao_$sufixo"];
            $barragemKey = ceil($posicao / 20); // Define a barragem (1-20, 21-40, etc.)
            $barragens[$barragemKey][] = $jogador;
        }

        // Se a última barragem tiver menos de 20 jogadores, mescla com a anterior
        $lastKey = array_key_last($barragens);
        if ($lastKey > 1 && count($barragens[$lastKey]) < 20) {
            $barragens[$lastKey - 1] = array_merge($barragens[$lastKey - 1], $barragens[$lastKey]);
            unset($barragens[$lastKey]);
        }

        // Inicia a tabela
        $tabelas .= "<table class='table table-striped table-bordered text-center'>";
        $tabelas .= "<thead class='thead-dark'><tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Pontos</th>
                        <th>Disponibilidade</th>
                        <th>RCSA</th>
                        <th>JAT</th>
                    </tr></thead><tbody>";

        // Exibição por barragem
        foreach ($barragens as $barragemKey => $listaJogadores) {
            if (empty($listaJogadores)) continue;

            // Exibir a barragem (1, 2, 3...)  
            $tabelas .= "<tr class='table-secondary'>
                            <td colspan='6'><strong>Barragem $barragemKey</strong></td>
                         </tr>";

            $contador = 0;

            foreach ($listaJogadores as $jogador) {
                $contador++;

                // Badge de posição (agora fica antes do nome)
                $posicaoBadge = "<span class='badge bg-success text-white'>{$jogador["posicao_$sufixo"]}</span>";

                // Badge de Último Jogo Feminino/Misto
                $ultimoJogoBadge = "";
                if ($sufixo == "feminino" || $jogador["categoria_feminino"] == 1) {
                    $ultimoJogoBadge = $jogador["ultimojogo_feminino"] == 1 
                        ? "<span class='badge bg-info text-white'>last: femin</span>" 
                        : "<span class='badge bg-secondary text-white'>last: misto</span>";
                }

                // Processa a disponibilidade (1 a 5 em verde ou vermelho)
                $disponiveis = explode(",", $jogador["disponibilidade"]);
                $disponibilidadeBadges = "";
                for ($i = 1; $i <= 5; $i++) {
                    $cor = in_array($i, $disponiveis) ? "bg-success text-white" : "bg-danger text-white";
                    $disponibilidadeBadges .= "<span class='badge $cor me-1'>$i</span>";
                }

                // Monta a linha da tabela
                $tabelas .= "<tr>
                                <td>$contador</td>
                                <td>$posicaoBadge {$jogador['nome_completo']} $ultimoJogoBadge</td>
                                <td>{$jogador["pontuacao_$sufixo"]}</td>
                                <td>$disponibilidadeBadges</td>
                                <td>{$jogador["rcsa_$sufixo"]}</td>
                                <td>{$jogador["jat_$sufixo"]}</td>
                            </tr>";
            }
        }

        $tabelas .= "</tbody></table>";
    }

    return $tabelas;
}





        
}